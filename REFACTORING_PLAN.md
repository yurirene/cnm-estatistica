# REFACTORING_PLAN.md — Plano de modernização incremental

**Projeto:** cnm-estatistica  
**Premissas:** sem reescrita total · sem impor Clean Architecture/Repositories em todos os módulos · respeitar particularidades · compatível com produção (Laravel 10 / PHP ^8.1)  
**Documentos-base:** `CODEBASE_AUDIT.md`, `SECURITY_AUDIT.md`, `PERFORMANCE_AUDIT.md`  
**Data:** 2026-11-28  

---

## 1. Princípios de execução

1. **Segurança e integridade de dados primeiro** — antes de refactors estéticos.
2. **Hotspots, não campanhas globais** — fatiar classes grandes ao tocá-las.
3. **Usar Gamificação / Estatística Inteligente como referência** de DI, tipagem e testes — não como molde obrigatório para CRUD simples.
4. **Cada PR pequeno, reversível**, com Feature/Unit tests nos caminhos tocados.
5. **Boy scout:** ao editar um arquivo legado, melhorar tipagem/FormRequest/logging localmente.
6. **Repository só onde query se repete** — ausência de Repository não é problema.

---

## 2. Qualidade — achados que alimentam o plano

### Q-01 — Complexidade ciclomática concentrada

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `CongressoNacionalController::exportRelatorioEstatistico` (~L1615–1738); `EstatisticaService::getDadosRelatorioGeral` (~L405–797); totalizadores Formulario* (~250–320 LOC); `GamificacaoAcompanhamentoService::montarOrganizacoes` |
| **Descrição** | Métodos com dezenas de branches e centenas de linhas. |
| **Evidência** | Contagem de linhas + pontos de decisão (ver CODEBASE_AUDIT CA-09). |
| **Impacto** | Bugs, reviews impossíveis, regressões. |
| **Prioridade** | **Alta** |
| **Sugestão** | Extrair exporters/builders; `array_sum_fields` para `somarCampos`. |
| **Risco de regressão** | Alto — testes de caracterização. |

### Q-02 — Duplicação CRUD / flash / diretorias / formulários

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | Sinodal/Federacao/Local Controllers; Diretorias*; Formulario*Services; mensagens flash repetidas |
| **Descrição** | Blocos try/catch idênticos; `Controller::MSG_*` pouco usados. |
| **Evidência** | ~35 arquivos com “Algo deu Errado!”; APIs espelhadas. |
| **Impacto** | Correções inconsistentes. |
| **Prioridade** | **Média** |
| **Sugestão** | Trait de redirect/flash; controllers parametrizados por tipo. |
| **Risco de regressão** | Médio. |

### Q-03 — Tratamento de exceções inconsistente

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | Controllers CRUD (swallow + flash); `FileCast` catch vazio; `AppServiceProvider` catch vazio L36–39; exports que devolvem `$th->getMessage()` |
| **Descrição** | Mix de engolir, logar+rethrow genérico, `abort`, e vazamento de mensagem. |
| **Evidência** | Padrões listados na auditoria de qualidade. |
| **Impacto** | Falhas silenciosas; info disclosure. |
| **Prioridade** | **Alta** |
| **Sugestão** | Sempre logar; Handler mapeia DomainException → flash; nunca catch vazio. |
| **Risco de regressão** | Baixo–médio. |

### Q-04 — Cobertura de testes insuficiente nos módulos críticos

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `tests/` — 27 Unit (Gamificação, EI, …) · 2 Feature; sem testes Auth/ACL, Estatistica clássica, Congresso, Instâncias, Produtos, Digesto |
| **Descrição** | Testes densos nos módulos novos; legado crítico sem rede de segurança. |
| **Evidência** | Inventário `tests/Feature` e `tests/Unit`. |
| **Impacto** | Refactors arriscados; regressões silenciosas. |
| **Prioridade** | **Alta** |
| **Sugestão** | Feature tests: login+ACL, delete destrutivo, submit formulário, ACI, Linktree auth; Unit após extrair `getDadosRelatorioGeral`. |
| **Risco de regressão** | N/A (adição). |

### Q-05 — Rotas monolíticas e deletes GET

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `routes/web.php` (956L, ~34 grupos auth/dashboard repetidos) |
| **Descrição** | Organização por comentários; verbos HTTP incorretos (segurança). |
| **Evidência** | Ver SECURITY SEC-04 + CODEBASE CA. |
| **Impacto** | Manutenção e CSRF. |
| **Prioridade** | **Alta** (verbos) / **Média** (split de arquivos) |
| **Sugestão** | `routes/dashboard/*.php` includes; um group auth; migrar deletes. |
| **Risco de regressão** | Médio. |

