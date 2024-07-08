<?php
require_once("../server/auth.php");
require_once("../server/mail.php");

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
$role2 = $heads["Acc-Role"];

if($role2 != "employee" && $role2 != "veterinarian")
{
	http_response_code(400);
	exit("Role invalide");
}

if(strlen($pass) < 1)
{
	http_response_code(400);
	exit("Mot de passe invalide");
}

if(!isEmailAddress($user))
{
	http_response_code(400);
	exit("Adresse e-mail invalide");
}

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, $config->sql->database, $config->sql->port);

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

$res2 = sendMail($user, "Votre compte " . ($role2 == "employee" ? "employé" : "vétérinaire") . " Arcadia", "Votre compte Arcadia a été crée.<br>Veuillez contacter un administrateur pour obtenir votre mot de passe.");

if($res2 != "" && $res2 != -1)
{
	http_response_code(400);
	exit("Erreur lors de l'envoi de l'e-mail d'inscription");
}

$hash = password_hash($pass, PASSWORD_DEFAULT);

$res3 = $sqli->execute_query("INSERT INTO accounts (userId, email, password, role) VALUES (UUID_TO_BIN(UUID()), ?, ?, ?);", [$user, $hash, $role2]);

if(!$res3)
{
	http_response_code(400);
	exit("Erreur inconnue (2," . $sqli->errno . ")");
}