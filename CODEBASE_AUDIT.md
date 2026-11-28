# CODEBASE_AUDIT.md — Diagnóstico arquitetural

**Projeto:** cnm-estatistica  
**Stack confirmada:** Laravel `v10.48.29` · PHP `^8.1` (ambiente local observado: PHP 8.4.1)  
**Escopo:** análise estática somente leitura — nenhum arquivo de aplicação foi alterado.  
**Data:** 2026-11-28  

---

## 1. Resumo executivo

O sistema é um monólito Laravel evoluído por demanda ao longo de ~4 anos. A maior parte dos módulos **já delega regra de negócio a Services**; o problema estrutural não é “falta de Repository/Clean Architecture”, e sim:

1. **Services estáticos gigantes** (god services) no núcleo estatístico.
2. **Um controller outlier** (`CongressoNacionalController`, 1741 linhas).
3. **Dois estilos arquiteturais coexistindo**: legado (static services + DataTables + Helpers) vs. módulos recentes (Gamificação / Estatística Inteligente com DI, DTOs, Enums, testes).
4. **Acoplamento radial** em `EstatisticaService` (~35 consumidores).
5. **Rotas monolíticas** (`routes/web.php`, 956 linhas) com grupos `auth`/`dashboard` repetidos.

**Métricas aproximadas**

| Camada | Quantidade |
|--------|------------|
| Arquivos PHP em `app/` | ~394 |
| Controllers | 61 |
| Services | 129 |
| Models | 68 |
| DataTables | 31 |
| Form Requests | 17 |
| Policies | 1 (`PlacarPolicy`) |
| Repositories | **0** |
| Jobs (`app/Jobs`) | **0** |
| Testes (`*Test.php`) | 29 (Feature: 2 · Unit: 27) |

---

## 2. Organização dos módulos

### 2.1 Estrutura observada

| Área | Path | Estilo |
|------|------|--------|
| Controllers | `app/Http/Controllers/` (+ Auth, Api, Apps, Congresso, Diretorias, Estatistica, Formularios, Instancias, Produtos) | Misto fat/thin |
| Services | `app/Services/` (+ Api, Apps, Estatistica, EstatisticaInteligente, Formularios, Gamificacao, Instancias, Produtos) | Dominante |
| Models | `app/Models/` (+ Apps, AuxilioPmf, ComissaoExecutiva, CongressoNacional, Diretorias, Estatistica, Gamificacao, Pesquisas, Produtos) | Relativamente enxutos |
| DataTables | `app/DataTables/` | Yajra — 31 classes |
| Strategies | `app/Strategies/` (ChatBot, PesquisaGrafico, Produtos) | Factory + Strategy |
| Interfaces | `app/Interfaces/` (3) + contratos em EstatisticaInteligente | Parcial |
| Events / Listeners / Observers / Resolutions | pastas vazias ou quase | Refatoração interrompida |

### 2.2 Rotas

| Arquivo | Linhas | Papel |
|---------|--------|-------|
| `routes/web.php` | 956 | Quase todo o produto |
| `routes/api.php` | 49 | Linktree, iClaudia, Sicom/Executiva |
| `routes/console.php` / `channels.php` | mínimos | — |

**Padrão inconsistente comprovado:** módulos novos (Gamificação, Estatística Inteligente) usam Providers/DTOs/Contratos; o restante é Controller → Service estático + DataTable + Helper.

---

## 3. Achados arquiteturais

