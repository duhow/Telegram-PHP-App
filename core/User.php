<?php

namespace TelegramApp;

class User {
	public $id = NULL;
	public $telegram = NULL;
	protected $db;
	protected $extra = array();
	protected $loaded = FALSE;

	function __construct($input = NULL, $db = NULL){
		if($input instanceof \Telegram\User){
			$this->telegram = $input;
			$this->id = $input->id;
		}elseif(is_array($input)){
			foreach($input as $k => $v){ $this->$k = $v; }
		}elseif(is_numeric($input)){
			$this->id = $input;
		}
		if(!empty($db)){ $this->setDB($db); }
	}

	public function __get($key){
		if(isset($this->$key)){ return $this->$key; }
		if(isset($this->extra[$key])){ return $this->extra[$key]; }
		return NULL;
	}

	public function __set($key, $value){
		if(isset($this->$key)){ $this->$key = $value; }
		else{ $this->extra[$key] = $value; }

		if($this->loaded === TRUE){
			$this->update($key, $value);
		}
	}

	protected function update($key, $value){}
	public function load(){}

	public function setDB($db){
		$this->db = $db;
	}
}
