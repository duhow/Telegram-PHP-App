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

	private function exists($key, $language = NULL){
		if(empty($language)){ $language = $this->language; }
		$this->load($language);
		if(!isset($this->loaded[$language][$key])){ return FALSE; }
		return TRUE;
	}

	public function get($key, $language = NULL){
		if(empty($language)){ $language = $this->language; }
		if(!$this->exists($key, $language)){ return NULL; }
		return $this->loaded[$language][$key];
	}

	public function get_multi($key, $index = 0, $language = NULL){
		if(empty($language)){ $language = $this->language; }
		if(!$this->exists($key, $language)){ return NULL; }

		if($index < 0){ $index = 0; }
		$val = $this->loaded[$language][$key];
		if($index >= count($val)){ $index = count($val) - 1; } // Set last element.

		return $val[$index];
	}

	public function get_gender($key, $male = TRUE, $language = NULL){
		if(empty($language)){ $language = $this->language; }
		if($male === TRUE or $male == 1 or strtolower($male) == "male"){ $male = 0; }
		else{ $male = 1; }

		return $this->get_multi($key, $male, $language);
	}

	public function get_random($key, $language = NULL){
		if(empty($language)){ $language = $this->language; }
		if(!$this->exists($key, $language)){ return NULL; }

		$val = $this->loaded[$language][$key];
		if(!is_array($val)){ $val = [$val]; }
		return $val[mt_rand(0, count($val) - 1)];
	}

	public function get_all($key){
		$final = array();
		foreach($this->loaded as $lang => $data){
			if(isset($data[$key])){ $final[$lang] = $data[$key]; }
		}
		return $final;
	}

	public function parse($key, $replace, $language = NULL){
		$text = $this->get($key, $language);
		if(empty($text)){ return NULL; }

		if(strpos($text, "%s") !== FALSE){
			if(!is_array($replace)){ $replace = [$replace]; }
			$pos = 0;
			foreach($replace as $r){
				$pos = strpos($text, "%s", $pos);
				if($pos === FALSE){ break; }
				$text = substr_replace($text, $r, $pos, 2); // 2 = strlen("%s")
			}
		}
		return $text;
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
