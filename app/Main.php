<?php

class Main extends TelegramApp\Module {

	function __construct(){
		parent::__construct();
	}

	function hooks(){
		if(
			$this->telegram->text_has(["hi", "hello"]) or
			$this->telegram->text_command("start")
		){
			$this->hello_world();
		}
	}

	function hello_world(){
		$this->telegram->send
			->text("Hello World from <b>Telegram-PHP!</b>", "HTML")
		->send();

		$this->end();
	}
}

?>
