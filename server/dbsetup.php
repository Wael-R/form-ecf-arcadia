<?php
// * database setup; creates required sql databases and default entries

if(php_sapi_name() != "cli")
{
	http_response_code(403);
	exit();
}

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
	sessionId BINARY(16) UNIQUE,
	userId BINARY(16),
	ipAddress VARCHAR(45),
	created DATETIME NOT NULL,
	PRIMARY KEY (sessionId),
	FOREIGN KEY (userId) REFERENCES accounts(userId) ON DELETE CASCADE
);

-- todo: add services
-- todo: add animals
-- todo: add habitats
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

$req = "INSERT INTO accounts (userId, email, password, role) VALUES (UUID_TO_BIN(UUID()), ?, ?, 'admin'); -- todo? move this to an actual sql file";

$sqli->query("USE arcadia");
$stmt = $sqli->prepare($req);

if($stmt)
{
	$email = "admin@arcadia";
	$pass = password_hash("123451", PASSWORD_DEFAULT);

	$stmt->bind_param("ss", $email, $pass);

	$stmt->execute();
	$stmt->close();
}
else
{
	echo("Error creating admin account: " . $sqli->error . "\n\n");
}

echo("\nDatabase created.\n\n");