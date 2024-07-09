Repo pour le projet ECF Studi été 2024

[Tests end to end](https://github.com/Wael-R/ecf-arcadia-tests)

[Charte graphique](documents/charte_graphique.pdf)\
[Documentation technique](documents/documentation_technique.pdf)\
[Gestion de projet](documents/gestion_projet.pdf)\
[Manuel d'utilisation](documents/manuel.pdf)

[Instructions en anglais ici](README.md)

**Ce projet utilise des fichiers .htaccess pour empêcher l'accès non autorisé au dossiers spécifiques au serveur**\
**Il faut utiliser soit un serveur web apache (comme vu ci dessous avec WAMP) ou manuellement exclure les dossiers `server/` et `components/` de l'accès public**

# Déployer localement sur Windows avec WAMP

## Installer WampServer
[Téléchargez et installez WampServer](https://wampserver.aviatechno.net)

Lancez WampServer

**Ce projet est fait pour, et testé avec PHP 8.2, donc veillez à ce que le serveur tourne bien une version >= 8.2.0:**\
Certaines fonctionnalités PHP nécessaires pour ce projet ne sont pas disponibles dans les anciennes versions\
Cliquez sur l'icône WAMP dans la zone de notifications et selectionnez `PHP > Version > 8.2.0` (ou une version plus récente)

Dans le dossier www de votre installation wamp, créez un nouveau dossier pour le projet

Ouvrez votre navigateur et allez sur `localhost/`\
Sous `Your Projects` (Vos Projets), votre nouveau dossier devrait apparaitre.

Cliquez sur `Add a Virtual Host` (Ajouter un Hôte Virtuel) et inserez-y le nom de votre hôte et le chemin complet vers le nouveau dossier

Faites un clic droit sur l'icône de WAMP et selectionnez `Tools > Restart DNS`

Vous pouvez maintenant accéder à votre hôte en utilisant son nom au lieu de `localhost` dans la barre d'addresse du navigateur\
Un projet vide devrait afficher la page par défaut `Index of /`.

Retournez sur localhost et allez sur PhpMyAdmin

Par défaut, l'utilisateur root de MySQL n'a pas de mot de passe\
Connectez vous en tant que `root`.

Sous 'general settings', selectionnez `Change password` (Modifier le mot de passe)\
Entrez un nouveau mot de passe. Souvenez vous du mot de passe, car vous en aurez besoin plus tard.

## Installer MongoDB
[Téléchargez MongoDB en zip](https://www.mongodb.com/try/download/community)

Extrayez le fichier zip n'importe où (vu que mongodb ne fait pas partie de wamp, il doit être installé séparément)

Créez les dossiers `data/` et `logs/` dans votre installation mongodb, ainsi qu'un fichier de configuration `mongodb.conf`

Dans le fichier de configuration, ajoutez ces lignes:
```
dbpath=<VOTRE INSTALLATION MONGODB>/data/
logpath=<VOTRE INSTALLATION MONGODB>/logs/mongodb.log

bind_ip=127.0.0.1
port=27017
```
Vous pouvez changer le port si besoin

Ouvrez une ligne de commande **en tant qu'administrateur**:
- Effectuez un `cd` vers votre installation mongodb
- Executez `mongod.exe --install --config <FICHIER CONFIG MONGODB>`

Ouvrez services.msc puis cherchez et démarrez le service MongoDB

## Installer le driver PHP MongoDB
[Téléchargez le driver PHP MongoDB](https://github.com/mongodb/mongo-php-driver/releases/)\
Choisissez la version thread safe (avec `-ts-` dans le nom) qui correspond à votre version php.

Extrayez le fichier `php_mongodb.dll` dans le dossier `ext` de votre version php:\
`C:/wamp64/bin/php/php<VERSION>/ext/`

De retour dans le dossier php, ouvrez `phpForApache.ini` (PAS `php.ini`! celui ci n'est utilisé qu'en mode ligne de commande)\
Défilez vers la partie où les extensions sont définies (ctrl+f `extension=`), et ajoutez `extension=php_mongodb.dll`

Une fois fini, si WAMP est toujours ouvert, cliquez sur son icône et choisissez `Restart all services` (Redémarrer tout les services)

## Cloner le projet
Clonez le projet dans le dossier de votre hôte virtuel:
- avec git bash:
	- `cd C:/wamp64/www/` (ou le chemin vers le dossier www de votre installation wamp)
	- `git clone https://github.com/Wael-R/form-ecf-arcadia <LE NOM DE VOTRE HOTE>`
- avec un client git graphique
- ou en téléchargeant le projet en tant que zip et en l'extrayant dans le dossier de votre hôte

## Installer les dépendances
Ce projet dépend des librairies MongoDB et PHPMailer.

[Téléchargez et installez Composer](https://getcomposer.org/download/)

Ouvrez une ligne de commande:
- Effectuez un `cd` vers le dossier de votre projet
- Executez `composer install`

Ceci va automatiquement installer les librairies nécessaires, telles qu'elles sont définies dans `composer.json`.

## Préparer le projet
Ouvrez le dossier `server/` et executez `run-setup.bat`

Suivez les instructions à l'écran pour créer le fichier de configuration et la base de données\
Entre parenthèses sont les valeurs par défaut de chaque option. Appuyez sur entrer pour les utiliser telles quelles.\
Si vous avez changé le mot de passe de l'utilisateur MySQL, vous en aurez besoin ici.\
Si vous ne pouvez pas vous connecter à votre base de données MySQL, retournez sur PhpMyAdmin et vérifiez que vous utilisez le bon port en haut de l'écran.\
Si aucun serveur SMTP n'est défini, l'application ne pourra pas envoyer d'e-mails, ce qui rendra le formulaire de contact inutilisable.

Une fois fini, ouvrez PhpMyAdmin et selectionnez `Import` en haut\
Glissez le fichier `server/data.sql` dans le cadre `Choose File`, défilez vers le bas et cliquez sur Import.

Votre application devrait maintenant être prête!\
Vous pouvez accéder au dashboard administrateur en vous connectant avec le login et mot de passe que vous avez utilisé précédemment dans la création de base de données.

Pour les tests, la verification CSRF peut être désactivée en ouvrant `server/config.json` et remplaceant la valeur de `csrfChecks` par `false`\
En production, cette option doit toujours être `true`!