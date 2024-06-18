<?php
// * database setup; creates required sql databases and default entries

if(php_sapi_name() != "cli")
{
	http_response_code(403);
	exit();
}

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

echo("This will recreate all database tables and overwrite their data.\nType 'OK' to proceed...\n\n");

if(readline() != "OK")
	exit("\nCanceled setup.\n\n");

echo("\n");
$hostname = readline("Enter the MySQL server hostname (localhost): ");
if($hostname == "")
	$hostname = "localhost";

echo("\n");
$hostport = readline("Enter the MySQL server host port (3306): ");
if($hostport == "")
	$hostport = "3306";

echo("\n");
$username = readline("Enter the MySQL server username (root): ");
if($username == "")
	$username = "root";

$password = prompt_silent("\nEnter the MySQL server password (1234): ");
if($password == "")
	$password = "1234";

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
		"username" => $username,
		"password" => $password,
	],
	"mail" => [
		"hostname" => $mailHostname,
		"port" => $mailPort,
		"username" => $mailUsername,
		"password" => $mailPassword,
	],
	"sessionTimeout" => 12,
	"sessionIPLock" => true,
];

$path = __DIR__ . "/config.json";
$json = json_encode($config, JSON_PRETTY_PRINT);

file_put_contents($path, $json);

$config = json_decode($json); // a bit silly, turns the array into objects

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