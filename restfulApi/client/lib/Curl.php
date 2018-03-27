<?php

/**
 * 
 */
class Curl {

    protected $ch;
    protected $timeout;
    static $instance;

    function __contstuct() {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        $this->timeout = 5;
    }

    static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function getdata($url, $data = "") {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_ACCEPT_ENCODING, 'application/json');
        return $this;
    }

    function postdata($url, $data, $json = false) {
        $this->ch = curl_init();
        if ($json) {
            $headers['Content-Type'] = 'application/json';
            curl_setopt(self::$ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        return $this;
    }

    function putdata($url, $data, $json = false) {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("X-HTTP-Method-Override: PUT")); //设置HTTP头信息 
        return $this;
    }

    function deldata($url, $data = "", $json = false) {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        return $this;
    }

    function patchdata($url, $data, $json = false) {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        return $this;
    }

    function run() {
        $file_content = curl_exec($this->ch);
        curl_close($this->ch);
        RETURN $file_content;
    }

    function curlImage($filepath) {
        if (class_exists('\CURLFile', false)) {
            $field = new \CURLFile(realpath($filepath));
        } else {
            $field = '@' . realpath($filepath);
        }
        return $field;
    }

    function sendStreamFile($url, $file) {
        if (empty($url) || empty($file)) {
            return false;
        }
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'content-type:application/x-www-form-urlencoded',
                'content' => $file
            )
        );
        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        return $response;
    }

}
