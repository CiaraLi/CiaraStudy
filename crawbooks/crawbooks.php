<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of crawbooks
 *
 * @author ciara
 */
class crawbooks {

    //put your code here
    public $isbn;
    public $bookinfo;

    public function craw($isbn, $bookdata = [], $debug = false, $crawinfo = true, $picture = true, $saveimg = false) {
        $debug == true ? error_reporting(E_ERROR) : error_reporting(0);
        // repMethod - S- Save in Db
        //J - json response 
        //数据源正则定义
        define('_math_isbn', '/[^0-9]+/');
        define('_math_page', '/[^0-9]+/');
        define('_math_word_count', '/[^0-9]+/');
        define('_math_price', '/[^0-9.]+/');
        define('_math_edition', '/[^0-9\/.\-]+/');
        define('_math_pubdate', '/[^A-Za-z0-9.:\/-]+/');
        define('_math_dimensions', '/[^0-9]+/');
        define('_math_quantity', '/[^0-9]+/');
        define('_math_weight', '/[^0-9.]+/');
        define('_math_discount', '/[^0-9.]+/');
        define('_math_cip', '/[^A-Za-z0-9.-_=]+/');
        define('_math_longtitle', '/【[^】]*】/');
        define('_math_space', '/[ ]+/');

        header('Content-Type: text/html; charset=utf-8');
        ini_set('mbstring.substitute_character', "none");
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', '-1');

        $formArr = Crawfunc::Crawler_Dangdang($isbn, $bookdata, $crawinfo, $picture, $saveimg);
        $formArr = Crawfunc::Crawler_DangdangWap($isbn, $formArr, $crawinfo, $picture, $saveimg);
        $formArr = Crawfunc::Crawler_Amazon($isbn, $formArr, $crawinfo, $picture, $saveimg);
        count(array_filter($formArr)) > count($formArr) / 2 ? null : $formArr = Crawfunc::Crawler_Taoshu($isbn, $formArr, $crawinfo, $picture, $saveimg);
        $formArr = $this->parseBookdata($formArr, $bookdata, $picture, $saveimg);
        return ($formArr);
    }

    private function parseBookdata($bookdata) {
        $bookdata['language_code'] = $this->getLangCode(empty($bookdata['language_code']) ? null : '', $bookdata['category_code']);
        $bookdata['pubdate'] = $this->parse_date(empty($bookdata['pubdate']) ? null : $bookdata['pubdate']);
        $bookdata['pubcountry'] = $this->getCountryCode(empty($bookdata['pubcountry']) ? null : $bookdata['pubcountry']);
        $bookdata['package_type'] = $this->parse_binding(empty($bookdata['package_type']) ? null : $bookdata['package_type']);
        $bookdata['set_type'] = $this->parse_settype(empty($bookdata['set_type']) ? null : $bookdata['set_type'], $bookdata['package_num']);
        $bookdata['package_count'] = empty($bookdata['set_type']) || $bookdata['set_type'] == 1 ? 1 : intval($bookdata['package_count']);
        $bookdata['author'] = str_replace(['编制', '原著', '著'], '', $bookdata['author']);
        $bookdata['package_num'] = empty($bookdata['package_num']) ? 1 : $bookdata['package_num'];
        $bookdata['edition'] = empty($bookdata['edition']) ? 1 : $bookdata['edition'];

        $bookdata['discount'] = $this->parseDiscount(empty($bookdata['discount']) ? null : '', $bookdata['discount']);
        if (empty($bookdata['discount']) && !empty($bookdata['sell_price']) && !empty($bookdata['price'])) {
            $bookdata['discount'] = $this->parseDiscount($bookdata['sell_price'] * 1.0 / $bookdata['price']);
        }
        return $bookdata;
    }

    function get_contents_curl($url, $utf = 0, $refer = '') {
        $headers[] = $this->UserAgent(); //<-- this is user agent
        $headers[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $headers[] = "Accept-Language:en-us,en;q=0.5";
        $headers[] = "Accept-Encoding:gzip,deflate";
        $headers[] = "Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $headers[] = "Keep-Alive:115";
        $headers[] = "Connection:keep-alive";
        $headers[] = "Cache-Control:max-age=0";
        $headers['Referer'] = $refer;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_ENCODING, "UTF-8");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        usleep(1000000);
        if ($utf) {
            $data = iconv("GBK", "UTF-8", $data);
        }
        return $data;
    }

