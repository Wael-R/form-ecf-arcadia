<?php
require_once("../server/auth.php");

$role = authCheck();

if($role != "veterinarian")
	connectionFail("Permissions insuffisantes");

if($_SERVER['REQUEST_METHOD'] != "POST" || !checkCSRF())
{
	http_response_code(405);
	exit();
}

$id = $_POST["id"];
$date = $_POST["date"] ?? "";
$health = $_POST["health"] ?? "";
$food = $_POST["food"] ?? "";
$amount = $_POST["amount"] ?? "";
$comment = $_POST["comment"] ?? "";

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, $config->sql->database, $config->sql->port);

if(strlen($date) < 1)
{
	http_response_code(400);
	exit("Date invalide");
}

if(strlen($health) < 1)
{
	http_response_code(400);
	exit("État invalide");
}

if(strlen($food) < 1)
{
	http_response_code(400);
	exit("Nourriture invalide");
}

if(strlen($amount) < 1)
{
	http_response_code(400);
	exit("Quantité invalide");
}

$res = $sqli->execute_query("UPDATE animals SET health = ? WHERE animalId = ?;", [$health, $id]);

if(!$res)
{
	http_response_code(400);
	exit("Erreur inconnue (1," . $sqli->errno . ")");
}

$res2 = $sqli->execute_query("INSERT INTO animalReports (animal, date, food, amount, comment) VALUES (?, DATE_FORMAT(?, '%Y-%m-%d %H:%i:00'), ?, ?, ?);", [$id, $date, $food, $amount, $comment]);

if(!$res2)
{
	http_response_code(400);
	exit("Erreur inconnue (2," . $sqli->errno . ")");
}