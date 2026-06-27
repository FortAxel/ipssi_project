# Storybook Kids

Application web de lecture d'histoires pour enfants — projet fil rouge CDA (Symfony + React).

## Quick start

**Prérequis** : Git 2.x, Docker 24+, Docker Compose v2. Node.js 20+ optionnel (hot-reload frontend uniquement).

```bash
git clone https://github.com/FortAxel/ipssi_project.git
cd ipssi_project

cp .env.example .env
# Éditer .env : APP_SECRET, JWT_PASSPHRASE, mots de passe MySQL en production

docker compose up -d --build
```

Au premier démarrage, le conteneur `app-init` installe les dépendances PHP, migre la base et charge les fixtures (5 histoires + comptes démo) si la base est vide.

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

## Fonctionnalités

| ID | Domaine | Détail |
|----|---------|--------|
| F1 | Auth & profil | Inscription, JWT, modification e-mail / mot de passe, suppression de compte |
| F2 | Catalogue | Liste, détail, recherche, filtres par catégorie |
| F3 | Lecture | Pages, navigation, progression X/Y, reprise |
| F4 | Favoris | Toggle, page dédiée, historique (Profil) |
| F5 | Admin | CRUD histoires / pages, upload images, utilisateurs |
| F6 | TTS | Edge TTS (`POST /api/tts/synthesize`) + secours Web Speech API |

**RGPD** : route `/privacy`, `PATCH /api/me`, `DELETE /api/me`.

## Stack

| Couche | Technologie |
|--------|-------------|
| API | Symfony 7.2, PHP 8.4, JWT (Lexik) |
| Données | MySQL 8, Doctrine ORM |
| Front | React 19, TypeScript, Vite |
| Infra | Docker Compose — nginx, PHP-FPM, MySQL, phpMyAdmin |
| Qualité | PHP-CS-Fixer, ESLint, PHPUnit, Vitest, GitHub Actions |

