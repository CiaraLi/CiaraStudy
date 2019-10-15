<?php
/**
 * Notes:国际日期处理类
 * User: Ciara
 * Date: 2019/8/28
 * Time: 14:26
 */

namespace common\libraries;

use Faker\Generator;
use Faker\Provider\DateTime;


class DateBuilder
{

    protected $_time;
    protected $_client = 8;
    protected $_server = 8;
    protected $_offset = 0;
    protected $_format = 'Y-m-d H:i:s';
    private static $_init;

    /**
     * Notes:日期构造器
     * User: Ciara
     * Date: 2019/8/28
     * Time: 16:04
     * @return DateBuilder
     */
    static function init()
    {
        if (!self::$_init instanceof DateBuilder) {
            self::$_init = new self();
        }
        return self::$_init;
    }

    /**
     * Notes:设置客户端时区
     * User: Ciara
     * Date: 2019/8/28
     * Time: 16:04
     * @param string $timezone
     * @return $this
     */
    function setTimeZone($timezone = "Asia/Shanghai")
    {
        if (empty($timezone)) {
            $client = $this->_server;
        } else {
            $timezone = date_create(gmdate($this->_format), timezone_open($timezone));
            $client = date_offset_get($timezone) / 60 / 60;
        }
        $this->_client = $client;
        return $this;
    }

    function setFormat($format)
    {
        $this->_format = $format;
        return $this;
    }

    /**
     * Notes:客户端转为服务器时间
     * User: Ciara
     * Date: 2019/8/28
     * Time: 16:04
     * @param $timezone string 客户端时区设置 e.g. Asia/Shanghai
     * @return DateBuilder
     */
    static function toServer($timezone = "")
    {
        self::init()->setTimeZone($timezone);
        self::init()->_offset = self::init()->_server - self::init()->_client;
        return self::init();
    }

    /**
     * Notes:服务器时间转为客户端时间
     * User: Ciara
     * Date: 2019/8/28
     * Time: 16:04
     * @param $timezone string 客户端时区设置 e.g. Asia/Shanghai
     * @return DateBuilder
     */
    static function toClient($timezone = "")
    {
        self::init()->setTimeZone($timezone);
        self::init()->_offset = self::init()->_client - self::init()->_server;
        return self::init();
    }

    /**
     * Notes:格式化时间(进行时区计算)
     * User: Ciara
     * Date: 2019/8/28
     * Time: 16:05
     * @param $time int 时间、默认为服务器时间
     * @return false|string
     */
    function date($time)
    {
        empty($time) ? $time = strtotime(gmdate($this->_format)) + self::init()->_server * 60 * 60 : null;
        $time += self::init()->_offset * 60 * 60;
        return date($this->_format, $time);
    }

    /**
     * Notes:获取时间戳(进行时区计算)
     * User: Ciara
     * Date: 2019/8/28
     * Time: 16:13
     * @param string $date
     * @return false|float|int
     */
    function time($date = "")
    {
        $time = empty($date) ? time() : strtotime($date);
        $time += self::init()->_offset * 60 * 60;
        return $time;
    }

    /**
     * Notes:Notes:格式化时间（不进行时间转换）
     * User: Ciara
     * Date: 2019/8/28
     * Time: 16:05
     * @param $time 时间、默认为服务器时间
     * @return false|string
     */
    static function dateTime($time = "", $format = "")
    {
        empty($time) ? $time = time() + self::init()->_server * 60 * 60 : null;
        empty($format) ? $format = self::init()->_format : null;

        return date($format, $time);
    }

    /**
     * Notes:获取时间戳（不进行时间转换）
     * User: Ciara
     * Date: 2019/8/28
     * Time: 16:14
     * @param string $date
     * @return false|int
     */
    static function timeStamp($date = "")
    {
        $time = empty($date) ? time() : strtotime($date);
        return $time;
    }

}