<?php
require_once(__DIR__ . "/../vendor/autoload.php");

require_once("auth.php");

use PHPMailer\PHPMailer\PHPMailer;

function sendMail(array|string $to, string $subject, string $content)
{
	global $config;

	if($config->mail->hostname == "")
		return -1;

	$mail = new PHPMailer(false);

	$mail->isSMTP();

	$mail->Host = $config->mail->hostname;
	$mail->Port = $config->mail->port;

	$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
	$mail->SMTPAuth = true;

	$mail->Username = $config->mail->username;
	$mail->Password = $config->mail->password;

	$mail->setFrom($config->mail->username);

	if(is_array($to))
	{
		foreach($to as $email)
			$mail->addBCC($email);
	}
	else
		$mail->addAddress($to);

	$mail->CharSet = "UTF-8";
	$mail->Subject = $subject;
	$mail->msgHTML($content);

	if(!$mail->send())
		return $mail->ErrorInfo;

	return "";
}