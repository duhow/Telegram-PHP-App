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

	// TODO
	public function setVar($key, $value){
		self::$key = $value;
	}

	public function setTelegram($tg){ return self::setVar('telegram', $tg); }
	public function setDB($db){ return self::setVar('db', $db); }
	public function setCore($core){ return self::setVar('core', $core); }
	public function setUser($user){ return self::setVar('user', $user); }
}

?>
