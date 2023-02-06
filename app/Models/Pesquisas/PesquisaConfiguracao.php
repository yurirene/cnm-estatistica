<?php

namespace App\Models\Pesquisas\Pesquisas;

use Illuminate\Database\Eloquent\Model;

class PesquisaConfiguracao extends Model
{
    protected $table = 'pesquisa_configuracoes';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = ['configuracao' => 'array'];

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
}
