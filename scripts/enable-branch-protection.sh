#!/usr/bin/env bash
set -euo pipefail

# Ativa proteção da master: PR obrigatório + check "tests".
# Pré-requisitos: gh auth login && git push da branch com o workflow.

REPO="${1:-yurirene/cnm-estatistica}"
BRANCH="${2:-master}"

echo "Aplicando branch protection em ${REPO}:${BRANCH}"

gh api \
  --method PUT \
  -H "Accept: application/vnd.github+json" \
  "/repos/${REPO}/branches/${BRANCH}/protection" \
  -f required_status_checks='{"strict":true,"contexts":["tests"]}' \
  -F enforce_admins=true \
  -f required_pull_request_reviews='{"required_approving_review_count":0,"dismiss_stale_reviews":true}' \
  -F restrictions= \
  -F allow_force_pushes=false \
  -F allow_deletions=false \
  -F block_creations=false \
  -F required_conversation_resolution=true

echo "Branch protection aplicada. Confira em Settings → Branches."
echo "Hotfix: use branches hotfix/* via PR (ver docs/branching.md)."
