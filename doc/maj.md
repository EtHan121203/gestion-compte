# Always up-to-date !

## Etape 1 : Récupérer le code

### Vous avez installé avec git

1. Déplacement à la racine du projet

```shell
cd /var/www/html/gestion-compte
```

2. Récupérer la dernière version

```shell
git pull
```

3. Servez vous à boire.

### vous n'avez pas installé avec git (ex via ftp)

1. Téléchargez la dernière version de l'application via https://github.com/quot17/gestion-compte
2. Déplacez l'intégralité du code téléchargé sur votre serveur afin d'écraser les anciens fichiers
3. Connectez-vous en ligne de commande sur votre serveur et déplacez vous dans le dossier

```shell
cd /var/www/html/gestion-compte
```

## Etape 2 : Finaliser la mise à jour

1. Exécution de l'installation des dépendances

```shell
composer install
```

2. Mise à jour de la base de données

```shell
php bin/console doctrine:migrations:migrate
```

3. Installer les nouveaux media

```shell
php bin/console assetic:dump
```

4. Vider le cache de production

```shell
php bin/console cache:clear --env=prod
```

**Voilà ! Votre application est maintenant à jour**
