<?php
function connectionFail(string $message)
{
	http_response_code(403);
	exit($message);
}

function authLogin()
{
	$heads = apache_request_headers();
	$user = base64_decode($heads["Auth-Username"]);
	$pass = base64_decode($heads["Auth-Password"]);

	if($user == "" || $pass == "")
	{
		// ideally, these messages would be in separate files for localisation
		connectionFail("E-mail ou mot de passe manquant");
		return;
	}

	$config = json_decode(file_get_contents(__DIR__ . "/config.json"));

	$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

	$res = $sqli->execute_query("SELECT BIN_TO_UUID(userId) as userId, password FROM accounts WHERE email = ?;", [$user]);

	if(!$res)
	{
		connectionFail("Erreur inconnue (" . $sqli->errno . ")");
		return;
	}

	$account = $res->fetch_row();

	if($account == false)
	{
		connectionFail("Erreur inconnue (" . $sqli->errno . ")");
		return;
	}
	else if($account == null)
	{
		connectionFail("E-mail incorrecte");
		return;
	}

	$uid = $account[0];
	$hash = $account[1];

	if(!password_verify($pass, $hash))
	{
		connectionFail("Mot de passe incorrecte");
		return;
	}

	$res2 = $sqli->execute_query("INSERT INTO sessions (userId, token, created, ipAddress) VALUES (UUID_TO_BIN(?), UUID_TO_BIN(UUID()), NOW(), ?);", [$uid, $_SERVER['REMOTE_ADDR']]);
	$iid = $sqli->insert_id;

	if(!$res2)
	{
		connectionFail("Erreur lors de la création de la session (" . $sqli->errno . ")");
		return;
	}

	$res3 = $sqli->execute_query("SELECT BIN_TO_UUID(token) as token FROM sessions WHERE sessionId = ?;", [$iid]);

	if(!$res3)
	{
		connectionFail("Erreur lors de la connexion (" . $sqli->errno . ")");
		return;
	}

	$session = $res3->fetch_row();

	if(!$session)
	{
		if($session == false)
			connectionFail("Erreur lors de la sauvegarde de la session (" . $sqli->errno . ")");
		else
			connectionFail("Erreur lors de la sauvegarde de la session");

		return;
	}
	else
	{
		$sid = $session[0];

		if($sid)
			setcookie("session", $sid, ["httponly" => true, "samesite" => true]); //todo? add "secure" => true (needs ssl)
	}
}

function authCheck(): string
{
	if(!isset($_COOKIE['session']))
	{
		connectionFail("");
		return "";
	}

	$sid = $_COOKIE['session'];

	$config = json_decode(file_get_contents(__DIR__ . "/config.json"));

	$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

	$res = $sqli->execute_query("SELECT sessionId, userId, created, ipAddress FROM sessions WHERE token = UUID_TO_BIN(?) AND created < DATE_ADD(NOW(), INTERVAL ? HOUR);", [$sid, $config->sessionTimeout]);

	if(!$res)
	{
		connectionFail("Erreur lors de la récuperation de la session (" . $sqli->errno . ")");
		return "";
	}

	$session = $res->fetch_row();

	if($session == false)
	{
		connectionFail("Erreur inconnue (" . $sqli->errno . ")");
		return "";
	}
	else if($session == null)
	{
		connectionFail("Session invalide");
		return "";
	}

	$ip = $session[3];

	if($config->sessionIPLock && $ip != $_SERVER['REMOTE_ADDR'])
	{
		connectionFail("Session inaccessible depuis cette addresse");
		return "";
	}

	$res2 = $sqli->execute_query("UPDATE sessions SET created = NOW() WHERE sessionId = ?;", [$session[0]]);

	if(!$res2)
	{
		connectionFail("Erreur lors de la mise à jour de la session (" . $sqli->errno . ")");
		return "";
	}

	$res3 = $sqli->execute_query("SELECT role FROM accounts WHERE userId = ?;", [$session[1]]);

	if(!$res3)
	{
		connectionFail("Erreur lors de la récuperation du compte (" . $sqli->errno . ")");
		return "";
	}

	$role = $res3->fetch_row();

	if($role == false)
	{
		connectionFail("Erreur inconnue (" . $sqli->errno . ")");
		return "";
	}
	else if($role == null)
	{
		connectionFail("Compte invalide");
		return "";
	}

	return $role[0];
}