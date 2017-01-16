<?php

namespace TelegramApp;

class Module {
	private $telegram;
	private $db;

	function __construct($tg = NULL, $db = NULL){
		if(!empty($tg)){ $this->setTelegram($tg); }
		if(!empty($db)){ $this->setTelegram($db); }
	}

	function hooks(){

	}

	function run(){
		$this->hooks();
	}

	function end(){
		die();
	}

	function setTelegram($tg){
		$this->telegram = $tg;
		return $this;
	}

	function setDB($db){
		$this->db = $db;
		return $this;
	}
}

?>
