<?php
require_once("../server/auth.php");

$role = authCheck();

if($role != "admin")
	connectionFail("Permissions insuffisantes");

if($_SERVER['REQUEST_METHOD'] != "POST" || !checkCSRF())
{
	http_response_code(405);
	exit();
}

$heads = apache_request_headers();
$user = base64_decode($heads["Acc-Username"]);
$pass = base64_decode($heads["Acc-Password"]);
$role = $heads["Acc-Role"];

if($role != "employee" && $role != "veterinarian")
{
	http_response_code(400);
	exit("Role invalide");
}

if(!isEmailAddress($user))
{
	http_response_code(400);
	exit("Adresse e-mail invalide");
}

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

$res = $sqli->execute_query("SELECT BIN_TO_UUID(userId) as userId FROM accounts WHERE email = ?;", [$user]);

if(!$res)
{
	http_response_code(400);
	exit("Erreur inconnue (1," . $sqli->errno . ")");
}

$account = $res->fetch_row();

if($account)
{
	http_response_code(400);
	exit("Cette e-mail existe déjà");
}

$hash = password_hash($pass, PASSWORD_DEFAULT);

$res2 = $sqli->execute_query("INSERT INTO accounts (userId, email, password, role) VALUES (UUID_TO_BIN(UUID()), ?, ?, ?);", [$user, $hash, $role]);

if(!$res2)
{
	http_response_code(400);
	exit("Erreur inconnue (2," . $sqli->errno . ")");
}