### Q-06 — Código morto e nomenclatura

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | Controllers/Services/DataTables órfãos; typo `secreatria_produtos`; `DataTable` vs `Datatable` |
| **Descrição** | Ruído e inconsistência. |
| **Evidência** | CODEBASE CA-10 / auditoria qualidade. |
| **Impacto** | Baixo operacional; médio cognitivo. |
| **Prioridade** | **Baixa–Média** |
| **Sugestão** | Remover órfãos confirmados; alias gradual do typo de role. |
| **Risco de regressão** | Baixo (exceto role slug). |

### Q-07 — Tipagem inconsistente

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | Controllers ~34% return types; Gamificação/EI 100% |
| **Descrição** | Dois padrões no mesmo codebase. |
| **Evidência** | Amostra por pasta. |
| **Impacto** | DX e erros em runtime evitáveis. |
| **Prioridade** | **Baixa** (campanha) / **Média** (ao editar) |
| **Sugestão** | Exigir tipagem em PRs novos; boy scout no legado. |
| **Risco de regressão** | Baixo. |

---

## 3. Roadmap por fases

### Fase 0 — Blindagem (1–2 sprints) — **obrigatória antes de refactors grandes**

| Ordem | Item | Origem | Prioridade | Entrega | Regressão |
|------:|------|--------|------------|---------|-----------|
| 1 | Remover/proteger `GET /coletor-dados/restaurar/{local}` | SEC-03 | Crítica | Rota só auth + ownership + POST | Média |
| 2 | Autenticar API Linktree | SEC-01 | Crítica | Sanctum/token + CORS allowlist | Alta (cliente) |
| 3 | Eliminar senha `123`; reset seguro | SEC-02 | Crítica | Senha aleatória + force change | Média |
| 4 | Trocar deletes GET → POST/DELETE | SEC-04 | Alta | Rotas + UI | Média |
| 5 | Ownership Sites/Congresso | SEC-05/06 | Alta | Checks 403 | Baixa |
| 6 | Digesto `exibir` por ID + validar uploads | SEC-08 | Alta | Path seguro + mimes | Baixa–média |
| 7 | Feature tests mínimos (ACL, delete, formulário, Linktree) | Q-04 | Alta | Suite Feature | — |

**Critério de saída:** críticos de segurança fechados; rede mínima de testes nos fluxos tocados.

---

### Fase 1 — Performance dos hot paths (2–3 sprints)

| Ordem | Item | Origem | Prioridade | Entrega | Regressão |
|------:|------|--------|------------|---------|-----------|
| 1 | Reescrever agregação de qualidade/ranking | PERF-01 | Crítica | Queries agregadas + testes de paridade | Médio |
| 2 | Prefetch `formulariosEntregues` | PERF-02 | Crítica | DatatableAjax otimizado | Médio |
| 3 | Agregar/cachear mapa | PERF-03 | Crítica | 1 query agregada + cache | Médio |
| 4 | Índices compostos + `parametros.nome` | PERF-07 | Alta | Migration ADD INDEX | Baixo |
| 5 | `with()`/`withCount` nos DataTables listados | PERF-05/06 | Alta | Diff por DataTable | Baixo |
| 6 | Memoizar `getAnoReferencia` | PERF-10 | Média | Cache request | Baixo |

**Critério de saída:** dashboard/Ajax/ranking sem explosão de queries (validar com Debugbar/Telescope em staging).

---

### Fase 2 — Assincronicidade e carga (2–4 sprints)

| Ordem | Item | Origem | Prioridade | Entrega | Regressão |
|------:|------|--------|------------|---------|-----------|
| 1 | Introduzir queue real (`redis`/`database`) em produção | PERF-04 | Crítica | Workers + monitoramento | Médio |
| 2 | Jobs pós-formulário: estatística, gamificação, análise | PERF-04/09 | Crítica | Hooks despacham Jobs | Médio (eventual) |
| 3 | `atualizarTodosOsDados` / rebuilds chunked | PERF-08 | Alta | Command/Job | Médio |
| 4 | Batch jurisdição + `upsert` ranking | PERF-09 | Alta | Resolver + tests Gamificação | Alto |
| 5 | Exports/PDF assíncronos (maiores) | PERF-12 | Média | Job + download | Médio |

**Critério de saída:** submit de formulário responde rápido; jobs idempotentes; falhas observáveis.

---

### Fase 3 — Hardening e qualidade estrutural (contínuo)

