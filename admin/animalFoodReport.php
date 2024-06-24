<?php
require_once("../server/auth.php");

$role = authCheck();

if($role != "employee")
	connectionFail("Permissions insuffisantes");

if($_SERVER['REQUEST_METHOD'] != "POST" || !checkCSRF())
{
	http_response_code(405);
	exit();
}

$id = $_POST["id"];
$date = $_POST["date"] ?? "";
$food = $_POST["food"] ?? "";
$amount = $_POST["amount"] ?? "";

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

if(strlen($date) < 1)
{
	http_response_code(400);
	exit("Date invalide");
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

$res = $sqli->execute_query("INSERT INTO animalFoodReports (animal, date, food, amount) VALUES (?, DATE_FORMAT(?, '%Y-%m-%d %H:%i:00'), ?, ?);", [$id, $date, $food, $amount]);

if(!$res)
{
	http_response_code(400);
	exit("Erreur inconnue (1," . $sqli->errno . ")");
}