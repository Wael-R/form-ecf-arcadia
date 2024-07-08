<?php
require_once("../server/auth.php");
require_once("../server/utility.php");

$role = authCheck();

if($role != "admin" && $role != "veterinarian")
	connectionFail("Permissions insuffisantes");

if($_SERVER['REQUEST_METHOD'] != "GET")
{
	http_response_code(405);
	exit();
}

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, $config->sql->database, $config->sql->port);

$res = $sqli->execute_query(
	"SELECT h.habitatId, h.name, h.description, t.habitatThumbId, t.source, c.habitatCommentId, c.date, c.comment
		FROM habitats AS h
		LEFT JOIN habitatThumbnails AS t ON h.habitatId = t.habitat
		LEFT JOIN habitatComments AS c ON h.habitatId = c.habitat;"
);

if(!$res)
{
	http_response_code(400);
	exit("Erreur inconnue (1," . $sqli->errno . ")");
}

$habs = [];
$keys = [];

while($hab = $res->fetch_row())
{
	$id = $hab[0];
	$title = $hab[1];
	$desc = $hab[2];

	$thumbId = $hab[3];
	$thumb = $hab[4];

	$commentId = $hab[5];
	$commentDate = $hab[6];
	$commentComment = $hab[7];

	$key = $keys[$id] ?? count($habs);
	$keys[$id] = $key;

	if(!isset($habs[$key]))
		$habs[] = [];

	$array = [
		"id" => $id,
		"title" => $title,
		"desc" => $desc,
	];

	$array["thumbs"] = null;

	if($thumbId)
		$array["thumbs"] = ["id" => $thumbId, "src" => $thumb];

	$array["comments"] = null;

	if($commentId)
		$array["comments"] = ["id" => $commentId, "date" => $commentDate, "comment" => $commentComment];

	$habs[$key] = mergeKeys($array, $habs[$key], "id", "title", "desc");
}

echo(json_encode($habs));