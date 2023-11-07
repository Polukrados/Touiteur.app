# Exercice 4 :

Pour vérifier si une relation universelle pour Touiteur vérifie la Troisième Forme Normale (3FN), nous devons examiner si la relation remplit les conditions suivantes :

1. La relation doit être en Seconde Forme Normale (2FN), ce qui signifie qu'elle ne doit pas contenir de dépendances partielles d'aucun attribut sur une clé candidate.
2. La relation doit être en Première Forme Normale (1FN), ce qui signifie que tous les attributs doivent être atomiques (non divisibles).
3. Il ne doit pas y avoir de dépendances transitives, c'est-à-dire qu'aucun attribut non-clé ne doit dépendre d'autres attributs non-clés.

Pour la relation universelle de Touiteur, nous supposons qu'elle contient tous les attributs des tables séparées que vous avez mentionnées précédemment dans la conversation.
Pour qu'une telle relation universelle soit en 3FN, chaque attribut non-clé devrait dépendre uniquement de la clé primaire de la relation universelle et non d'une combinaison de clés ou d'autres attributs non-clés.

Voici un résumé de la vérification :

- **En 1FN :** Si la relation universelle contient uniquement des attributs atomiques, alors elle est en 1FN.
- **En 2FN :** Si tous les attributs non-clé dépendent de la totalité de la clé primaire (et non d'une partie de celle-ci) dans le cas d'une clé composite, alors la relation est en 2FN.
- **En 3FN :** Si en plus de la 1FN et 2FN, aucun attribut non-clé ne dépend d'un autre attribut non-clé, alors la relation est en 3FN.

Cependant, dans une relation universelle, il est courant d'avoir des dépendances transitives, surtout quand des tables normalisées sont dénormalisées en une seule.
Pour les données de Touiteur, comme vous avez des tables séparées pour `utilisateurs`, `touites`, `tags`, etc., les dépendances fonctionnelles sont probablement nombreuses et complexes.
Par exemple, le `utilisateurID` dans la table `touites` dépend de `utilisateurID` dans la table `utilisateurs`.
Si ces deux tables sont combinées, cette dépendance fonctionnelle peut introduire une dépendance transitive, violant ainsi la 3FN dans une relation universelle.

En conclusion, il est peu probable qu'une relation universelle pour Touiteur soit en 3FN, car la normalisation tend à diviser les données en tables plus petites pour éliminer les dépendances fonctionnelles et transitives.
Pour affirmer catégoriquement si la relation universelle est en 3FN, il faudrait analyser toutes les dépendances fonctionnelles dans l'ensemble des données.

# Exercice 5 :

**Détails des clés pour chaque table et justifions la 3FN pour chacune :**

### Table `utilisateurs`
**Clé primaire :** utilisateurID
- `nom`, `prenom`, `email`, `mdp` sont des attributs qui dépendent de `utilisateurID`.

**Justification pour la 3FN :**
- Chaque attribut non-clé (`nom`, `prenom`, `email`, `mdp`) est dépendant directement de la clé primaire `utilisateurID`, et il n'y a pas de dépendance entre ces attributs non-clés.
- Il n'existe pas de dépendances fonctionnelles transitives impliquant des attributs non-primes.
- Par conséquent, la table `utilisateurs` est conforme à la 3FN.

### Table `abonnementtags`
**Clé primaire :** (utilisateurID, tagID)
- `utilisateurID` fait référence à un utilisateur.
- `tagID` fait référence à un tag.

**Justification pour la 3FN :**
- Tous les attributs sont des clés (soit partie de la clé primaire composée), donc il n'y a pas d'attribut non-clé.
- Il n'y a pas de dépendance fonctionnelle entre les attributs non-clés, car il n'y a pas d'attributs non-clés.
- La table est en 3FN car elle répond aux critères établis.

### Table `evaluations`
**Clé primaire :** (touiteID, utilisateurID)
- `touiteID` fait référence à un touite (message).
- `utilisateurID` fait référence à un utilisateur qui a donné la note.
- `note` est la note donnée par l'utilisateur au touite.

**Justification pour la 3FN :**
- La `note` dépend fonctionnellement et uniquement de la clé primaire composée (touiteID, utilisateurID).
- Il n'y a pas de dépendance fonctionnelle transitive puisque `note` est dépendant directement de la clé primaire.
- La table `evaluations` respecte donc les conditions de la 3FN.

### Table `images`
**Clé primaire :** imageID
- `description` et `cheminFichier` sont fonctionnellement dépendants de `imageID`.

**Justification pour la 3FN :**
- Les attributs `description` et `cheminFichier` dépendent directement de la clé primaire `imageID`.
- Il n'existe pas de dépendances fonctionnelles entre les attributs non-clés.
- Ainsi, la table `images` est en conformité avec la 3FN.

### Table `suivi`
**Clé primaire :** (followerID, followedID)
- `followerID` est l'ID de l'utilisateur qui suit.
- `followedID` est l'ID de l'utilisateur qui est suivi.

**Justification pour la 3FN :**
- Chaque attribut fait partie de la clé primaire composée et il n'y a pas d'attributs non-clés.
- La table est automatiquement en 3FN, car il n'y a pas d'attributs non-clés et donc pas de dépendances fonctionnelles transitive à évaluer.

### Table `tags`
**Clé primaire :** tagID
- `libelle` et `description` dépendent de `tagID`.

**Justification pour la 3FN :**
- Il n'y a pas de dépendance fonctionnelle entre les attributs non-clés `libelle` et `description`.
- Ces attributs dépendent directement de la clé primaire `tagID`.
- La table `tags` est donc en 3FN.

### Table `touites`
**Clé primaire :** touiteID
- `texte`, `utilisateurID` et `datePublication` dépendent de `touiteID`.

**Justification pour la 3FN :**
- La clé primaire `touiteID` est la seule source de dépendance pour les autres attributs.
- Il n'existe pas de dépendance fonctionnelle transitive car `utilisateurID` peut avoir plusieurs `touiteID` associés.
- La table `touites` est conforme à la 3FN.

### Table `touitesimages`
**Clé primaire :** (TouiteID, ImageID)
- Cette association représente une relation de plusieurs-à-plusieurs entre les touites et les images.

**Justification pour la 3FN :**
- Il n'y a pas d'attribut non-prime dans cette table.
- La table est en 3FN car il n'y a pas de dépendances fonctionnelles transitives à considérer.

### Table `touitestags`
**Clé primaire :** (TouiteID, TagID)
- Cette table forme une relation de plusieurs-à-plusieurs entre les touites et les tags.

**Justification pour la 3FN :**
- Absence d'attributs non-prime à évaluer pour les dépendances fonctionnelles.
- La table est en 3FN car il n'y a pas de dépendances fonctionnelles transitives à considérer.

### Table `touitesutilisateurs`
**Clé primaire :** (UtilisateurID)
- Cette table forme une relation entre les touites et les utilisateurs.

**Justification pour la 3FN :**
- Absence d'attributs non-prime à évaluer pour les dépendances fonctionnelles.
- La table est en 3FN car il n'y a pas de dépendances fonctionnelles transitives à considérer.

Chaque table semble respecter les règles de la 3FN individuellement.
Les attributs non-clés dépendent directement des clés primaires, et il n'y a pas de dépendances fonctionnelles transitives.
Donc, selon la structure de la base de données fournie, chaque table est en 3FN.