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

// $core->load('User');
// $User = new User($tg->user);

if($config['mysql']['enable']){
	require 'libs/PHP-MySQLi-Database-Class/MysqliDb.php';
	// require 'libs/PHP-MySQLi-Database-Class/dbObject.php';

	$mysql = new MysqliDb($config['mysql']);
	$core->setDB($mysql);
	// $User->setDB($mysql);
}

// $core->setUser($User);
$core->load('Main', TRUE);

?>
