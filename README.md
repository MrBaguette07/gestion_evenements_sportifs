# Gestion d'événements sportifs

Ce projet Symfony 7 permet de gérer des événements sportifs, de leur création à l'inscription des participants.

## Fonctionnalités principales
- Liste des événements disponibles.
- Détails d'un événement et des participants associés.
- Ajout de nouveaux événements avec validation des champs.
- Inscription des participants avec vérifications (email unique par événement).
- Calcul de distances entre deux points géographiques (formule de Haversine).

---

## Installation et démarrage

### Prérequis
- PHP ≥ 8.1
- Composer
- MySQL ou un autre système de gestion de base de données
- Symfony CLI (optionnel mais recommandé)

### Installation
1. Clonez le dépôt :
   ```bash
   git clone https://github.com/MrBaguette07/gestion_evenements_sportifs.git
   cd gestion_evenements_sportifs
   ```

2. Installez les dépendances :
   ```bash
   composer install
   ```

3. Configurez la base de données dans le fichier `.env` :
   ```dotenv
   DATABASE_URL="mysql://root:@127.0.0.1:3306/gestion_evenements"
   ```

4. Créez la base de données et appliquez les migrations :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. Lancez le serveur Symfony :
   ```bash
   symfony server:start
   ```

6. Accédez à l'application dans votre navigateur :
http://localhost:8000/

---

## Routes principales

| Route                          | Description                          |
|--------------------------------|--------------------------------------|
| `/`                            | Page d'accueil                      |
| `/events`                      | Liste des événements              |
| `/events/new`                  | Créer un événement              |
| `/events/{id}`                 | Détails d'un événement          |
| `/events/{eventId}/participants/new` | Ajouter un participant            |
| `/events/{id}/distance`        | Calculer la distance à un événement |

---

## Fonctionnement

### **1. Création d'un événement**
- Remplissez les champs requis : nom, date, lieu.
- Validation :
  - La date doit être dans le futur.

### **2. Liste des événements**
- Affiche tous les événements existants.
- Actions possibles : voir les détails ou ajouter des participants.

### **3. Inscription d'un participant**
- Fournissez un nom et une adresse email.
- Validation :
  - L'email doit être valide et unique pour l'événement.

### **4. Calcul de distance**
- Fournissez la latitude et longitude de l'utilisateur dans l'URL.
- Affiche la distance entre l'utilisateur et le lieu de l'événement.

---

## Contributeurs
- [Gauthier CORION](https://github.com/MrBaguette07)

## Licence
Ce projet est sous licence MIT.

