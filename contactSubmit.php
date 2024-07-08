<?php
require_once("./server/auth.php");
require_once("./server/mail.php");

if($_SERVER['REQUEST_METHOD'] != "POST" || !checkCSRF())
{
	http_response_code(405);
	exit();
}

$email = $_POST["email"] ?? "";
$title = $_POST["title"] ?? "";
$message = $_POST["message"] ?? "";

if(strlen($email) < 1)
{
	http_response_code(400);
	exit("Email invalide");
}

if(strlen($title) < 1)
{
	http_response_code(400);
	exit("Sujet invalide");
}

if(strlen($message) < 1)
{
	http_response_code(400);
	exit("Message invalide");
}

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, $config->sql->database, $config->sql->port);

$res = $sqli->execute_query("SELECT email FROM accounts WHERE role = 'employee';");

if(!$res)
{
	http_response_code(400);
	exit("Erreur inconnue (1," . $sqli->errno . ")");
}

$employees = [];

while($emp = $res->fetch_row())
	$employees[] = $emp[0];

$res2 = sendMail($employees, "Contact Arcadia: " . $title, "Message de " . htmlspecialchars($email) . " :<br><br>" . str_replace("\n", "<br>", htmlspecialchars($message)));

if($res2 != "")
{
	if($res2 == -1)
	{
		http_response_code(400);
		exit("Aucun serveur mail disponible");
	}
	else
	{
		http_response_code(400);
		exit("Erreur lors de l'envoi de l'e-mail");
	}
}