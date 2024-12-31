<?php

namespace App\Services\Formularios;

use App\Factories\PesquisaGraficoFactory;
use App\Helpers\FormHelper;
use App\Models\Federacao;
use App\Models\FormularioComplementarFederacao;
use App\Models\FormularioComplementarSinodal;
use App\Models\Local;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FormularioComplementarService
{
    public const TIPO_GRAFICO = [
        null => 'Sem Gráfico',
        'barras' => 'Barras',
        'linhas' => 'Linhas',
        'pizza' => 'Pizza',
        'polar' => 'Polar',
    ];

    public const QUANTIDADE = 1;
    public const PORCENTAGEM = 2;
    public const TIPO_DADO = [
        null => 'Não Aplicado',
        self::QUANTIDADE => 'Quantidade',
        self::PORCENTAGEM => 'Porcentagem'
    ];

    public const TAMANHO = [
        'barras' => '4',
        'linhas' => '5',
        'pizza' => '3',
        'polar' => '4',
    ];
    public const TIPO_FORMULARIO_FEDERACAO = "federacao";
    public const TIPO_FORMULARIO_SINODAL = "sinodal";
    public const TIPO_FORMULARIO_LOCAL = "local";
    public const CLASSES = [
        self::TIPO_FORMULARIO_SINODAL => FormularioComplementarSinodal::class,
        self::TIPO_FORMULARIO_FEDERACAO => FormularioComplementarFederacao::class
    ];

    public const CLASSES_INSTANCIAS = [
        self::TIPO_FORMULARIO_LOCAL => Local::class,
        self::TIPO_FORMULARIO_FEDERACAO => Federacao::class
    ];
    
    public const CLASSES_SERVICES_RELATORIO = [
        self::TIPO_FORMULARIO_LOCAL => [
            'classe' => FormularioLocalService::class,
            'metodo' => 'getFormularioLocal'
        ],
        self::TIPO_FORMULARIO_FEDERACAO => [
            'classe' => FormularioFederacaoService::class,
            'metodo' => 'getFormularioDaFederacao'
        ]
    ];

    public static function getFormularioComplementar(string $instanciaId, string $tipo): Model
    {
        $classe = self::CLASSES[$tipo];
        $campo = "{$tipo}_id";
        $formulario = $classe::where($campo, $instanciaId)->first();

        if ($formulario === null) {
            $formulario = $classe::create([
                $campo => $instanciaId
            ]);
        }

        return $formulario;
    }

    /**
     * Retorna os dados do formulário complementar sinodal
     * 
     * @param string $localId
     * 
     * @return FormularioComplementarFederacao|null
     */
    public static function getFormularioSinodal(
        string $instanciaId,
        string $tipo = FormularioComplementarService::TIPO_FORMULARIO_LOCAL
    ): ?Model {
        $classe = self::CLASSES_INSTANCIAS[$tipo];
        $instancia = $classe::find($instanciaId);
        $formulario = FormularioComplementarSinodal::where('sinodal_id', $instancia->sinodal_id)->first();

        $classeServiceFormulario = self::CLASSES_SERVICES_RELATORIO[$tipo]['classe'];
        $metodoServiceFormulario = self::CLASSES_SERVICES_RELATORIO[$tipo]['metodo'];
        $formularioInstancia = $classeServiceFormulario::$metodoServiceFormulario($instanciaId);

        if ($formularioInstancia != null) {
            $formulario->resposta = $formularioInstancia->campo_extra_sinodal;
        }

        return $formulario;
    }

    /**
     * Retorna os dados do formulário complementar da federação
     * 
     * @param string $localId
     * 
     * @return FormularioComplementarFederacao|null
     */
    public static function getFormularioFederacao(string $localId): ?Model
    {
        $local = Local::find($localId);
        $formulario = FormularioComplementarFederacao::where('federacao_id', $local->federacao_id)->first();
        $formularioLocal = FormularioLocalService::getFormularioLocal($localId);

        if ($formularioLocal != null) {
            $formulario->resposta = $formularioLocal->campo_extra_federacao;
        }

        return $formulario;
    }

    public static function update(string $id, array $request, string $tipo)
    {
        DB::beginTransaction();
        try {
            $referencias = self::referenciaCamposFormulario($request['formulario']);
            $request['formulario'] = self::padronizarFormulario($request['formulario']);
            $classe = self::CLASSES[$tipo];
            $formulario = $classe::find($id);
            $formulario->update([
                'formulario' => $request['formulario'],
                'referencias' => $referencias,
                'status' => $request['status'] == FormHelper::SWITCH_ON
            ]);
            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            Log::error('Erro ao atualizar o Formulário Complementar', [
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao salvar o formulário", 1);
        }
    }

    public static function referenciaCamposFormulario(string $formulario) : array
    {
        try {
            $json_formulario = json_decode($formulario, true);
            $array_formulario = json_decode($json_formulario, true);
            $referencias = [];

            foreach ($array_formulario as $key => $campo) {
                if (in_array($campo['type'], ['button', 'paragraph','header'])) {
                    continue;
                }

                $referencias[$key] = [
                    $campo['name'] => [
                        'label' => $campo['label'],
                        'campo' => isset($campo['label'])
                            ? Str::snake(FormHelper::removerAcentos($campo['label']))
                            : '',
                        'required' => $campo['required'] ?? false,
                        'description' => $campo['description'] ?? '',
                    ]
                ];
                
                if ($campo['type'] == 'number') {
                    $referencias[$key][$campo['name']]['min'] = $campo['min'] ?? 0;
                    $referencias[$key][$campo['name']]['max'] = $campo['max'] ?? null;
                }

                if (!isset($campo['values'])) {
                    continue;
                }

                foreach ($campo['values'] as $opcao) {
                    $referencias[$key][$campo['name']]['valores'][] = [
                        'value' => $opcao['value'],
                        'label' => $opcao['label']
                    ];
                }
            }

            return $referencias;
        } catch (Throwable $th) {
            Log::error('Erro ao montar a referencia dos campos', [
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    public static function padronizarFormulario(string $formulario): string
    {
        $dados = json_decode(json_decode($formulario), true);
        
        foreach ($dados as $key => $campo) {
            if (isset($campo['type']) && $campo['type'] == 'header') {
                $dados[$key]['subtype'] = 'h3';
            }
        }
        
        return json_encode($dados);
    }

    public static function tratarResposta(array $request, array $referencias): array
    {        
        $resposta = [];

        try {
            foreach ($referencias as $parametros) {
                foreach ($parametros as $campo => $opcoes) {
                    if (!isset($request[$campo])) {
                        continue;
                    }

                    $resposta[$campo] = $request[$campo];
                }
            }   
        } catch (Throwable $th) {
            Log::error('Erro ao tratar a resposta', [
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
        }

        return $resposta;
    }

    public static function tratarRespostasComplementaresSinodal(
        array $request,
        string $instanciaId,
        string $tipo = FormularioComplementarService::TIPO_FORMULARIO_LOCAL
    ): array {
        $formularios = self::getFormularioSinodal($instanciaId, $tipo);

        if ($formularios == null) {
            return [];
        }
        
        return self::tratarResposta($request, $formularios->referencias);
    }
    
    public static function tratarRespostasComplementaresFederacao(array $request, string $localId): array
    {
        $formularios = self::getFormularioFederacao($localId);

        if ($formularios == null) {
            return [];
        }
        
        return self::tratarResposta($request, $formularios->referencias);
    }

    public static function getRespostas(string $id, string $tipo): array
    {
        $formulario = self::getFormularioComplementar($id, $tipo);
        
        if ($formulario == null) {
            return [];
        }
        
        $referencias = $formulario->referencias;

    }
}
