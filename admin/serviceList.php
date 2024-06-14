<?php
require_once("../server/auth.php");

$role = authCheck();

if($role != "admin" && $role != "employee")
	connectionFail("Permissions insuffisantes");

if($_SERVER['REQUEST_METHOD'] != "GET")
{
	http_response_code(405);
	exit();
}

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

$res = $sqli->execute_query("SELECT serviceId, name, description FROM services;");

if(!$res)
{
	http_response_code(400);
	exit("Erreur inconnue (1," . $sqli->errno . ")");
}

$svcs = [];

while($svc = $res->fetch_row())
{
	$id = $svc[0];
	$title = $svc[1];
	$desc = $svc[2];

	$svcs[] = ["id" => $id, "title" => $title, "desc" => $desc, "thumbs" => []];
}

echo(json_encode($svcs));