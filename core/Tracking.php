<?php

namespace TelegramApp;

class Tracking {
	protected $token = NULL;
    protected $telegram = NULL;
    protected $url_base = "";

    public function __construct($token){
        if(empty($token) || !is_string($token)){
            throw new \Exception('Token should be a string', 2);
        }
       $this->token = $token;
    }

    public function track($action){}
    public function createUrl($url){}

    public function setTelegram($telegram){
        if(is_string($telegram)){
            // Do stuff to JSON -> Message
        }
        $this->telegram = $telegram;
        return $this;
    }
}
