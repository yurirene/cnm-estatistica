<?php

namespace App\Services\Produtos;

use App\Models\Auditable;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use Throwable;

class AuditoriaProdutosService
{
    /**
     * Registrar os novos registros na tabela de log
     * @param $model
     * @param $usuario
     * @param $acao
     * @return bool|\Illuminate\Database\Eloquent\Model|Auditable
     */
    public static function store($model, $usuario, $acao, $acessoExterno = null)
    {
        try {
            $registros = self::formatarRegistros($model, $usuario, $acao, $acessoExterno);
            return !empty($registros) ? self::salvarRegistroNoCSV($registros) : false;
        } catch (Throwable $exception) {
            return false;
        }
    }

    /**
     * Formatar os dados que serão registrados
     * @param $model
     * @param $usuario
     * @param $acao
     * @return array
     */
    public static function formatarRegistros($model, $usuario, $acao, $acessoExterno = null)
    {
        try {
            $colunas = self::colunas($model, $acao);
            if (empty($colunas)) {
                return [];
            }

            return [
                'table' => $model->getTable(),
                'table_id' => $model->getKey() ?: null,
                'acao' => $acao,
                'coluna' => $colunas['coluna'],
                'valor_antigo' => $colunas['coluna_antiga'],
                'valor_novo' => $colunas['coluna_nova'],
                'user_id' => $usuario,
                'user_agent' => request()->userAgent(),
            ];
        } catch (Throwable $exception) {
            return [];
        }
    }

    /**
     * Tratar as colunas antigas e novas antes da realização do registro
     * @param $model
     * @param $acao
     * @return array
     */
    public static function colunas($model, $acao)
    {
        try {
            $oldValues = [];
            $dirty = $model->getDirty();
            $novos = json_encode($dirty);

            foreach ($dirty as $dirtyColumns => $value) {
                $oldValues[$dirtyColumns] = $model->getOriginal($dirtyColumns);
            }

            $colunas = array_keys($oldValues);

            if (
                $model->getTable() == 'clientes'
                && $acao == 'updating'
                && (
                    in_array('atualizacao_fluxo', $colunas)
                    || in_array('atualizacao_deposito', $colunas)

                )
            ) {
                return [];
            }

            if ($acao == 'deleting') {
                $colunas = array_keys($model->getOriginal());
                $antigos = json_encode($model->getOriginal());
            } else {
                $antigos = json_encode($oldValues);
            }

            return [
                'coluna' => json_encode($colunas),
                'coluna_antiga' => $acao != 'created' ? $antigos : null,
                'coluna_nova' => $acao != 'deleting' ? $novos : null,
            ];
        } catch (Throwable $exception) {
            return [];
        }
    }

    public static function salvarRegistroNoCSV(array $registro)
    {
        //load the CSV document from a string
        $nome = date('d-m-y') . '.csv';
        $path = "public/produtos/auditoria/";
        $pathFile = "{$path}{$nome}";
        $fileExists = Storage::exists($pathFile);

        if (!$fileExists) {
            $csvFile = tmpfile();
            $csvPath = stream_get_meta_data($csvFile)['uri'];
            $fd = fopen($csvPath, 'w');
            fputcsv($fd, array_keys($registro)); // Add header if file is created
            fclose($fd);
            $pathFile = Storage::putFileAs($path, $csvPath, $nome);
        }
        $file = fopen(Storage::path($pathFile), 'a'); // Open file in append mode
        fputcsv($file, array_values($registro));
        fclose($file);
    }
}
