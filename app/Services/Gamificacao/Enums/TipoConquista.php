<?php

namespace App\Services\Gamificacao\Enums;

use App\Services\Gamificacao\GamificacaoConfiguracaoService;

enum TipoConquista: string
{
    case ConexaoMissionaria = 'conexao_missionaria';
    case PmfOficial = 'pmf_oficial';
    case PmfParceria = 'pmf_parceria';
    case Djp = 'djp';
    case Esporadico = 'esporadico';
    case SeloEntregaAntecipada = 'selo_entrega_antecipada';
    case SeloEngajamento100 = 'selo_engajamento_100';

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

    public function ehSelo(): bool
    {
        return in_array($this, [
            self::SeloEntregaAntecipada,
            self::SeloEngajamento100,
        ], true);
    }

    public function ehLancavelManual(): bool
    {
        return ! $this->ehSelo();
    }

    public function label(): string
    {
        return match ($this) {
            self::ConexaoMissionaria => 'Conexão Missionária',
            self::PmfOficial => 'PMF Oficial',
            self::PmfParceria => 'PMF em Parceria',
            self::Djp => 'DJP',
            self::Esporadico => 'Evento esporádico',
            self::SeloEntregaAntecipada => 'Selo Entrega Antecipada',
            self::SeloEngajamento100 => 'Selo 100% de Engajamento',
        };
    }
}