**Écarts documentés** : Edge TTS (vs Google/Polly au CDCF), Vitest (vs Jest), pas de CD automatisé, modération BDD via phpMyAdmin (pas d'écran in-app).

## Variables d'environnement

Fichier unique à la racine : `.env` (copier depuis `.env.example`, monté dans le conteneur PHP).

| Variable | Rôle | Défaut / remarque |
|----------|------|-------------------|
| `APP_ENV` | `prod` ou `dev` | `prod` |
| `APP_DEBUG` | `0` ou `1` | `0` en prod |
| `APP_PORT` | Port HTTP application | `8080` |
| `PHPMYADMIN_PORT` | Port phpMyAdmin | `8081` |
| `DEFAULT_URI` | URL publique API | alignée sur `APP_PORT` |
| `APP_SECRET` | Secret Symfony | ≥ 32 caractères aléatoires en prod |
| `MYSQL_*` | Identifiants MySQL | voir `.env.example` |
| `DATABASE_URL` | Doctrine | mot de passe = `MYSQL_PASSWORD`, hôte `mysql` |
| `JWT_PASSPHRASE` | Clé privée JWT | ne pas changer après 1er boot sans régénérer les clés |
| `CORS_ALLOW_ORIGIN` | Origines autorisées | regex localhost / 127.0.0.1 |
| `TTS_*` | Synthèse vocale Edge | `TTS_ENABLED=1` par défaut |

Points clés :

- le mot de passe dans `DATABASE_URL` doit correspondre à `MYSQL_PASSWORD` ;
- `DEFAULT_URI` doit refléter l'URL réelle (ex. `http://localhost:8080` ou URL publique) ;
- liste complète et commentaires : fichier `.env.example` à la racine du dépôt.

## Environnements

| | Docker prod | Docker dev | Tests CI |
|---|-------------|------------|----------|
| Usage | Livraison, démo, serveur | Debug Symfony | GitHub Actions / PHPUnit local |
| Config | `APP_ENV=prod` `APP_DEBUG=0` | `APP_ENV=dev` `APP_DEBUG=1` | `APP_ENV=test` |
| Frontend | Build nginx `:8080` | nginx `:8080` ou `npm run dev` → `:5173` | Vitest sur l'hôte |
| Backend | Composer `--no-dev` | Composer + dev dependencies | SQLite via `backend/.env.test` |
| BDD | MySQL (volume `mysql_data`) | MySQL | SQLite |
| phpMyAdmin | Oui | Oui | — |

**Mode prod** — application complète sur `:8080`, sans Node sur le serveur :

```env
APP_ENV=prod
APP_DEBUG=0
```

```bash
docker compose up -d --build
```

**Mode dev** — hot-reload React :

```env
APP_ENV=dev
APP_DEBUG=1
```

```bash
docker compose up -d --build
cd frontend && npm install && npm run dev
```

**phpMyAdmin** (port `8081`) : pas d'écran de modération in-app ; consultation et correction des données en base (comptes, contenus) en dev et prod.

## Déploiement

Déploiement **manuel** reproductible. Pas de dump SQL séparé : migrations Doctrine + fixtures au premier boot.

### Installation locale ou serveur

```bash
git clone https://github.com/FortAxel/ipssi_project.git
cd ipssi_project
cp .env.example .env
# Personnaliser .env (secrets, DEFAULT_URI = URL publique)
docker compose up -d --build
curl -s http://127.0.0.1:8080/api/health
```

### Déploiement sur un serveur

1. Cloner le dépôt (branche `main` ou tag release).
2. Copier et personnaliser `.env` (`APP_SECRET`, `JWT_PASSPHRASE`, mots de passe MySQL forts, `DEFAULT_URI` = URL publique).
3. Ouvrir `APP_PORT` (défaut `8080`) ; restreindre `PHPMYADMIN_PORT` au réseau admin si exposé.
4. `docker compose up -d --build`
5. Vérifier `GET /api/health`.
6. Placer un reverse proxy HTTPS (Nginx, Caddy, Traefik) devant `APP_PORT` (recommandé).

**Mise à jour** :

```bash
git pull
docker compose up -d --build
```

### Opérations courantes

```bash
# Rebuild frontend seul (après modif React en prod)
docker compose up -d --build nginx

# Rebuild complet
docker compose up -d --build

# Réinitialiser la base (conteneurs + volume)
docker compose down -v
docker compose up -d --build

# Arrêter
docker compose down

# Logs
docker compose logs -f
docker compose logs -f php
```

**JWT** — si login renvoie 500 après changement de `JWT_PASSPHRASE` :

```bash
docker compose exec php php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction --env=prod
```

### CI vs CD

| | Mise en œuvre |
|---|---------------|
| **CI** | GitHub Actions (`.github/workflows/ci.yml`) — push/PR sur `main` et `develop` : lint PHP, PHPUnit, lint front, Vitest, build React |
| **CD** | Non automatisé vers un serveur ; déploiement manuel `docker compose up -d --build` sur la cible |

Évolution possible (non réalisée) : pipeline CI → build image Docker → push registry → script serveur `docker pull && compose up`.

### Stratégie de mise en production

Stratégie retenue : **recreate** en fenêtre de maintenance — `docker compose down`, pull du code, `docker compose up -d --build`, vérification healthcheck. Adaptée au projet pédagogique et à une instance unique.

| Stratégie | Principe | Applicabilité |
|-----------|----------|---------------|
| **Recreate** *(actuel)* | Arrêt → rebuild → redémarrage | Simple, documenté, suffisant ici |
| **Blue/Green** | Nouvelle stack en parallèle, bascule routage après validation | Reverse proxy / load balancer, double environnement |
| **Rolling update** | Mise à jour conteneur par conteneur | Orchestrateur multi-replicas (Kubernetes, Swarm) |

## Architecture

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

| Composant | Rôle |
|-----------|------|
| **nginx** | Fichiers statiques React (build Vite) + proxy `/api` et `/images` |
| **php** | API REST Symfony (JWT, Doctrine, TTS) |
| **app-init** | One-shot : Composer, migrations, fixtures, clés JWT si absentes |
| **mysql** | Persistance — volume `mysql_data` |
| **phpmyadmin** | Administration BDD |

Couches logiques : React (présentation) → API Symfony → Doctrine → MySQL. Fichiers : `docker-compose.yml`, `docker/nginx/`, `docker/php/`, `docker/init/bootstrap.sh`.

## Sécurité

| Risque | Mesure |
|--------|--------|
| Injection SQL | Doctrine ORM, requêtes paramétrées |
| XSS | Échappement React |
| Auth | JWT, mots de passe hachés |
| Autorisation | `ROLE_USER` / `ROLE_ADMIN` |
| Isolation données | Progression et favoris liés au token ; tests API |
| Secrets | `.env` et clés JWT hors dépôt (gitignore) |
| CORS | Nelmio — `CORS_ALLOW_ORIGIN` |
| Upload | Types MIME autorisés, 5 Mo max, noms aléatoires |
| RGPD | `/privacy`, `PATCH /api/me`, `DELETE /api/me` |

API stateless JWT — pas de CSRF Symfony (pas de formulaires Twig).

## Tests & CI

| Suite | Outil |
|-------|-------|
| Backend unitaire | PHPUnit — entité `ReadingProgress` |
| Backend API | PHPUnit — health, auth, compte, favoris, progression |
| Frontend | Vitest — i18n, composants |
| Lint | PHP-CS-Fixer, ESLint |

**Local (identique à la CI)** :

```bash
cd backend
cp .env.test .env
composer install --no-interaction --prefer-dist
composer lint:php
JWT_PASSPHRASE=ci_test_passphrase php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction
APP_ENV=test composer test

cd frontend
npm ci && npm run lint && npm test && npm run build
```

`docker compose exec php composer test` charge le `.env` prod/dev, pas l'environnement test SQLite — utiliser les commandes ci-dessus.

## Guide utilisateur (démo)

**Parent** (`parent@demo.local` / `parent123`) :

1. Connexion → catalogue (recherche, filtres).
2. Ouvrir une histoire → lecture page par page → bouton **Écouter** (TTS si activé).
3. Fermer / rouvrir → reprise à la dernière page.
4. Favori (cœur) → page **Favoris**.
5. **Profil** → historique, modifier e-mail / mot de passe, suppression compte.
6. **Confidentialité** → `/privacy`.

**Admin** (`admin@demo.local` / `admin123`) :

1. **Administration** → nouvelle histoire (titre, description, couverture, statut PUBLISHED).
2. **Gérer les pages** → texte + illustration.
3. Vérifier l'histoire dans le catalogue parent.

Durée indicative : ~10 min de parcours applicatif (+ présentation architecture en soutenance).

## DevOps — retour d'expérience

| Sujet | Solution |
|-------|----------|
| `composer --no-dev` en prod | Dépendances runtime en `require` |
| JWT / passphrase | Régénération auto dans `docker/init/bootstrap.sh` |
| Seed initial | Fixtures au premier boot si BDD vide |
| Prod + dev | Un seul `docker-compose.yml`, bascule via `APP_ENV` |
| SPA sans Node sur serveur | Build React intégré à l'image nginx |
| Reset BDD | `docker compose down -v` puis rebuild |

## Perspectives

Export RGPD (portabilité), modération utilisateurs in-app, déploiement continu automatisé (registry Docker), tests E2E, scan OWASP ZAP, hébergement cloud HTTPS — évolutions possibles hors périmètre livré.
