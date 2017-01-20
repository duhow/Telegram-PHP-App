<?php

namespace TelegramApp;

class Functions {
	protected static $telegram;
	protected static $db;
	protected static $core;
	protected static $user;

	public function __construct($tg = NULL, $db = NULL){
		if(!empty($tg)){ self::setTelegram($tg); }
		if(!empty($db)){ self::setDB($db); }
	}

	public function setTelegram($tg){
		self::$telegram = $tg;
	}

	public function setDB($db){
		self::$db = $db;
	}

	public function setCore($core){
		self::$core = $core;
	}

	public function setUser($user){
		self::$user = $user;
	}
}

?>
