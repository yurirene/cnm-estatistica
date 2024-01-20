<?php

namespace App\Services\Estatistica;

/**
 * Classe responsável por validar os campos dos formulários
 *  e salvar no banco de dados. Classe criada para dinamizar o formulário estatístico
 */
final abstract class FormularioEstatisticoAbstract
{
    /**
     * Retornar os dados de validacao dos formularios de umps locais
     *
     * @return array
     */
    public static function validacaoFormularioLocal(): array
    {
        return [];
    }

    /**
     * Retorna se o campo é valido conforme cadastrado
     *
     * @param array $dados
     * @return boolean
     */
    public static function validarCamposEValoresDosFormularios(array $dados): bool
    {
        return false;
    }

    /**
     * Validar se os parametros e dados informados estão corretos para um input select
     *
     * @param array $dados
     * @return boolean
     */
    public static function validarInputSelect(array $dados): bool
    {
        return false;
    }

    /**
     * Validar se os parametros e dados informados estão corretos para um input text
     *
     * @param array $dados
     * @return boolean
     */
    public static function validarInputText(array $dados): bool
    {
        return false;
    }

    /**
     * Validar se os parametros e dados informados estão corretos para um input number
     *
     * @param array $dados
     * @return boolean
     */
    public static function validarInputNumber(array $dados): bool
    {
        return false;
    }

    /**
     * Validar se os parametros e dados informados estão corretos para um input radio
     *
     * @param array $dados
     * @return boolean
     */
    public static function validarInputRadio(array $dados): bool
    {
        return false;
    }

    /**
     * Responsável por salvar no banco de dados
     *
     * @param array $dados
     * @return void
     */
    public static function salvarNoBD(array $dados): void
    {

    }
}
