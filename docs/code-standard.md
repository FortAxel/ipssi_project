---
title: "Standards de code"
subtitle: "Projet Fil Rouge CDA – Storybook Kids"
author: "FORTUNATO Axel"
date: "Mai 2026"
---

# Standards de code

Ce document définit les conventions du projet **Storybook Kids** pour le back-end (Symfony / PHP), le front-end (React / TypeScript) et les pratiques transverses (Git, API, sécurité). Il complète le CDCF (jalon 1) et la méthodologie (jalon 2).

**Référence unique** : tout nouveau code doit respecter ce document. Les outils automatisés (EditorConfig, PHP-CS-Fixer, ESLint, Prettier) en sont le reflet.

---

## 1. Principes généraux

| Règle | Détail |
|-------|--------|
| **Langue du code** | Anglais : identifiants, commentaires, messages de commit, issues GitHub, noms de routes API, clés JSON. |
| **Langue de l’UI** | Français : libellés visibles par les parents (boutons, titres, messages d’erreur utilisateur). |
| **Langue de la doc projet** | Français pour les livrables pédagogiques (`docs/jalon-*`) ; ce document et le README technique peuvent mélanger FR (explications) et EN (exemples de code). |
| **Lisibilité** | Le code se lit comme du prose : noms explicites, fonctions courtes, peu de commentaires redondants. |
| **Responsabilité** | Une classe / un module = une raison de changer (SRP). Pas de logique SQL dans les contrôleurs. |
| **Pragmatisme** | Pas d’abstraction prématurée ; réutiliser les services et hooks existants avant d’en créer de nouveaux. |

---

## 2. Structure du dépôt

```
/
├── backend/          # API Symfony (PHP 8.2+)
├── frontend/         # SPA React + TypeScript
├── docs/             # Livrables et documentation projet
├── .editorconfig     # Indentation et fin de ligne (tout le dépôt)
├── .prettierrc.json  # Formatage front-end
└── docker-compose.yml  # Environnement local
```

Ne pas mélanger le code front et back dans un même dossier. Les assets publics (illustrations) vivent côté back (`public/uploads/`) ou CDN, pas dans `src/` React sauf icônes UI légères.

---

## 3. Nommage des fichiers et dossiers

### 3.1 Back-end (Symfony)

| Élément | Convention | Exemple |
|---------|------------|---------|
| **Classe PHP** | `PascalCase`, un fichier par classe, nom = nom de fichier | `StoryService.php` → `class StoryService` |
| **Entité Doctrine** | Singulier, `PascalCase` | `User.php`, `Story.php`, `ReadingProgress.php` |
| **Repository** | `{Entity}Repository.php` | `StoryRepository.php` |
| **Service métier** | `{Domaine}Service.php` | `FavoriteService.php` |
| **Contrôleur API** | `{Ressource}Controller.php` | `StoryController.php` |
| **DTO / Input** | `{Action}{Ressource}Input.php` ou `{Ressource}Input.php` | `ToggleFavoriteInput.php` |
| **Migration** | Générée par Doctrine (`VersionYYYYMMDDHHMMSS.php`) | Ne pas renommer manuellement |
| **Dossiers** | `src/Controller`, `src/Entity`, `src/Service`, `src/Repository`, `src/Dto` | Aligné sur le squelette Symfony |
| **Tests** | Miroir de `src/`, suffixe `Test` | `tests/Service/StoryServiceTest.php` |

### 3.2 Front-end (React)

| Élément | Convention | Exemple |
|---------|------------|---------|
| **Composant React** | `PascalCase.tsx` | `StoryCard.tsx`, `PageReader.tsx` |
| **Page (route)** | `PascalCase.tsx` dans `pages/` | `pages/CatalogPage.tsx` |
| **Hook personnalisé** | `use` + `PascalCase.ts` | `useAuth.ts`, `useStoryReader.ts` |
| **Service / API client** | `camelCase.ts` | `storyApi.ts`, `authApi.ts` |
| **Types partagés** | `camelCase.types.ts` ou `types/story.ts` | `story.types.ts` |
| **Styles module** | `ComponentName.module.css` | `StoryCard.module.css` |
| **Dossiers** | `kebab-case` pour les regroupements | `components/story-card/` |
| **Tests** | `*.test.tsx` ou `*.test.ts` à côté du fichier ou dans `__tests__/` | `StoryCard.test.tsx` |
| **Constantes** | `UPPER_SNAKE_CASE` dans un fichier dédié si partagé | `api.constants.ts` |

