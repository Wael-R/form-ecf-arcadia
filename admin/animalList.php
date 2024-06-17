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

$res = $sqli->execute_query("SELECT animalId, name, race FROM animals;");

if(!$res)
{
	http_response_code(400);
	exit("Erreur inconnue (1," . $sqli->errno . ")");
}

$anims = [];

while($anim = $res->fetch_row())
{
	$id = $anim[0];
	$title = $anim[1];
	$race = $anim[2];

	$res2 = $sqli->execute_query("SELECT animalThumbId, source FROM animalThumbnails WHERE animal = ?;", [$id]);

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

	$anims[] = ["id" => $id, "title" => $title, "desc" => $race, "thumbs" => $thumbs];
}

echo(json_encode($anims));