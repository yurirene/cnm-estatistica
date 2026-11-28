# Testes e banco local

## CI (GitHub Actions)

O pipeline em `.github/workflows/tests.yml` sobe MySQL 8, roda `migrate` e `phpunit`.  
**Não** importa dump de produção. Use factories / `TestingSeeder` / `RefreshDatabase`.

## Rodar testes localmente

1. Crie o banco `testing` no MySQL local.
2. Copie `.env.testing` para `.env` (ou exporte as variáveis `DB_*` do `.env.testing`).
3. Gere a key: `php artisan key:generate`.
4. `php artisan migrate --env=testing`
5. `vendor/bin/phpunit`

## Dump enxuto (somente máquina local)

Para explorar dados próximos de produção **sem** alterar a base de produção:

```bash
./scripts/dump-lean.sh
```

O arquivo sai em `storage/app/dumps/` (gitignored). Restaure apenas em MySQL **local**:

```bash
mysql -u root -p cnm_local < storage/app/dumps/lean-YYYYMMDD.sql
```

Nunca use esse dump no CI e nunca faça commit do arquivo.
