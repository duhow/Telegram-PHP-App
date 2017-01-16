<?php

$config['telegram'] = [
	'id'			=> 0,
	'key'			=> 'YOUR API KEY',
	'username'		=> 'MyNameBot',
	'first_name'	=> 'James Bot'
];

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

?>
