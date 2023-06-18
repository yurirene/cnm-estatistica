<?php

namespace App\Helpers;

class FormularioEventoHelper
{
    public const TEXT = "text";
    public const SELECT = "select";
    public const DATA = "data";
    public const TELEFONE = "telefone";
    public const REMOVER = "remover";

    public const OPCOES = [
        self::TEXT => 'Texto Comum',
        self::SELECT => 'Seleção',
        self::DATA => 'Data',
        self::TELEFONE => 'Telefone',
        self::REMOVER => 'Remover'
    ];

    public const INPUTS = [
        self::TEXT => '<input type="text" class="form-control" name="%_name_%" required>',
        self::DATA => '<input type="text" class="form-control isDate" name="%_name_%" required>',
        self::TELEFONE => '<input type="text" class="form-control isTelefone" name="%_name_%" required>',
        self::SELECT => '<select class="form-control" name="%_name_%" placeholder="Escolha uma opção"'
            . ' required>%_options_%</select>'
    ];

    /**
     * Função para renderizar o input que vai estar no form do site
     *
     * @param string $type
     * @param string $name
     * @param string $options
     * @return string
     */
    public static function mount(string $type, string $name, ?string $options = ''): string
    {
        if (!isset(self::INPUTS[$type])) {
            return '';
        }
        $input = self::INPUTS[$type];
        $name = self::formatName($name);
        $input = str_replace('%_name_%', $name, $input);
        if ($type == self::SELECT) {
            $inputOptions = '';
            foreach (explode(',', $options) as $option) {
                $inputOptions .= "<option value='{$option}'>{$option}</option>";
            }
            $input = str_replace('%_options_%', $inputOptions, $input);
        }
        return $input;
    }

    /**
     * Formata o campo name do input removendo espacos, acentos
     * e converte para minúsculo
     *
     * @param string $name
     * @return string
     */
    public static function formatName(string $name): string
    {
        return strtolower(str_replace(' ', '_', FormHelper::removerAcentos($name)));
    }

}
