<?php

namespace App\Services\Gamificacao\Regras;

use App\Models\Gamificacao\Conquista;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\TipoConquista;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;

class PontuadorEventos
{
    /**
     * @param  iterable<Conquista|object>  $conquistas
     * @return array<string, int>
     */
    public function agregarPorTipo(iterable $conquistas): array
    {
        $porTipo = [];

        foreach ($conquistas as $conquista) {
            $tipo = $this->tipoDe($conquista);
            if ($tipo === null || ! $tipo->ehEvento()) {
                continue;
            }

            $pontos = $tipo->pontos();
            if ($tipo->unicoNoAno()) {
                $porTipo[$tipo->value] = $pontos;
                continue;
            }

            $porTipo[$tipo->value] = ($porTipo[$tipo->value] ?? 0) + $pontos;
        }

        if (isset($porTipo[TipoConquista::Esporadico->value])) {
            $maxEsporadico = GamificacaoConfiguracaoService::getInt(
                'pilares.eventos.esporadico_maximo_pontos',
                4
            );
            $porTipo[TipoConquista::Esporadico->value] = min(
                $porTipo[TipoConquista::Esporadico->value],
                $maxEsporadico
            );
        }

        return $porTipo;
    }

    /**
     * @param  array<string, int>  $pontosPorTipo
     */
    public function total(array $pontosPorTipo, ?int $teto = null): int
    {
        $soma = 0;

        foreach ($pontosPorTipo as $tipoValor => $pontos) {
            $tipo = TipoConquista::tryFrom((string) $tipoValor);
            if ($tipo === null || ! $tipo->ehEvento()) {
                continue;
            }

            $valor = max(0, (int) $pontos);
            if ($tipo->unicoNoAno()) {
                $valor = min($valor, $tipo->pontos());
            } elseif ($tipo === TipoConquista::Esporadico) {
                $maxEsporadico = GamificacaoConfiguracaoService::getInt(
                    'pilares.eventos.esporadico_maximo_pontos',
                    4
                );
                $valor = min($valor, $maxEsporadico);
            }

            $soma += $valor;
        }

        $teto ??= Pilar::Eventos->maximo();

        return min(max(0, $teto), $soma);
    }

    private function tipoDe(object $conquista): ?TipoConquista
    {
        $tipo = $conquista->tipo ?? null;

        if ($tipo instanceof TipoConquista) {
            return $tipo;
        }

        return TipoConquista::tryFrom((string) $tipo);
    }
}
