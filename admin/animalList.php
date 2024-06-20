<?php
require_once("../server/auth.php");
require_once("../server/utility.php");

$role = authCheck();

if($role != "admin")
	connectionFail("Permissions insuffisantes");

if($_SERVER['REQUEST_METHOD'] != "GET")
{
	http_response_code(405);
	exit();
}

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

$res = $sqli->execute_query(
	"SELECT a.animalId, a.name, a.race, a.health, a.habitat, t.animalThumbId, t.source, r.animalReportId, r.date, r.comment
		FROM animals AS a
		LEFT JOIN animalThumbnails AS t ON a.animalId = t.animal
		LEFT JOIN animalReports AS r ON a.animalId = r.animal;"
);

if(!$res)
{
	http_response_code(400);
	exit("Erreur inconnue (1," . $sqli->errno . ")");
}

$anims = [];
$keys = [];

while($anim = $res->fetch_row())
{
	$id = $anim[0];
	$title = $anim[1];
	$race = $anim[2];
	$health = $anim[3];
	$habitat = $anim[4];

	$thumbId = $anim[5];
	$thumb = $anim[6];
	
	$reportId = $anim[7];
	$reportDate = $anim[8];
	$reportComment = $anim[9];

	$key = $keys[$id] ?? count($anims);
	$keys[$id] = $key;

	if(!isset($anims[$key]))
		$anims[] = [];

	$array = [
		"id" => $id,
		"title" => $title,
		"desc" => $race,
		"health" => $health,
		"habitat" => $habitat,
	];

	$array["thumbs"] = null;

	if($thumbId)
		$array["thumbs"] = ["id" => $thumbId, "src" => $thumb];

	$array["reports"] = null;

	if($reportId)
		$array["reports"] = ["id" => $reportId, "date" => $reportDate, "comment" => $reportComment];

	$anims[$key] = mergeKeys($array, $anims[$key], "id", "title", "desc", "health", "habitat");
}

echo(json_encode($anims));