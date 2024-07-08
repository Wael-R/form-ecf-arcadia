<?php
require_once("../server/auth.php");

$role = authCheck();

if($role != "employee")
	connectionFail("Permissions insuffisantes");

if($_SERVER['REQUEST_METHOD'] != "GET")
{
	http_response_code(405);
	exit();
}

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, $config->sql->database, $config->sql->port);

$res = $sqli->execute_query("SELECT reviewId, name, text FROM reviews WHERE NOT validated ORDER BY date DESC;");

if(!$res)
{
	http_response_code(400);
	exit("Erreur inconnue (1," . $sqli->errno . ")");
}

$revs = [];

while($review = $res->fetch_row())
{
	$id = $review[0];
	$name = $review[1];
	$content = $review[2];

	$revs[] = ["id" => $id, "name" => $name, "content" => $content];
}

echo(json_encode($revs));