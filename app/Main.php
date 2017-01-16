<?php

class Main extends TelegramApp\Module {

	function __construct(){
		parent::__construct();
	}

	function hooks(){
		if($this->telegram->text_has(["hi", "hello"])){
			$this->_hello_world();
		}
	}

	function start(){
		$this->_hello_world();
	}

	function _hello_world(){
		$this->telegram->send
			->text("Hello World from <b>Telegram-PHP!</b>", "HTML")
		->send();

		$this->end();
	}
}

?>
