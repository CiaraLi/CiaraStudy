<?php

date_default_timezone_set('Etc/GMT');
/**
 *  保存的Session 变量名 ,用于设置时区偏移量，北京时间设置为+8；
 */
defined('TIME_OFFSET') or define('TIME_OFFSET', 'time_offset');

/**
 * Description of Times
 * 多时区转化类 
 *  
 * 1:设置本地时区偏移:                      $_SESSION[TIME_OFFSET]='+8'; <br/> 
 * 2:数据库写入时间统一使用国际标准时间：   $timechar=Times::base(); <br/> 
 * 3:从数据库中读取时间时，转化为本地时间:  Times::local('Y-m-d H:i:s',$timechar); <br/> 
 * 4:单独获取任意时区的当前时间:            Times::local('Y-m-d H:i:s',null,'+8'); <br/> 
 * @author Ciara Li
 */
class Times {

    private static $offset = '+8';
    private static $formate = 'Y-m-d H:i:s';

    static function setTimeOffset($offset) {
        $offset = empty($offset) ? '+8' : intval($offset);
        $_SESSION[TIME_OFFSET] = $offset;
        self::$offset = $offset;
    }

    //Thu Jul 06 2017 16:39:40 GMT+0800 (中国标准时间)
    /**
     *  获取世界标准时间
     * @param string $format  格式化,为空是返回时间戳，不为空时同 date()函数的第一个参数
     * @param string $time  时间戳，不为空时是将此时间转为世界标准时间
     * @param string $g  时区
     * @return string 时间戳或时间字符串  
     */
    static function base($format = "", $time = "", $g = "") {
        if (empty($g)) {
            $offset = empty($_SESSION[TIME_OFFSET]) ? self::$offset : $_SESSION[TIME_OFFSET];
        } else {
            $timezone = date_create(gmdate(self::$formate), timezone_open($g));
            $offset = date_offset_get($timezone) / 60 / 60;
        }
        if (empty($time)) {
            $basetime = strtotime(gmdate(self::$formate));
        } else {
            $time = is_numeric($time) ? $time : strtotime($time);
            $basetime = $time - $offset * 60 * 60;
        }
        return empty($format) ? $basetime : date($format, $basetime);
    }

    /**
     * 服务器时间转换为客户端时间
     * @param string $format  格式化,为空是返回时间戳，不为空时同 date()函数的第一个参数
     * @param string $time  时间戳，不为空时是将世界标准时间 转为 本地时间
     * @param string $g  时区 如：北京时间：+8 
     * @return string   时间戳或时间字符串  
     */
    static function local($format = "", $time = "", $g = "") {
        if (empty($g)) {
            $offset = empty($_SESSION[TIME_OFFSET]) ? self::$offset : $_SESSION[TIME_OFFSET];
        } else {
            $timezone = date_create(gmdate(self::$formate), timezone_open($g));
            $offset = date_offset_get($timezone) / 60 / 60;
        }
        if (empty($time)) {
            $localtime = strtotime(gmdate(self::$formate)) + $offset * 60 * 60;
        } else {
            $time = is_numeric($time) ? $time : strtotime($time);
            $localtime = $time + $offset * 60 * 60;
        }
        return empty($format) ? $localtime : date($format, $localtime);
    }
 

    /**
     * 
     * @return int 
     */
    static function local_getMonthStart($format = "", $time = "") {
        if (empty($time)) {
            $time = self::local();
        } else {
            $time = is_numeric($time) ? $time : strtotime($time);
        }
        $start = strtotime(date('Y-m', $time));
        return empty($format) ? $start : date($format, $start);
    }

    /**
     * 
     * @return int timespan
     */
    static function local_getMonthEnd($format = "", $time = "") {
        if (empty($time)) {
            $time = self::local();
        } else {
            $time = is_numeric($time) ? $time : strtotime($time);
        }
        $nextmonth = strtotime(date('Y-m', $time) . " +1 month");
        $last = strtotime(date('Y-m-d', $nextmonth) . ' -1 day'); 
        return empty($format) ? $last : date($format, $last);
    }

}
