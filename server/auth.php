<?php
if(php_sapi_name() != "cli")
	session_start();

$config = json_decode(file_get_contents(__DIR__ . "/config.json"));

/** Generates a new CSRF token */
function updateCSRFToken()
{
	$_SESSION["csrfToken"] = md5(uniqid(mt_rand(), true));
}

/** Returns the current CSRF token */
function getCSRFToken()
{
	return $_SESSION["csrfToken"] ?? "";
}

/** Returns a 403 HTTP error alongside a custom message */
function connectionFail(string $message)
{
	http_response_code(403);
	exit($message);
}

/** Returns true if the input string is a valid email address */
function isEmailAddress(string $email): bool
{
	// must not contain any whitespace
	if(str_contains($email, " ") || str_contains($email, "\t") || str_contains($email, "\n") || str_contains($email, "\r"))
		return false;

	$numAt = substr_count($email, "@");

	// requires exactly one @
	if($numAt <= 0 || $numAt > 1)
		return false;

	// must have at least one character before and after the @
	$at = strpos($email, "@");
	if($at <= 0 || $at >= strlen($email) - 1)
		return false;

	$domain = substr($email, $at + 1);

	// email domain may not have more than one dot
	if(substr_count($domain, ".") > 1)
		return false;

	// email domain must have a name before the dot
	if($domain[0] == ".")
		return false;

	return true;
}

/** Returns true if the CSRF token sent in the Auth-Token header matches the currently set CSRF token */
function checkCSRF(): bool
{
	$heads = apache_request_headers();
	$token = $heads["Auth-Token"];

	if(!$token || $token != getCSRFToken())
		return false;

	return true;
}

/** Logs in a user, expects Auth-Username, Auth-Password and Auth-Token headers to be set */
function authLogin()
{
	global $config;

	if($_SERVER['REQUEST_METHOD'] != "POST" || !checkCSRF())
	{
		http_response_code(405);
		return;
	}

	$heads = apache_request_headers();
	$user = base64_decode($heads["Auth-Username"]);
	$pass = base64_decode($heads["Auth-Password"]);

	if($user == "" || $pass == "")
	{
		// ideally, these messages would be in separate files for localisation
		connectionFail("E-mail ou mot de passe manquant");
		return;
	}

	$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

	$res = $sqli->execute_query("SELECT BIN_TO_UUID(userId) as userId, password FROM accounts WHERE email = ?;", [$user]);

	if(!$res)
	{
		connectionFail("Erreur inconnue (1," . $sqli->errno . ")");
		return;
	}

	$account = $res->fetch_row();

	if(!$account)
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

		$sqli->execute_query("DELETE FROM sessions WHERE created < DATE_SUB(NOW(), INTERVAL ? HOUR);", [$config->sessionTimeout]);
	}
}

/** Checks if this user's session still exists and, if so, returns their account's role */
function authCheck(): string
{
	global $config;

	if(!isset($_COOKIE['session']))
	{
		connectionFail("");
		return "";
	}

	$sid = $_COOKIE['session'];

	$sqli = new mysqli($config->sql->hostname, $config->sql->username, $config->sql->password, "arcadia", $config->sql->port);

	$res = $sqli->execute_query("SELECT sessionId, userId, created, ipAddress FROM sessions WHERE token = UUID_TO_BIN(?) AND created > DATE_SUB(NOW(), INTERVAL ? HOUR);", [$sid, $config->sessionTimeout]);

	if(!$res)
	{
		connectionFail("Erreur lors de la récuperation de la session (" . $sqli->errno . ")");
		return "";
	}

	$session = $res->fetch_row();

	if(!$session)
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

	if(!$role)
	{
		connectionFail("Compte invalide");
		return "";
	}

	return $role[0];
}