# Guide du développeur

## Démarrage rapide

```bash
# Cloner et démarrer
git clone https://github.com/elefan-grenoble/gestion-compte.git
cd gestion-compte
docker compose build
docker compose up -d

# Charger des données de test
docker compose exec php bin/console doctrine:fixtures:load -n
```

Ajouter `127.0.0.1 membres.yourcoop.local` au fichier `/etc/hosts`.

Application : [http://membres.yourcoop.local:8000](http://membres.yourcoop.local:8000)
Connexion : `admin` / `password`

## Modèle de données

![modele V2](https://yuml.me/66888c7d.png)
<http://yuml.me/edit/66888c7d>

## mailcatcher

Pour récupérer les mails envoyés (mode DEV)

* [mailcatcher.me](https://mailcatcher.me/)

```bash
sudo apt-get install ruby-dev libsqlite3-dev
gem install mailcatcher
mailcatcher
```

Si la dernière commande ne marche pas, vérifiez que vous avez le dossier des gem Ruby dans votre `PATH`. Plus de détails [ici](https://guides.rubygems.org/faqs/#user-install).

## Guides lines

* [GitFlow](https://www.grafikart.fr/formations/git/git-flow)

## Symfony

* [official doc](https://symfony.com/doc/current/index.html)

## Materialize

* [official doc](https://materializecss.com/)

## Docker

Un _docker-compose.yml_ existe pour permettre le développement sous Docker. Suivez le [guide d'installation](install.md).

### Commandes utiles

```bash
# Logs en temps réel
docker compose logs -f php

# Accéder au conteneur
docker compose exec php bash

# Vider le cache
docker compose exec php bin/console cache:clear

# Exécuter une commande Symfony
docker compose exec php bin/console <commande>
```

N'oubliez pas de définir la variable d'environnement `DEV_MODE_ENABLED` dans le container qui exécute le code de l'application.

## Nix

Vous pouvez obtenir toutes les dépendances du projet en utilisant [Nix](https://nixos.org/download.html). Une fois installé lancez `nix develop --impure` et tous les outils nécessaires sont dans votre `PATH` à la bonne version, comme déclaré dans [flake.nix](../flake.nix).
Cela peut se faire automatiquement quand vous `cd` dans le répertoire si vous avez installé [direnv](https://direnv.net/).

Pour lancer l'instance mariadb de test utilisez `devenv up`.
Pour lancer l'application, utilisez `php bin/console server:run '*:8000'`

## Tests

### Avec Docker (recommandé)

Prérequis : avoir le docker-compose qui tourne en local.

```bash
# Créer la base de données de test + initialiser avec le schema
docker compose exec php bin/console --env=test doctrine:database:create
docker compose exec php bin/console --env=test doctrine:schema:create

# Lancer les tests
docker compose exec php ./vendor/bin/phpunit
```

### Sans Docker

```bash
# Créer la base de données de test + initialiser avec le schema
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:schema:create

# Lancer les tests
php ./vendor/bin/phpunit
```