    function parse_date($date = '', $outformate = '') {
        $data = $date = trim($date);
        $matches = [
            '(?<year>\d{2,4})\s*年(?<month>\d\d?)\s*月', //2010年8月 
            '(?<year>\d{4})[-\/.](?<month>\d\d?)[-\/.](?<day>\d\d?)', //2016-06-01 2016/06/01 2016.06.01
            '(?<month>\d\d?)[-\/.](?<day>\d\d?)[-\/.](?<year>\d{4})\s*', //06-01-2016 
            '(?<year>\d{4})[-\/.](?<month>\d\d?)', //2016-06 2016/06 2016.06
            '^(?<year>\d{4})(?<month>\d{2})(?<day>\d{2})$', //20160601
            '^(?<year>\d{4})(?<month>\d{2})$', //201606
            '^(?<year>\d{4})$', //2016
        ];
        foreach ($matches as $key => $value) {
            $pregs = '';
            preg_match('/' . $value . '/', $date, $pregs);
            if ($pregs) {
                if (!empty($pregs['year'])) {
                    $year = $pregs['year'];
                    $timestr = $year;
                    if (!empty($pregs['month'])) {
                        $month = $pregs['month'];
                        $timestr .= '-' . $month;
                        if (!empty($pregs['day'])) {
                            $day = $pregs['day'];
                            $timestr .= '-' . $day;
                        }
                    }
                }
            }
            if (!empty($timestr)) {
                break;
            }
        }
        if (!empty($timestr)) {
            $timespan = strtotime(trim($timestr, '-'));
            if (!empty($timestr)) {
                $data = empty($outformate) ? date('Y-m-d', $timespan) : date($outformate, $timespan);
            }
        }
        return $data;
    }

    /**
     *  包 装：
     * @param type $binding
     * @return int
     */
    function parse_binding($binding = '') {
        switch ($binding) {
            case '精装':
                return 2;
            case '盒装':
                return 3;
            default :
                return 1;
        }
    }

    /**
     * 是否套装
     * @param type $settype
     * @return int
     */
    function parse_settype($settype = '', $num) {
        switch ($settype) {
            case '是':
                return 2;
            default :
                if ($num > 1) {
                    return 2;
                } else {
                    return 1;
                }
        }
    }

    function getLangCode($country = '') {
        if (strpos($country, '英文') !== false) {
            return 'en';
        } else if (strpos($country, '中文') !== false) {
            return 'zh';
        } else {
            return '';
        }
    }

    function getCountryCode($country = '') {
        if (strpos($country, '中国') !== false) {
            return 'CN';
        } else {
            return '';
        }
    }

    function getQuantityUnitCode($unit = '', $default = "blet1") {
        if (strpos($unit, '册') !== false) {
            return 'blet1';
        } else if (strpos($unit, '套') !== false) {
            return 'set';
        } else if (strpos($unit, '箱') !== false) {
            return 'box';
        } else {
            return $default;
        }
    }

    /*
     * 1 0.2 58 100 0 
     */

    function parseDiscount($discount = '') {
        preg_match("/([0][.]\d\d\d+)/", trim($discount), $math);
        preg_match("/[0][.](\d\d?)/", trim($discount), $math1);
        preg_match("/(\d\d?)\s*折/", trim($discount), $math2);
        preg_match("/(\d\d?)$/", trim($discount), $math3);
        $num = $discount * 100;
        if (!empty($math[1])) {
            $num = $math[1] * 100;
            return round(100 - $num, 2);
        } else if (!empty($math1[1])) {
            $num = strlen($math1[1]) == 1 ? $math1[1] * 10 : $math1[1];
            return 100 - $num;
        } else if (!empty($math2[1])) {
            $num = strlen($math2[1]) == 1 ? $math2[1] * 10 : $math2[1];
            return 100 - $num;
        } else if (!empty($math3[1])) {
            $num = strlen($math3[1]) == 1 ? $math3[1] * 10 : $math3[1];
            return 100 - $num;
        } else {
            return 0;
        }
    }

}
