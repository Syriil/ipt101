<?php
require_once 'vendor/autoload.php'; 

session_start(); 

$client = new Google_Client(); 
$client->setClientId('630759677866-dhr2r5noe97a9nna4ssdng5ahcqiuu03.apps.googleusercontent.com'); 
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/Lab3/google-callback.php'); 
$client->addScope('email'); 
$client->addScope('profile'); 

$authUrl = $client->createAuthUrl(); 
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit(); 
?>
