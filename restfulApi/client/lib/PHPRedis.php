<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BeesRedis
 *
 * @author ciara
 */
class PHPRedis {

    private static $handler;

    private static function handler() {
        if (!self::$handler) {
            self::$handler = new Redis();
            self::$handler->connect('127.0.0.1', '6379');
        }
        return self::$handler;
    }

    /**
     * get the value
     * @param type $key
     * @return type
     */
    public static function get($key) {
        $value = self::handler()->get($key);
        $value_serl = @unserialize($value);
        if (is_object($value_serl) || is_array($value_serl)) {
            return $value_serl;
        }
        return $value;
    }

    /**
     *  Set the  value in argument as value of the key.
     * @param type $key  
     * @param type $value  
     * @param int $ttl   with a iseconds  to live 
     * @return type 
     */
    public static function set($key, $value, $ttl = null) {
        if (is_object($value) || is_array($value)) {
            $value = serialize($value);
        }
        if ($ttl !== false) {
            $ttl = intval($ttl);
            return self::handler()->setEx($key, $ttl, $value);
        } else {
            return self::handler()->set($key, $value);
        }
    }

    /**
     * check the  key.
     * @param type $key
     * @return int
     */
    public static function exist($key) {
        if (is_object($key) || is_array($key)) {
            return 0;
        }
        return self::handler()->exists($key);
    }

    /**
     * Delete the  value in argument as value of the key.
     * @param type $key
     * @return type
     */
    public static function del($key) {
        if (!is_array($key)) {
            $key = (array) $key;
        }

        return self::handler()->delete($key);
    }

}
