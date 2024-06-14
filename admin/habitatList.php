<?php
require_once("../server/auth.php");

$role = authCheck();

if($role != "admin")
	connectionFail("Permissions insuffisantes");

if($_SERVER['REQUEST_METHOD'] != "GET")
{
	http_response_code(405);
	exit();
}

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

$res = $sqli->execute_query("SELECT habitatId, name, description FROM habitats;");

if(!$res)
{
	http_response_code(400);
	exit("Erreur inconnue (1," . $sqli->errno . ")");
}

$habs = [];

while($hab = $res->fetch_row())
{
	$id = $hab[0];
	$title = $hab[1];
	$desc = $hab[2];

	$res2 = $sqli->execute_query("SELECT habitatThumbId, source FROM habitatThumbnails WHERE habitat = ?;", [$id]);

	if(!$res2)
	{
		http_response_code(400);
		exit("Erreur inconnue (2," . $sqli->errno . ")");
	}

	$thumbs = [];

	while($thumb = $res2->fetch_row())
	{
		$thumbId = $thumb[0];
		$thumbSrc = $thumb[1];

		$thumbs[] = ["id" => $thumbId, "src" => $thumbSrc];
	}

	$habs[] = ["id" => $id, "title" => $title, "desc" => $desc, "thumbs" => $thumbs];
}

echo(json_encode($habs));