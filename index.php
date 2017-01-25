<?php

// define error log
require 'config.php';
define('CREATOR', $config['creator']);

require 'libs/Telegram-PHP/src/Autoloader.php';
require 'core/Core.php';
require 'core/Module.php';
require 'core/Functions.php';
require 'core/User.php';
require 'core/Chat.php';

if($config['telegram']['id'] == 0){
	die("Please edit config.php before running.");
}

$bot = new Telegram\Bot($config['telegram']);
$tg = new Telegram\Receiver($bot);

$core = new TelegramApp\Core();
$core->setTelegram($tg);

if(file_exists('app/User.php')){
	$core->load('User');
	$User = new User($tg->user);
	$core->addInherit('user', $User);
}

if(file_exists('app/Chat.php')){
	$core->load('Chat');
	$Chat = new Chat($tg->chat);
	$core->addInherit('chat', $Chat);
}

if($config['mysql']['enable']){
	require 'libs/PHP-MySQLi-Database-Class/MysqliDb.php';
	// require 'libs/PHP-MySQLi-Database-Class/dbObject.php';

	$mysql = new MysqliDb($config['mysql']);
	$core->setDB($mysql);
	if($core->is_loaded('User')){ $User->setDB($mysql); }
	if($core->is_loaded('Chat')){ $Chat->setDB($mysql); }
}

$core->load('Main', TRUE);

?>
