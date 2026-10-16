# Motor narrativo determinístico

O módulo `App\Services\EstatisticaInteligente` gera o JSON de análise **sem chamar IA**: deriva indicadores dos formulários, classifica por faixas e escolhe textos no catálogo. O resultado é gravado em `analises_estatisticas` e o dashboard só lê.

## Princípio

```text
DADOS CALCULADOS → EVENTO (o quê) → TEXTO (como dizer) → JSON
```

Regras nunca montam frase. Catálogo nunca classifica número. O mesmo contexto produz sempre o mesmo JSON (idempotência).

Este módulo segue o padrão já usado em `App\Services\Gamificacao`: enums, DTOs, regras pequenas, registry injetável e testes unitários sem banco.

## Pipeline

```text
array $contexto
       │
       ▼
ContextoIaDTO::fromArray()     ← borda tolerante; chave ausente não lança
       │
       ▼
InsightRuleRegistry            ← avalia as 7 regras
       │
       ▼
EventoNarrativoDTO[]           ← código + severidade + variáveis
       │
       ├─► TemplateRendererService     → frases (variante por hash)
       ├─► ResumoExecutivoMontador     → 2–3 frases (crescimento → comparativo → qualidade)
       └─► PerguntaEstrategicaRegistry → perguntas por combinação de eventos
       │
       ▼
AnaliseIaResult::toArray()     ← schema da SPEC-005 §28
```

Entrada da interface (SPEC-005 §27):

```php
app(AnaliseIaInterface::class)->analisar($prompt, $contexto);
```

`$prompt` é ignorado no provedor `template` (não há LLM). `$contexto` é um array; o service hidrata `ContextoIaDTO` na borda.

## Camadas

| Pasta | Responsabilidade |
| --- | --- |
| `Contratos/` | Interfaces estáveis para o resto do sistema plugar depois |
| `Enums/` | Vocabulário fechado (evento, severidade, categoria, intenção) |
| `DTOs/` | Dados imutáveis que atravessam as camadas |
| `Regras/` | Classificação numérica → evento |
| `Catalogo/` | Texto, interpolação, resumo, perguntas |
| `Intencao/` | Classificador de pergunta livre (sem UI) |

Não existe árvore Domain/Application/Infrastructure. O projeto não usa isso.

## Contratos

### `AnaliseIaInterface`

Único ponto de entrada para quem for gerar análise (job, service, tela). Hoje só existe `TemplateAnaliseService`. Provedores OpenAI/Anthropic entram depois **sem mudar o caller**: basta outro bind no `EstatisticaInteligenteServiceProvider`.

### `InsightRuleInterface`

```php
public function avaliar(ContextoIaDTO $contexto): array; // EventoNarrativoDTO[]
public function categoria(): CategoriaIndicadorEnum;
```

Dados insuficientes → `[]`. Nunca lança por campo ausente.

### `CatalogoNarrativoInterface`

Separa a origem dos textos da renderização. A implementação atual lê `config/estatistica_narrativa.php`. Um adapter Eloquent pode substituir isso depois, sem tocar nas regras.

## Evento, não número

Cada regra resolve para um `EventoNarrativoEnum` (`crescimento_forte`, `renovacao_baixa`, …). O catálogo é indexado por esse código.

O próprio enum carrega o mapa editorial:

- `severidade()` — positivo, informativo, atenção, crítico (fixo; não é calculado em tempo real)
- `categoria()` — usada para fatiar `tendencias` e `comparacoes` no JSON
- `titulo()` — rótulo do item `{titulo, descricao, tipo}`

Severidade define o fatiamento do JSON:

- **positivo** → `destaques`
- **atenção** e **crítico** → `pontos_atencao`
- categoria **tendencia** → `tendencias`
- categoria **comparativo** → `comparacoes`

Um evento pode aparecer em mais de uma lista (ex.: acima da mediana é destaque **e** comparação).

## Regras

Uma classe por categoria, todas recebendo `FaixasNarrativas` (exceto anomalia, que só olha se a lista veio vazia).

| Classe | O que detecta |
| --- | --- |
| `CrescimentoInsightRule` | Faixas de variação anual; `valor_anterior = 0` → indeterminado; recuperação pós-retração **soma** ao evento do ano N |
| `DemografiaInsightRule` | Renovação geracional; concentração etária jovem/adulta ou distribuição equilibrada (nunca os dois juntos) |
| `AtividadesInsightRule` | Diversidade, concentração de categoria, aumento/queda do total |
| `ComparativoInsightRule` | Percentil vs. mediana de porte; salto abaixo↔acima → mudança relevante (adicional) |
| `AnomaliaInsightRule` | Lista não vazia → um único `ANOMALIA_DETECTADA` |
| `QualidadeInsightRule` | Score alto / médio / baixo |
| `TendenciaInsightRule` | 3+ anos de `crescimento_ativos_anual`: alta consistente, queda consistente ou oscilante |