### 3.3 Fichiers à ne pas renommer librement

- `index.php`, `public/index.php`, configs Symfony (`config/`, `routes.yaml`)
- Fichiers générés : `var/`, `vendor/`, `node_modules/`, `dist/`

---

## 4. Nommage des identifiants (code)

### 4.1 PHP

| Type | Style | Exemple |
|------|-------|---------|
| Classe / Interface / Enum | `PascalCase` | `ReadingProgress`, `StoryStatus` |
| Méthode | `camelCase` | `findPublishedStories()` |
| Propriété / variable | `camelCase` | `$currentPageId` |
| Constante classe | `UPPER_SNAKE_CASE` | `MAX_PAGE_SIZE` |
| Paramètre | `camelCase` | `int $storyId` |
| Table SQL / colonne | `snake_case` (anglais) | `reading_progress`, `last_read_at` |
| Rôle Symfony | Préfixe `ROLE_` | `ROLE_USER`, `ROLE_ADMIN` |
| Statut métier (enum) | Anglais, `SCREAMING_SNAKE` ou backed enum | `DRAFT`, `PUBLISHED`, `ARCHIVED` |

**Entités** : propriétés en `camelCase` en PHP ; mapping Doctrine vers colonnes `snake_case` si besoin (`#[ORM\Column(name: 'last_read_at')]`).

### 4.2 TypeScript / React

| Type | Style | Exemple |
|------|-------|---------|
| Composant | `PascalCase` | `function StoryCard()` |
| Hook | `use` + `PascalCase` | `useReadingProgress()` |
| Fonction / variable | `camelCase` | `fetchStoryById`, `isLoading` |
| Type / Interface | `PascalCase` | `Story`, `StoryCardProps` |
| Props interface | `{Component}Props` | `StoryCardProps` |
| Enum TS | `PascalCase` membres ou string union | `type StoryStatus = 'DRAFT' \| 'PUBLISHED'` |
| Fichier export default composant | Nom = nom du composant | `StoryCard.tsx` exporte `StoryCard` |
| Booléen | Préfixe `is`, `has`, `can`, `should` | `isFavorite`, `hasNextPage` |
| Handler événement | Préfixe `handle` | `handleToggleFavorite` |
| Callback props | Préfixe `on` | `onPageChange` |

Éviter les abréviations obscures (`usr`, `str`) sauf acronymes métier (`tts`, `id`).

---

## 5. Documentation du code

### 5.1 Quand documenter

- **Oui** : API publique (services, hooks exportés), règles métier non évidentes, contraintes de sécurité, effets de bord, paramètres et valeur de retour des méthodes publiques PHP.
- **Non** : getters triviaux, code auto-explicatif (`$isFavorite = $favorite !== null`).

### 5.2 PHP — PHPDoc (méthodes publiques et services)

```php
/**
 * Creates or updates reading progress for the authenticated user.
 *
 * @throws StoryNotFoundException When the story does not exist or is not published
 */
public function upsertProgress(int $userId, int $storyId, int $currentPageId): ReadingProgress
```

- `@param` avec type + description courte si le nom ne suffit pas.
- `@return` omis si le type de retour PHP est explicite (PHP 8.2+).
- `@throws` pour les exceptions métier propagées.

**Propriétés d’entité** : pas de PHPDoc systématique ; typer avec les attributs PHP 8 (`private string $email`).

### 5.3 TypeScript — TSDoc (hooks, fonctions utilitaires exportées)

```typescript
/**
 * Fetches a published story with ordered pages for the reader.
 */
export async function fetchStoryWithPages(storyId: number): Promise<StoryDetail> {
```

Pour les **composants React** : documenter seulement si le comportement n’est pas clair via les props typées :

```typescript
export interface StoryCardProps {
  /** Story displayed in the catalog grid */
  story: StorySummary;
  /** Called when the user toggles the favorite heart */
  onToggleFavorite: (storyId: number) => void;
}
```

### 5.4 Commentaires inline

- En **anglais**, phrase complète ou fragment court.
- Expliquer le **pourquoi**, pas le **quoi**.
- Marqueurs temporaires autorisés : `// TODO:` avec issue GitHub si possible ; pas de `// FIXME` sans ticket.

