<?php

namespace App\Services;

use App\Models\ComprovanteACI;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\Parametro;
use App\Models\ValorAciAno;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Gamificacao\GamificacaoHook;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ComprovanteAciService
{

    /**
     * Porcentagem a ser passada pela sinodal da ACI para a CNM
     * @var float
     */
    public const PORCENTAGEM_SINODAL = 0.25;
    
    public static function store(Request $request) : ComprovanteACI
    {
        try {
            $comprovante = ComprovanteACI::updateOrCreate([
                'sinodal_id' => auth()->user()->sinodal_id,
                'ano' => $request->ano
            ], [
                'sinodal_id' => auth()->user()->sinodal_id,
                'ano' => $request->ano,
                'status' => false
            ]);

            if (!is_null($comprovante->path)) {
                $real_path = __DIR__ . '/../../storage/app/public';
                $complete_path = str_replace('/storage', $real_path ,$comprovante->path);
                unlink($complete_path);
            }

            if ($request->has('arquivo')) {
                $path = $request->file('arquivo')->store('public/comprovante_aci');
                $comprovante->update([
                    'path' => '/' . str_replace('public', 'storage', $path)
                ]);
            }

            GamificacaoHook::aposAci((string) $comprovante->sinodal_id);

            return $comprovante;
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    public static function alterarStatus(ComprovanteACI $comprovante) : void
    {
        try {
            $comprovante->update([
                'status' => $comprovante->status == ComprovanteACI::STATUS_PENDENTE
                    ? ComprovanteACI::STATUS_APROVADO
                    : ComprovanteACI::STATUS_PENDENTE,
            ]);
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    public static function marcarMetaAtingida(ComprovanteACI $comprovante): void
    {
        try {
            $comprovante->update([
                'status' => $comprovante->status == ComprovanteACI::STATUS_META_ATINGIDA
                    ? ComprovanteACI::STATUS_APROVADO
                    : ComprovanteACI::STATUS_META_ATINGIDA,
            ]);
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    /**
     * Retorna um array contendo os anos de envio do comprovante de aci
     *
     * @return array
     */
    public static function getAnosCadastrados() : array
    {
        return ComprovanteACI::selectRaw('DISTINCT(ano) as ano')
            ->groupBy('ano')
            ->get()
            ->pluck('ano', 'ano')
            ->toArray();
    }

    public static function totalizadorAciNecessaria($id, ?int $ano = null)
    {
        try {
            $ano ??= (int) EstatisticaService::getAnoReferencia();
            $totalSocios = self::totalSociosAtivosFederacoesAtivas($id, $ano);
            $paramValorAci = ValorAciAno::valorPara($ano);
            $valorMinimoACI = floatval(Parametro::where('nome', 'min_aci')->first()->valor)/100;
            $aciNecessaria = $totalSocios * $paramValorAci * self::PORCENTAGEM_SINODAL * $valorMinimoACI;
            
            return [
                'valor' => number_format($aciNecessaria, 2, ',', '.'),
                'total_socios' => $totalSocios
            ];
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro no Totalizador", 1);
        }
    }

    public static function valorPrevisto(string $sinodalId, int $ano): string
    {
        $totalSocios = self::totalSociosAtivosFederacoesAtivas($sinodalId, $ano);
        $valorPrevisto = $totalSocios * ValorAciAno::valorPara($ano) * self::PORCENTAGEM_SINODAL;

        return 'R$' . number_format($valorPrevisto, 2, ',', '.');
    }

    public static function totalSociosAtivosFederacoesAtivas(string $sinodalId, int $ano): int
    {
        $federacoesAtivasIds = Federacao::where('sinodal_id', $sinodalId)
            ->where('status', true)
            ->pluck('id');

        return FormularioFederacao::whereIn('federacao_id', $federacoesAtivasIds)
            ->where('ano_referencia', $ano)
            ->get()
            ->sum(fn (FormularioFederacao $formulario) => intval($formulario->perfil['ativos'] ?? 0));
    }
}
