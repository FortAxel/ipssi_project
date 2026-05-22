# Storybook Kids App

Children’s story reading app — CDA capstone project.

## Prerequisites

- **Docker** and **Docker Compose**
- **Node.js** 20+ and **npm** (frontend dev server only)

PHP/Composer on the host are optional if you use Docker for the API.

## Quick start (clone → Docker → ready database)

```bash
git clone https://github.com/FortAxel/ipssi_project.git
cd ipssi_project

cp .env.example backend/.env

docker compose up -d --build

cd frontend && npm install && npm run dev
```

| Service | URL |
|---------|-----|
| API | http://127.0.0.1:8088 |
| Frontend (dev) | http://127.0.0.1:5173 |
| phpMyAdmin | http://127.0.0.1:8081 |
| MySQL | `127.0.0.1:3307` — user `storybook` / password `storybook` |

Quick check: `curl -s http://127.0.0.1:8088/api/health`

On first start, the **`app-init`** one-shot service runs:

1. `composer install` (if needed)
2. JWT key pair generation (if missing)
3. `doctrine:migrations:migrate`
4. `doctrine:fixtures:load` — **5 French stories** + demo accounts (only when the `story` table is empty)

### Environment file

`backend/.env` is **not** in Git. After clone, copy the template:

```bash
cp .env.example backend/.env
```

JWT keys under `backend/config/jwt/*.pem` are generated on first `docker compose up` if missing. See [`.env.example`](.env.example) for variables (DB, JWT, CORS, TTS).

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

## Demo content (5 stories)

Seed data shipped in Git:

- `backend/data/stories/*.json`
- `backend/public/images/*.jpg`

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

**CI**: [`.github/workflows/ci.yml`](.github/workflows/ci.yml) on push/PR to `main` and `develop`.

Generate JWT keys locally before PHPUnit if missing:

```bash
cd backend && php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction
```

## Documentation

- [docs/jalon-5-beta/README.md](docs/jalon-5-beta/README.md) — milestone 5 deliverable
- [docs/code-standard.md](docs/code-standard.md) — naming and conventions
- [docs/jalon-2-conception-ui-ux/conception-ui-ux.md](docs/jalon-2-conception-ui-ux/conception-ui-ux.md) — UI/UX spec

## Text-to-speech

Free **Microsoft Edge TTS** (no API key). Enabled when `TTS_ENABLED=1` in `backend/.env` or Docker env. Rebuild after Dockerfile changes: `docker compose up -d --build`.

## UI stack

Jalon 2 design system: Fredoka + Open Sans, palette `#4A90E2` / `#F5D76E` / `#7ED957` / `#FF7BA5`. User-facing copy in French (`frontend/src/i18n/fr.ts`); code and API routes in English per `docs/code-standard.md`.
