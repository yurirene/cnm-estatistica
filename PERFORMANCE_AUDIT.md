# PERFORMANCE_AUDIT.md — Auditoria de performance

**Projeto:** cnm-estatistica  
**Stack:** Laravel 10.48.29 · PHP ^8.1  
**Escopo:** análise estática (sem APM/`EXPLAIN` de produção) — nenhum arquivo alterado.  
**Data:** 2026-11-28  

**Legenda:** **Comprovado** = padrão inequívoco no código · **Oportunidade** = alto impacto esperado, sem métrica de produção.

---

## 1. Resumo executivo

Não há `app/Jobs/` nem `ShouldQueue` no projeto. `QUEUE_CONNECTION=sync` no `.env.example`. Processamentos pesados (relatório geral, gamificação, análises, mapa, rankings) rodam **no request HTTP** ou no schedule do console.

| Prioridade | Achados-chave |
|------------|---------------|
| **P0 / Crítica** | N+1 em `getDadosQualidadeEstatistica`, `formulariosEntregues`, `MapaService`; cadeia síncrona no submit de formulário |
| **P1 / Alta** | DataTables sem `with()`; índices compostos ausentes; `atualizarTodosOsDados` / relatório geral em memória |
| **P2 / Média–Baixa** | `exists()` vs `get()->isNotEmpty()`; exports Excel; cache de parâmetros |

---

## 2. Achados

### PERF-01 — N+1 cascata em qualidade/ranking estatístico

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/Services/Estatistica/EstatisticaService.php` `getDadosQualidadeEstatistica` L328–366; helpers `getDadosFormularioFederacao` / `Local` / `calcularQualidade` |
| **Descrição** | Para cada sinodal, múltiplas queries de relatórios, porcentagens, qualidade e ranking; trabalho duplicado (porcentagem + `qtd_*` recalculam os mesmos dados). |
| **Evidência** | `Sinodal::...->get()->map` com `$item->relatorios()->...->get()->count()`, chamadas aninhadas a `getPorcentagem*` / `getDadosFormulario*` / `calcularQualidade` / `getPontuacaoUltimaAtualizacao`; `$item->regiao->nome` lazy. |
| **Impacto** | Request Ajax de acompanhamento/ranking pode gerar milhares de queries. |
| **Prioridade** | **Crítica** |
| **Sugestão** | Agregações com `withCount`/joins/`GROUP BY`; carregar rankings em lote; eager `regiao`; calcular uma vez e reutilizar. |
| **Risco de regressão** | Médio — regras de qualidade/ranking delicadas; testes de paridade. |
| **Tipo** | Comprovado |

---

### PERF-02 — N+1 triplo em `formulariosEntregues` (DatatableAjax)

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/Services/DatatableAjaxService.php` ~L212–255 |
| **Descrição** | Por sinodal → federação → local: `relatorios()`, `federacoes()`, `locais()` em loops; `Parametro`/`ValorAciAno` dentro do loop. |
| **Evidência** | Estrutura `each` aninhada com queries Eloquent por item. |
| **Impacto** | Painel Ajax O(S×F×L) queries. |
| **Prioridade** | **Crítica** |
| **Sugestão** | Prefetch com `whereIn` (2–3 queries); cachear `min_aci` fora do loop. |
| **Risco de regressão** | Médio. |
| **Tipo** | Comprovado |

---

