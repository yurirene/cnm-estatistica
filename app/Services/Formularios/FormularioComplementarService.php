<?php

namespace App\Services\Formularios;

use App\Factories\PesquisaGraficoFactory;
use App\Helpers\FormHelper;
use App\Models\Federacao;
use App\Models\FormularioComplementarFederacao;
use App\Models\FormularioComplementarSinodal;
use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
use App\Models\FormularioSinodal;
use App\Models\Local;
use App\Models\User;
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

    public static function getFormularioComplementar(string $instanciaId, string $tipo, $ano): Model
    {
        $classe = self::CLASSES[$tipo];
        $campo = "{$tipo}_id";
        $formulario = $classe::where($campo, $instanciaId)
            ->where('ano', $ano)
            ->first();

        if ($formulario === null) {
            $formulario = $classe::create([
                $campo => $instanciaId,
                'ano' => $ano
            ]);
        }

        return $formulario;
    }

    /**
     * Retorna os dados do formulário complementar sinodal
     *
     * @param string $localId
     *
     * @return FormularioComplementarSinodal|null
     */
    public static function getFormularioSinodal(
        string $instanciaId,
        string $tipo = FormularioComplementarService::TIPO_FORMULARIO_LOCAL
    ): ?Model {
        $classe = self::CLASSES_INSTANCIAS[$tipo];
        $instancia = $classe::find($instanciaId);
        $formulario = FormularioComplementarSinodal::where('sinodal_id', $instancia->sinodal_id)->first();

        if ($formulario == null) {
            return null;
        }

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

        if ($formularioLocal != null && $formulario != null) {
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
                'status' => isset($request['status'])
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

        if ($formularios->formulario == null || empty($formularios->referencias)) {
            return [];
        }

        return self::tratarResposta($request, $formularios->referencias);
    }

    public static function tratarRespostasComplementaresFederacao(array $request, string $localId): array
    {
        $formularios = self::getFormularioFederacao($localId);
        if (!$formularios || $formularios->formulario == null || empty($formularios->referencias)) {
            return [];
        }

        return self::tratarResposta($request, $formularios->referencias);
    }

    public static function getRespostas(string $id, string $tipo, int $ano): array
    {
        $formulario = self::getFormularioComplementar($id, $tipo, $ano);

        if ($formulario == null || empty($formulario->referencias)) {
            return [];
        }

        $referencias = self::tratarReferencia($formulario->referencias);
        $chaves = array_keys($referencias);
        $perguntas = self::getPerguntasComChave($referencias);

        $instancia = auth()->user()->role->name;

        $respostasFederacao = [];

        if ($instancia == User::ROLE_SINODAL) {
            $respostasFederacao = self::getRespostasFederacoes($chaves, $perguntas, $ano);
        }

        $respostasLocal = [];

        if (in_array($instancia, [User::ROLE_SINODAL, User::ROLE_FEDERACAO])) {
            $respostasLocal = self::getRespostasLocais($chaves, $perguntas, $ano, $instancia);
        }

        return self::formatarResposta($formulario->referencias, $respostasFederacao, $respostasLocal);
    }

    public static function getPerguntasComChave(array $referencias): array
    {
        $perguntas = [];

        foreach ($referencias as $chave => $dado) {
            $perguntas[$chave] = $dado[0]['label'];
        }

        return $perguntas;

    }

    public static function getRespostasFederacoes(array $chaves, array $perguntas, int $ano): array
    {
        $instancia = auth()->user()->instancia();
        $federacoesId = $instancia->federacoes->pluck('id');
        $respostas = FormularioFederacao::with('federacao')
            ->whereIn('federacao_id', $federacoesId)
            ->where('ano_referencia', $ano)
            ->whereNotNull('campo_extra_sinodal')
            ->get()
            ->pluck('campo_extra_sinodal', 'federacao.nome')
            ->toArray();

        $totalizador = [];

        foreach ($chaves as $chave) {
            $totalizador[$chave]['totalizador'] = 0;
            $totalizador[$chave]['pergunta'] = $perguntas[$chave];
        }

        foreach ($respostas as $federacao => $resposta) {
            $respostaArray = json_decode($resposta, true);

            foreach ($chaves as $chave) {
                if (isset($respostaArray[$chave])) {
                    $totalizador[$chave]['respostas'][$federacao] = $respostaArray[$chave];
                    $totalizador[$chave]['totalizador'] += intval($respostaArray[$chave]);
                }
            }
        }

        return $totalizador;
    }
    public static function getRespostasLocais(array $chaves, array $perguntas, int $ano, $campoInstancia): array
    {
        $instancia = auth()->user()->instancia();
        $federacoesId = $instancia->locais->pluck('id')->toArray();
        $campo = "campo_extra_{$campoInstancia}";

        $respostas = FormularioLocal::with('local')
            ->whereIn('local_id', $federacoesId)
            ->where('ano_referencia', $ano)
            ->whereNotNull($campo)
            ->get()
            ->pluck($campo, 'local.nome')
            ->toArray();

        $totalizador = [];

        foreach ($chaves as $chave) {
            $totalizador[$chave]['totalizador'] = 0;
            $totalizador[$chave]['pergunta'] = $perguntas[$chave];
        }

        foreach ($respostas as $local => $resposta) {
            $respostaArray = json_decode($resposta, true);

            foreach ($chaves as $chave) {
                if (isset($respostaArray[$chave])) {
                    $totalizador[$chave]['respostas'][$local] = $respostaArray[$chave];
                    $totalizador[$chave]['totalizador'] += intval($respostaArray[$chave]);
                }
            }
        }

        return $totalizador;
    }

    public static function formatarResposta($referencias, $valoresFederacao, $valoresLocal): array
    {
        $referencias = self::tratarReferencia($referencias);
        $temDadoFederacao = !empty($valoresFederacao);
        $temDadoLocal = !empty($valoresLocal);
        $retorno = [];

        foreach ($referencias as $campo => $dados) {
            if ($temDadoFederacao) {
                $retorno['federacao'][$dados[0]['label']] = $valoresFederacao[$campo];
            }

            if ($temDadoLocal) {
                $retorno['local'][$dados[0]['label']] = $valoresLocal[$campo];
            }
        }
        return $retorno;
    }

    public static function tratarReferencia(array $dados, bool $retornarApenasChaves = false): array
    {
        $referencias = [];

        foreach ($dados as $dado) {
            $referencias[array_keys($dado)[0]] = array_values($dado);
        }

        if ($retornarApenasChaves) {
            return array_keys($referencias);
        }

        return $referencias;
    }

    public static function getAnosToSelect(): array
    {
        $instancia = auth()->user()->role->name;
        $instanciaId = auth()->user()->instancia()->id;

        if ($instancia == User::ROLE_SINODAL) {
            $formularios = FormularioComplementarSinodal::where('sinodal_id', $instanciaId)
                ->get()
                ->pluck('ano', 'ano')
                ->toArray();
        } else {
            $formularios = FormularioComplementarFederacao::where('federacao_id', $instanciaId)
                ->get()
                ->pluck('ano', 'ano')
                ->toArray();
        }

        $formularios[date('Y')] = date('Y');

        return $formularios;
    }
}
