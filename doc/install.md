# Installation

## Démarrage rapide avec Docker (recommandé)

### Prérequis

* [Docker](https://docs.docker.com/get-docker/)
* [Docker Compose](https://docs.docker.com/compose/install/)

### Installation en 3 étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/elefan-grenoble/gestion-compte.git
cd gestion-compte

# 2. Construire les conteneurs
docker compose build

# 3. Lancer les conteneurs
docker compose up -d
```

### Accès à l'application

Ajouter `127.0.0.1 membres.yourcoop.local` au fichier `/etc/hosts` :

```bash
echo "127.0.0.1 membres.yourcoop.local" | sudo tee -a /etc/hosts
```

L'application est accessible sur :

* **Application** : [http://membres.yourcoop.local:8000](http://membres.yourcoop.local:8000)
* **phpMyAdmin** : [http://localhost:8081](http://localhost:8081)

### Connexion

Créer l'utilisateur super admin en visitant :
[http://membres.yourcoop.local:8000/user/install_admin](http://membres.yourcoop.local:8000/user/install_admin)

Ou se connecter avec les identifiants par défaut (si les fixtures ont été chargées) :

* **Login** : `admin`
* **Mot de passe** : `password`

### Charger des données de test (optionnel)

```bash
# Charger toutes les fixtures
docker compose exec php bin/console doctrine:fixtures:load -n

# Ou charger uniquement les périodes (sans les shifts)
docker compose exec php bin/console doctrine:fixtures:load -n --group=period
```

### Commandes utiles

```bash
# Voir les logs
docker compose logs -f php

# Arrêter les conteneurs
docker compose down

# Redémarrer les conteneurs
docker compose restart

# Accéder au conteneur PHP
docker compose exec php bash

# Vider le cache
docker compose exec php bin/console cache:clear
```

### Importer un dump de base de données

```bash
# Supprimer la base existante et la recréer
docker compose exec database mariadb -uroot -psecret -e 'DROP DATABASE IF EXISTS symfony; CREATE DATABASE IF NOT EXISTS symfony;'

# Importer le dump
docker compose exec database mariadb -uroot -psecret symfony < votre_dump.sql

# Ou via phpMyAdmin : http://localhost:8081
```

---

## Alternative : Utilisation avec Nix

Vous pouvez obtenir toutes les dépendances du projet en utilisant [Nix](https://nixos.org/download.html). Une fois installé lancez `nix develop --impure` et tous les outils nécessaires sont dans votre `PATH` à la bonne version, comme déclaré dans [flake.nix](../flake.nix).

Cela peut se faire automatiquement quand vous `cd` dans le répertoire si vous avez installé [direnv](https://direnv.net/).

```bash
# Lancer l'instance mariadb de test
devenv up

# Lancer l'application
php bin/console server:run '*:8000'
```

---

## Installation sur un serveur (production)

### Prérequis

* PHP (version 7.2 et supérieure)
* [Composer](https://getcomposer.org/)
* Mysql (ou mariadb)
* php-mysql (ou php-pdo_mysql)
* php-xml
* php-gd

### Installation

Clone code

```bash
git clone https://github.com/elefan-grenoble/gestion-compte.git
cd gestion-compte
```

Lancer la configuration

```bash
composer install
```

Creer la base de donnée

```shell
php bin/console doctrine:database:create
```

Migrer : creation du schema

```shell
php bin/console doctrine:migration:migrate
```

Installer les medias

```shell
php bin/console assetic:dump
```

Lancer le serveur (si pas de serveur web)

```shell
php bin/console server:start
```

Attention, par défaut ce serveur n'est pas accessible depuis l'extérieur vu qu'il écoute en local seulement (127.0.0.1).
Pour le rendre accessible, il faut utiliser la commande suivante :

```shell
php bin/console server:start *:8080
```

Pour un usage en production, il est très fortement recommandé d'utiliser un vrai serveur Web tel que Apache ou Nginx.

Ajouter ``127.0.0.1 membres.yourcoop.local`` au fichier _/etc/hosts_.

Visiter [http://membres.yourcoop.local/user/install_admin](http://membres.yourcoop.local/user/install_admin) pour créer l'utilisateur super admin (valeurs par défaut : admin:password)

## Autres

### En Prod

Avec nginx, ligne necessaire pour avoir les images dynamiques de qr et barecode (au lieu de 404)

```
location ~* ^/sw/(.*)/(qr|br)\.png$ {
 rewrite ^/sw/(.*)/(qr|br)\.png$ /app.php/sw/$1/$2.png last;
}
```

### crontab

```
# generate shifts in 27 days (same weekday as yesterday)
55 5 * * * php YOUR_INSTALL_DIR_ABSOLUTE_PATH/bin/console app:shift:generate $(date -d "+27 days" +\%Y-\%m-\%d)

# free pre-booked shifts
55 5 * * * php YOUR_INSTALL_DIR_ABSOLUT_PATH/bin/console app:shift:free $(date -d "+21 days" +\%Y-\%m-\%d)

# send reminder 2 days before shift
0 6 * * * php YOUR_INSTALL_DIR_ABSOLUT_PATH/bin/console app:shift:reminder $(date -d "+2 days" +\%Y-\%m-\%d)

# execute routine for cycle_end/cycle_start, everyday
5 6 * * * php YOUR_INSTALL_DIR_ABSOLUT_PATH/bin/console app:user:cycle_start

# send alert on shifts booking (low)
0 10 * * * php YOUR_INSTALL_DIR_ABSOLUT_PATH/bin/console app:shift:send_alerts $(date -d "+2 days" +\%Y-\%m-\%d) 1

# send a reminder mail to the user who generate the last code but did not validate the change.
45 21 * * * php YOUR_INSTALL_DIR_ABSOLUT_PATH/bin/console app:code:verify_change --last_run 24
```

### Mise en route

* Suivez le [guide de mise en route](start.md)
