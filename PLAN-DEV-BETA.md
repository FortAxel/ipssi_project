# Plan de développement — version bêta

Application **Storybook Kids** : API Symfony + SPA React + MySQL, containerisée.

Le plan est découpé en **phases par priorité CDCF** : livrer le **minimum (P1)** le plus tôt possible, puis des **upgrades (P2)** dans un ordre fixe — **TTS avant progression et favoris**.

Références : [docs/code-standard.md](docs/code-standard.md), CDCF (jalons 1–4).

---

## Priorités CDCF (rappel)

| Priorité | Fonctionnalités | Objectif |
|----------|-----------------|----------|
| **P1 — MVP bêta** | F1 Auth, F2 Catalogue, F3 Lecture page à page, F5 Admin | Démo utilisable : se connecter, lire, gérer les contenus |
| **P2 — Upgrade 1** | F6 TTS (lecture audio) | Mode « autonomie » + contrainte API externe du référentiel |
| **P2 — Upgrade 2** | F4 Progression sauvegardée, favoris, historique | Rétention et reprise de lecture |
| **Qualité** | Tests, CI, doc, sécurité | Livrable jalon 5 complet |

**Hors MVP (reportés après le minimum)** : sauvegarde de progression, favoris, historique, bouton cœur, page « Mes favoris », champs API liés à F4.

**F3 au MVP** : navigation prev/next, texte + illustration, indicateur **« Page X / Y »** (calculé côté front, **sans** reprise au dernier point).

---

## Phase A — MVP bêta (P1) : le plus vite possible

Objectif de fin de phase : un parent **se connecte**, **parcourt le catalogue**, **lit une histoire** page par page ; un admin **crée / publie** du contenu. Pas encore d’audio ni de favoris ni de reprise automatique.

---

### A.1 — Prérequis projet

**Travail**

- Dépôt Git sur la branche de travail (`develop` ou `dev/beta_usable`).
- [docs/code-standard.md](docs/code-standard.md) et outillage lint en place.

**Doit fonctionner**

- Conventions et commandes lint documentées.

---

### A.2 — Environnement Docker

**Travail**

- `docker-compose.yml` : PHP/Symfony, MySQL, (optionnel) outil DB web.
- `.env.example`, volumes, réseau.

**Doit fonctionner**

- `docker compose up -d` OK ; MySQL joignable depuis PHP.

---

### A.3 — Squelette back-end Symfony

**Travail**

- API JSON dans `backend/`, Doctrine → MySQL, CORS vers le front dev.
- Dossiers `Controller`, `Entity`, `Repository`, `Service`, `Dto`.
- Route santé `GET /api/health`, PHP-CS-Fixer.

**Doit fonctionner**

- Health check 200 ; `composer lint:php` OK.

---

### A.4 — Modèle de données MVP + migrations

**Travail**

- Entités **P1** : `User`, `Story`, `Page` (statuts DRAFT / PUBLISHED / ARCHIVED, etc.).
- **Ne pas implémenter encore** les APIs F4 ; tables `favorite` et `reading_progress` :
  - soit ajoutées ici pour éviter une seconde migration plus tard (schéma vide, inutilisé),
  - soit ajoutées uniquement à l’upgrade 2 (migration dédiée).
- Migrations versionnées.

**Doit fonctionner**

- `doctrine:migrations:migrate` sur base vide → schéma cohérent avec le MPD pour le périmètre choisi.

---

### A.5 — Fixtures et données de démo

**Travail**

- 1 admin, 1 parent, 2–3 histoires PUBLISHED (plusieurs pages), 1 DRAFT.
- Illustrations placeholder dans `public/`.
- Comptes de test documentés.

**Doit fonctionner**

- Fixtures chargées ; histoires publiées lisibles en base avec pages ordonnées.

---

### A.6 — Authentification (F1)

**Travail**

- JWT (Lexik ou équivalent), register / login, `GET /api/me`.
- Rôles `ROLE_USER`, `ROLE_ADMIN`, mots de passe hachés.

**Doit fonctionner**

- Inscription + connexion + profil ; 401 sans token ; 403 admin pour un parent.

---

### A.7 — API catalogue et lecture (F2 + F3)

**Travail**

- `GET /api/stories` (PUBLISHED uniquement) — **sans** `isFavorite` ni progression.
- `GET /api/stories/{id}` + pages triées par `page_number`.
- Recherche / filtre simple optionnel.

**Doit fonctionner**

- Parent connecté : liste + détail + pages ; DRAFT invisible (404 ou absent).

---

### A.8 — API administration (F5)

**Travail**

