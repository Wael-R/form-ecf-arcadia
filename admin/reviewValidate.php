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

$id = $_POST["id"] ?? "";
$approved = $_POST["approved"] ?? "";

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, $config->sql->database, $config->sql->port);

if($approved)
{
	$res = $sqli->execute_query("UPDATE reviews SET validated = 1 WHERE reviewId = ?;", [$id]);

	if(!$res)
	{
		http_response_code(400);
		exit("Erreur inconnue (1," . $sqli->errno . ")");
	}
}
else
{
	$res = $sqli->execute_query("DELETE FROM reviews WHERE reviewId = ?;", [$id]);

	if(!$res)
	{
		http_response_code(400);
		exit("Erreur inconnue (2," . $sqli->errno . ")");
	}
}