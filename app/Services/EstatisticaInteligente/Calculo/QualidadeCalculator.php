<?php

namespace App\Services\EstatisticaInteligente\Calculo;

final class QualidadeCalculator
{
    /**
     * @param  array{completude: int, consistencia: int, historico: int, anomalias: int}  $pesos
     */
    public function __construct(private readonly array $pesos)
    {
    }

    public static function fromConfig(): self
    {
        return new self(config('estatistica_regras.qualidade', [
            'completude' => 40,
            'consistencia' => 30,
            'historico' => 20,
            'anomalias' => 10,
        ]));
    }

    /**
     * @param  array<string, mixed>  $perfil
     * @param  array<int, mixed>  $anomalias
     */
    public function score(array $perfil, bool $temHistorico, array $anomalias): float
    {
        $completude = $this->completude($perfil);
        $consistencia = $this->consistencia($perfil);
        $historico = $temHistorico ? 100.0 : 0.0;
        $anomalia = $anomalias === [] ? 100.0 : 0.0;

        $totalPesos = array_sum($this->pesos);
        if ($totalPesos <= 0) {
            return 0.0;
        }

        $nota = (
            $completude * ($this->pesos['completude'] ?? 0)
            + $consistencia * ($this->pesos['consistencia'] ?? 0)
            + $historico * ($this->pesos['historico'] ?? 0)
            + $anomalia * ($this->pesos['anomalias'] ?? 0)
        ) / $totalPesos;

        return round($nota, 1);
    }

    /**
     * @param  array<string, mixed>  $perfil
     */
    private function completude(array $perfil): float
    {
        $obrigatorios = ['ativos', 'cooperadores', 'menor19', 'de19a23', 'de24a29', 'de30a35'];
        $preenchidos = 0;
        foreach ($obrigatorios as $chave) {
            if (array_key_exists($chave, $perfil) && $perfil[$chave] !== null && $perfil[$chave] !== '') {
                $preenchidos++;
            }
        }

        return ($preenchidos / count($obrigatorios)) * 100;
    }

    /**
     * @param  array<string, mixed>  $perfil
     */
    private function consistencia(array $perfil): float
    {
        $ativos = (int) ($perfil['ativos'] ?? 0);
        $cooperadores = (int) ($perfil['cooperadores'] ?? 0);
        $total = $ativos + $cooperadores;
        $faixas = (int) ($perfil['menor19'] ?? 0)
            + (int) ($perfil['de19a23'] ?? 0)
            + (int) ($perfil['de24a29'] ?? 0)
            + (int) ($perfil['de30a35'] ?? 0);

        if ($total === 0 && $faixas === 0) {
            return 100.0;
        }

        return $total === $faixas ? 100.0 : 0.0;
    }
}
