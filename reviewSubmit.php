<?php
require_once("./server/auth.php");

if($_SERVER['REQUEST_METHOD'] != "POST" || !checkCSRF())
{
	http_response_code(405);
	exit();
}

$name = $_POST["name"] ?? "";
$review = $_POST["review"] ?? "";

if(strlen($name) < 1)
{
	http_response_code(400);
	exit("Nom invalide");
}

if(strlen($review) < 1)
{
	http_response_code(400);
	exit("Avis invalide");
}

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

$res = $sqli->execute_query("INSERT INTO reviews (name, text) VALUES (?, ?)", [$name, $review]);

if(!$res)
{
	http_response_code(400);
	exit("Erreur inconnue (1," . $sqli->errno . ")");
}