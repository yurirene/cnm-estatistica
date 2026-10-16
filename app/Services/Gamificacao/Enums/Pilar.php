<?php

namespace App\Services\Gamificacao\Enums;

use App\Services\Gamificacao\GamificacaoConfiguracaoService;

enum Pilar: string
{
    case Estatistica = 'estatistica';
    case Aci = 'aci';
    case Evangelismo = 'evangelismo';
    case ComissaoExecutiva = 'comissao_executiva';
    case BonusMissionario = 'bonus_missionario';
    case SpeedRun = 'speed_run';
    case Eventos = 'eventos';
    case Resgate = 'resgate';

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
        return in_array($this, [self::ComissaoExecutiva, self::BonusMissionario], true);
    }

    public function exclusivoFederacao(): bool
    {
        return in_array($this, [self::SpeedRun, self::Eventos, self::Resgate], true);
    }

    public function cor(): string
    {
        return match ($this) {
            self::Estatistica => 'cyan',
            self::Aci => 'teal',
            self::Evangelismo => 'green',
            self::ComissaoExecutiva, self::SpeedRun => 'orange',
            self::BonusMissionario, self::Eventos => 'gold',
            self::Resgate => 'warn',
        };
    }
}
