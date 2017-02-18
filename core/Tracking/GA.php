<?php

namespace TelegramApp\Tracking;

class GA extends \TelegramApp\Tracking {
    protected $url_base = "http://www.google-analytics.com/collect";
	private $content = array();
	private $_version = 1;

	public function __construct($token){
		parent::__construct($token);
		$this->_reset();
	}

	private function _reset(){
		$this->content = array();
		$this->content['v'] = $this->_version;
		$this->content['tid'] = $this->token;
	}

	private function set($key, $value, $optional = FALSE){
		if($optional && $value !== NULL){ $this->content[$key] = $value; }
		return $this;
	}

	public function user($id = NULL){
		if(empty($id) && !empty($this->telegram)){ $id = $this->telegram->user->id; }
		$this->set('cid', $id);
		return $this;
	}

	public function pageview($host, $page, $title){
		$this
			->set('t', 'pageview')
			->set('dh', $host)
			->set('dp', $page)
			->set('dt', $title);
		return $this->track(TRUE);
	}

	public function screenview($appname, $version, $id, $installid, $screen = NULL){
		$this
			->set('t', "screenview")
			->set('an', $appname)
			->set('av', $version) // (4.2.0)
			->set('aid', $id) // (com.foo.test)
			->set('aiid', $installid) // (com.android.vending)
			->set('cd', $screen, TRUE); // (Home)
		return $this->track(TRUE);
	}

	public function timing($category, $variable, $militime, $label = NULL){
		$this
			->set('t', "timing")
			->set('utc', $category)
			->set('utv', $variable)
			->set('utt', $militime)
			->set('utl', $label, TRUE);
		return $this->track(TRUE);
	}

	public function event($category, $action, $label = NULL, $value = NULL){
		$this
			->set('t', "event")
			->set('ec', $category)
			->set('ea', $action)
			->set('el', $label, TRUE)
			->set('ev', $value, TRUE);
		return $this->track(TRUE);
	}

	public function social($action, $network, $target){
		$this
			->set('t', "social")
			->set('sa', $action) // (like)
			->set('sn', $network) // (facebook)
			->set('st', $target); // (/home)
		return $this->track(TRUE);
	}

	public function exception($description, $fatal = FALSE){
		if($fatal === TRUE){ $fatal = 1; }
		$this
			->set('t', "exception")
			->set('exd', $description)
			->set('exf', $fatal); // error fatal?
		return $this->track(TRUE);
	}

	public function user_override($ip, $useragent){
		$this
			->set('uip', $ip, TRUE)
			->set('ua', $useragent, TRUE);
		return $this;
	}

	public function non_interaction($value = NULL){
		$this->set('ni', $value);
		return $this;
	}

    public function track($action = "Message"){
		// if(!isset($this->content['tid']) or empty($this->content['tid'])){ return FALSE; }
		if(!isset($this->content['cid']) or empty($this->content['cid'])){
			$cid = (!empty($this->telegram) ? $this->telegram->user->id : mt_rand(0, 10000000));
			$this->user($cid);
		}

		if($action !== TRUE && is_string($action)){
			return $this->event("Telegram", $action);
		}

		$data = http_build_query($this->content);
		$ch = curl_init();
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $this->url_base);
		curl_setopt($ch,CURLOPT_POST, TRUE);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		//execute post
		$result = curl_exec($ch);
		curl_close($ch);
		$this->_reset();
		return $result; // Pixel
    }

    public function createUrl($url){
        // TODO
    }
}
