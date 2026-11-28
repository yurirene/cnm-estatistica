#!/usr/bin/env bash
set -euo pipefail

# Dump enxuto para validação LOCAL. Não altera produção.
# Uso: ./scripts/dump-lean.sh
# Requer variáveis DB_* (ou defaults abaixo) apontando para uma conexão de LEITURA.

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUT_DIR="${ROOT_DIR}/storage/app/dumps"
mkdir -p "${OUT_DIR}"

STAMP="$(date +%Y%m%d-%H%M%S)"
OUT_FILE="${OUT_DIR}/lean-${STAMP}.sql"

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"

if [[ -z "${DB_DATABASE}" ]]; then
  echo "Defina DB_DATABASE (e demais DB_*) para a origem do dump." >&2
  exit 1
fi

echo "Gerando dump enxuto de ${DB_DATABASE}@${DB_HOST}:${DB_PORT} -> ${OUT_FILE}"

MYSQL_PWD="${DB_PASSWORD}" mysqldump \
  -h "${DB_HOST}" \
  -P "${DB_PORT}" \
  -u "${DB_USERNAME}" \
  --single-transaction \
  --routines=false \
  --triggers=false \
  --no-tablespaces \
  "${DB_DATABASE}" \
  roles permissions permission_role parametros regioes estados \
  > "${OUT_FILE}"

# Subset de instâncias (ajuste LIMIT conforme necessário)
MYSQL_PWD="${DB_PASSWORD}" mysqldump \
  -h "${DB_HOST}" \
  -P "${DB_PORT}" \
  -u "${DB_USERNAME}" \
  --single-transaction \
  --no-create-info \
  --where="1 LIMIT 20" \
  "${DB_DATABASE}" sinodais \
  >> "${OUT_FILE}" || true

MYSQL_PWD="${DB_PASSWORD}" mysqldump \
  -h "${DB_HOST}" \
  -P "${DB_PORT}" \
  -u "${DB_USERNAME}" \
  --single-transaction \
  --no-create-info \
  --where="1 LIMIT 40" \
  "${DB_DATABASE}" federacoes \
  >> "${OUT_FILE}" || true

MYSQL_PWD="${DB_PASSWORD}" mysqldump \
  -h "${DB_HOST}" \
  -P "${DB_PORT}" \
  -u "${DB_USERNAME}" \
  --single-transaction \
  --no-create-info \
  --where="1 LIMIT 80" \
  "${DB_DATABASE}" locais \
  >> "${OUT_FILE}" || true

# Usuários anonimizados (estrutura apenas; dados sensíveis devem ser editados manualmente)
cat >> "${OUT_FILE}" <<'SQL'

-- ATENÇÃO: revise e anonimize e-mails/senhas antes de usar localmente.
-- Não faça commit deste arquivo.
SQL

echo "Dump gerado: ${OUT_FILE}"
echo "Restaure apenas em MySQL local. Nunca importe em produção."
