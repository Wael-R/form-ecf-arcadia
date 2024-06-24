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
$comment = $_POST["comment"] ?? "";

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

if(strlen($comment) < 1)
{
	http_response_code(400);
	exit("Commentaire invalide");
}

$res = $sqli->execute_query("INSERT INTO habitatComments (habitat, comment) VALUES (?, ?);", [$id, $comment]);

if(!$res)
{
	http_response_code(400);
	exit("Erreur inconnue (1," . $sqli->errno . ")");
}