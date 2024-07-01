<?php

namespace App\Services\Apps;

use App\Helpers\FormHelper;
use App\Models\Apps\Tesouraria\Lancamento;
use App\Models\Apps\Tesouraria\Categoria;
use App\Services\UserService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class TesourariaService
{

    /**
     * Salva um novo lançamento
     *
     * @param array $dados
     *
     * @return Lancamento|null
     */
    public static function store(array $dados): ?Lancamento
    {
        $lancamento = Lancamento::create([
            'descricao' => $dados['descricao'],
            'data_lancamento' => $dados['data_lancamento'],
            'valor' => FormHelper::converterParaFloat($dados['valor']),
            'tipo' => $dados['tipo'],
            'categoria_id' => $dados['categoria_id'],
            'sinodal_id' => UserService::getInstanciaUsuarioLogado(auth()->user())->id
        ]);

        if (!empty($dados['comprovante'])) {
            $path = self::salvarComprovante($dados['comprovante']);
            $lancamento->update([
                'comprovante' => $path,
            ]);
        }

        return $lancamento;
    }

    /**
     * Atualiza um lançamento
     *
     * @param array $dados
     * @param Lancamento $lancamento
     *
     * @return Lancamento
     */
    public static function update(array $dados, Lancamento $lancamento): Lancamento
    {
        $lancamento->update([
            'descricao' => $dados['descricao'],
            'data_lancamento' => $dados['data_lancamento'],
            'valor' => FormHelper::converterParaFloat($dados['valor']),
            'tipo' => $dados['tipo'],
            'categoria_id' => $dados['categoria_id']
        ]);

        if (!empty($dados['comprovante'])) {
            $path = self::salvarComprovante($dados['comprovante'], $lancamento->comprovante);
            $lancamento->update([
                'comprovante' => $path,
            ]);
        }

        return $lancamento;
    }

    /**
     * Remove um lançamento
     *
     * @param Lancamento $lancamento
     *
     * @return void
     */
    public static function delete(Lancamento $lancamento): void
    {
        $path = $lancamento->comprovante;
        $lancamento->delete();
        if (!empty($path)) {
            Storage::delete(
                str_replace(
                    'storage',
                    'public',
                    $path
                )
            );
        }
     }

    /**
     * Método para salvar comprovante
     *
     * @param UploadedFile $file
     * @param string|null $pathAntigo
     *
     * @return void
     */
    public static function salvarComprovante(UploadedFile $file, string $pathAntigo = null): string
    {
        if (!empty($pathAntigo)) {
            Storage::delete(
                str_replace(
                    'storage',
                    'public',
                    $pathAntigo
                )
            );
        }

        $nome = time().'.'. $file->getClientOriginalExtension();
        $sinodalId = auth()->user()->sinodais->first()->id;
        $path = $file->storeAs("/public/sinodais/{$sinodalId}/tesouraria/" . date('Y'), $nome);

        return str_replace(
            'public',
            'storage',
            $path
        );
    }

    /**
     * retorna as categorias para um select
     *
     * @return array
     */
    public static function categoriaToSelect(): array
    {
        return Categoria::all()
            ->where('sinodal_id', auth()->user()->sinodais->first()->id)
            ->pluck('nome', 'id')
            ->toArray();
    }


    /**
     * Salva uma nova categoria
     *
     * @param array $dados
     *
     * @return Categoria|null
     */
    public static function salvarCategoria(array $dados): ?Categoria
    {
        return Categoria::create([
            'nome' => $dados['nome'],
            'sinodal_id' => UserService::getInstanciaUsuarioLogado(auth()->user())->id
        ]);
    }

    /**
     * Atualiza uma categoria
     *
     * @param array $dados
     * @param Categoria $categoria
     *
     * @return Categoria
     */
    public static function atualizarCategoria(array $dados, Categoria $categoria): Categoria
    {
        $categoria->update([
            'nome' => $dados['nome'],
        ]);

        return $categoria;
    }

    /**
     * Remove uma categoria
     *
     * @param Categoria $categoria
     *
     * @return void
     */
    public static function removerCategoria(Categoria $categoria): void
    {
        $categoria->delete();
    }

    /**
     * Retorna os tipos de lançamentos
     *
     * @return array
     */
    public static function getTipos(): array
    {
        return Lancamento::TIPOS;
    }

    public static function totalizadores(?array $filtro = []): array
    {
        $ultimoDiaDoMes = Carbon::now()->subMonth()->lastOfMonth();
        $saldoInicial = self::getTotalAte($ultimoDiaDoMes);
        $entradas = self::getTotalAte(
            Carbon::now()->lastOfMonth(),
            Carbon::now()->firstOfMonth(),
            Lancamento::TIPO_ENTRADA
        );
        $saidas = self::getTotalAte(
            Carbon::now()->lastOfMonth(),
            Carbon::now()->firstOfMonth(),
            Lancamento::TIPO_SAIDA
        );

        $saldoFinal = $saldoInicial + $entradas - $saidas;

        return [
            'saldoInicial' => number_format($saldoInicial, 2, ',', '.'),
            'entradas' => number_format($entradas, 2, ',', '.'),
            'saidas' => number_format($saidas, 2, ',', '.'),
            'saldoFinal' => number_format($saldoFinal, 2, ',', '.')
        ];
    }

    /**
     * Undocumented function
     *
     * @param DateTime $ultimoDia
     * @param DateTime|null $primeiroDia
     * @param integer|null $tipo
     *
     * @return float
     */
    public static function getTotalAte(
        DateTime $ultimoDia,
        DateTime $primeiroDia = null,
        int $tipo = null
    ): float {
        $lancamentos = Lancamento::where('data_lancamento', '<=', $ultimoDia)
            ->when(!is_null($tipo), function ($sql) use ($tipo) {
                return $sql->where('tipo', '=', $tipo);
            })
            ->when(!is_null($primeiroDia), function ($sql) use ($primeiroDia) {
                return $sql->where('data_lancamento', '>=', $primeiroDia);
            })
            ->get();
        $total = 0;

        foreach ($lancamentos as $lancamento) {
            $valor = $lancamento->getRawOriginal('valor');

            if ($lancamento->tipo == Lancamento::TIPO_SAIDA) {
                $valor = $valor * -1;
            }

            $total += $valor;
        }

        return (float) $total;
    }

}