- CRUD histoires et pages, changement de statut publish / archive.
- Routes `ROLE_ADMIN`, validation DTO.

**Doit fonctionner**

- Admin : brouillon → pages → publish → visible catalogue parent ; archive → disparaît du catalogue.

---

### ✅ Checkpoint A — Back-end MVP validable (Postman / curl)

**Doit fonctionner (ensemble)**

- Docker + API : auth, catalogue, lecture (données pages), admin.
- **Pas** de routes `reading-progress`, `favorites`, `tts`.

---

### A.9 — Squelette front React

**Travail**

- Vite + React + TypeScript, routage, `AuthProvider`, client HTTP + JWT.
- Routes MVP : login, register, catalogue, lecteur, admin — **pas** de route favoris.
- `VITE_API_URL`, ESLint / Prettier.

**Doit fonctionner**

- `npm run dev` ; pages privées inaccessibles sans login.

---

### A.10 — Layout et charte (UI/UX jalon 2)

**Travail**

- Header, titre, contenu ; responsive ; i18n FR (libellés communs).

**Doit fonctionner**

- Même habillage sur toutes les pages MVP.

---

### A.11 — Authentification front (F1)

**Travail**

- Pages login / register, déconnexion, profil minimal.

**Doit fonctionner**

- Parcours inscription → catalogue → déconnexion → reconnexion.

---

### A.12 — Catalogue front (F2)

**Travail**

- Grille de cartes (couverture, titre) — **sans** cœur favori, **sans** badge de reprise.
- Clic → lecteur (éventuel écran résumé + bouton « Lire »).

**Doit fonctionner**

- Histoires publiées visibles ; navigation vers le lecteur.

---

### A.13 — Lecteur page par page (F3, sans F4)

**Travail**

- Texte + image, précédent / suivant, indicateur **« Page X / Y »**.
- `GET /api/stories/{id}` uniquement.
- **Pas** d’appel `PUT /api/reading-progress` ; à l’ouverture, **toujours page 1** (ou choix explicite « Commencer » documenté).

**Doit fonctionner**

- Lecture du début à la fin dans la session ; navigation fluide.
- Rechargement de la page : retour page 1 (comportement MVP assumé).

---

### A.14 — Interface admin front (F5)

**Travail**

- Liste histoires (tous statuts), formulaire histoire, gestion pages, publier / archiver.
- Menu admin visible seulement pour `ROLE_ADMIN`.

**Doit fonctionner**

- Admin fait tout le cycle contenu depuis l’UI ; parent ne voit pas l’admin.

---

### ✅ Checkpoint B — **MVP bêta démontrable (fin Phase A)**

**Doit fonctionner (parcours complet)**

| Acteur | Parcours |
|--------|----------|
| **Parent** | Connexion → catalogue → lecture page par page (indicateur X/Y) |
| **Admin** | Connexion → création histoire → pages → publication → visible catalogue |

**Explicitement pas encore livré** : audio (F6), favoris, reprise automatique, historique (F4).

→ **Tag / note « beta-mvp »** possible ici : déjà présentable au formateur sur le cœur métier P1.

---

## Phase B — Upgrade 1 : lecture audio TTS (F6)

Objectif : bouton **« Écouter »** sur le lecteur (mode autonomie). **Avant** toute progression / favoris.

---

### B.1 — TTS back-end (si proxy API requis)

**Travail**

- `TtsService` + `POST /api/tts` (`pageId`, options) **ou** choix documenté : **Web Speech API** uniquement côté navigateur (pas de route back).
- Clé API en `.env` si cloud ; pas de stockage audio en BDD.

**Doit fonctionner**

- Texte de la page courante converti en audio (via API ou navigateur selon le choix).

---

### B.2 — TTS front (lecteur)

**Travail**

- Bouton « Écouter » sur le lecteur, états actif / arrêt, messages d’erreur FR.

**Doit fonctionner**

- Audio page courante sans bloquer prev/next ; échec TTS géré proprement.

---

### ✅ Checkpoint C — MVP + audio

**Doit fonctionner**

- Parcours Phase A + lecture audio sur au moins une page de démo.

---

## Phase C — Upgrade 2 : progression et favoris (F4)

Objectif : reprise de lecture, favoris, page dédiée. **Après** le TTS.

---

### C.1 — Schéma F4 (si pas fait en A.4)

**Travail**

- Tables / entités `Favorite`, `ReadingProgress` + migration si absentes.

**Doit fonctionner**

- Migration appliquée sans casser les données existantes.

---

### C.2 — API progression de lecture

**Travail**

