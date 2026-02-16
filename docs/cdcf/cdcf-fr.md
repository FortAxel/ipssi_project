# Cahier des Charges Fonctionnel  
## Présentation fonctionnelle du projet

## 1. Contexte métier

Le projet s'inscrit dans le domaine des applications numériques destinées au jeune public, plus précisément dans le secteur de la lecture et du divertissement éducatif pour enfants. La lecture d'histoires joue un rôle fondamental dans le développement de l'imaginaire, du langage et de la concentration chez les enfants. Toutefois, les supports numériques existants sont souvent peu adaptés à un usage encadré, trop complexes ou insuffisamment structurés pour répondre aux besoins spécifiques de ce public.

L'application proposée vise à répondre à ce constat en offrant une plateforme dédiée à la lecture d'histoires pour enfants, reposant sur des contenus narratifs illustrés. Chaque histoire est structurée en pages successives, chaque page correspondant à un paragraphe court accompagné d'une illustration. Cette structuration permet une lecture dynamique, adaptée aux capacités d'attention des enfants.

**L'application est conçue pour accompagner l'enfant dans son évolution et son autonomie progressive face à la lecture. Elle propose trois modes d'utilisation complémentaires :**
- **Pour les plus jeunes** : les parents lisent les histoires à l'enfant en utilisant l'application comme support visuel
- **En phase d'apprentissage** : l'enfant peut utiliser la fonctionnalité de synthèse vocale pour écouter les histoires de manière autonome
- **Pour les lecteurs confirmés** : l'enfant lit lui-même les histoires, l'application devenant un outil d'entraînement à la lecture

Cette approche évolutive permet à l'application de rester pertinente sur plusieurs années, accompagnant l'enfant de la petite enfance jusqu'à l'acquisition de l'autonomie en lecture.

L'application s'adresse principalement aux parents, qui jouent un rôle d'intermédiaire et de supervision. Elle permet de proposer un environnement numérique sécurisé, simple d'utilisation et pensé pour une consultation régulière des histoires par les enfants.

---

## 2. Objectifs du projet

L'objectif principal du projet est de concevoir et développer une application web permettant la consultation d'histoires pour enfants dans un cadre sécurisé et structuré, **accompagnant l'enfant dans son évolution de la petite enfance jusqu'à l'autonomie en lecture**.

Les objectifs fonctionnels du projet sont les suivants :

- Permettre à un utilisateur de créer un compte et de s'authentifier de manière sécurisée.
- Offrir un accès à un catalogue d'histoires pour enfants, illustrées et structurées en pages.
- Permettre la lecture d'une histoire page par page, en associant texte et image.
- Proposer différents modes de consultation adaptés au niveau de l'enfant (lecture par les parents, lecture audio autonome, lecture personnelle).
- Proposer une expérience de lecture simple, fluide et adaptée à un jeune public.
- Mettre à disposition des fonctionnalités de gestion telles que les favoris et l'historique de lecture.
- Prévoir une interface d'administration permettant la gestion et la modération des histoires.
- Enrichir l'expérience utilisateur par l'intégration d'un service externe de synthèse vocale.

Ces objectifs sont définis de manière réaliste et atteignable dans le cadre d'un projet de développement individuel sur une durée de six mois.

---

## 3. Périmètre fonctionnel (exigences fonctionnelles)

### Fonctionnalité 1 : Gestion des utilisateurs

L'application permet la gestion des utilisateurs avec les fonctionnalités suivantes :
- Création de compte utilisateur (parent)
- Authentification et déconnexion
- Gestion du profil utilisateur
- Attribution de rôles (utilisateur standard, administrateur)

### Fonctionnalité 2 : Catalogue d'histoires

L'utilisateur authentifié peut :
- Consulter la liste des histoires disponibles
- Accéder au détail d'une histoire (titre, description, illustration de couverture)
- Filtrer ou rechercher les histoires selon des critères simples

### Fonctionnalité 3 : Lecture d'une histoire

L'application permet :
- La lecture d'une histoire page par page
- L'affichage simultané du texte et de l'illustration associée
- Une navigation simple entre les pages (page suivante / précédente)
- Un indicateur de progression dans l'histoire

