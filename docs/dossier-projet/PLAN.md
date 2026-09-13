# Plan de travail — Dossier de projet CDA

Base : copie conforme de `docs/jalon-6-rapport-final/rapport-final.md`.
Projet principal : **Storybook Kids** (projet fil rouge, coche toutes les cases du référentiel).
Projets d'entreprise : **HyperSearchX** et **goStoriesAI**, ajoutés pour pouvoir en parler à l'oral.

---

## 1. Couverture actuelle vs checklist IPSSI

| Checklist | Chapitre existant | État |
|---|---|---|
| I. Présentation personnelle + projet en anglais | — | **à créer** |
| II. Présentation du projet (contexte, solution, technos) | 1 (partiel) | à compléter par une intro courte |
| III. Cahier des charges | 1 | OK |
| IV. Méthodologie, versioning, DevOps | 2 | OK |
| V. Conception UI/UX | 3 | OK |
| VI. Modélisation MERISE | 4 | OK |
| VII. Conception UML | 5 | à compléter (classes : composition / agrégation) |
| VIII. Architecture multi-couches | 6 | à compléter (distinction MVC / n-tiers) |
| IX. Sécurité | 7 | à compléter (CSRF, force brute) |
| X. Politique de tests | 8 | OK |
| XI. Déploiement et mise en production | 9 | OK |
| XII. Veille technologique | — | **à créer** |
| XIII. Veille sécurité | — | **à créer** |
| XIV. Difficultés rencontrées | — | **à créer** |
| XV. Conclusion et ouverture | 11 (partiel) | à compléter (compétences acquises) |
| XVI. Remerciements | — | **à créer** |
| XVII. Annexes | — | **à créer** |

Le chapitre 10 (guide utilisateur et scénario de démonstration) est conservé : il sert de
support à la démonstration technique attendue en soutenance.

---

## 2. Modifications techniques préalables

- Front matter : titre `Dossier de projet — Storybook Kids`, sous-titre
  `Titre professionnel Concepteur développeur d'applications`, date à jour, `toc-depth: 2`.
- Chemins d'images : les six références `./assets/…` (lignes 412, 420, 426, 434, 442, 727)
  doivent devenir `../jalon-6-rapport-final/assets/…`. Toutes les autres références
  (`../jalon-2-…`, `../jalon-3-…`, `../jalon-4-…`) sont déjà valides depuis ce dossier.
- Remplacer les caractères `→` par `->` (rendu LaTeX).
- Compilation : `pandoc` + `docs/cda-template.tex` + `docs/metadata.yaml`, moteur `tectonic`,
  polices système (`Helvetica Neue` / `Menlo`).

---

## 3. Sections à ajouter

### 3.1 En ouverture — Personal introduction & project overview (≈ 1 page)
Exigé par la checklist (point I) et par le référentiel. Deux paragraphes en anglais :
qui je suis et mon parcours ; ce qu'est Storybook Kids, le problème résolu, la pile technique.

### 3.2 Après le sommaire — Tableau des compétences (≈ 1 page)
Les compétences du référentiel CDA en regard de la réalisation qui les démontre et du
chapitre où elle est décrite. C'est ce tableau que le jury utilise pour naviguer.
Il mentionne les trois projets, ce qui légitime la présence de HSX et goStoriesAI.

### 3.3 Compléments dans les chapitres existants

- **Chapitre 5 (UML)** : le diagramme d'entités métier existe ; ajouter un paragraphe qui
  nomme explicitement les relations de composition (une histoire et ses pages) et
  d'agrégation (un compte et ses favoris). Le jury pose la question.
- **Chapitre 6 (Architecture)** : ajouter un encadré distinguant l'architecture **logique**
  (MVC côté Symfony : contrôleurs, entités, services) de l'architecture **physique**
  (n-tiers : navigateur / nginx+PHP / MySQL). La confusion entre les deux est le piège classique.
- **Chapitre 7 (Sécurité)** : les mesures OWASP sont là, mais **CSRF** et **attaques par force
  brute** ne sont jamais nommées. Ajouter une ligne pour chacune (jetons CSRF sur les
  formulaires, limitation des tentatives de connexion), même pour dire ce qui est en place
  et ce qui ne l'est pas.

### 3.4 Nouveau chapitre — Extraits de code significatifs (≈ 2 pages)
Trois extraits commentés, chacun introduit par son intention (pas de copier-coller brut) :
sauvegarde de la progression de lecture, contrôle d'accès sur une route protégée,
et un test représentatif.

### 3.5 Nouveau chapitre — Jeu d'essai (≈ 1,5 page)
Sur la fonctionnalité la plus représentative, la reprise de lecture : jeu de données en
entrée, résultats attendus, résultats obtenus, cas limites (histoire jamais ouverte,
utilisateur non connecté). Attendu explicitement par le référentiel de certification.

### 3.6 Nouveau chapitre — Veille technologique et veille sécurité (≈ 2 pages)
Sources suivies et rythme, puis deux exemples concrets de veille ayant eu un effet sur le
projet. Côté sécurité : une menace actuelle analysée, avec la mesure préventive
correspondante dans le projet.

### 3.7 Nouveau chapitre — Difficultés rencontrées (≈ 1,5 page)
Trois à quatre difficultés réelles, chacune sur le même schéma : le problème, ce que j'ai
essayé, la solution retenue, ce que j'en ai tiré. Y inclure une difficulté d'organisation,
pas uniquement technique.

### 3.8 Nouveau chapitre — Projets réalisés en entreprise chez LICENCESINFO (≈ 4 pages)
Objectif : donner de la matière pour l'oral sans déplacer le centre de gravité du dossier.

- Introduction : pourquoi cette section existe (alternance, volume horaire, complémentarité).
- **HyperSearchX** : besoin, architecture en couches, moteur de recherche, sécurité des
  données de boutique, déploiement.
- **goStoriesAI** : besoin, architecture avec traitements en arrière-plan, application iOS
  et versionnement des données locales, mise en production.
- Clôture : ce que ces deux projets apportent en plus de Storybook Kids.

### 3.9 Fin de dossier
- **Conclusion** : compléter le bilan existant par une rubrique « compétences acquises ».
- **Remerciements** : équipe encadrante IPSSI, LICENCESINFO, ressources utilisées.
- **Annexes** : liste des documents techniques, captures et extraits de code, avec renvois.

---

## 4. Fil conducteur

La checklist insiste sur ce point : chaque chapitre doit introduire le suivant. Une fois les
sections en place, ajouter une phrase de transition en fin de chaque chapitre.

---

## 5. Volume visé

40 à 60 pages hors annexes. Le rapport du jalon 6 en fait environ 40 ; les ajouts
ci-dessus représentent une douzaine de pages supplémentaires.
