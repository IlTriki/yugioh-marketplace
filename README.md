# Symfony 7.1 Boilerplate

## Installation en local

1. Cloner le projet sur votre machine et de préférence dans WSL2 si vous êtes sous Windows
2. Dans votre terminal lancer la commande `docker compose up -d`
3. Pour accéder à l'application `docker exec -it symfony_php bash` puis `composer install`
4. Puisque j'utilise tailwind, je compile le css avec la commande `php bin/console tailwind:build`
5. Pour créer la base de données `php bin/console doctrine:database:create`
6. Pour créer les tables dans la base de données `php bin/console doctrine:schema:update --force`
7. Pour les migrations `php bin/console doctrine:migrations:diff`
8. Pour les appliquer `php bin/console doctrine:migrations:migrate`
9. Pour les fixtures `php bin/console doctrine:fixtures:load`
6. Toutes les commandes Symfony sont à lancer dans le container PHP
7. Vous pouvez accéder à l'application sur `localhost:8081` dans votre navigateur (si vous avez un autre service qui tourne sur le port 8081, vous pouvez changer le port dans le fichier `docker-compose.yml`)
