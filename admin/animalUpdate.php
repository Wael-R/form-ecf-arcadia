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
$race = $_POST["description"] ?? "";
$delete = $_POST["delete"] ?? "";
$thumb = $_POST["thumb"] ?? "";
$thumbId = $_POST["thumbId"] ?? "";
$habitat = $_POST["habitat"] ?? "";

$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, $config->sql->database, $config->sql->port);

if($delete == "" && $thumb == "" && $habitat == "")
{
	if(strlen($title) < 1)
	{
		http_response_code(400);
		exit("Nom invalide");
	}

	if(strlen($race) < 1)
	{
		http_response_code(400);
		exit("Race invalide");
	}
}
else if($id == 0)
{
	http_response_code(400);
	exit("Animal invalide");
}

if($id == 0) // create
{
	$res = $sqli->execute_query("SELECT animalId FROM animals WHERE name = ?;", [$title]);

	if(!$res)
	{
		http_response_code(400);
		exit("Erreur inconnue (1," . $sqli->errno . ")");
	}

	$animal = $res->fetch_row();

	if($animal)
	{
		http_response_code(400);
		exit("Cet animal existe déjà");
	}

	$res2 = $sqli->execute_query("INSERT INTO animals (name, race) VALUES (?, ?);", [$title, $race]);

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
		if($thumb != "")
		{
			$res = $sqli->execute_query("SELECT source FROM animalThumbnails WHERE animalThumbId = ? AND animal = ?;", [$thumbId, $id]);

			if(!$res)
			{
				http_response_code(400);
				exit("Erreur inconnue (3," . $sqli->errno . ")");
			}

			$row = $res->fetch_row();

			if(!$row)
			{
				http_response_code(400);
				exit("Cette image n'existe pas");
			}

			$src = $row[0];

			$res2 = $sqli->execute_query("DELETE FROM animalThumbnails WHERE animalThumbId = ?;", [$thumbId]);

			if(!$res2)
			{
				http_response_code(400);
				exit("Erreur inconnue (4," . $sqli->errno . ")");
			}

			$res3 = $sqli->execute_query("SELECT animalThumbId FROM animalThumbnails WHERE source = ?;", [$src]);

			if(!$res3)
			{
				http_response_code(400);
				exit("Erreur inconnue (3," . $sqli->errno . ")");
			}

			if(!$res3->fetch_row())
			{
				// no more thumbnails use this image; delete it
				unlink(dirname(__DIR__) . $src);
			}
		}
		else
		{
			$res = $sqli->execute_query("DELETE FROM animals WHERE animalId = ?;", [$id]);

			if(!$res)
			{
				http_response_code(400);
				exit("Erreur inconnue (5," . $sqli->errno . ")");
			}
		}
	}
	else
	{
		if($thumb != "")
		{
			if($_FILES)
			{
				$file = $_FILES["source"];

				if($file["error"] > 0)
				{
					http_response_code(400);

					if($file["error"] == 1)
						exit("Fichier trop volumineux");
					else
						exit("Erreur lors de l'envoi du fichier (" . $file["error"] . ")");
				}

				$path = $file["tmp_name"];
				$ext = strstr($file["name"], ".");
				$type = mime_content_type($path);

				if($type != "image/png" && $type != "image/jpg" && $type != "image/jpeg")
				{
					http_response_code(400);
					exit("Format invalide");
				}

				$dir = "/content/uploads/animal/";
				$final = $dir . sha1_file($path) . $ext;

				$res = $sqli->execute_query("SELECT animalThumbId FROM animalThumbnails WHERE animal = ? AND source = ?;", [$id, $final]);

				if(!$res)
				{
					http_response_code(400);
					exit("Erreur inconnue (6," . $sqli->errno . ")");
				}

				if(!$res->fetch_row())
				{
					$res2 = $sqli->execute_query("INSERT INTO animalThumbnails (animal, source) VALUES (?, ?);", [$id, $final]);

					if(!$res2)
					{
						http_response_code(400);
						exit("Erreur inconnue (7," . $sqli->errno . ")");
					}
				}

				if(!is_dir(dirname(__DIR__) . $dir))
					mkdir(dirname(__DIR__) . $dir);

				move_uploaded_file($path, dirname(__DIR__) . $final);
			}
		}
		else if($habitat != "")
		{
			$res = $sqli->execute_query("UPDATE animals SET habitat = ? WHERE animalId = ?;", [$habitat, $id]);

			if(!$res)
			{
				http_response_code(400);
				exit("Erreur inconnue (8," . $sqli->errno . ")");
			}
		}
		else
		{
			$res = $sqli->execute_query("UPDATE animals SET name = ?, race = ? WHERE animalId = ?;", [$title, $race, $id]);
	
			if(!$res)
			{
				http_response_code(400);
				exit("Erreur inconnue (9," . $sqli->errno . ")");
			}
		}
	}
}