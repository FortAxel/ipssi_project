---
title: "Document de Conception UI/UX"
subtitle: "Jalon 2 – Projet Fil Rouge CDA"
author: "FORTUNATO Axel"
date: "Février 2026"
numbersections: false
---

\newpage

## 1. Zoning / Sitemap

### 1.1 Structure générale de l’application

L’application est organisée autour des écrans principaux suivants :

- Page d’accueil (Catalogue)
- Page de lecture d’une histoire
- Page des favoris
- Page de connexion / inscription
- Page profil utilisateur
- Interface d’administration (gestion des histoires)

### 1.2 Sitemap simplifié

**Parcours principal**

1. Connexion / inscription  
2. Catalogue (accueil)  
3. Détail d’une histoire  
4. Lecture page par page  

**Depuis le catalogue**

- Accès aux favoris, au profil, déconnexion  
- Ajout / retrait d’une histoire aux favoris (carte ou fiche)  

**Depuis la lecture**

- Retour au catalogue  
- Accès aux favoris (menu)  
- Navigation page précédente / suivante  
- Lecture audio (selon périmètre fonctionnel)  

La page **Favoris** reprend la même grille que le catalogue : seules les histoires marquées favori y figurent.


![Sitemap de l’application](./assets/sitemap.png){ width=100% }

**À noter :** le sitemap inclut l’action « ajouter aux favoris » depuis « Mes favoris » lorsque l’utilisateur retire un favori par erreur : il peut le rétablir tant que la page n’est pas rechargée.

---

\newpage

## 2. Zoning des écrans principaux

### 2.1 Structure
Structure en trois zones principales :

**Header**
- Logo illustré
- Boutons présents pour la navigation entre les pages

**Titre**
- Titre de la page (catalogue, favoris) ou de l'histoire (Le petit prince)

