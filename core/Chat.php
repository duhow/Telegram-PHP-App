<?php

namespace TelegramApp;

class Chat {
	public $id = NULL;
	public $telegram = NULL;
	protected $db;
	protected $extra = array();
	protected $loaded = FALSE;

	function __construct($input = NULL){
		if($input instanceof \Telegram\Chat){
			foreach($input as $k => $v){ $this->$k = $v; }
			$this->id = $input->id;
		}elseif(is_array($input)){
			foreach($input as $k => $v){ $this->$k = $v; }
		}elseif(is_numeric($input)){
			$this->id = $input;
		}
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

	public function is_group(){
		return ($this->type != "private");
	}

	public function setVar($key, $value){
		$this->$key = $value;
		return $this;
	}

	public function setDB($db){	return $this->setVar('db', $db); }
}