| Ordem | Item | Origem | Prioridade | Entrega | Regressão |
|------:|------|--------|------------|---------|-----------|
| 1 | XSS avisos/sites (sanitizar) | SEC-07 | Alta | Purifier/CSP | Médio |
| 2 | `$fillable` / filtrar `role_id` | SEC-09 | Alta | User + models tocados | Médio |
| 3 | FormRequests nos endpoints tocados | SEC-10 / Q | Média | Por módulo | Baixo |
| 4 | Padronizar exceções + logging | Q-03 | Alta | Handler + remover catch vazio | Baixo |
| 5 | Fatiar `routes/web.php` | Q-05 | Média | `routes/dashboard/*` | Médio |
| 6 | Extrair `CongressoNacionalService` + exporters | CA-01 / Q-01 | Crítica (dívida) | Controller < 200–300L | Alto |
| 7 | Partir `EstatisticaService` | CA-02 | Alta | 3–4 services focados | Alto |
| 8 | Deduplicar Instâncias/Diretorias/Formularios | CA-08 / Q-02 | Média | Traits/parametrização | Médio |
| 9 | Remover código morto | CA-10 / Q-06 | Média | PR limpeza | Baixo |
| 10 | Config produção (DEBUG, cookie secure, CORS, Log Viewer) | SEC-12 | Média | Docs + example | Baixo |
| 11 | Migrar services tocados de static → DI | CA-03 | Média | Boy scout | Médio |
| 12 | Tipagem ao editar | Q-07 | Baixa | PRs | Baixo |

---

### Fase 4 — Oportunidades (backlog, não bloquear)

- Avaliar remoção/uso real do Sanctum vs tokens custom.
- Limpar declaração duplicada Yajra no `composer.json`.
- Generated columns / denormalização de métricas JSON (PERF-11).
- Alias `secretaria_produtos` para corrigir typo `secreatria_produtos`.
- Expandir Policies só onde IDOR é recorrente (não Policy-everywhere).
- View composers no lugar de `DashboardHelper` orquestrador.
- Throttle dedicado no login; validação Telegram no webhook iClaudia.

---

## 4. Mapa de risco × valor

```
Alto valor / baixo risco     → Fase 0 itens 1,5,6; índices; with() DataTables; catch vazio
Alto valor / alto risco      → Partir EstatisticaService; Congresso; Jobs pós-formulário; jurisdição
Baixo valor / baixo risco    → Tipagem boy scout; naming Datatable; remover órfãos
Baixo valor / alto risco     → Reescrita Clean Architecture; Repository universal; rename role slug
```

**Evitar explicitamente**

- Reescrever o sistema.
- Introduzir Clean Architecture em todos os módulos.
- Criar camada Repository por convenção.
- Refatorar Gamificação/EI “para ficar igual ao legado” (são a referência).
- Amend/force de mudanças sem testes nos hotspots numéricos.

---

## 5. Compatibilidade com produção

| Tema | Nota |
|------|------|
| Laravel 10 / PHP ^8.1 | Manter; PHP 8.4 local ok se CI/prod alinhados |
| Filas | Hoje `sync` — ativar worker exige processo/supervisor em produção |
| Migrations de índice | Rodar em janela; `ALGORITHM=INPLACE` quando possível; backup |
| Sanctum no Linktree | Coordenar release app cliente |
| Cache | Invalidar `ano_referencia`/mapa ao mudar parâmetros |
| Debugbar | Já em `require-dev` — usar em staging, nunca em prod |

---

## 6. Definição de pronto por PR

- [ ] Problema rastreável a ID (SEC-/PERF-/CA-/Q-)
- [ ] Sem mudança fora do escopo
- [ ] Teste automatizado ou checklist manual documentado
- [ ] Sem novas queries N+1 introduzidas (Debugbar em staging)
- [ ] Sem novos `Route::get(.../delete)`
- [ ] Sem catch vazio novo
- [ ] Rollback claro (migration down / feature flag se Jobs)

---

## 7. Ordem recomendada consolidada (executiva)

1. **Segurança crítica** — coletor restaurar, Linktree, senha `123`  
2. **CSRF/IDOR/uploads** — deletes GET, Sites/Congresso, Digesto  
3. **Testes Feature mínimos** — rede de segurança  
4. **N+1 hot paths** — qualidade, DatatableAjax, mapa  
5. **Índices + eager DataTables**  
6. **Filas** — pós-formulário e rebuilds  
7. **Fatiar Congresso + EstatisticaService**  
8. **XSS, mass assignment, FormRequests, exceções, rotas**  
9. **Higiene contínua** — morto, tipagem, DI boy scout  

Detalhes e evidências: ver os três documentos de auditoria.