### CA-01 — God Controller: Congresso Nacional

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/Http/Controllers/Congresso/CongressoNacionalController.php` (1741 linhas; ~30 métodos públicos) |
| **Descrição** | Controller concentra ORM, validação, PDF, ZIP, Storage, HTTP externo, exportações CSV e fluxos Sinodal/Federação espelhados. |
| **Evidência** | Imports L5–36 misturam Models/Services/PDF/Storage/Http/Zip/DB; pares duplicados `indexSinodal`/`indexFederacao`, `storeDelegadoSinodal`/`storeDelegadoFederacao`; exports ~L786–1738. |
| **Impacto** | Manutenção cara, alto risco de regressão, impossível testar unitariamente, hotspot de bugs de autorização. |
| **Prioridade** | **Crítica** |
| **Sugestão** | Extrair `CongressoNacionalService` (+ exporters); unificar fluxos Sinodal/Federação por estratégia/parametrização; manter controller fino. |
| **Risco de regressão** | Alto — cobrir com Feature tests antes de fatiar. |
| **Tipo** | Problema comprovado |

---

### CA-02 — God Service: EstatisticaService

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/Services/Estatistica/EstatisticaService.php` (921 linhas; métodos 100% estáticos) |
| **Descrição** | Serviço central acoplado a formulários, ranking, qualidade, relatório geral, DataTables, Gamificação, ChatBot, Commands e Dashboard. |
| **Evidência** | Referenciado em ~35 arquivos; `getDadosRelatorioGeral` ≈ L405–797 (~393 LOC); `getDadosQualidadeEstatistica` ≈ L328–366. |
| **Impacto** | Mudança local propaga efeitos colaterais; testabilidade baixa; gargalo de performance (ver PERFORMANCE_AUDIT). |
| **Prioridade** | **Alta** |
| **Sugestão** | Partir por responsabilidade: Qualidade/Ranking, RelatórioGeral, AnoReferencia/Parametros, FormulariosCounts — sem forçar Repository. |
| **Risco de regressão** | Alto — exigir paridade numérica nos rankings/relatórios. |
| **Tipo** | Problema comprovado |

---

### CA-03 — Services majoritariamente estáticos (legado)

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | Ex.: `DigestoService.php` (769L), `ComissaoExecutivaService.php` (710L), `Apps/TesourariaService.php` (823L), `GmailService.php` (615L); contraste: `Gamificacao/*`, `EstatisticaInteligente/*` |
| **Descrição** | Padrão `public static` impede DI/mocks; módulos novos já usam instanciação + DI. |
| **Evidência** | Contagem: dezenas de services root com todos os métodos static; Gamificação/EI com return types 100% e construtores. Só ~8 controllers usam `__construct`/DI. |
| **Impacto** | Testes frágeis, acoplamento oculto, onboarding difícil. |
| **Prioridade** | **Alta** (estrutural) |
| **Sugestão** | Boy scout: ao tocar um service, migrar para instância + bind no container; usar Gamificação como referência interna — **não** converter tudo de uma vez. |
| **Risco de regressão** | Médio se feito módulo a módulo. |
| **Tipo** | Problema comprovado / dívida estrutural |

---

### CA-04 — Ausência de Repositories (não é bug por si só)

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/` — 0 classes `*Repository*` |
| **Descrição** | Persistência vive em Services, Controllers, Models e DataTables. |
| **Evidência** | Busca sem matches de Repository; queries em Services/DataTables. |
| **Impacto** | Nenhum impacto negativo automático. Problema só onde a **mesma query complexa** se repete em vários callers (ex.: contagens de formulário por ano). |
| **Prioridade** | **Baixa** como padrão global; **Média** pontual onde houver duplicação de query |
| **Sugestão** | Introduzir query objects / métodos de model ou services focados **somente** onde a query se repete — não criar camada Repository universal. |
| **Risco de regressão** | Baixo se incremental. |
| **Tipo** | Oportunidade de melhoria (não problema automático) |

---

### CA-05 — Controllers com lógica de negócio residual

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/Http/Controllers/TransferenciasController.php` (~L44–80); `app/Http/Controllers/Api/LinktreeController.php` (CRUD direto no model); Congresso (CA-01) |
| **Descrição** | Alguns fluxos ainda misturam transação, Eloquent e efeitos colaterais no controller. |
| **Evidência** | `TransferenciasController`: `DB::beginTransaction`, `TransferenciaUnidade::create`, update Federacao/Local, `Artisan::call('cache:clear')`, chamada a gamificação. Linktree: Eloquent direto sem service/auth. |
| **Impacto** | Dificulta reuso e testes; no caso Linktree há impacto de segurança (SECURITY_AUDIT). |
| **Prioridade** | **Média** (arquitetura) / **Crítica** (Linktree — segurança) |
| **Sugestão** | Extrair `TransferenciaUnidadeService`; proteger e encapsular Linktree. |
| **Risco de regressão** | Médio. |
| **Tipo** | Problema comprovado |

