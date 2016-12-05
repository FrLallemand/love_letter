#Love letter

## Composition du groupe.
Quentin Ladeveze, François Lallemand

## Frameworks utilisés :
	- Slim (Framework PHP)
	- Twig (Gestionnaire de templates)
	- Eloquent (ORM)
	- JQuery (Framework javascript)
	- Bootstrap (Framework css)

## Base de données utilisée :
La base de données utilisée est MySQL (MariaDB).
La base de données se crée en important le fichier love_letter/conf/love_letter.sql.
L'accès se configure dans le fichier love_letter/conf/database.conf.ini, en précisant le nom de la base de données, le nom d'utilisateurs et le mot de passe.

## Autres dépendances et installation:
Les dépendances sont gérées grâce à composer [site](https://getcomposer.org/).
Dans le dossier love_letter/src, executez la commande suivante :
  	`composer install`

L\'url rewrite doit être activé sur le serveur. La racine du site (index.php) se trouve dans love_letter/src/public.