O `InsightRuleRegistry` recebe as regras no construtor (não é estático). Testes podem passar um subconjunto.

## Faixas e textos (config, não código)

Thresholds em `config/estatistica_regras.php`, hidratados em `FaixasNarrativas`. Ajustar faixa não exige mudar a classe da regra.

Textos e perguntas em `config/estatistica_narrativa.php`. Placeholders `{percentual}`, `{ano_atual}`, `{indice}`, etc.

Seleção de variante (não aleatória, para não quebrar idempotência):

```text
crc32(contextoHash + evento) % N
```

Mesmo contexto + mesmo evento = sempre a mesma frase. `contextoHash` é SHA-256 do JSON canônico do DTO.

Em runtime, placeholder órfão é removido e logado. Nos testes, interpolação residual `{chave}` falha.

## JSON de saída

`AnaliseIaResult::toArray()`:

```json
{
  "titulo": "Panorama estatístico 2026",
  "resumo": "...",
  "destaques": [{ "titulo": "...", "descricao": "...", "tipo": "positivo" }],
  "pontos_atencao": [],
  "tendencias": [],
  "comparacoes": [],
  "perguntas_estrategicas": []
}
```

`modeloIa` (`template-engine-v1`) fica na propriedade do result, para persistência futura em `analises_estatisticas`. Não entra nesse array (não faz parte do schema §28).

## Classificador de intenção

`IntencaoClassifier` mapeia a pergunta do usuário para `historico`, `comparativo`, `crescimento`, `atividades` ou `desconhecida`, por palavras-chave. Não chama IA. A tela “Pergunte à estatística” ainda não existe; a classe já é testável isoladamente.

## Binding

`EstatisticaInteligenteServiceProvider` registra:

- singletons de faixas, catálogo, renderer, perguntas e registry
- `AnaliseIaInterface` → `TemplateAnaliseService` quando `AI_PROVIDER=template` (padrão)

Qualquer outro valor de `AI_PROVIDER` lança `InvalidArgumentException` de forma explícita (OpenAI/Anthropic ainda não existem).

Variáveis em `.env.example`:

```env
AI_PROVIDER=template
AI_ENABLED=false
```

`catalogo_version` em `config/estatistica.php` é o gancho de cache da SPEC-005: mudar textos → subir a versão → invalidar análises antigas.

## Persistência e hook

A análise **não é recalculada no GET** do dashboard. O fluxo de escrita:

```text
FormularioLocal/Federacao/Sinodal::store (após commit)
       │
       ▼
EstatisticaInteligenteHook
       │
       ▼
MontadorContextoService  → ContextoIaDTO (YoY + percentil de pares)
       │
       ▼
TemplateAnaliseService
       │
       ▼
analises_estatisticas   (updateOrCreate; skip se mesmo hash + catalogo_version)
```

Cascata do save local: UMP → federação → sinodal → região → nacional. O index só faz `SELECT`.

Pares (percentil de `membros_ativos`):

- local: UMPs da mesma federação (mín. 3); senão mesmo porte no Brasil
- federação: federações da mesma sinodal (mín. 3); senão mesmo porte
- sinodal: sinodais da mesma região
- região: outras regiões
- nacional: só comparativo anual

Backfill: `php artisan estatistica:analisar {ano?} {--forcar}`

## Testes

`tests/Unit/EstatisticaInteligente/`, sem banco.

- Uma classe por regra, com limites inclusivos/exclusivos (`4.99` / `5.00` / `15.00`, `valor_anterior = 0`, contexto vazio)
- Integridade do catálogo: todo evento do enum tem variante; interpolação não deixa `{chave}`
- Hash estável (mesma entrada → mesmo JSON)
- Golden file `tests/fixtures/estatistica_inteligente/panorama_2026.json` (números da SPEC-005 §59)
- Derivação YoY, percentil de pares, qualidade, cascata do hook

Rodar:

```bash
./vendor/bin/phpunit tests/Unit/EstatisticaInteligente
```

## O que este módulo não faz

- Tabelas `indicadores_estatisticos` / valores pré-calculados por indicador
- Livewire / “Pergunte à estatística”
- OpenAI/Anthropic
- Ranking nominal “melhores UMPs” ou comparação pelo nome
