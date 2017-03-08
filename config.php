<?php

$config['telegram'] = [
	'id'			=> 0,
	'key'			=> 'YOUR API KEY',
	'username'		=> 'MyNameBot',
	'first_name'	=> 'James Bot'
];

$config['creator'] = 0; // Telegram User ID of owner

$config['mysql'] = [
	'host'		=> 'localhost',
	'username'	=> 'mysql',
	'password'	=> '',
	'db'		=> 'telegram',
	'port'		=> 3306,
	'prefix'	=> NULL,
	'charset'	=> NULL
];

$config['mysql']['enable'] = TRUE;

$config['tracking'] = FALSE;
// $config['tracking'] = ['Botan' => 'API KEY'];
// $config['tracking'] = ['GA' => 'UA-11111111-1'];

$config['log'] = FALSE;
$config['repeat_updateid'] = 3;

?>
