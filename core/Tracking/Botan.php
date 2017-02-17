<?php

namespace TelegramApp\Tracking;

class Botan extends TelegramApp\Tracking {
    protected $url_base = "https://api.botan.io/track?token=#TOKEN&uid=#UID&name=#ACTION";

    public function track($action = "Message"){
        $data = $this->telegram->dump();
        if(!isset($data['message'])){ return FALSE; }

        $uid = $data['message']['from']['id'];

        $url = str_replace(
            ['#TOKEN','#UID','#ACTION'],
            [$this->token, $uid, $action],
            $this->url_base
        );

        return $this->request($url, $data['message']);
    }

    public function createUrl($url){
        // TODO
    }

    // ------------------
    // Source:
    // https://github.com/botanio/sdk/blob/master/Botan.php
    // ------------------

    protected function request($url, $body){
        $options = [
            'http' => [
                'header'  => 'Content-Type: application/json',
                'method'  => 'POST',
                'content' => json_encode($body)
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        if ($response === false)
            throw new \Exception('Error Processing Request', 1);
        $HTTPCode = $this->getHTTPResponseCode($http_response_header);
        if ($HTTPCode !== 200)
            throw new \Exception("Bad HTTP responce code: $HTTPCode".print_r($http_response_header, true));
        $responseData = json_decode($response, true);
        if ($responseData === false)
            throw new \Exception('JSON decode error');
        return $responseData;
    }

    protected function getHTTPResponseCode($headers){
        $matches = [];
        $res = preg_match_all('/[\w]+\/\d+\.\d+ (\d+) [\w]+/', $headers[0], $matches);
        if ($res < 1)
        	throw new \Exception('Incorrect response headers');
        $code = intval($matches[1][0]);
        return $code;
    }
}
