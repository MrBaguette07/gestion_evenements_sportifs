# Application de Gestion des Réservations et des Services

## Description

Cette application Symfony permet aux utilisateurs de réserver des créneaux pour différents services, tandis que les administrateurs peuvent gérer les services et les réservations. L'accès aux fonctionnalités de gestion est restreint aux utilisateurs ayant le rôle `ROLE_ADMIN`.

## Fonctionnalités

### Utilisateurs
- **Inscription et Connexion** : Les utilisateurs peuvent s'inscrire et se connecter pour accéder à leurs réservations.
  - **URL d'inscription** : `/register`
  - **URL de connexion** : `/login`
- **Réservation de Créneaux** : Les utilisateurs connectés peuvent réserver des créneaux pour des services disponibles.
  - **URL de réservation** : `/booking/new`
- **Historique de Réservations** : Les utilisateurs peuvent consulter et gérer leurs réservations.
  - **URL des réservations utilisateur** : `/booking`

### Administrateurs
- **Gestion des Services** : Les administrateurs peuvent ajouter, modifier et supprimer des services.
  - **URL de la liste des services** : `/services`
  - **URL pour ajouter un service** : `/services/new`
  - **URL pour modifier un service** : `/services/edit/{id}`
  - **URL pour supprimer un service** : `/services/delete/{id}`
- **Gestion des Réservations** : Les administrateurs peuvent consulter toutes les réservations et les confirmer ou les annuler.
  - **URL de gestion des réservations** : `/admin/bookings`

## Prérequis

- **PHP 8.1** ou supérieur
- **Symfony CLI**
- **Composer**
- **MySQL** ou un autre système de gestion de base de données compatible
- **Node.js et npm** (pour gérer les assets front-end, si nécessaire)

## Installation

1. **Cloner le dépôt** :
    ```bash
    git clone https://github.com/votre_nom/votre_projet.git
    cd votre_projet
    ```

2. **Installer les dépendances** :
    ```bash
    composer install
    ```

3. **Configurer la base de données** :
    - Copiez le fichier `.env` en `.env.local` :
      ```bash
      cp .env .env.local
      ```
    - Dans `.env.local`, configurez la connexion à votre base de données, par exemple :
      ```dotenv
      DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/nom_de_la_base"
      ```

4. **Créer la base de données et les tables** :
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```

5. **Charger les données de test (facultatif)** :
    ```bash
    php bin/console doctrine:fixtures:load
    ```

6. **Installer les assets front-end** (si nécessaire) :
    ```bash
    npm install
    npm run dev
    ```

## Utilisation

### Lancer le serveur de développement

Pour démarrer l'application, utilisez la commande suivante :
```bash
symfony serve
```