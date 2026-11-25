<?php

namespace App\Services\Gamificacao\Enums;

use App\Services\Gamificacao\GamificacaoConfiguracaoService;

enum Pilar: string
{
    case Estatistica = 'estatistica';
    case Aci = 'aci';
    case Evangelismo = 'evangelismo';
    case ComissaoExecutiva = 'comissao_executiva';
    case ConexaoMissionaria = 'conexao_missionaria';
    case EntregaAntecipada = 'entrega_antecipada';
    case Eventos = 'eventos';

    public function label(): string
    {
        return (string) GamificacaoConfiguracaoService::get("pilares.{$this->value}.label", $this->value);
    }

    public function maximo(): int
    {
        return GamificacaoConfiguracaoService::getInt("pilares.{$this->value}.max");
    }

    /** @return int[] */
    public function valoresLegais(): array
    {
        return GamificacaoConfiguracaoService::get("pilares.{$this->value}.legais", [0]);
    }

    public function exclusivoSinodal(): bool
    {
        return in_array($this, [self::ComissaoExecutiva, self::ConexaoMissionaria], true);
    }

    public function exclusivoFederacao(): bool
    {
        return in_array($this, [self::EntregaAntecipada, self::Eventos], true);
    }

    public function cor(): string
    {
        return match ($this) {
            self::Estatistica => 'cyan',
            self::Aci => 'teal',
            self::Evangelismo => 'green',
            self::ComissaoExecutiva, self::EntregaAntecipada => 'orange',
            self::ConexaoMissionaria, self::Eventos => 'gold',
        };
    }
}
