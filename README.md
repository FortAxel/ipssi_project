# Storybook Kids App

Children’s story reading app — CDA capstone project.

## Quick start (clone → Docker → ready database)

```bash
docker compose up -d --build
```

On first start, the **`app-init`** one-shot service runs:

1. `composer install` (if needed)
2. JWT key pair generation (if missing)
3. `doctrine:migrations:migrate`
4. `doctrine:fixtures:load` — **5 French stories** + demo accounts (only when the `story` table is empty)

| Service | URL |
|---------|-----|
| API | http://127.0.0.1:8088 |
| Frontend (dev) | http://127.0.0.1:5173 — `cd frontend && npm install && npm run dev` |
| phpMyAdmin | http://127.0.0.1:8081 |
| MySQL | `127.0.0.1:3307` — user `storybook` / password `storybook` |

### Demo accounts

| Role | Email | Password |
|------|-------|----------|
| Parent | `parent@demo.local` | `parent123` |
| Admin | `admin@demo.local` | `admin123` |

### Reset the database

```bash
docker compose down -v
docker compose up -d --build
```

## Versioned content (5 stories)

Files **to commit to Git**:

- `backend/data/stories/*.json` — 5 French stories
- `backend/public/images/*.jpg` — matching illustrations

Root folders `stories/` and `images/` (raw export) are **gitignored**. To regenerate the bundle:

```bash
# 1. Place the full export in stories/ + images/ at the repo root
python3 scripts/consolidate-content.py
# 2. Commit backend/data/ and backend/public/images/
```

## Project layout

```
backend/                 # Symfony API
frontend/                # React SPA
backend/data/stories/    # seed JSON (5 stories)
docker/init/             # DB bootstrap on docker compose up
docs/                    # Jalon deliverables & code standards
```

## Quality checks (jalon 5)

| Layer | Command | When Docker is up |
|-------|---------|-------------------|
| Backend lint | `cd backend && composer lint:php` | `docker compose exec php composer lint:php` |
| Backend tests | `cd backend && composer test` | `docker compose exec php composer test` |
| Frontend lint | `cd frontend && npm run lint` | — |
| Frontend tests | `cd frontend && npm test` | — |
| Frontend build | `cd frontend && npm run build` | — |

**CI** : GitHub Actions workflow [`.github/workflows/ci.yml`](.github/workflows/ci.yml) runs on push/PR to `main`, `develop`, and `dev/beta_usable`.

Generate JWT keys locally before PHPUnit if missing:

```bash
cd backend && php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction
```

## Documentation

- [PLAN-DEV-BETA.md](PLAN-DEV-BETA.md) — beta roadmap (MVP, TTS, favorites)
- [docs/jalon-5-beta/README.md](docs/jalon-5-beta/README.md) — **jalon 5** : périmètre livré, écarts, démo
- [docs/code-standard.md](docs/code-standard.md) — naming and conventions
- [docs/jalon-2-conception-ui-ux/conception-ui-ux.md](docs/jalon-2-conception-ui-ux/conception-ui-ux.md) — UI/UX spec (colors, typography, zoning)

## Text-to-speech (gratuit, API externe)

Synthèse via **Microsoft Edge TTS** (sans clé API, limites imposées par Microsoft). Configurable dans `backend/.env` ou variables Docker :

| Variable | Exemple | Rôle |
|----------|---------|------|
| `TTS_ENABLED` | `1` | Active `POST /api/tts/synthesize` |
| `TTS_VOICE` | `fr-FR-EloiseNeural` | Voix française (ton chaleureux) |
| `TTS_RATE` | `-5%` | Débit (plus lent pour enfants) |

Après modification : `docker compose up -d --build` (l’image PHP installe le CLI `edge-tts`).

## UI stack

The frontend follows the Jalon 2 design system: Fredoka + Open Sans, palette `#4A90E2` / `#F5D76E` / `#7ED957` / `#FF7BA5`, 32px radius, header + title bar + main content. User-facing labels are in French (`frontend/src/i18n/fr.ts`); code identifiers and API routes are in English per `docs/code-standard.md`.
