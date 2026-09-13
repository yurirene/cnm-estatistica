<?php

namespace App\Services\EstatisticaInteligente\Enums;

enum CategoriaIndicadorEnum: string
{
    case Crescimento = 'crescimento';
    case Demografia = 'demografia';
    case Familia = 'familia';
    case Educacao = 'educacao';
    case Emprego = 'emprego';
    case Atividades = 'atividades';
    case Espiritualidade = 'espiritualidade';
    case Evangelizacao = 'evangelizacao';
    case Social = 'social';
    case Comunhao = 'comunhao';
    case Inclusao = 'inclusao';
    case Financeiro = 'financeiro';
    case Organizacional = 'organizacional';
    case Qualidade = 'qualidade';
    case Comparativo = 'comparativo';
    case Tendencia = 'tendencia';
    case Anomalia = 'anomalia';
}