```php
// Bad: increment counter
$count++;

// Good: reset progress when the referenced page was deleted by an admin
$progress->setCurrentPage(null);
```

---

## 6. Standards PHP (Symfony)

### 6.1 Normes appliquées

- **PSR-1** / **PSR-12** : style de code.
- **Symfony** : structure de projet, conventions de bundles et de config.
- **`declare(strict_types=1);`** en tête de chaque fichier PHP dans `src/` et `tests/`.

### 6.2 Organisation des couches

```
HTTP Request
  → Controller (validation Input, HTTP codes, JSON)
    → Service (règles métier)
      → Repository (requêtes Doctrine)
        → Entity
```

- Contrôleur : pas de logique métier lourde ; pas de `$em->flush()` direct sans passer par un service si la règle est réutilisée.
- Repository : requêtes réutilisables ; paramètres liés (jamais de concaténation SQL utilisateur).
- Réponses API : tableaux ou DTO sérialisés ; **jamais** exposer `password`, `roles` bruts sensibles, tokens internes.

### 6.3 Exemple de nommage API (routes)

| Verbe | Route | Action |
|-------|-------|--------|
| `GET` | `/api/stories` | Liste publiée |
| `GET` | `/api/stories/{id}` | Détail + pages |
| `PUT` | `/api/reading-progress/{storyId}` | Upsert progression |
| `POST` | `/api/favorites/toggle` | Toggle favori |
| `POST` | `/api/auth/register` | Inscription |
| `POST` | `/api/auth/login` | JWT |

Préfixe `/api`, noms de ressources au **pluriel**, identifiants en path, corps JSON en **camelCase** côté client (serializer Symfony peut mapper vers propriétés PHP).

### 6.4 Formatage automatique

Fichier : `backend/.php-cs-fixer.dist.php` (règles `@Symfony`).

```bash
cd backend
composer require --dev friendsofphp/php-cs-fixer
vendor/bin/php-cs-fixer fix          # corriger
vendor/bin/php-cs-fixer fix --dry-run --diff   # vérifier (CI)
```

Scripts Composer recommandés :

```json
"scripts": {
  "lint:php": "php-cs-fixer fix --dry-run --diff",
  "lint:php:fix": "php-cs-fixer fix"
}
```

---

## 7. Standards TypeScript / React

### 7.1 Normes appliquées

- **TypeScript strict** (`strict: true` dans `tsconfig.json`).
- **ESLint** : `frontend/eslint.config.js` (typescript-eslint, react-hooks, pas de `any`).
- **Prettier** : racine `.prettierrc.json` (guillemets simples, trailing commas).

### 7.2 Composants

- Composants fonctionnels uniquement (pas de classes).
- Un composant par fichier ; exports nommés préférés aux `default` sauf pages lazy-loaded.
- Props typées avec une interface dédiée ; destructuring en tête de fonction.
- Pas de logique API directe dans les composants de présentation : passer par `services/` ou hooks.

```typescript
export function StoryCard({ story, onToggleFavorite }: StoryCardProps) {
  return (/* ... */);
}
```

### 7.3 État et données

| Besoin | Outil |
|--------|--------|
| Auth / token | Context `AuthProvider` + hook `useAuth` |
| Données serveur | `fetch` ou client léger dans `services/` |
| État UI local | `useState` / `useReducer` |
| État global léger | Context ; éviter Redux sauf besoin réel |

### 7.4 Chaînes utilisateur (i18n light)

Centraliser les textes FR dans `frontend/src/i18n/fr.ts` (ou par feature `catalog.fr.ts`) :

```typescript
export const catalogLabels = {
  title: 'Catalogue',
  emptyState: 'Aucune histoire pour le moment.',
} as const;
```

### 7.5 Formatage et lint

```bash
cd frontend
npm install
npm run lint
npm run format:check
npm run format        # corriger
```

---

## 8. API REST et JSON

| Élément | Convention |
|---------|------------|
| Format | JSON, `Content-Type: application/json` |
| Encodage | UTF-8 |
| Champs JSON | `camelCase` |
| Dates | ISO 8601 (`2026-05-22T14:30:00+00:00`) |
| Erreurs | `{ "error": "message_code", "message": "Texte FR pour l'UI" }` ou structure Symfony Problem+JSON |
| Codes HTTP | `200` OK, `201` Created, `204` No Content, `400` Bad Request, `401` Unauthorized, `403` Forbidden, `404` Not Found, `422` Validation |

