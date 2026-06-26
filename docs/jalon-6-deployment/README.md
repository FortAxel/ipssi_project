# Jalon 6 — Guide de déploiement

Document pour déployer **Storybook Kids** depuis Git jusqu’à une application accessible, sans connaissance préalable du projet.

**Dépôt** : https://github.com/FortAxel/ipssi_project

---

## 1. Prérequis

| Outil | Version minimale |
|-------|------------------|
| Git | 2.x |
| Docker | 24+ |
| Docker Compose | v2 (plugin `docker compose`) |

Node.js n’est **pas** requis pour faire tourner l’application (frontend buildé dans Docker). Il sert uniquement au développement frontend avec rechargement à chaud.

---

## 2. Installation (procédure standard)

```bash
git clone https://github.com/FortAxel/ipssi_project.git
cd ipssi_project

cp .env.example .env
# Éditer .env : APP_SECRET, JWT_PASSPHRASE, mots de passe MySQL en production

docker compose up -d --build
```

Premier démarrage : le conteneur `app-init` installe les dépendances PHP, migre la base et charge les fixtures (5 histoires + comptes démo) si la base est vide.

### Vérification

```bash
curl -s http://127.0.0.1:8080/api/health
# → {"status":"ok","service":"storybook-kids-api"}
```

| Service | URL par défaut |
|---------|----------------|
| Application (SPA + API) | http://127.0.0.1:8080 |
| phpMyAdmin | http://127.0.0.1:8081 |
| MySQL (client externe) | `127.0.0.1:3307` |

### Comptes démo

| Rôle | E-mail | Mot de passe |
|------|--------|--------------|
| Parent | `parent@demo.local` | `parent123` |
| Admin | `admin@demo.local` | `admin123` |

---

## 3. Environment variables

Single `.env` file at the project root (mounted into the PHP container).

**Authoritative list with descriptions and defaults:** [`.env.example`](../../.env.example)

Minimum steps:

```bash
cp .env.example .env
# Set APP_SECRET, JWT_PASSPHRASE, and MySQL passwords for production
```

Key points:

- `DATABASE_URL` password must match `MYSQL_PASSWORD`
- `DEFAULT_URI` must match `APP_PORT` (e.g. `http://localhost:8080`)
- Do not change `JWT_PASSPHRASE` after first boot without regenerating keys (see §5)

---

## 4. Environnements

| | Docker `APP_ENV=prod` | Docker `APP_ENV=dev` | Tests CI / PHPUnit |
|---|----------------------|---------------------|-------------------|
| Usage | Livraison, démo jury | Debug backend Symfony | Pipeline GitHub Actions |
| Frontend | Buildé dans nginx (`:8080`) | Buildé dans nginx **ou** `npm run dev` (`:5173`) | Vitest sur l'hôte |
| Composer | `--no-dev` | avec dev dependencies | `backend/.env.test` + SQLite |
| phpMyAdmin | Oui — modération / gestion des données | Oui — inspection BDD en développement | — |

### Mode prod (défaut)

```env
APP_ENV=prod
APP_DEBUG=0
```

```bash
docker compose up -d --build
```

→ Application complète sur http://127.0.0.1:8080, sans `npm run dev`.

### Mode dev

```env
APP_ENV=dev
APP_DEBUG=1
```

```bash
docker compose up -d --build
cd frontend && npm install && npm run dev
```

→ API Docker sur `:8080`, interface React avec hot-reload sur http://127.0.0.1:5173.

### phpMyAdmin

Inclus dans la stack Docker (port `8081`). L'application n'a pas d'écran de modération utilisateurs : phpMyAdmin permet de consulter ou corriger les données en base (comptes, contenus) en développement comme en production.

---

## 5. Opérations courantes

### Rebuild après modification du frontend (mode prod)

```bash
docker compose up -d --build nginx
```

### Rebuild complet

```bash
docker compose up -d --build
```

### Réinitialiser la base de données

```bash
docker compose down -v
docker compose up -d --build
```

### Arrêter la stack

```bash
docker compose down
```

### Logs

```bash
docker compose logs -f
docker compose logs -f php
```

### Problème de connexion (JWT)

Si login/inscription renvoie une erreur 500 après changement de `JWT_PASSPHRASE` :

```bash
docker compose exec php php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction --env=prod
```

---

## 6. Déploiement sur un serveur

1. Cloner le dépôt sur le serveur.
2. Copier et personnaliser `.env` (secrets forts, `DEFAULT_URI` = URL publique).
3. Ouvrir les ports `APP_PORT` et éventuellement `PHPMYADMIN_PORT` (ou les restreindre au réseau admin).
4. Lancer `docker compose up -d --build`.
5. Vérifier `GET /api/health`.
6. (Recommandé) Placer un reverse proxy HTTPS (Nginx, Caddy, Traefik) devant le port `APP_PORT`.

Aucune étape CD n'est imposée : le déploiement est **manuel** et reproductible via les commandes ci-dessus.

---

## 7. Architecture Docker

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   nginx     │────▶│  PHP-FPM    │────▶│   MySQL     │
│  SPA + /api │     │  Symfony    │     │             │
└─────────────┘     └─────────────┘     └─────────────┘
       │                                        ▲
       │              ┌─────────────┐           │
       └──────────────│ phpMyAdmin  │───────────┘
                      └─────────────┘
```

- **nginx** : fichiers statiques React (build) + proxy `/api` vers Symfony + `/images` depuis le backend.
- **app-init** : one-shot au démarrage (composer, migrations, fixtures, JWT).
- **php** : API Symfony 7.2, PHP 8.4.
- **mysql** : persistance via volume `mysql_data`.

---

**Références** : README racine — `docs/code-standard.md` — CDC technique ch. XI.
