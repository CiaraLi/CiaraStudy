<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductRequest
 *
 * @author chenglongliu
 */
class TokenRequest extends Request {

    function __construct() {
        
    }

    function getToken($param = null) {
        return '请使用Post';
    }

    function postToken() {
        $authcode = $this->postData('authcode');
        $appid = $this->postData('appid');
        $apps = new Apps();
        if ($apps) {
            $data = $apps->GetAppToken($appid, $authcode);
        }
        $rtnVal['status'] = empty($data) || (!is_array($data) && $data < 0 ) ? -1 : 1;
        $rtnVal['apitoken'] = $data;
        return $rtnVal;
    }

}
