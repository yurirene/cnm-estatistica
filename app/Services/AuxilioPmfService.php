<?php

namespace App\Services;

use App\Models\AuxilioPmf\AuxilioPmfEdital;
use App\Models\AuxilioPmf\AuxilioPmfProposta;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AuxilioPmfService
{
    public static function isAdmin(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (!$user || !$user->role) {
            return false;
        }

        return in_array($user->role->name, [
            User::ROLE_ADMINISTRADOR,
            User::ROLE_DIRETORIA,
            User::ROLE_SECRETARIADO_COMUM,
            User::ROLE_SEC_EXECUTIVA,
        ], true);
    }

    public static function isSinodal(?User $user = null): bool
    {
        $user ??= auth()->user();

        return $user && $user->role && $user->role->name === User::ROLE_SINODAL;
    }

    public static function getEditalVigente(): ?AuxilioPmfEdital
    {
        return AuxilioPmfEdital::query()
            ->where('recebimento_aberto', true)
            ->orderByDesc('ano')
            ->orderByDesc('created_at')
            ->first()
            ?? AuxilioPmfEdital::query()
                ->orderByDesc('ano')
                ->orderByDesc('created_at')
                ->first();
    }

    public static function storeEdital(Request $request): AuxilioPmfEdital
    {
        try {
            return AuxilioPmfEdital::create([
                'titulo' => $request->titulo,
                'ano' => $request->ano,
                'data_limite_proposta' => $request->data_limite_proposta,
                'link_edital' => $request->link_edital,
                'link_proposta' => $request->link_proposta,
                'link_relatorio' => $request->link_relatorio,
                'recebimento_aberto' => true,
            ]);
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
            throw $th;
        }
    }

    public static function updateEdital(AuxilioPmfEdital $edital, Request $request): AuxilioPmfEdital
    {
        try {
            $edital->update([
                'titulo' => $request->titulo,
                'ano' => $request->ano,
                'data_limite_proposta' => $request->data_limite_proposta,
                'link_edital' => $request->link_edital,
                'link_proposta' => $request->link_proposta,
                'link_relatorio' => $request->link_relatorio,
            ]);

            return $edital->fresh();
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
            throw $th;
        }
    }

    public static function encerrar(AuxilioPmfEdital $edital): void
    {
        try {
            $edital->update(['recebimento_aberto' => false]);
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
            throw $th;
        }
    }

    public static function propostaDaSinodal(AuxilioPmfEdital $edital, ?string $sinodalId = null): ?AuxilioPmfProposta
    {
        $sinodalId ??= auth()->user()->sinodal_id;

        if (!$sinodalId) {
            return null;
        }

        return AuxilioPmfProposta::query()
            ->where('edital_id', $edital->id)
            ->where('sinodal_id', $sinodalId)
            ->first();
    }

    public static function temRejeicaoNoAno(int $ano, ?string $sinodalId = null): bool
    {
        $sinodalId ??= auth()->user()->sinodal_id;

        if (!$sinodalId) {
            return false;
        }

        return AuxilioPmfProposta::query()
            ->where('sinodal_id', $sinodalId)
            ->where('status', AuxilioPmfProposta::STATUS_REJEITADA)
            ->whereHas('edital', fn ($q) => $q->where('ano', $ano))
            ->exists();
    }

    public static function podeEnviarProposta(AuxilioPmfEdital $edital, ?string $sinodalId = null): bool
    {
        $sinodalId ??= auth()->user()->sinodal_id;

        if (!$sinodalId || !$edital->estaRecebendo()) {
            return false;
        }

        if (self::temRejeicaoNoAno((int) $edital->ano, $sinodalId)) {
            return false;
        }

        $proposta = self::propostaDaSinodal($edital, $sinodalId);

        if (!$proposta) {
            return true;
        }

        return $proposta->isPendente() && empty($proposta->getRawOriginal('path_proposta'));
    }

    public static function podeSubstituirProposta(AuxilioPmfProposta $proposta): bool
    {
        return $proposta->isPendente()
            && $proposta->edital
            && $proposta->edital->estaRecebendo();
    }

    public static function storeProposta(Request $request, AuxilioPmfEdital $edital): AuxilioPmfProposta
    {
        try {
            $sinodalId = auth()->user()->sinodal_id;

            if (!$sinodalId) {
                throw new Exception('Usuário sem sinodal vinculada.');
            }

            if (self::temRejeicaoNoAno((int) $edital->ano, $sinodalId)) {
                throw new Exception('Proposta rejeitada neste ano. Não é possível submeter outra.');
            }

            if (!$edital->estaRecebendo()) {
                throw new Exception('O recebimento de propostas está encerrado ou fora do prazo.');
            }

            $proposta = self::propostaDaSinodal($edital, $sinodalId);

            if ($proposta && !$proposta->isPendente()) {
                throw new Exception('Já existe uma inscrição para este edital.');
            }

            if (!$proposta) {
                $proposta = AuxilioPmfProposta::create([
                    'edital_id' => $edital->id,
                    'sinodal_id' => $sinodalId,
                    'status' => AuxilioPmfProposta::STATUS_PENDENTE,
                ]);
            }

            self::salvarArquivo($proposta, 'path_proposta', $request->file('arquivo'));

            return $proposta->fresh();
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
            throw $th;
        }
    }

    public static function excluirArquivoProposta(AuxilioPmfProposta $proposta): void
    {
        try {
            if (!self::podeSubstituirProposta($proposta)) {
                throw new Exception('Não é possível excluir esta proposta.');
            }

            self::removerArquivo($proposta, 'path_proposta');
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
            throw $th;
        }
    }

    public static function storeRelatorio(Request $request, AuxilioPmfProposta $proposta): AuxilioPmfProposta
    {
        try {
            if (!$proposta->isAprovada()) {
                throw new Exception('Só é possível anexar o relatório após a aprovação da proposta.');
            }

            self::salvarArquivo($proposta, 'path_relatorio', $request->file('arquivo'));

            return $proposta->fresh();
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
            throw $th;
        }
    }

    public static function excluirArquivoRelatorio(AuxilioPmfProposta $proposta): void
    {
        try {
            if (!$proposta->isAprovada()) {
                throw new Exception('Não é possível excluir este relatório.');
            }

            self::removerArquivo($proposta, 'path_relatorio');
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
            throw $th;
        }
    }

    public static function aprovar(AuxilioPmfProposta $proposta): void
    {
        try {
            if (!$proposta->isPendente()) {
                throw new Exception('Somente propostas pendentes podem ser aprovadas.');
            }

            if (empty($proposta->getRawOriginal('path_proposta'))) {
                throw new Exception('A proposta não possui arquivo anexado.');
            }

            $proposta->update(['status' => AuxilioPmfProposta::STATUS_APROVADA]);
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
            throw $th;
        }
    }

    public static function rejeitar(AuxilioPmfProposta $proposta): void
    {
        try {
            if (!$proposta->isPendente()) {
                throw new Exception('Somente propostas pendentes podem ser rejeitadas.');
            }

            $proposta->update(['status' => AuxilioPmfProposta::STATUS_REJEITADA]);
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);
            throw $th;
        }
    }

    public static function urlArquivo(?string $pathPublico): ?string
    {
        if (empty($pathPublico)) {
            return null;
        }

        return asset($pathPublico);
    }

    private static function salvarArquivo(AuxilioPmfProposta $proposta, string $campo, UploadedFile $arquivo): void
    {
        $proposta->{$campo} = $arquivo;
        $proposta->save();
    }

    private static function removerArquivo(AuxilioPmfProposta $proposta, string $campo): void
    {
        $raw = $proposta->getRawOriginal($campo);

        if (!empty($raw)) {
            try {
                Storage::delete($raw);
            } catch (Throwable $th) {
            }
        }

        $proposta->forceFill([$campo => null])->save();
    }
}
