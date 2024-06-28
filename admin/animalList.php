<?php
require_once("../vendor/autoload.php");
require_once("../server/auth.php");
require_once("../server/utility.php");

$role = authCheck();

if($role != "admin" && $role != "veterinarian" && $role != "employee")
	connectionFail("Permissions insuffisantes");

if($_SERVER['REQUEST_METHOD'] != "GET")
{
	http_response_code(405);
	exit();
}

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);
$mongo = new MongoDB\Client("mongodb://" . $config->mongo->hostname . ":" . $config->mongo->port);

$res = $sqli->execute_query(
	"SELECT
			a.animalId, a.name, a.race, a.health, a.habitat, -- animal
			t.animalThumbId, t.source, -- thumbnail
			r.animalReportId, r.date, r.food, r.amount, r.comment, -- report
			f.animalFoodId, f.date, f.food, f.amount -- food
		FROM animals AS a
		LEFT JOIN animalThumbnails AS t ON a.animalId = t.animal
		LEFT JOIN animalReports AS r ON a.animalId = r.animal
		LEFT JOIN animalFoodReports AS f ON a.animalId = f.animal;"
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
	$reportFood = $anim[9];
	$reportAmount = $anim[10];
	$reportComment = $anim[11];
	
	$foodId = $anim[12];
	$foodDate = $anim[13];
	$foodFood = $anim[14];
	$foodAmount = $anim[15];

	$stats = $mongo->arcadia->animals->findOne(["id" => strval($id)]);

	if(!$stats)
		$views = 0;
	else
		$views = $stats["views"];

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
		"views" => $views,
	];

	$array["thumbs"] = null;

	if($thumbId)
		$array["thumbs"] = ["id" => $thumbId, "src" => $thumb];

	$array["reports"] = null;

	if($reportId)
		$array["reports"] = ["id" => $reportId, "date" => $reportDate, "food" => $reportFood, "amount" => $reportAmount, "comment" => $reportComment];

	$array["food"] = null;

	if($foodId)
		$array["food"] = ["id" => $foodId, "date" => $foodDate, "food" => $foodFood, "amount" => $foodAmount];
		
	$anims[$key] = mergeKeys($array, $anims[$key], "id", "title", "desc", "health", "habitat", "views");
}

echo(json_encode($anims));