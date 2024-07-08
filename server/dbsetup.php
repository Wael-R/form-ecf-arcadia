<?php
// * database setup; creates required sql databases and default entries

if(php_sapi_name() != "cli")
{
	http_response_code(403);
	exit();
}

require_once("../vendor/autoload.php");
require_once("./auth.php");

// * SOURCE: https://gist.github.com/scribu/5877523
// slightly modified, couldn't find any cleaner way of doing this on windows
function prompt_silent($prompt = "") {
	echo($prompt);

	echo("\033[107;97m");
	$input = fgets(STDIN);
	echo("\033[0m");

	return rtrim($input, "\r\n");
}

echo("Step 1: Configure credentials and hosts\n");

$skip = false;

if(is_file(__DIR__ . "/config.json"))
{
	echo("A config file already exists, type 'SKIP' to keep it as is...\n\n");
	if(readline() == "SKIP")
		$skip = true;
}
else
	echo("\n");

if(!$skip)
{
	$hostname = readline("Enter the MySQL server hostname (localhost): ");
	if($hostname == "")
		$hostname = "localhost";

	echo("\n");
	$hostport = readline("Enter the MySQL server host port (3306): ");
	if($hostport == "")
		$hostport = "3306";

	echo("\n");
	$database = readline("Enter the MySQL database name (arcadia): ");
	if($database == "")
		$database = "arcadia";
		
	echo("\n");
	$username = readline("Enter the MySQL server username (root): ");
	if($username == "")
		$username = "root";

	echo("\n");
	$password = prompt_silent("Enter the MySQL server password (1234): ");
	if($password == "")
		$password = "1234";

	echo("\n");
	$mongoRemote = readline("Connect to a remote MongoDB server? (Enter for NO, type anything for YES): ");
	if($mongoRemote == "")
		$mongoRemote = false;
	else
		$mongoRemote = true;

	echo("\n");
	$mongoHostname = readline("Enter the MongoDB server hostname (localhost): ");
	if($mongoHostname == "")
		$mongoHostname = "localhost";

	if(!$mongoRemote)
	{
		echo("\n");
		$mongoPort = readline("Enter the MongoDB server host port (27017): ");
		if($mongoPort == "")
			$mongoPort = "27017";

		$mongoUsername = "";
		$mongoPassword = "";
	}
	else
	{
		$mongoPort = "";

		echo("\n");
		$mongoUsername = readline("Enter the MongoDB server username (root): ");
		if($mongoUsername == "")
			$mongoUsername = "root";

		echo("\n");
		$mongoPassword = readline("Enter the MongoDB server password (1234): ");
		if($mongoPassword == "")
			$mongoPassword = "1234";
	}

	$mailHostname = "";
	$mailPort = "";
	$mailUsername = "";
	$mailPassword = "";

	echo("\n");
	$mailHostname = readline("Enter the email SMTP server hostname: ");
	if($mailHostname == "")
		echo("No SMTP server set; no emails will be sent\n");
	else
	{
		echo("\n");
		$mailPort = readline("Enter the email SMTP server port (587): ");
		if($mailPort == "")
			$mailPort = "587";

		while(true)
		{
			echo("\n");
			$mailUsername = readline("Enter the SMTP email to use: ");

			if(!isEmailAddress($mailUsername))
				echo("Invalid email address");
			else
				break;
		}

		while(true)
		{
			$mailPassword = prompt_silent("\nEnter the SMTP password to use: ");

			if(strlen($mailPassword) < 1)
				echo("Invalid password");
			else
			{
				$con = prompt_silent("Confirm password: ");

				if($con != $mailPassword)
					echo("Passwords don't match\n");
				else
					break;
			}
		}
	}

	$config = [
		"sql" => [
			"hostname" => $hostname,
			"port" => $hostport,
			"database" => $database,
			"username" => $username,
			"password" => $password,
		],
		"mongo" => [
			"hostname" => $mongoHostname,
			"port" => $mongoPort,
			"remote" => $mongoRemote,
			"username" => $mongoUsername,
			"password" => $mongoPassword,
		],
		"mail" => [
			"hostname" => $mailHostname,
			"port" => $mailPort,
			"username" => $mailUsername,
			"password" => $mailPassword,
		],
		"sessionTimeout" => 12,
		"sessionIPLock" => true,
		"csrfChecks" => true,
	];

	$path = __DIR__ . "/config.json";
	$json = json_encode($config, JSON_PRETTY_PRINT);

	file_put_contents($path, $json);

	echo("\nCredentials saved.\n\n");
	
	$config = json_decode($json); // a bit silly, turns the array into objects
}
else
	$config = json_decode(file_get_contents(__DIR__ . "/config.json"));

echo("Step 2: Create databases\nThis will recreate all database tables and overwrite their data.\nType 'OK' to proceed...\n\n");

if(readline() != "OK")
	exit("\nCanceled database creation.\n\n");

