<?php
/**
 * Notes:Url地址转换
 * User: Ciara
 * Date: 2019/8/29
 * Time: 17:22
 */

namespace common\libraries;


use  common\components\alifile\AliOSS;

class UrlBuilder
{
    public $_path;
    private static $_init;
    private $baseDir;//项目根目录
    private $baseUrl;//项目根目录

    private function __construct($path)
    {
        $this->_path = $path;
        $this->baseDir = "@backend/";
        $this->baseUrl = "@backendUrl";
    }

    static function init($path)
    {
        self::$_init = new self($path);
        return self::$_init;
    }

    /**
     * Notes:获取文件网络地址
     * User: Ciara
     * Date: 2019/10/15
     * Time: 14:05
     * @return bool|string
     */
    function file()
    {
        if (empty($this->_path)) {
            return '';
        }
        //解析网络文件
        preg_match("/^(http(s?):\/\/.*)$/", $this->_path, $match);
        if (!empty($match)) {
            return $this->_path;
        }
        //解析阿里云文件
        if ($match = $this->_isAlifile()) {
            return $this->getAliUrl();
        }

        //解析本地文件
        $path = $this->baseDir . $this->_path;
        return \Yii::getAlias(str_replace("//", '/', str_replace("./", '/', $path)));

        return $urlpath;
    }

    function check()
    {
        $full=$this->fullPath();
        $dir=pathinfo($this->fullPath(),PATHINFO_DIRNAME);
        if(!is_dir($dir)){
            mkdir($dir ,0777,true);
        }
        if(!is_file($full)){
            touch($full);
        }
    }
    function url()
    {
        $path = $this->baseUrl . $this->_path;
        return \Yii::getAlias(str_replace("//", '/', str_replace("./", '/', $path)));
    }

    function path()
    {
        return $this->_path;
    }


    function fullPath()
    {
        $path = $this->baseDir . "/web/" . $this->_path;
        return \Yii::getAlias(str_replace("//", '/', str_replace("./", '/', $path)));
    }


    function thumbUrl()
    {
        //解析阿里云文件
        if ($match = $this->_isAlifile()) {
            return $this->getAliThumbUrl();
        }
        return '';
    }

    /**
     * 解析阿里资源略缩图路径，返回临时访问链接
     * @param string $path
     * @return string
     */
    function getAliThumbUrl($width = 0, $height = 0)
    {
        if ($match = $this->_isAlifile()) {
            $file = $match[2];
            $bucket = $match[1];
            $alioss = new AliOSS(array('object' => $file, 'bucket' => $bucket));
            $urlpath = $alioss->setThumb($width ? $width : 200, $height ? $height : 200)->signUrl();
            return $urlpath;
        }
        return '';
    }

    /**
     * 解析阿里资源路径，返回临时访问链接
     * @param string $path
     * @return string
     */
    function getAliUrl()
    {
        if ($alioss = AliOSS::load($this->_path)) {
            $urlpath = $alioss->signUrl();
            return $urlpath;
        }
        return '';
    }


    /**
     * 文件服务
     * @param type $path
     * @return string
     */
    private function _isAlifile()
    {
        if (empty($this->_path)) {
            return false;
        }
        preg_match("/^ali[@](.*)[@](.*)$/", $this->_path, $match);
        if (!empty($match['1'])) {
            return $match;
        }
        return false;
    }

}