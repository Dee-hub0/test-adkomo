# Car Reservation 🛫

### Prérequis:

- PHP 8.2.0

- Symfony 7.1.5

### Instructions

- Colnez le projet 'https://github.com/Dee-hub0/test-adkomo.git'

- Depuis le terminal, se positionner sur le dossier et, executez la commande suivante pour créer la BD

  `php bin/console doctrine:database:create`

  `php bin/console make:migration`

  `php bin/console doctrine:migrations:migrate`

- Pour charger des données initiales dans la base de données

  `php bin/console doctrine:fixtures:load`

- Démarrez le serveur de développement Back-end :
  `symfony serve`

### Tests (en erreur)

- Vous pouvez lancer et excecuter les tests unitaires de l'application en utilisant la commande suivante :

`php bin/phpunit`

### Tests API Postman

- Le fichier "test-adkomo.postman_collection" est une exportation d'une collection Postman qui contient l'ensemble des tests des endpoints de l'API (GET, PUT, POST et DELETE).
