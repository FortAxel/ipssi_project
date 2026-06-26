# Storybook Kids App

Children’s story reading app — CDA capstone project.

## Prerequisites

- **Docker** and **Docker Compose**
- **Node.js** 20+ (optionnel — uniquement pour `npm run dev`)

## Quick start

```bash
git clone https://github.com/FortAxel/ipssi_project.git
cd ipssi_project

cp .env.example .env
docker compose up -d --build
```

| Service | URL |
|---------|-----|
| Application | http://127.0.0.1:8080 |
| phpMyAdmin | http://127.0.0.1:8081 |
| MySQL | `127.0.0.1:3307` |

```bash
curl -s http://127.0.0.1:8080/api/health
```

### Comptes démo

| Rôle | E-mail | Mot de passe |
|------|--------|--------------|
| Parent | `parent@demo.local` | `parent123` |
| Admin | `admin@demo.local` | `admin123` |

## Modes prod / dev

Configuration: see [`.env.example`](.env.example) (copy to `.env` before first start).

| | prod | dev |
|---|------|-----|
| `.env` | `APP_ENV=prod` `APP_DEBUG=0` | `APP_ENV=dev` `APP_DEBUG=1` |
| Frontend | Docker `:8080` (build inclus) | Docker `:8080` **ou** `npm run dev` → `:5173` |
| Relancer | `docker compose up -d --build` | idem |

**Dev frontend** (hot-reload) :

```bash
cd frontend && npm install && npm run dev
```

**Rebuild** après modification du React en prod :

```bash
docker compose up -d --build nginx
```

**Tout supprimer** (conteneurs + base de données) :

```bash
docker compose down -v
docker compose up -d --build
```

## Déploiement

Guide pas à pas (variables, prod/dev, serveur, dépannage) : [docs/jalon-6-deployment/README.md](docs/jalon-6-deployment/README.md)

## Qualité

| Couche | Commande | Docker démarré |
|--------|----------|----------------|
| Lint backend | `cd backend && composer lint:php` | `docker compose exec php composer lint:php` |
| Tests backend | `cd backend && composer test` | `docker compose exec php composer test` |
| Lint frontend | `cd frontend && npm run lint` | — |
| Tests frontend | `cd frontend && npm test` | — |

**CI** : [`.github/workflows/ci.yml`](.github/workflows/ci.yml) sur push/PR vers `main` et `develop`.

## Documentation

- [docs/jalon-6-deployment/README.md](docs/jalon-6-deployment/README.md) — déploiement
- [docs/jalon-5-beta/README.md](docs/jalon-5-beta/README.md) — livrable jalon 5
- [docs/code-standard.md](docs/code-standard.md) — conventions

## Synthèse vocale

TTS **Microsoft Edge** (gratuit). Activé via `TTS_ENABLED=1` dans `.env`.