---

### CA-06 — Models: mass assignment amplo e scopes acoplados a auth

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | ~59 models com `$guarded = ['id','created_at','updated_at']`; scopes em `Sinodal`, `Federacao`, `Local`, `Estado`, `User`, etc. |
| **Descrição** | Quase todos os campos são mass-assignable; vários scopes usam `auth()`/`Gate`, acoplando domínio à sessão HTTP. |
| **Evidência** | Grep `$guarded` em `app/Models`; ex. `Sinodal::scopeQuery` / `scopeMinhaRegiao` com `auth()`. `User::$fillable` inclui `role_id`. Models em geral enxutos (~3.2k linhas no total). |
| **Impacto** | Risco de mass assignment se `$request->all()` chegar ao model; scopes dificultam uso em Jobs/CLI/testes. |
| **Prioridade** | **Alta** (segurança correlata) / **Média** (design de scopes) |
| **Sugestão** | Preferir `$fillable` explícito nos models tocados; passar contexto de usuário para scopes/query builders em vez de `auth()` interno. |
| **Risco de regressão** | Médio em fills; baixo em scopes se dual-path. |
| **Tipo** | Problema comprovado |

---

### CA-07 — Acoplamento transversal e Helpers orquestradores

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/Helpers/DashboardHelper.php`; `app/Services/Estatistica/EstatisticaService.php`; `app/Scopes/InstanciaScope` ↔ `UserService` |
| **Descrição** | Helper de dashboard resolve múltiplos services por Gate; scopes chamam services; formulários ↔ estatística ↔ gamificação ↔ EI. |
| **Evidência** | `DashboardHelper::make()` instancia 8+ services por perfil; grafo Formulario→Estatistica→Gamificacao/EI. |
| **Impacto** | Ciclos de dependência, boot frágil, views com lógica de domínio. |
| **Prioridade** | **Média** |
| **Sugestão** | View composers / DTOs de dashboard; reduzir Helper a formatação; orquestração no controller/service de aplicação. |
| **Risco de regressão** | Médio (muitas blades). |
| **Tipo** | Problema comprovado |

---

### CA-08 — Duplicação estrutural

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | Controllers Sinodal/Federacao/Local; Diretorias*; FormularioLocal/Federacao/SinodalService; `CongressoNacionalController` pares Sinodal/Federação; `Uuid` vs `GenericTrait`; `routes/web.php` grupos repetidos |
| **Descrição** | CRUD e totalizadores quase literais entre níveis hierárquicos; flash/try-catch repetidos (~35 arquivos com “Algo deu Errado!”). |
| **Evidência** | Blocos try/catch idênticos; APIs espelhadas `store`/`delete`/`verificarColeta`/`showFormulario`; traits UUID duplicados. |
| **Impacto** | Bugs corrigidos em um lugar e esquecidos em outro. |
| **Prioridade** | **Média** |
| **Sugestão** | Parametrizar por tipo de instância; trait de flash; unificar totalizadores; fatiar rotas por domínio. |
| **Risco de regressão** | Médio. |
| **Tipo** | Problema comprovado |

---

### CA-09 — Classes excessivamente complexas (top)

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | Ver tabela abaixo |
| **Descrição** | Hotspots de complexidade ciclomática e tamanho. |
| **Evidência** | `wc -l` e análise de métodos: |

| Linhas | Path |
|--------|------|
| 1741 | `CongressoNacionalController.php` |
| 956 | `routes/web.php` |
| 921 | `EstatisticaService.php` |
| 861 | `PlanejamentoEstrategicoService.php` |
| 823 | `TesourariaService.php` |
| 769 | `DigestoService.php` |
| 710 | `ComissaoExecutivaService.php` |
| 654 | `GamificacaoAcompanhamentoService.php` / `GraficoEstatisticaService.php` |

Métodos notáveis: `exportRelatorioEstatistico` (~L1615–1738), `getDadosRelatorioGeral` (~L405–797), totalizadores de formulário (250–320 LOC). |
| **Impacto** | Bugs, onboarding lento, impossibilidade prática de review. |
| **Prioridade** | **Alta** |
| **Sugestão** | Extrair por caso de uso; limitar novos métodos a ~50–80 LOC; testes de caracterização antes. |
| **Risco de regressão** | Alto nos hotspots. |
| **Tipo** | Problema comprovado |

---

### CA-10 — Padrões inconsistentes e código morto / incompleto

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `FormularioEstatisticoAbstract` (stubs); `DocumentosAutomaticosController` vazio; `AuthHelper` sem usos; DataTables Demandas*; `AtividadeService` referencia model inexistente; Events/Observers vazios; `EnviarMsgService` importado e ausente; Form Requests só em ~8 controllers |
| **Descrição** | Clean Architecture “iniciada” e abandonada; FormRequest parcial; Policy só em Placar. |
| **Evidência** | Pastas vazias; classes órfãs sem referências; ratio FormRequest/Controller ≈ 0,28. |
| **Impacto** | Ruído cognitivo; risco de fatal error se caminhos mortos forem reativados. |
| **Prioridade** | **Média** (morto) / **Baixa** (inconsistência estética) |
| **Sugestão** | Remover órfãos confirmados em PRs pequenos; documentar padrão oficial: Gates + middleware para ACL; FormRequest ao tocar endpoints; DI no estilo Gamificação para módulos novos. |
| **Risco de regressão** | Baixo na remoção de mortos. |
| **Tipo** | Problema comprovado (morto) + oportunidade (padronização) |

---

### CA-11 — Dependências Composer

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `composer.json` |
| **Descrição** | Dependências majoritariamente justificadas; algumas redundantes ou pouco usadas. |
| **Evidência** | `laravel/sanctum` presente mas APIs reais usam tokens custom (`api-token-sicom/executiva`); `yajra/laravel-datatables` + `yajra/laravel-datatables-oracle` redundantes; `laravelcollective/html` ainda usado em blades; `opcodesio/log-viewer` com auth hardcoded em `AppServiceProvider` L29–34. |
| **Impacto** | Superfície de manutenção; Log Viewer acoplado a e-mail fixo. |
| **Prioridade** | **Média** (Log Viewer) / **Baixa** (Yajra/Sanctum) |
| **Sugestão** | Avaliar uso real do Sanctum; limpar meta-package Yajra; configurar Log Viewer via roles/config, não e-mail hardcoded. |
| **Risco de regressão** | Baixo–médio. |
| **Tipo** | Oportunidade + problema pontual (hardcode) |

---

## 4. O que está bem (calibragem)

- A maioria dos módulos **já usa Services** — não é um monólito “tudo no controller”.
- **Gamificação** e **Estatística Inteligente** são referência interna: Enums, DTOs, Calculadores, Guards, DI, testes unitários.
- Strategy/Factory em ChatBot, gráficos e relatórios de produtos.
- Models em geral sem SQL cru e sem god models.
- ACL de dashboard via `auth` + `auth-sistema` + Gates (funcional, ainda que frágil).

## 5. Conclusão arquitetural

Não se recomenda reescrita nem imposição de Clean Architecture/Repositories em todos os módulos. O caminho sustentável é: **corrigir hotspots**, **alinhar legado ao estilo dos módulos novos quando tocados**, e **tratar inconsistência como dívida gerenciável**, priorizando problemas reais de segurança, performance e complexidade concentrada.
