# Symfony 7.1 Boilerplate

Attention :

Windows :
- Il vous faut Docker Desktop et WSL2 pour lancer le projet sous Windows.
- Pour installer wsl lancer la commande `wsl --install` dans un terminal powershell en tant qu'administrateur.
- Pour installer Docker Desktop, télécharger le logiciel sur le site officiel de Docker.

Linux (Uniquement pour les utilisateurs de Linux pas pour WSL) :
- Il vous faut Docker et Docker-compose.
- Pour installer Docker et Docker-compose https://docs.docker.com/engine/install/ubuntu/#install-using-the-repository

## Stack technique

- PHP 8.3
- Symfony 7.1
- MySQL
- Docker
- Bootstrap (déjà intégré dans le projet)

## Initialisation de votre IDE

### PHPStorm

1. Ouvrir le projet dans PHPStorm
2. Installer les extensions Twig et Symfony
   - Aller dans File > Settings > Plugins
   - Installer les extensions (Twig, EA Inspection, PHP Annotations, .env files support)

### Visual Studio Code

1. Ouvrir le projet dans Visual Studio Code
2. Installer les extensions pour PHP, Twig et Symfony
   - Aller dans l'onglet Extensions
   - Installer les extensions (whatwedo.twig, TheNouillet.symfony-vscode, DEVSENSE.phptools-vscode,
     bmewburn.vscode-intelephense-client, zobo.php-intellisense)

## Installation en local

1. Cloner le projet sur votre machine et de préférence dans WSL2 si vous êtes sous Windows
2. Dans votre terminal lancer la commande `docker compose up -d`
3. Vérifier que les containers sont bien lancés avec la commande `docker container ps` vous devriez voir 4 containers
4. Pour accéder à la base de données `docker exec -it symfony_db mysql -u root -p`
5. Pour accéder à l'application `docker exec -it symfony_php bash` puis `composer install`
6. Toutes les commandes Symfony sont à lancer dans le container PHP
7. Vous pouvez accéder à l'application sur `localhost:8081` dans votre navigateur (si vous avez un autre service qui tourne sur le port 8081, vous pouvez changer le port dans le fichier `docker-compose.yml`)
8. Pour utiliser tailwind et recompiler le css à chaque changement au css `php bin/console tailwind:build --watch --poll`

## Utilisation

- N'hésitez pas à consulter la documentation de Symfony pour plus d'informations sur l'utilisation du framework : https://symfony.com/doc/current/index.html

- Notez comment fonctionne votre projet dans le fichier README.md et mettez à jour ce fichier au fur et à mesure de l'avancement de votre projet pour aider les autres développeurs à comprendre comment fonctionne votre projet.
