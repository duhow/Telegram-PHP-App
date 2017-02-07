<?php

class Main extends TelegramApp\Module {
	protected function hooks(){
		if($this->telegram->text_has(["hi", "hello"])){
			$this->_hello_world();
		}
	}

	public function start(){
		$this->_hello_world();
	}

	private function _hello_world(){
		$this->telegram->send
			->text("Hello World from <b>Telegram-PHP!</b>", "HTML")
		->send();

		$this->end();
	}

	protected function new_member($user){
		$this->telegram->send
			->text("Hello " .$user->first_name ."!")
		->send();
	}
}

?>
