<?php

require 'config.php';

function query($method, $data = NULL){
	global $config;
	$url = 'https://api.telegram.org/bot' .$config['telegram']['id'] .':' .$config['telegram']['key'] .'/' .$method;

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);

	if($data){
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}

	$response = curl_exec($ch);

	if ($response === false) {
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		echo "Curl returned error $errno: $error\n";
		curl_close($ch);
		return FALSE;
	}

	$http_code = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
	curl_close($ch);

	$response = json_decode($response, TRUE);
	if($http_code != 200){
		echo "Request has failed with error {$response['error_code']}: {$response['description']}\n";
		if ($http_code == 401) {
			throw new \Exception('Invalid access token provided');
		}
		return FALSE;
	}else{
		// $response = $response['result'];
	}

	return $response;
}

if(!isset($config) or $config['telegram']['id'] == 0 or strlen($config['telegram']['key']) < 32){
	die("Please write your data in config.php before set webhook.\n");
}

if(!isset($_SERVER['HTTPS'])){
	die("Run this setup in HTTPS!\n");
}

if(!isset($_SERVER['REQUEST_URI']) or empty($_SERVER['REQUEST_URI'])){
	die("Run this setup via curl or web browser!");
}

$bot = query("getMe");
if($bot === FALSE){
	die("Error while getting bot info, aborting.\n");
}

$boturl = "https://" .$_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI'];
$boturl = dirname($boturl) .'/'; // Set directory

$data = ['url' => $boturl];

foreach(scandir(getcwd()) as $file){
	$tmpname = strtolower($file);
	$ext = substr($tmpname, -4);
	if(in_array($ext, ['.pem', '.crt']) and is_readable($file)){
		$data['certificate'] = new \CURLFile(realpath($file));
		break;
	}
}

$webhook = query("setWebhook", $data);
if($webhook === FALSE){
	die("Error setting webhook.\n");
}

echo $webhook['description'];
if(isset($data['certificate'])){
	echo ' with certificate file';
}
if($webhook['result'] == TRUE){
	echo ' to ' .$boturl;
}

echo "\n";

unlink(__FILE__);
if(file_exists(__FILE__)){
	die("Could not delete setup, please delete it manually!\n");
}

?>
