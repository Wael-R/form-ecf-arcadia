<?php
require_once("../server/auth.php");

$role = authCheck();

if($role != "admin")
	connectionFail("Permissions insuffisantes");

if($_SERVER['REQUEST_METHOD'] != "POST" || !checkCSRF())
{
	http_response_code(405);
	exit();
}

$id = $_POST["id"];
$title = $_POST["title"] ?? "";
$description = $_POST["description"] ?? "";
$delete = $_POST["delete"] ?? "";

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

if($delete == "")
{
	if(strlen($title) < 1)
	{
		http_response_code(400);
		exit("Nom invalide");
	}

	if(strlen($description) < 1)
	{
		http_response_code(400);
		exit("Description invalide");
	}
}

if($id == 0) // create
{
	$res = $sqli->execute_query("SELECT habitatId FROM habitats WHERE name = ?;", [$title]);

	if(!$res)
	{
		http_response_code(400);
		exit("Erreur inconnue (1," . $sqli->errno . ")");
	}

	$habitat = $res->fetch_row();

	if($habitat)
	{
		http_response_code(400);
		exit("Cet habitat existe déjà");
	}

	$res2 = $sqli->execute_query("INSERT INTO habitats (name, description) VALUES (?, ?);", [$title, $description]);

	if(!$res2)
	{
		http_response_code(400);
		exit("Erreur inconnue (2," . $sqli->errno . ")");
	}
}
else
{
	if($delete != "")
	{
		$res = $sqli->execute_query("DELETE FROM habitats WHERE habitatId = ?;", [$id]);

		if(!$res)
		{
			http_response_code(400);
			exit("Erreur inconnue (3," . $sqli->errno . ")");
		}
	}
	else
	{
		$res = $sqli->execute_query("UPDATE habitats SET name = ?, description = ? WHERE habitatId = ?;", [$title, $description, $id]);

		if(!$res)
		{
			http_response_code(400);
			exit("Erreur inconnue (4," . $sqli->errno . ")");
		}
	}
}