Pagination catalogue (si implémentée) : `?page=1&limit=20` ; réponse `{ items, total, page, limit }`.

---

## 9. Base de données

- Tables et colonnes : **anglais**, `snake_case`.
- Clés étrangères : `{table}_id` (`story_id`, `user_id`).
- Index nommés explicitement dans les migrations si besoin : `idx_reading_progress_user_story`.
- Pas de logique métier en triggers SQL ; rester dans les services PHP.
- Mots de passe : jamais en clair ; `password` haché via Symfony `UserPasswordHasher`.

---

## 10. Sécurité et secrets

| Règle | Détail |
|-------|--------|
| Secrets | `.env`, `.env.local` — **jamais** commités ; exemple dans `.env.example` |
| JWT | Secret long, rotation documentée en déploiement |
| Clé TTS | Variable d’environnement uniquement |
| Entrées | Validation Symfony Validator / Zod côté front avant envoi |
| Uploads | Types MIME autorisés, noms générés, hors `web/` exécutable |
| CORS | Origines explicites (URL du front en dev/prod) |

---

## 11. Git

Aligné sur `docs/jalon-2-methodologie/methodologie_organisation.md`.

**Branches** : `main` (stable), `develop` (intégration), `feature/*`, `fix/*`, `hotfix/*`.

**Commits** (anglais) :

```
<type>: <description courte impérative>

feat: add story list endpoint
fix: prevent negative page offset
docs: update code standards
test: cover reading progress upsert
refactor: extract favorite toggle to service
```

Types : `feat`, `fix`, `docs`, `test`, `refactor`, `chore`, `ci`.

**Auto-revue avant merge** : diff relu, lint/tests passent, pas de secret dans le diff.

---

## 12. Tests

| Couche | Outil | Cible |
|--------|-------|--------|
| Back unitaire | PHPUnit | Services (`StoryService`, `ReadingProgressService`) |
| Back fonctionnel | PHPUnit `WebTestCase` | Routes API critiques (auth, lecture) |
| Front | Vitest + React Testing Library | Composants et hooks isolés |

- Nom des tests : `test{Behavior}{Condition}` en PHP ; `it('should ...')` ou `test('...')` en TS.
- Un comportement par test ; pas de dépendance à l’ordre d’exécution.
- Données de test : fixtures Doctrine / factories, pas de prod.

---

## 13. Docker et environnement

- Variables documentées dans `.env.example` (sans valeurs secrètes).
- Même versions PHP / Node entre dev local et CI.
- Ne pas committer `vendor/`, `node_modules/`, `var/cache/`.

---

## 14. Outils — récapitulatif

| Outil | Fichier | Commande |
|-------|---------|----------|
| EditorConfig | `.editorconfig` | Appliqué par l’IDE |
| Prettier | `.prettierrc.json` | `npm run format` (frontend) |
| ESLint | `frontend/eslint.config.js` | `npm run lint` |
| PHP-CS-Fixer | `backend/.php-cs-fixer.dist.php` | `composer lint:php` (après install) |

Intégration CI (jalon 5) : workflow `.github/workflows/ci.yml` — `composer lint:php`, `composer test`, `npm run lint`, `npm test`, `npm run build` sur `main`, `develop` et `dev/beta_usable`.

---

## 15. Checklist avant merge

> **Jalon 5** : état validé pour la bêta documenté dans `docs/jalon-5-beta/README.md` (section 6).

- [ ] Nommage conforme (fichiers + identifiants)
- [ ] Pas de secret ni de `console.log` de debug
- [ ] PHPDoc / TSDoc sur les API publiques ajoutées
- [ ] Texte UI en français via fichiers i18n
- [ ] `lint` / `format:check` / `php-cs-fixer --dry-run` OK
- [ ] Tests ajoutés ou mis à jour pour la logique métier touchée
- [ ] Migration Doctrine incluse si le schéma change

---

**Version** : 1.0
**Date** : Mai 2026
**Prochaine révision** : après scaffolding Symfony/React (ajustements chemins si le squelette diffère)