### PERF-03 — Mapa do dashboard: N queries por estado

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/Services/MapaService.php` `getDefaultMap` L31–50 + `getTotalizador` L53+; caller `DashboardController` |
| **Descrição** | Loop de estados; cada um executa várias queries (`FormularioLocal`, `Local`, `Federacao`, etc.) e agrega JSON em PHP. |
| **Evidência** | `foreach ($estados as $estado) { self::getTotalizador($estado); }` com `whereHas` + `->get()` + loops. |
| **Impacto** | Home do dashboard lenta; reutilizado também em painel nacional (`sociosBarras` por estado). |
| **Prioridade** | **Crítica** |
| **Sugestão** | Uma agregação por `estado_id` para o ano; cache TTL curto por `(ano, escopo usuário)`. |
| **Risco de regressão** | Médio. |
| **Tipo** | Comprovado |

---

### PERF-04 — Cadeia síncrona no submit de formulário (sem filas)

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `FormularioLocalService` / `FormularioFederacaoService` / `FormularioSinodalService` (hooks pós-store); `GamificacaoHook`; `EstatisticaInteligenteHook`; ausência de `app/Jobs` |
| **Descrição** | Após salvar formulário: `atualizarRelatorioGeral` (full scan) + totalizadores + recalcular gamificação + gerar análises — tudo no mesmo request. |
| **Evidência** | 0 Jobs; `QUEUE_CONNECTION=sync`; hooks síncronos; `getDadosRelatorioGeral` carrega todos formulários do ano. |
| **Impacto** | Timeout/latência no envio de relatório; piora com crescimento da base nacional. |
| **Prioridade** | **Crítica** |
| **Sugestão** | Após commit, despachar Jobs (`AtualizarEstatisticaGeral`, `RecalcularGamificacao`, `GerarAnalise`) com idempotência/debounce. |
| **Risco de regressão** | Médio — consistência eventual nos placares/análises. |
| **Tipo** | Comprovado |

---

### PERF-05 — DataTables sem eager loading

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | Ex.: `Instancias/LocalDataTable.php`, `Detalhamento/IgrejasDatatable.php`, `PresbiterioDatatable.php`, `ComissaoExecutiva/DelegadosDataTable.php`, `PesquisaDataTable.php`, `UserDataTable.php`, `ComprovanteAciDataTable.php`, `FederacaoDataTable.php`, `SinodalDataTable.php` |
| **Descrição** | Colunas acessam relações sem `with()`/`withCount`; alguns fazem `relatorios()->get()` por linha. |
| **Evidência** | Queries sem eager; contraste positivo: `MissionariosDataTable`, `DigestoDataTable`, `RankingDataTable` usam `with()`. |
| **Impacto** | Cada page draw multiplica queries. |
| **Prioridade** | **Alta** |
| **Sugestão** | `->with([...])` + `withCount`; eliminar `relatorios()->orderBy()->get()->first()` em favor de subquery. |
| **Risco de regressão** | Baixo (cuidado com searchable/orderable + join). |
| **Tipo** | Comprovado |

---

### PERF-06 — Comprovante ACI: queries por linha

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/DataTables/ComprovanteAciDataTable.php` ~L57–96; `ComprovanteAciService` totais |
| **Descrição** | Por linha: `FormularioSinodal::where->first`, totais de sócios via `Federacao::pluck` + `FormularioFederacao::get`. |
| **Evidência** | Callbacks de coluna com queries Eloquent. |
| **Impacto** | 3–5+ queries por comprovante na página. |
| **Prioridade** | **Alta** |
| **Sugestão** | Batch por `(sinodal_id, ano)`; eager `sinodal`. |
| **Risco de regressão** | Baixo–médio. |
| **Tipo** | Comprovado |

---

### PERF-07 — Índices compostos ausentes

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | Migrations: `create_formularios_table`, `formularios_federacao`, `formulario_sinodals`, `parameters`, `rankings`, `comprovantes_aci` |
| **Descrição** | Lookups frequentes por `(ano_referencia, local_id|federacao_id|sinodal_id)` e `parametros.nome` sem índice adequado; `rankings` sem índice em `sinodal_id`/`ano`. |
| **Evidência** | Migrations com FK single-column; sem composto com `ano_referencia`; `parametros.nome` sem unique/index. |
| **Impacto** | Full scans / lookups lentos à medida que as tabelas crescem. |
| **Prioridade** | **Alta** |
| **Sugestão** | ADD INDEX compostos alinhados às queries; unique em `parametros.nome` se aplicável; `(sinodal_id, ano_referencia)` em rankings/comprovantes. |
| **Risco de regressão** | Baixo (migration ADD INDEX). Validar em staging. |
| **Tipo** | Comprovado |

---

### PERF-08 — Relatório geral e `atualizarTodosOsDados` em memória

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `EstatisticaService::getDadosRelatorioGeral` L411–756; `atualizarTodosOsDados` L804–812; `EstatisticaController` |
| **Descrição** | Carrega todos os formulários do ano e agrega em PHP; endpoint HTTP percorre todos locais. |
| **Evidência** | `FormularioLocal/Federacao/Sinodal::...->get()` + somas; foreach completo sem chunk/queue. |
| **Impacto** | Pico de memória/CPU/DB; timeout. |
| **Prioridade** | **Alta** |
| **Sugestão** | Incremental update no submit; Job chunked para rebuild; expandir uso de `EstatisticaGeral` snapshot. |
| **Risco de regressão** | Médio–alto. |
| **Tipo** | Comprovado |

---

