# Espace adhérent super marché coopératifs

Application symfony pour la gestion d'une épicerie ou d'un super marché coopératif.

Ce code est à l'initiative de [l'éléfan](https://lelefan.org/), projet grenoblois de super marché coopératif.<br />
Il est open source, sous licence GPLv3.

Cet outil est utilisé par plus d'une dizaine d'autres coopératives en France.

## Démarrage rapide avec Docker

```bash
# 1. Cloner le dépôt
git clone https://github.com/elefan-grenoble/gestion-compte.git
cd gestion-compte

# 2. Construire et lancer les conteneurs
docker compose build
docker compose up -d

# 3. (Optionnel) Charger des données de test
docker compose exec php bin/console doctrine:fixtures:load -n
```

Ajouter `127.0.0.1 membres.yourcoop.local` au fichier `/etc/hosts`.

L'application est accessible sur [http://membres.yourcoop.local:8000](http://membres.yourcoop.local:8000)

**Connexion admin :** `admin` / `password`

## Captures d'écran

_cliquez pour voir l'image en grand_

|Page d'accueil (anonyme)|Page d'accueil (membre)|Page d'administration|
|---|---|---|
|![home_anon](doc/images/20121105_homepage_anon.png)|![home](doc/images/20121105_homepage_raphael.png)|![admin](doc/images/20121105_homepage_admin.png)|

## Projet

* [Liste des Issues](https://github.com/elefan-grenoble/gestion-compte/issues) (n'hésitez pas à en créer une pour lancer la discussion !)
* [Board Kanban](https://github.com/elefan-grenoble/gestion-compte/projects/5)

## Documentation

* [Guide d'installation](doc/install.md)
* [Guide de mise à jour](doc/maj.md)
* [Guide de mise en route](doc/start.md)
* [Guide du développeur](doc/dev.md)

## Stack technique

* PHP 7.4
* Symfony 3.4
* jQuery 3.6
* Materialize 1.2.1
* MySQL/MariaDB

## Liste des fonctionnalités

Voir la documentation sur le [wiki](https://github.com/elefan-grenoble/gestion-compte/wiki)