$mongo = new MongoDB\Client(getMongoQueryString());
$mongo->arcadia->dropCollection("animals");
$mongo->arcadia->dropCollection("schedule");
$mongo->arcadia->animals->createIndex(["views" => -1]);

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, null, $config->sql->port);

$res = $sqli->execute_query("CREATE DATABASE IF NOT EXISTS `" . str_replace("`", "", $config->sql->database) . "`;");
// technically, probably injection prone... but why would you inject sql while deploying?
// either way, using parameters is not possible for create database statements and some web servers require specific database name prefixes

if($res)
{
	$res = $sqli->execute_query("USE `" . str_replace("`", "", $config->sql->database) . "`;");

	if(!$res)
	{
		echo("Error using database: " . $sqli->error . "\n\n");
		return;
	}
}
else
{
	echo("Error creating database: " . $sqli->error . "\n\n");
	return;
}

$req = "
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS accounts;
CREATE TABLE accounts (
	userId BINARY(16) NOT NULL UNIQUE,
	email VARCHAR(50) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	role ENUM('admin', 'employee', 'veterinarian') NOT NULL,
	PRIMARY KEY (userId)
);

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

DROP TABLE IF EXISTS animalThumbnails;
DROP TABLE IF EXISTS animalReports;
DROP TABLE IF EXISTS animalFoodReports;
DROP TABLE IF EXISTS animals;
DROP TABLE IF EXISTS habitatThumbnails;
DROP TABLE IF EXISTS habitatComments;
DROP TABLE IF EXISTS habitats;
CREATE TABLE habitats (
	habitatId INT AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL UNIQUE,
	description TEXT NOT NULL,
	PRIMARY KEY (habitatId)
);

CREATE TABLE habitatThumbnails (
	habitatThumbId INT AUTO_INCREMENT,
	habitat INT NOT NULL,
	source VARCHAR(255) NOT NULL,
	PRIMARY KEY (habitatThumbId),
	FOREIGN KEY (habitat) REFERENCES habitats(habitatId) ON DELETE CASCADE
);

CREATE TABLE habitatComments (
	habitatCommentId INT AUTO_INCREMENT,
	habitat INT NOT NULL,
	date DATETIME NOT NULL DEFAULT NOW(),
	comment TEXT NOT NULL,
	PRIMARY KEY (habitatCommentId),
	FOREIGN KEY (habitat) REFERENCES habitats(habitatId) ON DELETE CASCADE
);

CREATE TABLE animals (
	animalId INT AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL UNIQUE,
	race VARCHAR(100) NOT NULL,
	health VARCHAR(50) NOT NULL DEFAULT 'Bonne santé',
	habitat INT NOT NULL,
	PRIMARY KEY (animalId),
	FOREIGN KEY (habitat) REFERENCES habitats(habitatId) ON DELETE CASCADE
);

CREATE TABLE animalThumbnails (
	animalThumbId INT AUTO_INCREMENT,
	animal INT NOT NULL,
	source VARCHAR(255) NOT NULL,
	PRIMARY KEY (animalThumbId),
	FOREIGN KEY (animal) REFERENCES animals(animalId) ON DELETE CASCADE
);

CREATE TABLE animalReports (
	animalReportId INT AUTO_INCREMENT,
	animal INT NOT NULL,
	date DATETIME NOT NULL,
	food VARCHAR(255) NOT NULL,
	amount VARCHAR(255) NOT NULL,
	comment TEXT,
	PRIMARY KEY (animalReportId),
	FOREIGN KEY (animal) REFERENCES animals(animalId) ON DELETE CASCADE
);

CREATE TABLE animalFoodReports (
	animalFoodId INT AUTO_INCREMENT,
	animal INT NOT NULL,
	date DATETIME NOT NULL,
	food VARCHAR(255) NOT NULL,
	amount VARCHAR(255) NOT NULL,
	PRIMARY KEY (animalFoodId),
	FOREIGN KEY (animal) REFERENCES animals(animalId) ON DELETE CASCADE
);

DROP TABLE IF EXISTS reviews;
CREATE TABLE reviews (
	reviewId INT AUTO_INCREMENT,
	date DATETIME NOT NULL DEFAULT NOW(),
	name VARCHAR(255) NOT NULL,
	text TEXT(2048) NOT NULL,
	validated BOOLEAN NOT NULL DEFAULT 0,
	PRIMARY KEY (reviewId)
);
";

$res = $sqli->multi_query($req);

if(!$res)
{
	echo("Error creating tables: " . $sqli->error . "\n\n");
	return;
}
else
{
	while($sqli->more_results())
	{
		if(!$sqli->next_result())
		{
			echo("Error creating tables: " . $sqli->error . "\n\n");
			return;
		}
	}
}

echo("\nDatabase created.\n\n");

$stmt = $sqli->prepare("INSERT INTO accounts (userId, email, password, role) VALUES (UUID_TO_BIN(UUID()), ?, ?, 'admin');");

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

		echo("\nPasswords don't match\n");
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