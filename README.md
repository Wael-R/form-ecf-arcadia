Repo pour le projet ECF Studi été 2024

[Tests end to end](https://github.com/Wael-R/ecf-arcadia-tests)

[Charte graphique](documents/charte_graphique.pdf)\
[Documentation technique](documents/documentation_technique.pdf)\
[Gestion de projet](documents/gestion_projet.pdf)\
[Manuel d'utilisation](documents/manuel.pdf)

[Instructions en français ici](README_FR.md)

**This project uses .htaccess files to prevent unauthorized access to server specific folders**\
**Either use an apache web server (as detailed below with WAMP) or manually exclude the `server/` and `components/` folders from public access**

# Deploy locally on Windows with WAMP

## Set up WampServer
[Download and install WampServer](https://wampserver.aviatechno.net)

Start WampServer

**This project was made for and tested on PHP 8.2, make sure your server is running a version >= 8.2.0:**\
Some PHP features required for this project may not be available in older versions\
Click the tray icon for WAMP and select `PHP > Version > 8.2.0` (or later)

In your wamp install's www folder, create a new folder for the project

Open your browser and go to `localhost/`\
Under `Your Projects`, there should be your new folder.

Click on `Add a Virtual Host` and input your virtual host's name and the full path to the new folder

Right click WAMP's tray icon, select `Tools > Restart DNS`

You can now access your virtual host using its name instead of `localhost` in your browser's address bar\
An empty project should display the default `Index of /` page.

Return to localhost and go to PhpMyAdmin

By default, the root MySQL user does not have a password\
Log in as `root`.

Under general settings, select `Change password`\
Enter a new password. Make sure to write it down elsewhere at least temporarily as you'll need it later.

## Set up MongoDB
[Download MongoDB as a zip](https://www.mongodb.com/try/download/community)

Extract the downloaded zip anywhere (since mongodb isn't built into wamp, it has to be managed separately)

Create the `data/` and `logs/` folders inside your mongodb install, as well as a `mongodb.conf` config file

Inside the config file, add these lines:
```
dbpath=<YOUR MONGODB INSTALL>/data/
logpath=<YOUR MONGODB INSTALL>/logs/mongodb.log

bind_ip=127.0.0.1
port=27017
```
Optionally replace the bind port to suit your needs

Open a command prompt **as admin**:
- `cd` to your mongodb install
- run `mongod.exe --install --config <MONGODB CONFIG PATH>`

Open services.msc then find and start the MongoDB service

## Set up the MongoDB PHP driver
[Download the MongoDB PHP driver](https://github.com/mongodb/mongo-php-driver/releases/)\
Pick the thread safe build (`-ts-` in the name) that matches your php version.

Extract the `php_mongodb.dll` file inside your php version's `ext` folder:\
`C:/wamp64/bin/php/php<VERSION>/ext/`

Back in the php folder, open `phpForApache.ini` (NOT `php.ini`! that one is only used in CLI mode)\
Scroll down to where extensions are defined (ctrl+f `extension=`), and add `extension=php_mongodb.dll`

Once complete, if WAMP is still running, click the tray icon and restart all services

## Clone the project
Clone the project into your virtual host folder:
- with git bash:
	- `cd C:/wamp64/www/` (or your wamp install's www folder)
	- `git clone https://github.com/Wael-R/form-ecf-arcadia <YOUR VIRTUALHOST NAME>`
- through a git gui client
- or by downloading the project as a zip and extracting it into your virtual host folder

## Install dependencies
This project depends on the MongoDB and PHPMailer libraries.

[Download and install Composer](https://getcomposer.org/download/)

Open a command prompt:
- `cd` to the project folder
- run `composer install`

This will automatically install the required libraries, as defined in `composer.json`.

## Set up the project
Open the `server/` folder and run `run-setup.bat`

Follow the on-screen instructions to create your config file and database\
In parentheses are the default values for each setting. Press enter to use them as is.\
If you changed your MySQL user password, you'll need it here.\
If you can't connect to your MySQL database, go back to PhpMyAdmin and check that you're using the right port at the top of the screen.\
If no SMTP server is set, the app will not be able to send emails, which will render the contact form unusable.

Once complete, open PhpMyAdmin and select `Import` at the top\
Drop the `server/data.sql` file into the `Choose File` prompt, scroll down and click import.

Your app should now be set up!\
You can access the admin dashboard by logging in using the credentials you provided earlier in the setup script.

For testing purposes, CSRF checks can be disabled by opening `server/config.json` and setting `csrfChecks` to `false`\
In production, this setting should always be left on `true`!