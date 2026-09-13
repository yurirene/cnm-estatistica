<?php

namespace App\Services\Gamificacao\Enums;

use App\Services\Gamificacao\GamificacaoConfiguracaoService;

enum TipoConquista: string
{
    case BonusMissionario = 'bonus_missionario';
    case PmfOficial = 'pmf_oficial';
    case PmfParceria = 'pmf_parceria';
    case Djp = 'djp';
    case Esporadico = 'esporadico';
    case Resgate = 'resgate';

    public function natureza(): NaturezaInstancia
    {
        $valor = GamificacaoConfiguracaoService::get("conquistas.{$this->value}.natureza");

        return NaturezaInstancia::from($valor);
    }

    public function pontos(): int
    {
        return GamificacaoConfiguracaoService::getInt("conquistas.{$this->value}.pontos");
    }

    public function unicoNoAno(): bool
    {
        return (bool) GamificacaoConfiguracaoService::get("conquistas.{$this->value}.unico_ano", false);
    }

    public function ehEvento(): bool
    {
        return in_array($this, [
            self::PmfOficial,
            self::PmfParceria,
            self::Djp,
            self::Esporadico,
        ], true);
    }

    public function label(): string
    {
        return match ($this) {
            self::BonusMissionario => 'Bônus Missionário',
            self::PmfOficial => 'PMF Oficial',
            self::PmfParceria => 'PMF Parceria',
            self::Djp => 'DJP',
            self::Esporadico => 'Evento esporádico',
            self::Resgate => 'Resgate (último trimestre)',
        };
    }
}
