<?php

defined('SESS_APITOKEN') or define('SESS_APITOKEN', 'API_TOKEN');

class Api {

    private static $authKey = ''; //客户端加密密钥 SECRETKEY
    private static $timeout = 5;

    function __contstuct() {
        
    }

    function __destruct() {
        
    }

    function __clone() {
        
    }

    static function setTimeout($time) {
        self::$timeout = intval($time);
    }

    /**
     * 加密
     * @param type $authToken
     * @param type $key
     * @return type
     */
    static function APIAuth_encode($authToken, $key = null) {
        empty($key) ? $key = self::$authKey : null;
        $encoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $authToken, MCRYPT_MODE_CBC, md5(md5($key))));
        $encoded_alter = str_replace("+", "1PLU1", $encoded);
        $encoded_alter = str_replace("/", "2SLA2", $encoded_alter);
        $encoded_alter = str_replace("=", "3EQU3", $encoded_alter);
        return $encoded_alter;
    }

    /**
     * 解密
     * @param type $authToken
     * @param type $key
     * @return type
     */
    static function APIAuth_decode($authToken, $key = null) {
        empty($key) ? $key = self::$authKey : null;
        $decoded_alter = str_replace("1PLU1", "+", $authToken);
        $decoded_alter = str_replace("2SLA2", "/", $decoded_alter);
        $decoded_alter = str_replace("3EQU3", "=", $decoded_alter);
        $decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($decoded_alter), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
        return $decoded;
    }

    static function checkToken($return) {
        if (is_string($return)) {
            $return = json_decode($return, true);
        }
        if (!empty($return['status']) && $return['status'] == 402 || $return['status'] == 403) {
            $_SESSION['SESS_APITOKEN'] = null;
        } else if ($return['status'] && !empty($return['apitoken'])) {
            $_SESSION['SESS_APITOKEN'] = ['apitoken' => $return['apitoken'], 'time' => Times::base()];
        }
    }

    static function getToken($url, $appid, $authcode) {
        $apitoken = empty($_SESSION['SESS_APITOKEN']) ? [] : $_SESSION['SESS_APITOKEN'];
        if (empty($apitoken) || empty($apitoken['apitoken']) || Times::base() - $apitoken['time'] >1 * 60) {
            $ch = Curl::getInstance(); 
            $ch->postdata($url, ['appid' => $appid, "authcode" => $authcode]);
            echo $json = $ch->run();
            if (is_string($json)) {
                $json = json_decode($json, true);
            }
            if (!empty($json['status']) && $json['status'] == 1 && !empty($json['apitoken'])) {
                $_SESSION['SESS_APITOKEN'] = ['apitoken' => $json['apitoken'], 'time' => time()];
                $token = $json['apitoken'];
            } else {
                $token = '';
            }
        } else {
            $token = $apitoken['apitoken'];
        }
        return $token;
    }

}
