<?php
// * database setup; creates required sql databases and default entries

if(php_sapi_name() != "cli")
{
	http_response_code(403);
	exit();
}

require_once("./auth.php");

echo("This will recreate all database tables and overwrite their data.\nType 'OK' to proceed...\n\n");

if(readline() != "OK")
	exit("\nCanceled database creation.\n\n");

$config = json_decode(file_get_contents(__DIR__ . "/config.json"));

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, null, $config->sql->port);

$req = "CREATE DATABASE IF NOT EXISTS arcadia;
USE arcadia;

DROP TABLE IF EXISTS accounts;
CREATE TABLE accounts (
	userId BINARY(16) NOT NULL UNIQUE,
	email VARCHAR(50) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	role ENUM('admin', 'employee', 'veterinarian') NOT NULL,
	PRIMARY KEY (userId)
);

DROP TABLE IF EXISTS sessions;
CREATE TABLE sessions (
	sessionId INT AUTO_INCREMENT,
	token BINARY(16) NOT NULL UNIQUE,
	userId BINARY(16) NOT NULL,
	ipAddress VARCHAR(45) NOT NULL,
	created DATETIME NOT NULL,
	PRIMARY KEY (sessionId),
	FOREIGN KEY (userId) REFERENCES accounts(userId) ON DELETE CASCADE
);

DROP TABLE IF EXISTS services;
CREATE TABLE services (
	serviceId INT AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL UNIQUE,
	description TEXT NOT NULL,
	PRIMARY KEY (serviceId)
);

DROP TABLE IF EXISTS habitats;
CREATE TABLE habitats (
	habitatId INT AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL UNIQUE,
	description TEXT NOT NULL,
	PRIMARY KEY (habitatId)
);

DROP TABLE IF EXISTS habitatThumbnails;
CREATE TABLE habitatThumbnails (
	habitatThumbId INT AUTO_INCREMENT,
	habitat INT NOT NULL,
	source VARCHAR(255) NOT NULL,
	PRIMARY KEY (habitatThumbId),
	FOREIGN KEY (habitat) REFERENCES habitats(habitatId) ON DELETE CASCADE
);

DROP TABLE IF EXISTS animals;
CREATE TABLE animals (
	animalId INT AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL UNIQUE,
	race VARCHAR(100) NOT NULL,
	habitat INT NOT NULL,
	PRIMARY KEY (animalId),
	FOREIGN KEY (habitat) REFERENCES habitats(habitatId) ON DELETE CASCADE
);

DROP TABLE IF EXISTS animalThumbnails;
CREATE TABLE animalThumbnails (
	animalThumbId INT AUTO_INCREMENT,
	animal INT NOT NULL,
	source VARCHAR(255) NOT NULL,
	PRIMARY KEY (animalThumbId),
	FOREIGN KEY (animal) REFERENCES animals(animalId) ON DELETE CASCADE
);

-- todo: add reviews
";

$res = $sqli->multi_query($req);

if(!$res)
{
	echo("Error creating database: " . $sqli->error . "\n\n");
	return;
}
else
{
	while($sqli->more_results())
	{
		if(!$sqli->next_result())
		{
			echo("Error creating database: " . $sqli->error . "\n\n");
			return;
		}
	}
}

echo("\nDatabase created.\n\n");

$req = "INSERT INTO accounts (userId, email, password, role) VALUES (UUID_TO_BIN(UUID()), ?, ?, 'admin'); -- todo? move this to an actual sql file";

$sqli->query("USE arcadia");
$stmt = $sqli->prepare($req);

// * SOURCE: https://gist.github.com/scribu/5877523
// slightly modified, couldn't find any cleaner way of doing this on windows
function prompt_silent($prompt = "") {
	echo($prompt);

	echo("\033[107;97m");
	$input = fgets(STDIN);
	echo("\033[0m");

	return rtrim($input, "\r\n");
}

if($stmt)
{
	while(true)
	{
		$email = readline("Enter a login email for the admin account: ");

		if(isEmailAddress($email))
			break;
		else
			echo("\nInvalid email address format\n");
	}

	$hash = "";

	while(true)
	{
		$pass = "";

		while($pass == "")
			$pass = trim(prompt_silent("\nEnter a password for the admin account: "));

		$con = trim(prompt_silent("\nConfirm password: "));
		if($pass == $con)
		{
			$hash = password_hash($pass, PASSWORD_DEFAULT);
			break;
		}

		echo("\nPasswords don't match.\n");
	}

	$stmt->bind_param("ss", $email, $hash);

	$res = $stmt->execute();
	$stmt->close();

	if(!$res)
	{
		echo("Error saving admin account: " . $sqli->error . "\n\n");
		return;
	}
}
else
{
	echo("Error creating admin account: " . $sqli->error . "\n\n");
}

echo("\nDatabase creation complete.\n\n");