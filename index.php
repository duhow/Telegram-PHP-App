<?php

// define error log
require 'config.php';

require 'libs/Telegram-PHP/src/Autoloader.php';

if($config['mysql']['enable']){
	require 'libs/PHP-MySQLi-Database-Class/MysqliDb.php';
	require 'libs/PHP-MySQLi-Database-Class/dbObject.php';
}

?>
