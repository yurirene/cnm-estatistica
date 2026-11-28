#!/usr/bin/env bash
set -euo pipefail

# Ativa proteção da master: PR obrigatório + check "tests".
# Pré-requisitos: gh auth login && workflow já existente na default branch (ou após merge).

REPO="${1:-yurirene/cnm-estatistica}"
BRANCH="${2:-master}"

echo "Aplicando branch protection em ${REPO}:${BRANCH}"

gh api \
  --method PUT \
  -H "Accept: application/vnd.github+json" \
  "/repos/${REPO}/branches/${BRANCH}/protection" \
  --input - <<EOF
{
  "required_status_checks": {
    "strict": true,
    "contexts": ["tests"]
  },
  "enforce_admins": true,
  "required_pull_request_reviews": {
    "dismiss_stale_reviews": true,
    "required_approving_review_count": 0
  },
  "restrictions": null,
  "allow_force_pushes": false,
  "allow_deletions": false,
  "block_creations": false,
  "required_conversation_resolution": true
}
EOF

echo "Branch protection aplicada. Confira em Settings → Branches."
echo "Hotfix: use branches hotfix/* via PR (ver docs/branching.md)."