**Contenu principal**
- Contenu de la page (le catalogue, la page d'histoire)

Ce zoning permet une séparation claire entre contenu visuel et contenu textuel, il permet de normaliser l'affichage entre les pages et de comprendre rapidement sur quelle page nous sommes (plus particulierement pour différencier catalogue et favoris).


### 2.1.1 Sur desktop

![zoning destop](./assets/desktop-zoning.png)

### 2.1.2 Sur mobile

![zoning mobile](./assets/phone-zoning.png)

---

\newpage

## 3. Wireframes (Maquettes basse fidélité)

Les wireframes ont été réalisés afin de définir l’organisation fonctionnelle avant toute réflexion graphique.

Objectifs :
- Définir la hiérarchie des informations
- Positionner les éléments interactifs
- Valider la structure des éléments

### 3.1 Wireframe catalogue

### 3.1.1 Catalogue desktop 

Choix réalisés :
- Carte large cliquable
- Favori positionné en haut à droite
- Progression visible immédiatement (Rond plus ou moins rempli en fonction de la progression)
- Séparation claire image / texte

![wireframe catalog destop](./assets/desktop-catalog-wireframe.png)

### 3.1.2 Catalogue mobile

Choix réalisés :
- Carte large cliquable
- Favori positionné en haut à droite
- Progression visible immédiatement
- Séparation claire image / texte

![wireframe catalog mobile](./assets/phone-catalog-wireframe.png)

### 3.2 Wireframe lecture

### 3.2.1 Lecture desktop 

Choix réalisés :
- Mise en page deux colonnes : image à gauche, texte à droite
- Bouton "Écouter" positionné centré sous le texte pour une lecture naturelle
- Navigation bas de page avec indication de progression (Page X sur Y)
- Séparation claire image / texte

![wireframe reading destop](./assets/desktop-reading-wireframe.png)

### 3.2.2 Lecture mobile

Choix réalisés :
- Mise en page colonne unique : image en haut, texte en dessous
- Menu hamburger en haut à gauche pour accéder à la navigation
- Bouton "Écouter" centré sous le texte
- Navigation compacte avec chevrons (< >) et indication de progression

![wireframe reading mobile](./assets/phone-reading-wireframe.png)

---

## 4. Charte graphique

![graphic chart](./assets/graphic-chart.png)

### 4.1 Couleurs

| Usage | Couleur | Code |
|-------|----------|------|
| Primary | Bleu principal | #4A90E2 |
| Secondary | Jaune doux | #F5D76E |
| Success (lecture audio) | Vert | #7ED957 |
| Favoris | Rose | #FF7BA5 |
| Blanc neutre | Blanc | #FFFFFF |
| Gris clair | Light Grey | #F2F2F2 |
| Gris foncé | Dark Grey | #333333 |

#### Justification 

- Le **bleu principal** inspire la confiance et la sécurité (important pour un public parental).
- Le **jaune secondaire** apporte une touche chaleureuse et enfantine.
- Le **vert** est associé à l’action positive (écouter).
- Le **rose** identifie visuellement les favoris.
- Les tons neutres assurent une bonne lisibilité.

L’ensemble crée une ambiance :
**Ludique, rassurante, douce et adaptée au jeune public.**

### 4.2 Typographie

**Titres et boutons :**
- Police : Fredoka One
- Taille : 28px
- Style : Regular

**Sous-titres :**
- Police : Open Sans
- Taille : 18px

**Texte courant :**
- Police : Open Sans
- Taille : 16px

**Texte secondaire :**
- Police : Open Sans
- Taille : 14px

#### Justification

Fredoka One apporte :
- Un aspect arrondi
- Une dimension enfantine
- Une bonne lisibilité sur écran

Open Sans garantit :
- Lisibilité optimale
- Neutralité
- Confort de lecture sur paragraphes longs

### 4.3 Composants principaux

![component](./assets/component.png)

Tout est volontairement trés arrondi pour adoucir les pages. 
Tous les composants respectent :
- Border-radius : 32px
- Ombres légères (shadow douce) de la couleur de l'élément (si c'est un bouton bleu l'ombre est bleue pour faire un effet néon)
- Effet hover sur desktop
- Effet pressed sur mobile
- États visuels : normal / hover / actif / focus

Les couleurs **primary** pourront être adaptées par thème ; la charte montre la variante principale, d’autres couleurs pourront être déclinées pour un univers plus enfantin.

### 4.4 Évolutions et options hors périmètre MVP

Certaines briques de la planche de composants relèvent d’**évolutions ultérieures** au socle MVP (lecture, catalogue, compte) : elles ne font pas partie du périmètre de la première livraison décrit au CDCF (Jalon 1) et illustré par les wireframes et les maquettes du parcours principal.

- **Recherche textuelle dans le catalogue** : **option future** — utile quand le catalogue grossira ; pour l’instant le filtrage par catégorie suffit. Si elle est ajoutée plus tard, elle reprendra la même charte et les mêmes états (focus, disabled) que les champs existants.

---

\newpage

## 5. Maquettes graphiques haute fidélité

### 5.0 Catalogue – wireframe, charte et haute fidélité

Le **catalogue** et les **favoris** reprennent la structure des wireframes §3.1 (grille de cartes, couverture, progression, cœur) et la **charte graphique §4**. Le travail haute fidélité le plus détaillé a été appliqué à l’**écran de lecture** (§5.1–5.2), où l’immersion et le bouton « Écouter » concentrent les choix graphiques. Pour le catalogue et les favoris, le rendu final correspond à l’application systématique de cette charte au wireframe, sans écran HF catalogue séparé dans ce livrable.

### 5.1 Version Desktop – Lecture d’histoire

![desktop reading hd](./assets/desktop-reading-hd.png)

Caractéristiques :
- Layout 2 colonnes
- Illustration immersive
- Texte encadré (bordure pointillée douce)
- Bouton “Écouter” mis en valeur
- Indicateur de progression visible
- Flèches de navigation larges et accessibles

Cette version met l’accent sur :
- L’immersion visuelle
- La clarté de lecture
- La simplicité d’interaction


### 5.2 Version Mobile – Lecture d’histoire

![phone reading hd](./assets/phone-reading-hd.png)

Caractéristiques :
- Layout vertical
- Illustration mise en avant en haut
- Bloc texte arrondi et centré
- Bouton “Écouter” large et accessible
- Navigation simplifiée
- Indicateur de progression centré

Cette version privilégie :
- L’usage à une main
- Des zones tactiles larges
- Une lecture confortable sur petit écran

### 5.3 Responsivité

Le passage Desktop → Mobile implique :

- Passage d’un layout horizontal à vertical
- Suppression des éléments secondaires
- Augmentation des zones cliquables
- Navigation simplifiée

L’application est conçue selon une approche **Desktop paysage**, puis adaptée au mobile vertical.

À noter: Sur tablette en paysage on utilisera la version desktop adaptée pour garder le texte lisible

---

## 6. Considérations UX

### 6.0 Adéquation avec le CDCF (Jalon 1)

Les choix d’interface ci-dessus **répondent directement** aux exigences du cahier des charges fonctionnel :

- **Compte et authentification (F1)** : parcours connexion / inscription et profil prévus dans la structure §1 et le zoning §2 ; données personnelles côté parent (cohérent avec le RGPD évoqué au Jalon 1).
- **Catalogue et filtrage simple (F2)** : liste d’histoires en cartes, accès au détail puis à la lecture (wireframes §3.1).
- **Lecture page par page (F3)** : navigation, texte + illustration, indicateur de progression (wireframes et HF §3.2, §5).
- **Favoris et historique (F4)** : même grille que le catalogue pour les favoris ; reprise de lecture gérée côté applicatif (parcours §6.4).
- **Administration (F5)** : écran dédié hors maquettes grand public (prévu dans le sitemap §1.1) ; séparation claire du parcours parent/enfant.
- **Lecture audio (F6)** : bouton « Écouter » mis en avant sur les maquettes HF lecture, cohérent avec le mode « autonomie » du CDCF.

Les **trois modes de lecture** du CDCF (parent qui lit, enfant en audio, enfant qui lit seul) sont supportés par une **même interface de page** : le parent peut lire à voix haute sur l’écran ; l’enfant peut activer l’audio ou lire le texte seul, avec des zones tactiles larges et une hiérarchie visuelle adaptée au jeune public (§6.2, §6.3).

### 6.1 Simplicité d’usage

L’interface respecte plusieurs principes :

- Peu d’actions simultanées à l’écran
- Boutons larges et explicites
- Icônes universelles (cœur, flèches)


### 6.2 Adaptation au jeune public

- Couleurs douces
- Formes arrondies
- Espacements généreux
- Texte centré et lisible
- Normalisation des pages


### 6.3 Accessibilité

- Contraste suffisant entre texte et fond
- Boutons bien identifiables
- Structure claire et répétitive
- Indicateur de progression visuel


### 6.4 Parcours utilisateur fluide

Parcours type :

Connexion  
→ Catalogue  
→ Choix d’une histoire  
→ Lecture page par page  
→ Reprise automatique au dernier point  

Aucun écran inutile ou complexe n’est introduit.

### 6.5 Feedback utilisateur

- Animation légère lors de l’ajout en favori
- Changement visuel du bouton audio lors de la lecture
- Indication claire de la page en cours
- Conservation automatique de la progression

---

## 7. Conclusion du jalon 2

À l’issue de ce jalon :

- L’architecture visuelle est définie
- La navigation est validée
- La charte graphique est formalisée
- Les maquettes haute fidélité illustrent le rendu final
- Les choix UX sont argumentés

Tous les éléments nécessaires au démarrage du développement front-end sont désormais clarifiés.
Les choix seront validés avant développement afin de limiter les retours arrière et garantir une cohérence entre conception graphique et implémentation technique.