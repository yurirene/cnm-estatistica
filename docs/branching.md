# Fluxo de branches e hotfix

## Regras

- A branch `master` **não** recebe push direto.
- Toda alteração entra via **Pull Request**.
- O check `tests` do GitHub Actions precisa estar **verde** para merge.
- Hotfix urgente **também** passa por PR.

## Fluxo normal

1. `git checkout -b feat/descricao` (ou `fix/…`, `chore/…`) a partir de `master` atualizada.
2. Implementar + testes.
3. Abrir PR para `master`.
4. Aguardar CI verde e review.
5. Merge (squash ou merge commit, conforme convenção do time).

## Hotfix

1. `git checkout master && git pull`
2. `git checkout -b hotfix/descricao-curta`
3. Correção mínima + teste que cubra o regressão.
4. PR para `master` com título `[hotfix] …`.
5. CI verde → merge imediato após review rápida.

Não há push direto na `master` mesmo em emergência. Em caso extremo, apenas um admin do repositório pode usar bypass de proteção — documente o motivo no PR.
