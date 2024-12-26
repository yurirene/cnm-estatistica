<?php

namespace App\Services\Instancias;

use App\Models\Diretorias\DiretoriaFederacao;
use App\Models\Diretorias\DiretoriaLocal;
use App\Models\Diretorias\DiretoriaSinodal;
use Illuminate\Database\Eloquent\Model;

class DiretoriaService
{
    public const CAMPOS_CARGOS = [
        0 => 'presidente',
        1 => 'vice_presidente',
        2 => 'secretaria_executiva',
        3 => 'primeiro_secretario',
        4 => 'segundo_secretario',
        5 => 'tesoureiro'
    ];

    public const CAMPOS_SECRETARIO_CAUSAS = [
        self::TIPO_DIRETORIA_SINODAL => [
            'chave' => 'secretario_sinodal',
            'valor' => 'Secretário Sinodal',
        ],
        self::TIPO_DIRETORIA_FEDERACAO => [
            'chave' => 'secretario_presbiterial',
            'valor' => 'Secretário Presbiterial'
        ],
        self::TIPO_DIRETORIA_LOCAL => [
            'chave' => 'conselheiro',
            'valor' => 'Conselheiro'
        ]
    ];

    /**
     * Classes das diretorias pelo tipo
     * 
     * @var array
     */
    public const CLASSES_DIRETORIAS = [
        self::TIPO_DIRETORIA_LOCAL => DiretoriaLocal::class,
        self::TIPO_DIRETORIA_FEDERACAO => DiretoriaFederacao::class,
        self::TIPO_DIRETORIA_SINODAL => DiretoriaSinodal::class
    ];

    public const CARGOS = [
        0 => 'Presidente',
        1 => 'Vice-Presidente',
        2 => 'Secretário-Executivo',
        3 => 'Primeiro(a) Secretário(a)',
        4 => 'Segundo(a) Secretário(a)',
        5 => 'Tesoureiro(a)'
    ];
    public const SECRETARIOS = [
        'evangelismo' => 'Evangelismo e Missões',
        'responsabilidade' => 'Responsabilidade Social',
        'comunicacao' => 'Marketing e Comunicação',
        'produtos' => 'Produtos',
        'estatistica' => 'Estatística',
        'educacao' => 'Educação Cristã',
        'esporte' => 'Esportes e Lazer',
        'musica' => 'Música',
        'outras' => 'Outra',
    ];

    /**
     * Tipos de diretoria local
     * 
     * @var string
     */
    public const TIPO_DIRETORIA_LOCAL = 'local';
    
    /**
     * Tipos de diretoria federação
     * 
     * @var string
     */
    public const TIPO_DIRETORIA_FEDERACAO = 'federacao';
    
    /**
     * Tipos de diretoria sinodal
     * 
     * @var string
     */
    public const TIPO_DIRETORIA_SINODAL = 'sinodal';

    /**
     * Retorna a diretoria da sinodal,local,federacao e se ainda não tiver um cadastrada, cadastra uma
     *
     * @param string $tipo DiretoriaService::TIPO_DIRETORIA_SINODAL|DiretoriaService::TIPO_DIRETORIA_FEDERACAO|DiretoriaService::TIPO_DIRETORIA_LOCAL
     * @param string|null $id - Se não passar cria a diretoria
     * @return DiretoriaSinodal|DiretoriaFederacao|DiretoriaLocal|null
     */
    public static function getDiretoria(string $tipo, ?string $id = null): ?Model
    {
        $diretoria = null;
        $classe = self::CLASSES_DIRETORIAS[$tipo];
        $campo = "{$tipo}_id";
        $diretoria = $classe::where(
            $campo,
            $id ?? auth()->user()->$campo
        )->first();

        if ($diretoria === null) {
            $diretoria = self::criarDiretoria($tipo);
        }

        return $diretoria;
    }

    /**
     * Cria uma diretoria automaticamente se não houver
     *
     * @return DiretoriaSinodal|DiretoriaFederacao|DiretoriaLocal|null
     */
    public static function criarDiretoria($tipo): ?Model
    {
        $classe = self::CLASSES_DIRETORIAS[$tipo];
        $campo = "{$tipo}_id";

        return $classe::create([
            $campo => $sinodalId ?? auth()->user()->$campo
        ]);
    }

    /**
     * Retorna os campos e o nome formatado dos cargos
     *
     * @param string $tipo - Sinodal, Federação, Local
     * 
     * @return array
     */
    public static function getCargos(string $tipo): array
    {
        $retorno = [];

        foreach (self::CAMPOS_CARGOS as $indice => $campo) {
            $retorno[$campo] = self::CARGOS[$indice];
        }

        $campoSecretarioCausas = self::CAMPOS_SECRETARIO_CAUSAS[$tipo];
        $retorno[$campoSecretarioCausas['chave']] = $campoSecretarioCausas['valor'];

        if ($tipo == self::TIPO_DIRETORIA_LOCAL) {
            unset($retorno['secretaria_executiva']);
        }

        return $retorno;
    }

    /**
     * Retorna os campos e o nome formatado dos cargos para exibição
     * 
     * @param string $id
     * @param string $tipo - DiretoriaService::TIPO_DIRETORIA_SINODAL|DiretoriaService::TIPO_DIRETORIA_FEDERACAO|DiretoriaService::TIPO_DIRETORIA_LOCAL
     * 
     * @return array
     */
    public static function getDiretoriaTabela(string $id, string $tipo): array
    {
        $retorno = [];
        $diretoria = self::getDiretoria($tipo, $id);

        foreach (self::CAMPOS_CARGOS as $indice => $campo) {
            $contato = "contato_{$campo}";
            $retorno['cargos'][self::CARGOS[$indice]]['nome'] = $diretoria->$campo;
            $retorno['cargos'][self::CARGOS[$indice]]['contato'] = $diretoria->$contato;
        }

        if ($tipo == self::TIPO_DIRETORIA_LOCAL) {
            unset($retorno['cargos'][self::CARGOS[2]]);
        }
        
        $campoSecretarioCausas = self::CAMPOS_SECRETARIO_CAUSAS[$tipo];
        $retorno['cargos'][$campoSecretarioCausas['valor']]['nome'] = $diretoria->{$campoSecretarioCausas['chave']};
        $retorno['cargos'][$campoSecretarioCausas['valor']]['contato'] = $diretoria->{'contato_'.$campoSecretarioCausas['chave']};

        $retorno['atualizacao'] = $diretoria->updated_at->format('d/m/Y') ?? 'Nunca atualizado';

        return $retorno;
    }

    /**
     * Atualiza os dados da diretoria da federação
     *
     * @param array $dados
     * @param  DiretoriaSinodal|DiretoriaFederacao|DiretoriaLocal $diretoria
     *
     * @return void
     */
    public static function update(array $dados, Model $diretoria): void
    {
        $diretoria->update($dados);
    }

    /**
     * Retorna o array de secretários
     * 
     * @return array
     */
    public static function getSecretarios(): array
    {
        return self::SECRETARIOS;
    }

}
