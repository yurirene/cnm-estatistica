<?php

namespace App\Services;

use App\Exceptions\ColetaDadosException;
use App\Models\ColetorDados;
use App\Models\FormularioComplementarSinodal;
use App\Models\Local;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Formularios\FormularioComplementarService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ColetorDadosService
{
    /**
     * Cria os formulários de coleta de dados
     * com base na quantidade informada
     * 
     * @param array $request
     * 
     * @return void
     */
    public static function store(array $request)
    {
        DB::beginTransaction();
        try {
            $localId = auth()->user()->local_id;

            if ((int) $request['quantidade'] < 0) {
                throw new Exception("Quantidade deve ser maior que 0");
            }

            for ($i = 0; $i < $request['quantidade']; $i++) {
                ColetorDados::create([
                    'local_id' => $localId,
                    'ano' => EstatisticaService::getAnoReferencia(),
                ]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao Salvar");
        }
    }

    /**
     * Salva a resposta do formulário
     * 
     * @param string $id
     * @param array $request
     * 
     * @return void
     */
    public static function responder(string $id, array $request)
    {
        DB::beginTransaction();
        try {
            $formulario = ColetorDados::findOrFail($id);
            $resposta = self::tratarResposta($request);
            $formulario->update([
                'resposta' => $resposta,
                'status' => true
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao Responder");
        }
    }

    /**
     * Remove um formulário se não estiver preenchido
     * 
     * @param string $id
     * 
     * @return void
     */
    public static function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $formulario = ColetorDados::findOrFail($id);
            
            if (!$formulario->status && !empty($formulario->resposta)) {
                throw new Exception("Formulário já respondido não pode ser removido", 1);
            }

            $formulario->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao Remover");
        }
    }

    /**
     * Retorna informações do formulário e da ump que pertence
     * 
     * @param string $id
     * 
     * @return array
     */
    public static function carregar(string $id): array
    {
        $formulario = ColetorDados::findOrFail($id);
        $local = $formulario->local;

        return [
            'formulario' => $formulario,
            'local' => $local,
            'estadosCivis' => self::getEstadoCivis(),
            'escolaridades' => self::getEscolaridade(),
            'deficiencias' => self::getDeficiencias(),
            'resposta' => $formulario->resposta['raw'] ?? []
        ];
    }

    /**
     * Retorna os estados civis
     * 
     * @return array
     */
    public static function getEstadoCivis(): array
    {
        return [
            'solteiros' => 'Solteiro',
            'casados' => 'Casado',
            'viuvos' => 'Viúvo',
            'divorciados' => 'Divorciado'
        ];
    }

    /**
     * Retorna a escolaridade
     *  
     * @return array
     */

    public static function getEscolaridade(): array
    {
        return [
            'fundamental_incompleto' => 'Fundamental Incompleto',
            'fundamental_completo' => 'Fundamental Completo',
            'medio_incompleto' => 'Médio Incompleto',
            'medio_completo' => 'Médio Completo',
            'tecnico_completo' => 'Técnico Completo',
            'tecnico_incompleto' => 'Técnico Incompleto' ,
            'superior_incompleto' => 'Superior Incompleto',
            'superior_completo' => 'Superior Completo',
            'pos_incompleto' => 'Pós-Graduação Incompleto',
            'pos_completo' => 'Pós-Graduação Completo'
        ];
    }

    /**
     * Retorna as deficiencia
     * 
     * @return array
     */
    public static function getDeficiencias(): array
    {
        return [
            'surdos' => 'Surdez',
            'auditiva' => 'Auditiva',
            'cegos' => 'Cegueira',
            'baixa_visao' => 'Baixa Visão',
            'fisica_inferior' => 'Física Inferior',
            'fisica_superior' => 'Física Superior',
            'neurologico' => 'Neurologico',
            'intelectual' => 'Intelectual',
            'outras' => 'Outras'
        ];
    }

    /**
     * Trata as resposta para o padrão V1 do formulário
     * 
     * @param array $request
     */
    public static function tratarResposta(array $request): array
    {
        if (empty($request['tipo']) || empty($request['sexo'])) {
            throw new ColetaDadosException("Há campos não respondidos", 400);
        }

        $perfil = [
            $request['tipo'] => 1,
            self::getChaveIdade($request['ano_nascimento']) => 1,
            $request['sexo'] => 1
        ];

        return [
            'tratado' => [
                'perfil' => $perfil,
                'estado_civil' => [
                    $request['estado_civil'] => 1,
                    'filhos' => $request['filhos'] 
                ],
                'escolaridade' => [
                    self::getChaveEscolaridade($request['escolaridade']) => 1
                ],
                'deficiencias' => self::getChaveDeficiencia($request['deficiencia'] ?? []),
            ],
            'raw' => $request
        ];
    }

    /**
     * Retorna a chave do perfil idade no padrão V1 do formulário
     * 
     * @param string $ano
     * 
     * @return string
     */
    public static function getChaveIdade(string $ano): string
    {
        $ano = (int) $ano;
        $idade = Carbon::now()->diffInYears(Carbon::createFromFormat('d/m/Y', "01/01/{$ano}"));
        $chave = '';

        if ($idade < 19) {
            $chave = 'menor19';
        } elseif ($idade >=19 && $idade < 24) {
            $chave = 'de19a23';
        } elseif ($idade >= 24 && $idade < 30) {
            $chave = 'de24a29';
        } elseif ($idade >= 30 && $idade < 35) {
            $chave = 'de30a35';
        } else {
            throw new ColetaDadosException("Idade Informada é maior que 35 e não pode ser contabilizada", 400);
        }

        return $chave;
    }


    /**
     * Retorna a chave da escolaridade no padrão V1 do formulário
     * 
     * @param string $escolaridade
     * 
     * @return string
     */
    public static function getChaveEscolaridade(string $escolaridade): string
    {
        $chave = 'fundamental';

        if (in_array($escolaridade, ['medio_incompleto', 'fundamental_completo'])) {
            $chave = 'fundamental';
        } elseif (in_array($escolaridade, ['medio_completo', 'tecnico_incompleto', 'superior_incompleto'])) {
            $chave = 'medio';
        } elseif (in_array($escolaridade, ['superior_completo', 'pos_incompleto'])) {
            $chave = 'superior';
        } elseif ($escolaridade == 'pos_completo') {
            $chave = 'pos';
        }

        return $chave;
    }


    /**
     * Retorna a chave da deficiencia no padrão V1 do formulário
     * 
     * @param array|null $deficiencias
     * 
     * @return array
     */
    public static function getChaveDeficiencia(?array $deficiencias = []): array
    {
        if (empty($deficiencias)) {
            return [];
        }
        
        $retorno = [];

        foreach ($deficiencias as $deficiencia) {
            $retorno[$deficiencia] = 1;
        }

        return $retorno;
    }

    /**
     * Formata a resposta para exibição no front
     */
    public static function formatarRespostaParaVisualizacao(array $resposta): array
    {
        try {
            $deficiencias = [];
            $listaDeficiencias = self::getDeficiencias();
            
            foreach ($resposta['deficiencia'] ?? [] as  $deficiencia) {
                $deficiencias[] = $listaDeficiencias[$deficiencia] ?? '';
            }
            
            $deficiencias = empty($deficiencias) ? 'Nenhuma' : implode(', ', $deficiencias);

            return [
                'tipo' => $resposta['tipo'] == 'ativos' ? 'Ativo' : 'Cooperador',
                'idade' => Carbon::now()->diffInYears(Carbon::createFromFormat('d/m/Y', "01/01/{$resposta['ano_nascimento']}")),
                'sexo' => $resposta['sexo'] == 'homens' ? 'Masculino' : 'Feminino',
                'estado_civil' => self::getEstadoCivis()[$resposta['estado_civil']],
                'filhos' => $resposta['filhos'] == '1' ? 'Sim' : 'Não',
                'escolaridade' => self::getEscolaridade()[$resposta['escolaridade']],
                'deficiencias' => $deficiencias
            ];
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'msg' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            throw $th;
        }
    }

    /**
     * Compila os dados para serem informados no formulário da UMP Local
     */
    public static function carregarDadosCompilados(): array
    {
        $ano = EstatisticaService::getAnoReferencia();
        $local = auth()->user()->local_id;

        $coletas = ColetorDados::where('local_id', $local)
            ->where('status', 1)
            ->where('ano', $ano)
            ->get();
        
        if ($coletas->isEmpty()) {
            return [];
        }

        $respostas = $coletas->pluck('resposta');

        
        $totalizador = [
            'perfil' => [
                'ativos' => 0,
                'cooperadores' => 0,
                'homens' => 0,
                'mulheres' => 0,
                'menor19' => 0,
                'de19a23' => 0,
                'de24a29' => 0,
                'de30a35' => 0
            ],
            'escolaridade' => [
                'fundamental' => 0,
                'medio' => 0,
                'tecnico' => 0,
                'superior' => 0,
                'pos' => 0,
            ],
            'estado_civil' => [
                'solteiros' => 0,
                'casados' => 0,
                'divorciados' => 0,
                'viuvos' => 0,
                'filhos' => 0,
            ],
            'deficiencias' => [
                'surdos' => 0,
                'auditiva' => 0,
                'cegos' => 0,
                'baixa_visao' => 0,
                'fisica_inferior' => 0,
                'fisica_superior' => 0,
                'neurologico' => 0,
                'intelectual' => 0,
            ],
        ];

        foreach ($respostas as $resposta) {
            $totalizador['perfil']['ativos'] += (
                isset($resposta['tratado']['perfil']['ativos'])
                ? intval($resposta['tratado']['perfil']['ativos'])
                : 0
            );
            $totalizador['perfil']['cooperadores'] += (
                isset($resposta['tratado']['perfil']['cooperadores'])
                ? intval($resposta['tratado']['perfil']['cooperadores'])
                : 0
            );
            $totalizador['perfil']['homens'] += (
                isset($resposta['tratado']['perfil']['homens'])
                ? intval($resposta['tratado']['perfil']['homens'])
                : 0
            );
            $totalizador['perfil']['mulheres'] += (
                isset($resposta['tratado']['perfil']['mulheres'])
                ? intval($resposta['tratado']['perfil']['mulheres'])
                : 0
            );
            $totalizador['perfil']['menor19'] += (
                isset($resposta['tratado']['perfil']['menor19'])
                ? intval($resposta['tratado']['perfil']['menor19'])
                : 0
            );
            $totalizador['perfil']['de19a23'] += (
                isset($resposta['tratado']['perfil']['de19a23'])
                ? intval($resposta['tratado']['perfil']['de19a23'])
                : 0
            );
            $totalizador['perfil']['de24a29'] += (
                isset($resposta['tratado']['perfil']['de24a29'])
                ? intval($resposta['tratado']['perfil']['de24a29'])
                : 0
            );
            $totalizador['perfil']['de30a35'] += (
                isset($resposta['tratado']['perfil']['de30a35'])
                ? intval($resposta['tratado']['perfil']['de30a35'])
                : 0
            );
            $totalizador['escolaridade']['fundamental'] += (
                isset($resposta['tratado']['escolaridade']['fundamental'])
                ? intval($resposta['tratado']['escolaridade']['fundamental'])
                : 0
            );
            $totalizador['escolaridade']['medio'] += (
                isset($resposta['tratado']['escolaridade']['medio'])
                ? intval($resposta['tratado']['escolaridade']['medio'])
                : 0
            );
            $totalizador['escolaridade']['tecnico'] += (
                isset($resposta['tratado']['escolaridade']['tecnico'])
                ? intval($resposta['tratado']['escolaridade']['tecnico'])
                : 0
            );
            $totalizador['escolaridade']['superior'] += (
                isset($resposta['tratado']['escolaridade']['superior'])
                ? intval($resposta['tratado']['escolaridade']['superior'])
                : 0
            );
            $totalizador['escolaridade']['pos'] += (
                isset($resposta['tratado']['escolaridade']['pos'])
                ? intval($resposta['tratado']['escolaridade']['pos'])
                : 0
            );
            $totalizador['estado_civil']['solteiros'] += (
                isset($resposta['tratado']['estado_civil']['solteiros'])
                ? intval($resposta['tratado']['estado_civil']['solteiros'])
                : 0
            );
            $totalizador['estado_civil']['casados'] += (
                isset($resposta['tratado']['estado_civil']['casados'])
                ? intval($resposta['tratado']['estado_civil']['casados'])
                : 0
            );
            $totalizador['estado_civil']['divorciados'] += (
                isset($resposta['tratado']['estado_civil']['divorciados'])
                ? intval($resposta['tratado']['estado_civil']['divorciados'])
                : 0
            );
            $totalizador['estado_civil']['viuvos'] += (
                isset($resposta['tratado']['estado_civil']['viuvos'])
                ? intval($resposta['tratado']['estado_civil']['viuvos'])
                : 0
            );
            $totalizador['estado_civil']['filhos'] += (
                isset($resposta['tratado']['estado_civil']['filhos'])
                ? intval($resposta['tratado']['estado_civil']['filhos'])
                : 0
            );
            $totalizador['deficiencias']['surdos'] += (
                isset($resposta['tratado']['deficiencias']['surdos'])
                ? intval($resposta['tratado']['deficiencias']['surdos'])
                : 0
            );
            $totalizador['deficiencias']['auditiva'] += (
                isset($resposta['tratado']['deficiencias']['auditiva'])
                ? intval($resposta['tratado']['deficiencias']['auditiva'])
                : 0
            );
            $totalizador['deficiencias']['cegos'] += (
                isset($resposta['tratado']['deficiencias']['cegos'])
                ? intval($resposta['tratado']['deficiencias']['cegos'])
                : 0
            );
            $totalizador['deficiencias']['baixa_visao'] += (
                isset($resposta['tratado']['deficiencias']['baixa_visao'])
                ? intval($resposta['tratado']['deficiencias']['baixa_visao'])
                : 0
            );
            $totalizador['deficiencias']['fisica_inferior'] += (
                isset($resposta['tratado']['deficiencias']['fisica_inferior'])
                ? intval($resposta['tratado']['deficiencias']['fisica_inferior'])
                : 0
            );
            $totalizador['deficiencias']['fisica_superior'] += (
                isset($resposta['tratado']['deficiencias']['fisica_superior'])
                ? intval($resposta['tratado']['deficiencias']['fisica_superior'])
                : 0
            );
            $totalizador['deficiencias']['neurologico'] += (
                isset($resposta['tratado']['deficiencias']['neurologico'])
                ? intval($resposta['tratado']['deficiencias']['neurologico'])
                : 0
            );
            $totalizador['deficiencias']['intelectual'] += (
                isset($resposta['tratado']['deficiencias']['intelectual'])
                ? intval($resposta['tratado']['deficiencias']['intelectual'])
                : 0
            );
        }

        return $totalizador;
    }

    /**
     * Remove todos os formulário
     * 
     * @param string $id Id da UMP Local
     * 
     * @return void
     */
    public static function restaurar(string $id)
    {
        DB::beginTransaction();

        try {
            $formularios = ColetorDados::where('local_id', $id)
                ->where('ano', EstatisticaService::getAnoReferencia())
                ->get();
            
            foreach ($formularios as $formulario) {
                $formulario->delete();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao Resetar ");
        }
    }
}
