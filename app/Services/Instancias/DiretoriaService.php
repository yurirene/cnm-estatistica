<?php

namespace App\Services\Instancias;

use App\Models\Diretorias\DiretoriaSinodal;

class DiretoriaService
{
    public const CAMPOS_CARGOS = [
        0 => 'presidente',
        1 => 'vice_presidente',
        2 => 'secretaria_executiva',
        3 => 'primeiro_secretario',
        4 => 'segundo_secretario',
        5 => 'tesoureiro',
        6 => 'secretario_sinodal'
    ];
    public const CARGOS = [
        0 => 'Presidente',
        1 => 'Vice-Presidente',
        2 => 'Secretário-Executivo',
        3 => 'Primeiro(a) Secretário(a)',
        4 => 'Segundo(a) Secretário(a)',
        5 => 'Tesoureiro(a)',
        6 => 'Secretário Sinodal'
    ];

    /**
     * Retorna a diretoria da sinodal e se ainda não tiver um cadastrada, cadastra uma
     *
     * @return DiretoriaSinodal|null
     */
    public static function getDiretoria(?string $sinodalId = null): ?DiretoriaSinodal
    {
        $diretoria = DiretoriaSinodal::where(
            'sinodal_id',
            $sinodalId ?? auth()->user()->sinodais->first()->id
        )
            ->first();

        if (is_null($diretoria)) {
            $diretoria = self::criarDiretoria($sinodalId);
        }

        return $diretoria;
    }

    /**
     * Cria uma diretoria automaticamente se não houver
     *
     * @return DiretoriaSinodal|null
     */
    public static function criarDiretoria(?string $sinodalId): ?DiretoriaSinodal
    {
        return DiretoriaSinodal::create([
            'sinodal_id' => $sinodalId ?? auth()->user()->sinodais->first()->id
        ]);
    }

    /**
     * Retorna os campos e o nome formatado dos cargos
     *
     * @return array
     */
    public static function getCargos(): array
    {
        $retorno = [];

        foreach (self::CAMPOS_CARGOS as $indice => $campo) {
            $retorno[$campo] = self::CARGOS[$indice];
        }

        return $retorno;
    }

    public static function getDiretoriaTabela(?string $sinodalId = null): array
    {
        $retorno = [];
        $diretoria = self::getDiretoria($sinodalId);

        foreach (self::CAMPOS_CARGOS as $indice => $campo) {
            $contato = "contato_{$campo}";
            $retorno['cargos'][self::CARGOS[$indice]]['nome'] = $diretoria->$campo;
            $retorno['cargos'][self::CARGOS[$indice]]['contato'] = $diretoria->$contato;
        }

        $retorno['atualizacao'] = $diretoria->updated_at->format('d/m/Y') ?? 'Nunca atualizado';

        return $retorno;
    }

    /**
     * Atualiza os dados da diretoria
     *
     * @param array $dados
     * @param DiretoriaSinodal $diretoria
     *
     * @return void
     */
    public static function update(array $dados, DiretoriaSinodal $diretoria): void
    {
        $diretoria->update($dados);
    }

}
