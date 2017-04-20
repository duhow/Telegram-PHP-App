<?php

// TODO

namespace TelegramApp\Tracking;

class Grafena extends \TelegramApp\Tracking {
	private $content = array();

	public function __construct($token){
		parent::__construct($token);
	}

	public function track($action = "Message", $category = "Telegram"){
		return NULL;
    }
}

 ?>
