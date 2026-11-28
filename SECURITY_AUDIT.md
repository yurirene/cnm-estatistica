# SECURITY_AUDIT.md — Auditoria de segurança

**Projeto:** cnm-estatistica  
**Stack:** Laravel 10.48.29 · PHP ^8.1  
**Escopo:** análise estática com evidências no código — nenhum arquivo alterado.  
**Data:** 2026-11-28  

**Legenda:** **Comprovado** = risco demonstrável no código · **Oportunidade** = padrão frágil sem exploit fechado nesta auditoria.

---

## 1. Resumo executivo

| Severidade | Temas principais |
|------------|------------------|
| **Crítica** | API Linktree sem autenticação; senha padrão `123`; coletor `restaurar` público |
| **Alta** | Deletes via GET (CSRF); IDOR Sites/Congresso; XSS stored; uploads sem mime; path traversal Digesto |
| **Média** | Mass assignment amplo; CORS `*`; validação inconsistente; vazamento de exceções |
| **Baixa** | Hardcodes, TrustProxies, throttle de login |

**SQL Injection:** nenhum caso comprovado de concatenação de input do usuário em `*Raw` / `DB::select`.  
**CSRF middleware:** ativo e sem exclusões em `VerifyCsrfToken` — o bypass ocorre via **mutações em GET**.

---

## 2. Achados

### SEC-01 — API Linktree CRUD sem autenticação

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `routes/api.php` L24–27; `app/Http/Controllers/Api/LinktreeController.php` |
| **Descrição** | Endpoints de listar, criar e remover links estão públicos. `login` valida credenciais mas **não emite token** nem protege as demais rotas. |
| **Evidência** | Rotas sem middleware; `links()` retorna `LinkTree::all()`; `addLink` cria com validação mínima; `removerLink` deleta por model binding. CORS `allowed_origins => ['*']` (`config/cors.php` L22) amplia exposição. |
| **Impacto** | Qualquer cliente na internet pode ler/alterar/apagar links do Linktree. |
| **Prioridade** | **Crítica** |
| **Sugestão** | Proteger com `auth:sanctum` (ou token de serviço); emitir token no login; restringir CORS às origens do app. |
| **Risco de regressão** | Alto no cliente Linktree (precisa passar a autenticar). |
| **Tipo** | Comprovado |

---

