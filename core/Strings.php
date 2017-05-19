<?php

namespace TelegramApp;

class Strings {
	public $language = NULL;
	private $loaded = array();
	private $folder = NULL;

	public function __construct($lang = "en"){
		if(!empty($lang)){
			$this->language = $lang;
		}

		$this->folder = dirname(__FILE__) ."/../locale/";
	}

	public function get($key, $language = NULL){
		if(empty($language)){ $language = $this->language; }
		$this->load($language);
		if(isset($this->loaded[$language][$key])){ return $this->loaded[$language][$key]; }
		return NULL;
	}

	public function get_all($key){
		$final = array();
		foreach($this->loaded as $lang => $data){
			if(isset($data[$key])){ $final[$lang] = $data[$key]; }
		}
		return $final;
	}

	public function load($language = NULL, $force = FALSE){
		if(empty($language)){ $language = $this->language; }
		if(isset($this->loaded[$language]) and !$force){ return $this; }

		$file = $this->folder . $language .".php";
		if($this->load_php($file, $language)){ return $this; }

		$file = $this->folder . $language .".json";
		if($this->load_json($file, $language)){ return $this; }
	}

	private function load_php($file, $lang){
		if(file_exists($file)){
			$this->loaded[$lang] = require $file;
			return TRUE;
		}
		return FALSE;
	}

	private function load_json($file, $lang){
		if(file_exists($file)){
			$this->loaded[$lang] = json_decode(file_get_contents($file), TRUE);
			return TRUE;
		}
		return FALSE;
	}
}

?>
