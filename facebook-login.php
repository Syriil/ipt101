<?php
require_once 'vendor/autoload.php'; 

session_start(); 

$fb = new \Facebook\Facebook([
  'app_id' => '782353517009684',
  'app_secret' => '4f727a4fadf9441e5b7847fa2ef2a5c6',
  'default_graph_version' => 'v12.0',
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; 

try {
  // Generate the login URL with the callback URL and permissions
  $loginUrl = $helper->getLoginUrl('http://localhost/Lab3/facebook-callback.php', $permissions);
  
  header('Location: ' . $loginUrl);
  exit();
} catch (\Facebook\Exceptions\FacebookSDKException $e) {
  // Handle Facebook SDK errors
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit();
}
?>