### SEC-02 — Senha padrão `123` em criação e reset

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/Services/UserService.php` L59 e L176; rotas `usuarios.reset-senha` / `usuarios.resetar-senha` em `routes/web.php` (~L139–140); seeders também usam `123` |
| **Descrição** | Usuários novos e reset recebem senha previsível. Há rota GET de reset (sem CSRF). |
| **Evidência** | `Hash::make('123')` no store e no reset; seeders `UserSeeder` / `UsuarioLocalSeeder`. |
| **Impacto** | Contas facilmente comprometidas após criação/reset; ataque em massa se e-mails forem conhecidos. |
| **Prioridade** | **Crítica** |
| **Sugestão** | Senha aleatória forte + envio por canal seguro; forçar troca no primeiro login; remover reset via GET. |
| **Risco de regressão** | Médio (fluxo de onboarding). |
| **Tipo** | Comprovado |

---

### SEC-03 — Coletor de dados: restaurar público destrutivo

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `routes/web.php` L96–98 (fora do grupo `auth`); `app/Services/ColetorDadosService.php` `restaurar` L536–549 |
| **Descrição** | `GET /coletor-dados/restaurar/{local}` apaga todos os formulários do local no ano de referência **sem autenticação**. |
| **Evidência** | Rota pública; service faz `ColetorDados::where('local_id', $id)->where('ano', ...)->get()` e `delete()` em loop. |
| **Impacto** | Destruição de dados por enumeração de IDs; CSRF/trivially explorável. |
| **Prioridade** | **Crítica** |
| **Sugestão** | Remover rota pública; exigir auth + ownership + POST/DELETE + confirmação; token de uso único se precisar de fluxo externo. |
| **Risco de regressão** | Médio (fluxo do coletor). |
| **Tipo** | Comprovado |

---

### SEC-04 — Mutações destrutivas via GET (bypass de CSRF)

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `routes/web.php` — dezenas de `Route::get(.../delete)` (ex.: L153, 168, 181, 195, 440, 503, 522, 650, 677, 744, 809, 822, 877, 896) |
| **Descrição** | Ações de exclusão/remoção usam GET. `VerifyCsrfToken` não se aplica a GET → CSRF clássico via `<img src>` / link. |
| **Evidência** | Grep `Route::get(.*delete` em `routes/web.php`; middleware CSRF com `$except = []` (correto, mas irrelevante para GET). |
| **Impacto** | Usuário autenticado pode ser induzido a apagar sinodais, produtos, delegados, etc. |
| **Prioridade** | **Alta** |
| **Sugestão** | Migrar para `DELETE`/`POST` + `@csrf`/`@method`; atualizar links/forms na UI. |
| **Risco de regressão** | Médio (UI + bookmarks). |
| **Tipo** | Comprovado |

---

### SEC-05 — IDOR em Sites / Eventos (sinodal_id / evento_id)

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/Services/Apps/SiteService.php` (ex. `atualizarConfig`, `adicionarNaGaleria` L96+, `removerDaGaleria`, `atualizarFotoDiretoria`); controllers de Apps/Site/Evento |
| **Descrição** | Operações recebem `sinodal_id` (ou equivalente) da URL/request **sem** comparar com a sinodal do usuário autenticado. |
| **Evidência** | `Sinodal::find($sinodal_id)` e mutações sem `abort_unless($sinodal_id === auth()->user()->sinodal_id, 403)`. |
| **Impacto** | Sinodal A com módulo Sites pode alterar galeria/config/site da sinodal B. |
| **Prioridade** | **Alta** |
| **Sugestão** | Ownership check em todas as actions; Preferir resolver sinodal a partir de `auth()->user()`. |
| **Risco de regressão** | Baixo. |
| **Tipo** | Comprovado |

---

### SEC-06 — IDOR em Congresso (delete/edit delegado)

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/Http/Controllers/Congresso/CongressoNacionalController.php` `deleteSinodal` L556–560; `editSinodal` (~L119–125) |
| **Descrição** | `findOrFail` + `delete()` / edição **sem** checar `sinodal_id` do usuário. Contraste: `deleteDocumentoSinodal` possui check de ownership. |
| **Evidência** | Código L556–560 sem ownership; documento com check em L634+. |
| **Impacto** | Exclusão/edição de delegados de outras sinodais por usuário autenticado com acesso ao módulo. |
| **Prioridade** | **Alta** |
| **Sugestão** | Policy/ownership em todas as actions; alinhar ao padrão já usado em documentos. |
| **Risco de regressão** | Baixo. |
| **Tipo** | Comprovado |

---

### SEC-07 — XSS stored (avisos e sites)

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `resources/views/dashboard/index/avisos.blade.php` L84 `{!! $aviso['texto'] !!}`; `AvisoService` persiste `texto` sem sanitização; sites `modelo_1.blade.php` / `modelo_evento.blade.php` com `{!! $sobreNos !!}` / `{!! $evento->descricao !!}` |
| **Descrição** | HTML do editor/usuário renderizado sem escape. |
| **Evidência** | `{!! !!}` + atribuição direta do request. Digesto usa `{!! !!}` mas com `e()` prévio em `destacar` — **mitigado** (oportunidade residual baixa). |
| **Impacto** | Script malicioso para quem visualiza avisos (usuários logados) ou visitantes do site público. |
| **Prioridade** | **Alta** |
| **Sugestão** | HTML Purifier / allowlist de tags; CSP; ou Markdown sanitizado. |
| **Risco de regressão** | Médio (conteúdo HTML rico). |
| **Tipo** | Comprovado |

---

### SEC-08 — Upload inseguro + path traversal no Digesto

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | Uploads: `DigestoService::store`, `ComprovanteAciService`, `SiteService`/`EventoService` (extensão do cliente); Exibição: `DigestoService::exibir` L310–320; rota pública `GET /digesto/exibir/{path}` (`web.php` ~L77–78) |
| **Descrição** | Vários uploads sem `mimes`/`max`; `exibir` concatena path do usuário em `PATH_DIR` sem `realpath`/whitelist. |
| **Evidência** | `response()->file(self::PATH_DIR . $path)` / `download(...)` sem sanitização; contraste: Tesouraria/Auxílio PMF/Congresso validam `mimes` em vários pontos. |
| **Impacto** | Upload de tipos perigosos; possível leitura de arquivos fora do diretório pretendido (LFI), dependendo do routing/encoding. |
| **Prioridade** | **Alta** |
| **Sugestão** | Validar mime/tamanho; servir Digesto por ID do model; `realpath` + prefixo obrigatório. |
| **Risco de regressão** | Baixo–médio. |
| **Tipo** | Comprovado |

---

### SEC-09 — Mass assignment e elevação de privilégio via `role_id`

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/Models/User.php` `$fillable` inclui `role_id`; `UserService::store/update`; ~59 models com `$guarded` amplo; usos de `$request->all()` em controllers (CE, Tesouraria, FluxoCaixa, Coletor, diretorias) |
| **Descrição** | Campos sensíveis podem ser atribuídos em massa se o request não for filtrado. |
| **Evidência** | `User` fillable com `role_id`/`password`; padrão `$guarded = ['id', timestamps]`; `EventoInscrito` quase abre `id`. |
| **Impacto** | Potencial elevação de privilégio por quem gerencia usuários; updates amplos em outros módulos. |
| **Prioridade** | **Alta** (User/role) / **Média** (demais) |
| **Sugestão** | Never trust `role_id` do request sem autorização admin; `$fillable` explícito; FormRequests. |
| **Risco de regressão** | Médio. |
| **Tipo** | Comprovado (padrão) — exploração depende do filtramento no service |

---

### SEC-10 — Validação inconsistente

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | Digesto, User, Comprovante ACI, Helpdesk, Aviso, `DashboardController::trocarSenha`; FormRequests existem em Auxílio PMF, Resoluções, Tarefas, etc. |
| **Descrição** | Cobertura parcial de FormRequest; vários endpoints aceitam Request cru. |
| **Evidência** | 17 Form Requests vs 61 controllers; ~8 controllers importam FormRequest; `trocarSenha` sem política de complexidade. |
| **Impacto** | Dados inválidos, uploads perigosos, senhas fracas. |
| **Prioridade** | **Média–Alta** |
| **Sugestão** | FormRequest ao tocar endpoints; regras mínimas de senha. |
| **Risco de regressão** | Baixo–médio. |
| **Tipo** | Comprovado |

---

### SEC-11 — Autorização baseada em slug + Policy escassa

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `app/Http/Middleware/AuthSistema.php`; `app/Providers/AuthServiceProvider.php` (Gates); 1 Policy (`PlacarPolicy`) |
| **Descrição** | ACL por nome de rota (`rota-permitida`); metadata `modulo` nas rotas não aplica middleware de role por si. Ownership inconsistente entre módulos. |
| **Evidência** | Contraste Auxílio PMF (checks de `sinodal_id`) vs Sites/Congresso (falhas). |
| **Impacto** | Fácil esquecer check em action nova; IDOR residual. |
| **Prioridade** | **Média** |
| **Sugestão** | Policies/ownership para recursos com ID; manter Gates para menu/módulos. |
| **Risco de regressão** | Médio. |
| **Tipo** | Comprovado (modelo frágil) |

---

### SEC-12 — Configurações e exposição

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `.env.example` (`APP_DEBUG=true`, `LOG_LEVEL=debug`, path de service account Google); `config/cors.php`; `config/session.php` (`SESSION_SECURE_COOKIE` ausente no example); `AppServiceProvider` Log Viewer e-mail hardcoded; `QUEUE_CONNECTION=sync` |
| **Descrição** | Defaults de exemplo inseguros se copiados para produção; CORS permissivo; cookie secure indefinido. |
| **Evidência** | `.env` e JSONs Google estão no `.gitignore` (bom); Log Viewer restringe por e-mail fixo L29–34. Mensagens de exceção vazadas em vários controllers. |
| **Impacto** | Debug em prod, XSS amplificado por CORS, sessão em HTTP, acesso a logs limitado mas frágil. |
| **Prioridade** | **Média** |
| **Sugestão** | `.env.example` com `APP_DEBUG=false`; `SESSION_SECURE_COOKIE=true` em HTTPS; CORS allowlist; Log Viewer por role; Handler global para não expor `$th->getMessage()`. |
| **Risco de regressão** | Baixo. |
| **Tipo** | Comprovado (config) / Oportunidade (TrustProxies, FORCE_HTTPS, throttle login) |

---

### SEC-13 — Autenticação API iClaudia / tokens custom

| Campo | Conteúdo |
|-------|----------|
| **Arquivo / localização** | `routes/api.php` L29–34 (`Route::any('/iClaudia')`); middlewares `api-token-sicom` / `api-token-executiva` |
| **Descrição** | Webhook iClaudia lê `php://input` sem evidência de validação de secret do Telegram nesta auditoria; tokens Sicom/Executiva via `env()` + comparação `!==` (timing). |
| **Evidência** | Rota aberta chama `IClaudiaController::process`; middlewares usam `env()` (anti-pattern com config cache). |
| **Impacto** | Abuso do bot se token não validado; tokens frágeis se vazarem. |
| **Prioridade** | **Média** (verificar validação Telegram no controller — tratar como oportunidade até confirmar) |
| **Sugestão** | Validar secret/Telegram; `config()` em vez de `env()`; `hash_equals`. |
| **Risco de regressão** | Médio. |
| **Tipo** | Oportunidade / parcial |

---

## 3. Controles positivos observados

- CSRF global ativo, sem exclusões.
- Dashboard atrás de `auth` + `auth-sistema`.
- Log Viewer exige usuário autenticado + callback.
- Vários uploads (CE, Congresso, Auxílio PMF, Resoluções, Missionário) com `mimes`.
- Digesto search SQL com bindings + `e()` no highlight.
- `.env` e service accounts Google no `.gitignore`.
- Auxílio PMF com ownership em várias actions.

---

## 4. Ordem recomendada de remediação (segurança)

1. SEC-03 coletor `restaurar` (dados)  
2. SEC-01 Linktree API  
3. SEC-02 senha `123`  
4. SEC-04 deletes GET → POST/DELETE  
5. SEC-05 / SEC-06 IDOR  
6. SEC-08 Digesto path + uploads  
7. SEC-07 XSS  
8. SEC-09 / SEC-10 mass assignment + validação  
9. SEC-12 config produção  
