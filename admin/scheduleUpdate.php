<?php
require_once("../vendor/autoload.php");
require_once("../server/auth.php");

$role = authCheck();

if($role != "admin")
	connectionFail("Permissions insuffisantes");

if($_SERVER['REQUEST_METHOD'] != "POST" || !checkCSRF())
{
	http_response_code(405);
	exit();
}

$from = $_POST["from"];
$to = $_POST["to"];
$days = $_POST["days"];

$mongo = new MongoDB\Client(getMongoQueryString());

$schedule = $mongo->arcadia->schedule->findOne(["id" => 0]);

if(!$schedule)
	$mongo->arcadia->schedule->insertOne(["id" => 0, "from" => $from, "to" => $to, "days" => $days]);
else
	$mongo->arcadia->schedule->updateOne(["id" => 0], ["\$set" => ["from" => $from, "to" => $to, "days" => $days]]);