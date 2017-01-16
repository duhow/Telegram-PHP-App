<?php

namespace TelegramApp;

class Module {
	protected static $telegram;
	protected static $db;

	public function __construct($tg = NULL, $db = NULL){
		if(!empty($tg)){ self::setTelegram($tg); }
		if(!empty($db)){ self::setDB($db); }
	}

	function hooks(){

	}

	public function run(){
		self::hooks();
	}

	private function end(){
		die();
	}

	public function setTelegram($tg){
		self::$telegram = $tg;
	}

	public function setDB($db){
		self::$db = $db;
	}
}

?>
