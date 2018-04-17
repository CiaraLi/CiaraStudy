<?php

class Apps {

    private static $authKey = 'apikeys123';
    private static $method = 'AES-256-CBC';
    private static $iv = '6223463998176155';
    public static $appid = 0;
    public static $secret = 0;
    public static $app = 0;
    private $validtime = 30 * 60; //s 

    const ID = 'app_id';
    const NAME = 'app_name';
    const AUTH = 'app_auth';
    const SECRET = 'app_secret';
    const URL = 'app_url';
    const ADDTIME = 'app_added_at';
    const LASTTIME = 'app_last_time';
    const STATUS = 'app_status';

    function __construct() {
        $this->tablename = 'apps';
        $this->id = self::ID;
    }

    /**
     * 加密 php7.1 以下
     * @param type $authToken
     * @param type $key
     * @return type
     */
    static function APIAuth_encode1($authToken, $key = null) {
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
    static function APIAuth_decode1($authToken, $key = null) {
        empty($key) ? $key = self::$authKey : null;
        $decoded_alter = str_replace("1PLU1", "+", $authToken);
        $decoded_alter = str_replace("2SLA2", "/", $decoded_alter);
        $decoded_alter = str_replace("3EQU3", "=", $decoded_alter);
        $decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($decoded_alter), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
        return $decoded;
    }
    
    
    /**
     * 加密 php7.1 以下
     * @param type $authToken
     * @param type $key
     * @return type
     */
    public function APIAuth_encode($authToken, $key = null) {
        empty($key) ? $key = self::$authKey : null;
        $encoded = openssl_encrypt(($authToken), self::method, md5($key), 0, self::iv);
        $encoded = base64_encode($encoded);
        $encoded_alter = str_replace("+", "1PLU1", $encoded);
        $encoded_alter = str_replace("/", "2SLA2", $encoded_alter);
        $encoded_alter = str_replace("=", "3EQU3", $encoded_alter);
        return $encoded_alter;
    }

    /**
     * 加密 php7.1以上
     * @param type $authToken
     * @param type $key
     * @return type
     */
    public function APIAuth_decode($authToken, $key = null) {
        empty($key) ? $key = self::$authKey : null;
        $decoded_alter = str_replace("1PLU1", "+", $authToken);
        $decoded_alter = str_replace("2SLA2", "/", $decoded_alter);
        $decoded_alter = str_replace("3EQU3", "=", $decoded_alter);
        $encodestr = base64_decode($decoded_alter);
        $decoded = rtrim(openssl_decrypt($encodestr, self::method, md5($key), 0, self::iv));
        return ($decoded);
    }


    public function getAuthkey($app) {
        $chkQuery = "SELECT * from " . $this->tablename . " WHERE  " . self::ID . " =" . intval($app) . " AND " . self::STATUS . " =1 ";
        $row = mysqlClass::getInstance()->getFirstone($chkQuery);
        self::$app = empty($row) ? null : $row;
        self::$secret = empty($row) ? null : $this->APIAuth_decode($row[self::SECRET], self::$authKey); //获取app的加密字符串
        self::$appid = empty($row) ? null : intval($app);
        return $row;
    }

    public function GetAppToken($appid, $authcode) {
        if ($this->getAuthkey($appid) && $authcode) {
            $codestr = $this->APIAuth_decode($authcode, self::$secret);
            $ip = empty($_SERVER['REMOTE_ADDR']) ? "" : $_SERVER['REMOTE_ADDR'];
            $time = Times::base();
            if ($codestr == self::$app[self::AUTH]) {
                $token = implode('##', [
                    self::$appid, $time, $ip
                ]);
                $token = $token . "##" . strlen($token);
                return $this->APIAuth_encode($token, self::$secret);
            }
        }
        return UNAUTHORIZED; //Access token Error 
    }

    public function chekAppCode($appid, $auth) {
        if ($this->getAuthkey($appid) && $auth) {
            $getDecode = $this->APIAuth_decode($auth, self::$secret);
            $exPlodeData = explode('##', $getDecode);
            if (empty($auth) || count($exPlodeData) < 4) {
                return ERROR_TOKEN; //Access token Error
            }
            $authlen = strlen($getDecode) - strlen($exPlodeData[3]) - 2;
            $app = intval(trim($exPlodeData[0]));
            $accessTIME = intval($exPlodeData[1]);
            if ($authlen == $exPlodeData[3] && self::$appid && $app && $app == self::$appid) { 
                if (Times::base() - $accessTIME > $this->validtime) {
                    self::$appid = null;
                    return ERROR_TOKEN; //Unauthorised User
                } else {
                    return SUCCESS;
                }
            } else {
                return ERROR_TOKEN; //Access token Error
            }
        } else {
            return UNAUTHORIZED; //Access token Error 
        }
        //$
    }

}
