<?php
require_once("../server/auth.php");

$role = authCheck();

if($role != "admin" && $role != "employee")
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
else if($id == 0)
{
	http_response_code(400);
	exit("Service invalide");
}

if($id == 0) // create
{
	$res = $sqli->execute_query("SELECT serviceId FROM services WHERE name = ?;", [$title]);

	if(!$res)
	{
		http_response_code(400);
		exit("Erreur inconnue (1," . $sqli->errno . ")");
	}

	$service = $res->fetch_row();

	if($service)
	{
		http_response_code(400);
		exit("Ce service existe déjà");
	}

	$res2 = $sqli->execute_query("INSERT INTO services (name, description) VALUES (?, ?);", [$title, $description]);

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
		$res = $sqli->execute_query("DELETE FROM services WHERE serviceId = ?;", [$id]);

		if(!$res)
		{
			http_response_code(400);
			exit("Erreur inconnue (3," . $sqli->errno . ")");
		}
	}
	else
	{
		$res = $sqli->execute_query("UPDATE services SET name = ?, description = ? WHERE serviceId = ?;", [$title, $description, $id]);
	
		if(!$res)
		{
			http_response_code(400);
			exit("Erreur inconnue (4," . $sqli->errno . ")");
		}
	}
}