- `ReadingProgressService`, `PUT /api/reading-progress/{storyId}`, GET optionnel (reprise / « continuer »).

**Doit fonctionner**

- Reprise au bon endroit par utilisateur ; isolation entre comptes.

---

### C.3 — API favoris

**Travail**

- `POST /api/favorites/toggle`, `GET /api/favorites`.
- Enrichir `GET /api/stories` avec `isFavorite` si utile.

**Doit fonctionner**

- Toggle cohérent ; liste favoris correcte.

---

### C.4 — Lecteur : sauvegarde et reprise (front)

**Travail**

- `PUT` progression à chaque changement de page ; au chargement, reprise dernière page.
- Indicateur complétion si dernière page atteinte.

**Doit fonctionner**

- Fermer le navigateur → rouvrir → même page ; fin d’histoire marquée terminée si règle métier active.

---

### C.5 — Favoris (front)

**Travail**

- Cœur sur catalogue / lecteur ; page « Mes favoris » ; route + entrée menu.

**Doit fonctionner**

- Ajout / retrait immédiat ; page favoris vide avec message FR.

---

### ✅ Checkpoint D — MVP + audio + F4

**Doit fonctionner**

- Démo complète : connexion → lecture avec reprise → favori → audio → admin publie une histoire.

---

## Phase D — Qualité et livrable jalon 5

À faire une fois le périmètre fonctionnel voulu pour la bêta est stable (au minimum fin Phase A ; idéalement Checkpoint D).

---

### D.1 — Tests automatisés

**Travail**

- PHPUnit : 1 service métier + 1 `WebTestCase` API (auth ou stories).
- Front : 1 test RTL/Vitest (login ou lecteur).
- Après upgrade 2 : tests sur `ReadingProgressService` / favoris.

**Doit fonctionner**

- `phpunit` et `npm test` verts en local.

---

### D.2 — Intégration continue

**Travail**

- GitHub Actions : lint PHP + front, PHPUnit, tests front.

**Doit fonctionner**

- Push `develop` / `main` → pipeline verte ou rouge explicite.

---

### D.3 — Documentation et scénario de démo

**Travail**

- README (install Docker, fixtures, comptes, lancement).
- `docs/jalon-5-beta/` : périmètre livré par phase (A / B / C), limites connues.
- Scénario de démo aligné sur le checkpoint atteint.

**Doit fonctionner**

- Reproduction de la démo sans aide, depuis le README seul.

---

### D.4 — Revue sécurité et clôture bêta

**Travail**

- OWASP (secrets, CORS, rôles, validation), pas de fuite inter-utilisateurs.
- Checklist [docs/code-standard.md](docs/code-standard.md) §15 — reprise point par point dans [docs/jalon-5-beta/README.md](docs/jalon-5-beta/README.md) §6.

**Doit fonctionner**

- Bêta **jalon 5** : stable pour présentation ; prête pour jalon 6 (déploiement).
- Livrable documenté : `docs/jalon-5-beta/README.md` (PDF via `docs/generate-pdf.sh`).

---

## Vue d’ensemble des phases

```
Phase A — MVP P1 (F1, F2, F3, F5)
  A.1 → A.2 → A.3 → A.4 → A.5 → A.6 → A.7 → A.8
        └─ Checkpoint A (API seule)
  A.9 → A.10 → A.11 → A.12 → A.13 → A.14
        └─ Checkpoint B ★ démo minimale

Phase B — Upgrade TTS (F6) — avant F4
  B.1 → B.2
        └─ Checkpoint C

Phase C — Upgrade F4 (progression + favoris)
  C.1 → C.2 → C.3 → C.4 → C.5
        └─ Checkpoint D ★ démo enrichie

Phase D — Qualité jalon 5
  D.1 → D.2 → D.3 → D.4
```

Le front (A.9+) peut démarrer dès le **Checkpoint A** : le back MVP est testable sans attendre les upgrades.

---

## Tableau fonctionnalités × phase

| CDCF | Phase A MVP | Phase B TTS | Phase C F4 |
|------|-------------|-------------|------------|
| F1 Auth | ✅ | — | — |
| F2 Catalogue | ✅ | — | enrichi (favori) |
| F3 Lecture | ✅ (sans reprise) | + audio | + reprise |
| F5 Admin | ✅ | — | — |
| F6 TTS | — | ✅ | — |
| F4 Favoris / progression | — | — | ✅ |

---

## Ordre des upgrades P2 (figé)

1. **TTS (F6)** — Phase B  
2. **Progression + favoris (F4)** — Phase C  

Ne pas inverser : le référentiel et la démo « mode autonomie » passent avant la rétention utilisateur.
