<?php
/**
 * Notes:字符串常用处理
 * User: Ciara
 * Date: 2019/8/28
 * Time: 14:01
 */

namespace common\libraries;


class StrBuilder
{

    /**
     * Notes:隐藏字符
     * User: Ciara
     * Date: 2019/8/28
     * Time: 14:18
     * @param $string
     * @param int $start
     * @param int $end
     * @return string
     */
    static function hide($string, $start = 3, $end = 4)
    {
        return empty($string) ? "" : (mb_substr($string, 0, $start) . '***' . (strlen($string) <= $start + $end ? "" : mb_substr($string, 0 - $end, $end)));
    }

    /**
     * Notes:隐藏字符
     * User: Ciara
     * Date: 2019/8/28
     * Time: 14:18
     * @param $string
     * @param int $start
     * @param int $end
     * @return string
     */
    static function hideEmail($string)
    {
        $matches = [];
        preg_match("/^(.*)@(.*)[.](.*)$/iS", $string, $matches);
        $hide1 = self::hide($matches[1]);
        $hide2 = self::hide($matches[2]);
        return "{$hide1}@{$hide2}." . ($matches[3] ?? "com");
    }

    /**
     * 字符串替换换行回车
     * @param $str
     * @return mixed
     */
    static function strTrim($str)
    {
        $search = array(" ", "　", "\n", "\r", "\t");
        $replace = array("", "", "", "", "");
        return str_replace($search, $replace, $str);
    }

    /**
     * Notes:ID随机字符
     * User: Ciara
     * Date: 2019/8/29
     * Time: 10:49
     * @param $key  string 前缀
     * @param $id int  ID
     * @return string
     */
    static function randIdChar($key, $id)
    {
        return $key . ($id % 1000 + 1000) . substr(uniqid(), -11);
    }

    /**
     * Notes: 过滤表情符
     * @param $str
     * @return string
     */
    static function filterEmoji($str)
    {
        $str = preg_replace_callback('/./u', function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $str);
        return trim($str);
    }

    /**
     * 生成数字码
     * @param int $codeLen 数字码长度
     *
     * @return int
     */
    static function randCode($codeLen = 4)
    {
        $int = 10;
        $min = pow($int, ($codeLen - 1)) - 1;
        $max = pow($int, $codeLen) - 1;
        return mt_rand($min, $max);

    }

    /**
     * Notes:检查手机号合法
     * User: Ciara
     * Date: 2019/9/12
     * Time: 17:13
     * @param $attribute
     * @return false|int
     */
    static function checkPhone($attribute)
    {
        return preg_match("/^1\d{10}$/is", $attribute);
    }


    /**
     * 生成订单号
     * @param $userId
     *
     * @return string
     */
    static function makeOrderSn($userId) {
        return (date('y', time()) % 9 + 1) . sprintf('%010d', time() - 946656000)
            . sprintf('%03d', (float) microtime() * 1000)
            . sprintf('%02d', $userId % 1000);
    }

    /**
     * 加密函数
     *
     * @param string $txt 需要加密的字符串
     * @param string $key 密钥
     * @return string 返回加密结果
     */
    static function encrypt($txt, $key = '')
    {
        if (empty($txt))
            return $txt;
        if (empty($key))
            $key = md5(config('md5_key'));
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
        $ikey = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
        $nh1 = rand(0, 64);
        $nh2 = rand(0, 64);
        $nh3 = rand(0, 64);
        $ch1 = $chars{$nh1};
        $ch2 = $chars{$nh2};
        $ch3 = $chars{$nh3};
        $nhnum = $nh1 + $nh2 + $nh3;
        $knum = 0;
        $i = 0;
        while (isset($key{$i}))
            $knum += ord($key{$i++});
        $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
        $txt = base64_encode(time() . '_' . $txt);
        $txt = str_replace(array('+', '/', '='), array('-', '_', '.'), $txt);
        $tmp = '';
        $j = 0;
        $k = 0;
        $tlen = strlen($txt);
        $klen = strlen($mdKey);
        for ($i = 0; $i < $tlen; $i++) {
            $k = $k == $klen ? 0 : $k;
            $j = ($nhnum + strpos($chars, $txt{$i}) + ord($mdKey{$k++})) % 64;
            $tmp .= $chars{$j};
        }
        $tmplen = strlen($tmp);
        $tmp = substr_replace($tmp, $ch3, $nh2 % ++$tmplen, 0);
        $tmp = substr_replace($tmp, $ch2, $nh1 % ++$tmplen, 0);
        $tmp = substr_replace($tmp, $ch1, $knum % ++$tmplen, 0);
        return $tmp;
    }

    /**
     * 解密函数
     *
     * @param string $txt 需要解密的字符串
     * @param string $key 密匙
     * @return string 字符串类型的返回结果
     */
    static function decrypt($txt, $key = '', $ttl = 0)
    {
        if (empty($txt))
            return $txt;
        if (empty($key))
            $key = md5(config('md5_key'));

        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
        $ikey = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
        $knum = 0;
        $i = 0;
        $tlen = @strlen($txt);
        while (isset($key{$i}))
            $knum += ord($key{$i++});
        $ch1 = @$txt{$knum % $tlen};
        $nh1 = strpos($chars, $ch1);
        $txt = @substr_replace($txt, '', $knum % $tlen--, 1);
        $ch2 = @$txt{$nh1 % $tlen};
        $nh2 = @strpos($chars, $ch2);
        $txt = @substr_replace($txt, '', $nh1 % $tlen--, 1);
        $ch3 = @$txt{$nh2 % $tlen};
        $nh3 = @strpos($chars, $ch3);
        $txt = @substr_replace($txt, '', $nh2 % $tlen--, 1);
        $nhnum = $nh1 + $nh2 + $nh3;
        $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
        $tmp = '';
        $j = 0;
        $k = 0;
        $tlen = @strlen($txt);
        $klen = @strlen($mdKey);
        for ($i = 0; $i < $tlen; $i++) {
            $k = $k == $klen ? 0 : $k;
            $j = strpos($chars, $txt{$i}) - $nhnum - ord($mdKey{$k++});
            while ($j < 0)
                $j += 64;
            $tmp .= $chars{$j};
        }
        $tmp = str_replace(array('-', '_', '.'), array('+', '/', '='), $tmp);
        $tmp = trim(base64_decode($tmp));

        if (preg_match("/\d{10}_/s", substr($tmp, 0, 11))) {
            if ($ttl > 0 && (time() - substr($tmp, 0, 11) > $ttl)) {
                $tmp = null;
            } else {
                $tmp = substr($tmp, 11);
            }
        }
        return $tmp;
    }


}