### Fonctionnalité 4 : Favoris et suivi de progression

L'utilisateur peut :
- Ajouter ou retirer une histoire de ses favoris
- Consulter l'avancée dans les histoires commencées (reprise de lecture au dernier point d'arrêt)
- Visualiser l'historique des histoires lues

### Fonctionnalité 5 : Interface d'administration

Un utilisateur disposant du rôle administrateur peut :
- Ajouter, modifier ou supprimer des histoires
- Gérer les pages associées à une histoire (texte et illustrations)
- Assurer la cohérence et la qualité des contenus proposés

### Fonctionnalité 6 : Lecture audio (API externe)

L'application propose une fonctionnalité de lecture audio des histoires via un service externe de synthèse vocale. Cette fonctionnalité permet d'écouter le contenu textuel d'une page ou d'une histoire complète, favorisant l'autonomie de l'enfant en phase d'apprentissage.

#### Hors périmètre
La génération automatique de nouvelles histoires par intelligence artificielle ne fait pas partie du périmètre du projet.

---

## 4. Exigences techniques

### Choix d'architecture

Le projet repose sur une architecture de type **API REST**, avec une séparation claire entre le front-end et le back-end :
- **Back-end** : API développée avec le framework Symfony 
- **Front-end** : application web développée avec React et TypeScript

**Justification du choix :**
L'architecture API REST avec front-end React séparé a été retenue pour les raisons suivantes :
- **Réactivité de l'interface** : React permet de créer une interface utilisateur fluide et réactive, particulièrement adaptée à l'expérience de lecture page par page
- **Expérience utilisateur** : Une Single Page Application (SPA) offre des transitions plus fluides entre les pages d'une histoire, sans rechargement complet de la page
- **Évolutivité** : Cette architecture permettrait à l'avenir d'envisager une application mobile native consommant la même API

### Composants techniques

- **Langage back-end** : PHP 8+
- **Framework back-end** : Symfony 
- **Front-end** : React avec TypeScript
- **Base de données** : MySQL
- **Outil d'administration de la base** : phpMyAdmin
- **ORM** : Doctrine (intégré à Symfony)
- **Conteneurisation** : Docker et Docker Compose
- **Gestion de version** : Git (hébergé sur GitHub)
- **Intégration Continue** : GitHub Actions
- **API externe** : Service de synthèse vocale (Text-to-Speech) - à Déterminer 

### Contraintes techniques imposées

Conformément au cahier des charges technique de la formation CDA, le projet respecte les contraintes suivantes :

