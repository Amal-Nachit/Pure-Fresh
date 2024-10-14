# Pure Fresh

Pure Fresh est une application web de gestion d'annonces, permettant aux utilisateurs de publier des annoonces, passer des commandes. Ce projet utilise Symfony pour le backend et Twig pour le rendu des templates.

## Table des matières

- [Fonctionnalités](#fonctionnalités)
- [Technologies utilisées](#technologies-utilisées)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Contributions](#contributions)
- [License](#license)

## Fonctionnalités

- Gestion des utilisateurs avec différents rôles (admin, utilisateur)
- Publication d'annonces par les utilisateurs
- Interface d'administration pour approuver ou refuser les annonces
- Notifications en temps réel sur le statut des annonces
- Suppression logique des annonces refusées

## Technologies utilisées

- **Symfony** : Framework PHP pour le développement backend
- **Twig** : Moteur de templates pour le rendu HTML
- **MySQL** : Système de gestion de base de données
- **JavaScript** : Langage de programmation pour l'interactivité côté client
- **Tailwind CSS** : Framework CSS pour le design réactif

## Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- PHP (version 8.0 ou supérieure)
- Composer
- MySQL ou MariaDB
- Node.js et npm (pour la gestion des dépendances front-end)

## Installation

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/Amal-Nachit/Pure-Fresh.git
   cd pure-fresh
   ```

2. Installez les dépendances PHP :
   ```bash
   composer install
   ```
   
3. Installez les dépendances JavaScript :
   ```bash
   npm install
   ```

4. Créez une base de données et configurez votre fichier .env :
   ```bash
   DATABASE_URL=mysql://username:password@127.0.0.1:3306/purefresh
   ```

5. Exécutez les migrations :
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

6. Lancez le serveur Symfony :
   ```bash
   symfony serve
   ```
