# Git workflow

> How this project's history is built. These rules apply to every change so the repository stays
> traceable and reproducible — the **full commit history is part of the jury dossier** and is never
> squashed away.

## Principles

- `main` is always in a working state and is **never committed to directly**.
- Every change happens on a short-lived branch, **one branch per ticket / user story**.
- History is **kept, never rewritten**: no `rebase`, no fast-forward on integration.
- Commits are small and **scoped by change type**, not a single "done" commit.

## Branching

Always start from an up-to-date `main`:

```bash
git checkout main
git pull origin main
git checkout -b <type>/<ticket>-<slug>
```

**Naming:** `<type>/<ticket>-<slug>`

- `<type>` is one of `feat`, `fix`, `chore`, `docs`, `refactor`, `test`, and reflects the **overall
  purpose of the ticket**, not each individual commit inside it.
- `<ticket>` is the planning ID (`AUTH-01`, `PROP-03`, `CMS-03`, …).
- `<slug>` is a short kebab-case description.

Example: `feat/CMS-03-homepage`.

A `feat/` branch contains everything the ticket needs — implementation, its tests and its docs — so a
single feature is **never** split across separate `test/` or `docs/` branches. A standalone `docs/`
or `test/` branch is only for work with **no owning feature ticket** (e.g. backfilling tests for old
code, fixing unrelated documentation).

**No ticket?** Pure chores, tooling, config and standalone docs/refactors are exempt from the ticket
rule: name the branch `<type>/<slug>` (e.g. `chore/migrations-setup`) and prefix commits with
`<type>: …`.

## Committing

Commit messages follow:

```
<ticket>: <short description>
```

Example: `PROJ-101: add login form validation`.

Within one branch, scope commits by change type while keeping the same ticket number:

```
CMS-03: add the homepage with hero slider and search
CMS-03: add tests for the homepage
CMS-03: document the homepage
```

Do **not** add an artificial "final clean commit" at the end of a branch: the merge commit itself is
the completion marker, and the full history is kept intentionally.

For ticket-less work, replace the ticket with the type: `chore: add migrations`.

## Pushing

- First push of a branch (sets upstream tracking): `git push -u origin <branch>`
- Later pushes: `git push`

## Integrating into `main`

Finished work is merged **directly into `main`**, always with a merge commit:

```bash
git checkout main
git pull origin main
git merge --no-ff <branch>
```

`--no-ff` forces a merge commit even when a fast-forward is possible, so the branch stays visible in
the graph. **Never** use `git rebase` or a fast-forward merge for this step.

## Cleaning up

After a successful merge, delete the branch:

```bash
git branch -d <branch>            # local
git push origin --delete <branch> # remote
```

Start the next ticket only from a freshly pulled `main`.

## Worked example — CMS-03 (homepage)

```bash
git checkout main
git pull origin main
git checkout -b feat/CMS-03-homepage

# … work …
git add app/Http/Controllers/HomeController.php routes/web.php resources/views/home.blade.php
git commit -m "CMS-03: add the homepage with hero slider and search"
git add tests/Feature/HomeTest.php
git commit -m "CMS-03: add tests for the homepage"
git add docs
git commit -m "CMS-03: document the homepage"

git push -u origin feat/CMS-03-homepage

git checkout main
git pull origin main
git merge --no-ff feat/CMS-03-homepage -m "CMS-03: merge homepage"
git push origin main
git branch -d feat/CMS-03-homepage
git push origin --delete feat/CMS-03-homepage
```

The resulting history (`git log --graph`) keeps the branch readable:

```
*   CMS-03: merge homepage
|\
| * CMS-03: document the homepage
| * CMS-03: add tests for the homepage
| * CMS-03: add the homepage with hero slider and search
|/
*   … previous main
```

## Quick reference

| Step | Command |
|---|---|
| Start | `git checkout main && git pull origin main && git checkout -b <type>/<ticket>-<slug>` |
| Commit | `git add <files> && git commit -m "<ticket>: <description>"` |
| Push (first) | `git push -u origin <branch>` |
| Push (later) | `git push` |
| Merge | `git checkout main && git pull origin main && git merge --no-ff <branch>` |
| Delete | `git branch -d <branch> && git push origin --delete <branch>` |
