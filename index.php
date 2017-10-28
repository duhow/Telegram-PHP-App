<?php

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

$core = new TelegramApp\Core();
// $core->addTimelog("Startup");

// Block unknown hosts
// ------------
if($config['safe_connect'] != FALSE){
	$addr = array();
	$pass = FALSE;

	if(is_array($config['safe_connect'])){ $addr = $config['safe_connect']; }
	$addr[] = "149.154.167."; // Add Default Telegram IP

	foreach($addr as $a){
		if(strpos($_SERVER['REMOTE_ADDR'], $a) !== FALSE){
			$pass = TRUE; break;
		}
	}

	$headers = apache_request_headers();
	// Need this headers
	$check = [
		"Content-Type" => "application/json",
		"Connection" => "keep-alive"
	];

	if($pass){
		foreach($check as $h => $v){
			$get = FALSE;
			$h = strtolower($h);
			// $v = strtolower($v);
			foreach($headers as $ah => $av){
				if($h == trim(strtolower($ah)) and $v == $av){
					$get = TRUE;
					break;
				}
			}
			if(!$get){
				$pass = FALSE;
				break;
			}
		}
	}

	if(!$pass){
		error_log("Access denied from " .$_SERVER['REMOTE_ADDR'] ." to bot " .$config['telegram']['username']);
		http_response_code(401);
		die();
	}
}

$bot = new Telegram\Bot($config['telegram']);
$tg = new Telegram\Receiver($bot);
$core->setTelegram($tg);

if($config['ignore_older_than'] > 5 and $tg->date(TRUE) >= $config['ignore_older_than']){
	die();
}

if(!$config['convert_emoji']){ $tg->send->convert_emoji = FALSE; }

// Log received data
// ------------
if($config['log']){
	$log = __DIR__ ."/log.txt";
	$set = (!file_exists($log));
	$fp = fopen($log, "a");
	fwrite($fp, $tg->dump(TRUE) ."\n");
	fclose($fp);
	if($set){
		chmod($log, 0220);
		exec("chattr +a $log");
	}
	unset($log, $set, $fp);
}

// Detect and remove Telegram lock
// ------------
if($config['repeat_updateid'] > 0){
	$updates = array();
	if(file_exists("updateid.txt")){
		$updates = file_get_contents("updateid.txt");
		$updates = explode("\n", $updates);
	}
	array_unshift($updates, $tg->id); // Add element to beginning
	if(count($updates) > $config['repeat_updateid']){
		array_pop($updates); // Remove last element
	}
	file_put_contents("updateid.txt", implode("\n", $updates));
	$c = 0;
	foreach($updates as $u){
		if($u == $updates[0]){ $c++; }
	}
	// Skip message - 200 OK
	if($c >= $config['repeat_updateid']){ die(); }
	unset($updates, $c);
}

// Blacklist user
// ------------
if(file_exists("blacklist.txt") && is_readable("blacklist.txt")){
	$users = file_get_contents("blacklist.txt");
	$users = explode("\n", $users);
	foreach($users as $u){
		if(empty($u) or substr($u, 0, 1) == "#"){ continue; }
		if(
			(is_numeric($u) && $tg->chat->id == $u) or
			(is_string($u) && isset($tg->chat->username) && $tg->chat->username == $u)
		){
			die(); // Exit bot.
		}
	}
}

// Load DB class
// ------------
if($config['mysql']['enable']){
	require 'libs/PHP-MySQLi-Database-Class/MysqliDb.php';
	// require 'libs/PHP-MySQLi-Database-Class/dbObject.php';

	$mysql = new MysqliDb($config['mysql']);
	$core->setDB($mysql);
}

// Load User model
// ------------
if(file_exists('app/User.php')){
	$core->load('User');
	$User = new User($tg->user);
	$User->setVar('telegram', $tg);
	$core->addInherit('user', $User);
}

// Load Chat model
// ------------
if(file_exists('app/Chat.php')){
	$core->load('Chat');
	$Chat = new Chat($tg->chat);
	$Chat->setVar('telegram', $tg);
	$core->addInherit('chat', $Chat);
}

// Force add MySQL
// ------------
if($config['mysql']['enable']){
	if($core->is_loaded('User')){ $User->setDB($mysql); }
	if($core->is_loaded('Chat')){ $Chat->setDB($mysql); }
}

// Add tracking functions
// ------------
if($config['tracking'] !== FALSE){
	require 'core/Tracking.php';
	$track = ['name' => key($config['tracking']), 'token' => current($config['tracking'])];
	if(!file_exists('core/Tracking/' .$track['name'] .'.php')){
		die('Tracking core does not exist.');
	}
	require 'core/Tracking/' .$track['name'] .'.php';

	$class = 'TelegramApp\\Tracking\\' .$track['name'];
	$Tracking = new $class($track['token']);
	$Tracking->setTelegram($tg);
	$core->addInherit('tracking', $Tracking);
	unset($track, $class);
}

// Add cache (currently Memcached)
// ------------
if($config['cache_memcached'] !== FALSE){
	$Cache = new Memcached("TGAPP" .$config['telegram']['id']);
	if(!count($Cache->getServerList())){
		$Cache->addServers($config['cache_memcached']);
	}
	$core->addInherit('cache', $Cache);
}

// Add locale / string Functions
// -----------
if(is_dir("locale")){
	require 'core/Strings.php';
	$lang = $tg->language;
	if(empty($lang)){ $lang = $config['language']; }

	$Strings = new TelegramApp\Strings($lang);
	$Strings->load();
	$core->addInherit('strings', $Strings);
}

// Load modules
foreach(scandir("app") as $file){
	if(is_readable("app/$file") && substr($file, -4) == ".php"){
		$name = substr($file, 0, -4);
		if(in_array($name, ["Main", "User", "Chat"])){ continue; }
		$core->load($name);
	}
}

$core->addTimelog("Running");
// Run bot
$core->load('Main', TRUE);

$core->addTimelog("Finished");

if(isset($config['log_time']) and $config['log_time']){
	$log = __DIR__ ."/logtime.txt";
	$set = (!file_exists($log));

	$times = $core->getTimelogs();
	$start = array_shift($times);
	$end = array_pop($times);
	$time = floor(($end[0] - $start[0]) * 1000);
	$str = floor($start[0]) ." " .$tg->id ." $time\n";

	$fp = fopen($log, "a");
	fwrite($fp, $str);
	fclose($fp);
}

?>
