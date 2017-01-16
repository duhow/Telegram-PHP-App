<?php

namespace TelegramApp;

class Module {
	protected $telegram;
	protected $db;

	public function __construct($tg = NULL, $db = NULL){
		if(!empty($tg)){ $this->setTelegram($tg); }
		if(!empty($db)){ $this->setDB($db); }
	}

	protected function hooks(){

	}

	public function run(){
		$this->hooks();
	}

	protected function end(){
		die();
	}

	public function setTelegram($tg){
		$this->telegram = $tg;
	}

	public function setDB($db){
		$this->db = $db;
	}
}

?>