- **Base de données relationnelle SQL** : Utilisation de MySQL avec modélisation normalisée
- **Conteneurisation Docker** : Environnement de développement et de production entièrement dockerisé
- **Intégration d'une API externe** : Service de synthèse vocale pour la lecture audio des histoires
- **Contrôle de version Git** : Stratégie de branches (main, develop, feature branches) avec commits réguliers et messages explicites
- **Intégration Continue (CI)** : Pipeline automatisée via GitHub Actions exécutant les tests à chaque push
- **Tests automatisés** : 
  - Tests unitaires (PHPUnit pour le back-end)
  - Tests fonctionnels (tests d'API)
  - Tests d'intégration front-end (Jest, React Testing Library)
- **Sécurité** : Application des bonnes pratiques OWASP
  - Protection contre les injections SQL (utilisation de Doctrine ORM)
  - Protection XSS (échappement automatique via React)
  - Protection CSRF (tokens Symfony)
  - Hachage des mots de passe (bcrypt/Argon2)
  - Gestion sécurisée des clés API (variables d'environnement)
- **Architecture multi-couches** : Respect du pattern MVC et séparation claire des responsabilités
- **Qualité du code** : Respect des standards PSR pour PHP, conventions ESLint pour TypeScript/React

---

## 5. Contraintes et enjeux du projet

### Contraintes temporelles

Le projet s'étend sur une durée de six mois (janvier à juin 2026) et est découpé en six jalons mensuels :
- **Jalon 1 (Janvier)** : Cahier des charges fonctionnel
- **Jalon 2 (Février)** : Méthodologie et conception UI/UX
- **Jalon 3 (Mars)** : Modélisation de la base de données
- **Jalon 4 (Avril)** : Conception technique et architecture
- **Jalon 5 (Mai)** : Développement, sécurité et tests (version bêta)
- **Jalon 6 (Juin)** : Déploiement et livrable final

Chaque jalon permet de valider une étape du projet avant de passer à la suivante, garantissant un avancement régulier et maîtrisé.

**Contexte de réalisation :**
Ce projet fil rouge est réalisé dans le cadre d'une formation en alternance. Le développement s'effectue en parallèle des périodes de formation à l'école et des missions professionnelles en entreprise. Cette contrainte impose une organisation rigoureuse et une gestion optimale du temps disponible :
- Travail sur le projet principalement durant les périodes de formation et en dehors des heures professionnelles
- Nécessité d'une priorisation stricte des fonctionnalités (approche MVP)

Cette contrainte temporelle forte renforce l'importance d'une méthodologie agile, d'un périmètre fonctionnel réaliste et d'une focalisation sur les fonctionnalités essentielles.

### Contraintes réglementaires

L'application manipule des données personnelles (comptes utilisateurs, historique de lecture). Elle devra donc respecter les principes généraux du **RGPD**, notamment :
- Transparence sur la collecte et l'utilisation des données
- Possibilité pour l'utilisateur de consulter, modifier et supprimer ses données
- Stockage sécurisé des informations sensibles (mots de passe hachés)
- Politique de confidentialité accessible dans l'application
- Pas de collecte de données sur les enfants sans consentement parental

### Dépendances externes et risques

Les principaux risques identifiés sont :

**Risques techniques :**
- Dépendance à une API externe de synthèse vocale (évolution, disponibilité, coûts)
- Complexité de l'intégration React/Symfony
- Gestion des images et du stockage (volumétrie des illustrations)

**Risques organisationnels :**
- Temps limité pour implémenter l'ensemble des fonctionnalités prévues
- Nécessité de maintenir un périmètre fonctionnel maîtrisé
- Apprentissage simultané de nouvelles technologies

**Mesures d'atténuation :**
- Choix d'une API de synthèse vocale fiable avec documentation complète
- Priorisation des fonctionnalités (MVP d'abord, puis enrichissements)
- Planification réaliste avec des objectifs par jalon
- Tests réguliers pour identifier rapidement les problèmes

---

## 6. Critères de succès du projet

Le projet sera considéré comme réussi si :

**Critères fonctionnels :**
- L'ensemble des fonctionnalités principales décrites dans ce document est implémenté et fonctionnel
- Les trois modes d'utilisation (lecture par les parents, audio autonome, lecture personnelle) sont opérationnels
- L'interface est intuitive et adaptée au public cible (parents et enfants)

**Critères techniques :**
- L'application est stable et sécurisée (respect des normes OWASP)
- Les temps de réponse de l'API sont acceptables 
- L'application est entièrement containerisée et déployable via Docker
- La pipeline CI/CD est fonctionnelle et automatisée

**Critères de qualité :**
- Le code est structuré, documenté et maintenable (respect des standards PSR et conventions React)
- La documentation technique est complète et à jour
- L'application est responsive (desktop et mobile)

**Critères de présentation :**
- Capacité à expliquer les choix techniques lors de la soutenance
- Démonstration fonctionnelle de l'application devant le jury

---

## 7. Conclusion

Ce cahier des charges fonctionnel constitue la référence du projet pour l'ensemble des phases de développement à venir. Il définit le cadre fonctionnel, les objectifs, les contraintes et les enjeux de l'application de lecture d'histoires pour enfants.

Le projet se distingue par son approche évolutive, permettant d'accompagner l'enfant sur plusieurs années dans son parcours d'apprentissage de la lecture. L'intégration d'une API de synthèse vocale et la structure en pages courtes illustrées constituent les éléments centraux de cette proposition de valeur.

Une fois validé, ce document servira de base à la phase de conception UI/UX (Jalon 2), puis à la modélisation de la base de données (Jalon 3) et à la mise en œuvre technique du projet dans les jalons suivants.