### PERF-09 — Gamificação: N+1 de jurisdição + save 1 a 1

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `JurisdicaoExercicioResolver.php`; `MontadorContexto.php`; `GamificacaoRankingService`; comando `gamificacao:recalcular` |
| **Descrição** | Sem snapshot: transferências por local em loop; ranking salva placares individualmente; recálculo diário síncrono. |
| **Evidência** | `TransferenciaUnidade::where('local_id',...)->get()` por local; `save()` em loop; schedule sem fila. |
| **Impacto** | Recálculo noturno e hooks pós-formulário caros. |
| **Prioridade** | **Alta** |
| **Sugestão** | Prefetch vínculos; `upsert`; Job queueable; aproveitar snapshot existente. |
| **Risco de regressão** | Alto (art. 41 / regras de vínculo) — testes obrigatórios. |
| **Tipo** | Comprovado |

---

### PERF-10 — Parâmetros / ano de referência sem cache

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `EstatisticaService::getAnoReferencia` L836–838; usos dentro de loops em DatatableAjax/Mapa |
| **Descrição** | `Parametro::where('nome', ...)->first()` repetido; contraste: `GamificacaoConfiguracaoService` usa `Cache::rememberForever`. |
| **Evidência** | Query direta sem memoization de request. |
| **Impacto** | Queries redundantes em hot paths. |
| **Prioridade** | **Média** |
| **Sugestão** | Cache/`once()` por request; índice em `parametros.nome`. |
| **Risco de regressão** | Baixo. |
| **Tipo** | Comprovado |

---

### PERF-11 — Agregação JSON em PHP

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `MapaService`, `ComprovanteAciService`, `MontadorContexto`, `PainelNacionalService::evolucao`, relatório geral |
| **Descrição** | Campos JSON (`perfil`, `aci`, `programacoes`) carregados integralmente para somar em PHP. |
| **Evidência** | `->get()` + `sum`/`filter` em Collections. |
| **Impacto** | I/O e memória altos; SQL direto limitado pelo schema. |
| **Prioridade** | **Média** |
| **Sugestão** | Colunas geradas/materializadas para métricas-chave; ou snapshots anuais. |
| **Risco de regressão** | Médio–alto (schema). |
| **Tipo** | Comprovado / oportunidade de schema |

---

### PERF-12 — Exports e PDF síncronos

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `BaseDadosFormularioExport.php` (`FromView` + `get()`); controllers Tesouraria/Congresso/Pesquisa |
| **Descrição** | Export Excel/PDF no request HTTP carrega datasets completos. |
| **Evidência** | `FromView` com collection; geração DomPDF no controller. |
| **Impacto** | Timeouts em bases grandes. |
| **Prioridade** | **Média** |
| **Sugestão** | `FromQuery` + chunk; Job + download assíncrono. |
| **Risco de regressão** | Médio. |
| **Tipo** | Comprovado |

---

### PERF-13 — Micro-otimizações comprovadas

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `ComissaoExecutivaService` (`get()->isNotEmpty()`); `AuthServiceProvider` gate apps (loop `apps()->where->get`); `DashboardHelper` `User::find(auth()->id())`; `DatatableAjaxService::listarVisualizados` |
| **Descrição** | Padrões que geram queries desnecessárias. |
| **Evidência** | Grep/`get()->isNotEmpty`; foreach de apps; re-fetch de user. |
| **Impacto** | Baixo–médio por request, cumulativo. |
| **Prioridade** | **Baixa–Média** |
| **Sugestão** | `exists()`; `whereIn` único; `auth()->user()`. |
| **Risco de regressão** | Baixo. |
| **Tipo** | Comprovado |

---

## 3. Cache existente (positivo)

- `GamificacaoConfiguracaoService` — `Cache::rememberForever`
- Role/permissions no `User`/`Role`
- `Federacao::dadosDatatable` / `Sinodal::dadosFederacaoLocal` — 1 dia
- `PlanejamentoEstrategicoService` — cache in-memory de formulários + select parcial (bom padrão interno)

## 4. Lacunas de cache (oportunidade)

- `Parametro` / ano referência / `min_aci` / `valor_aci`
- Resultado de `getDefaultMap` / painel nacional
- Rankings estatísticos (hoje recalculados no Ajax)
- Melhor uso de `EstatisticaGeral` como fonte do relatório

## 5. Ordem recomendada (performance)

1. PERF-01 + PERF-02 + PERF-03 (N+1 hot paths)  
2. PERF-04 (filas no submit)  
3. PERF-05 + PERF-06 (DataTables)  
4. PERF-07 (índices)  
5. PERF-08 + PERF-09 (rebuilds / gamificação)  
6. PERF-10–PERF-13 (cache e higiene)  
