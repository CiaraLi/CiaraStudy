<?php

ini_set('default_charset', 'UTF-8');
error_reporting(0);
ob_clean();
header('Content-Type: text/html; charset=utf-8');
ini_set('mbstring.substitute_character', "none");
ini_set('max_execution_time', -1);
ini_set('memory_limit', '-1');
define('FIND_PIC', './img/none1.jpg;./img/none2.gif;./img/none3.jpg; ./img/none4.png; ./img/none5.jpg;./img/none51.jpg;./img/none52.jpg;');
include 'function.php';


define('_OPENFILE', '../book/book.csv'); //打开文件路径
define('_SAVEPATH', '../R-book/');          //保存文件路径 以／结尾
define('_INITIALVAL', true);        //是否读取默认值 
define('_INTEGRANT', false);        //为true时 无搜索结果不会添加到库中
define('_UPDATE', false);        // 执行更新
define('_BOOKDATA', true);        // 抓取书目信息
define('_PICTURE', true);        // 抓取图片信息
define('_ISBNPRE', '');             //默认isbn前缀
define('_WEIGHTUNIT', 'g');            //单本重量单位 
define('_CURRENCYCODE', 'CNY');        //货币单位
define('_LANGCODE', '');        //国家
define('_PAPER', 'p');        //
define('_CIDCODE', '');        //固定的cid
//数据源位置定义
define('_header', 0);            //
define('_cid', 99);             //cid
define('_isbn', 99);             //isbn--
define('_title', 99);            //标题--
define('_author', 99);           //作者--
define('_price', 99);            //价格--
define('_publisher', 99);          //出版社--
define('_edition', 99);           //版次--
define('_pubdate', 99);           //出版日期--
define('_page', 99);            //页数--
define('_paper', 99);            //纸张、媒介
define('_dimensions', 99);         //开本--
define('_abstract', 99);           //简介 摘要
define('_directory', 99);           //目录
define('_author_intro', 99);           //作者介绍
define('_trial_chapter', 99);           //书摘
define('_other_info', 99);           //推荐
define('_series', 99);           //丛书名、系列--
define('_word_count', 99);         //字数
define('_WORDUNIT', 0);         //字数
define('_quantity', 99);          //库存数量
define('_weight', 99);           //单本重量
define('_discount', 99);          //折扣
define('_sku', 99);             //SKU
define('_currency_code', 88);        //货币单位
define('_pubcountry_code', 88);       //出版社国籍
define('_longtitle', 88);          //详细标题
define('_language_code', 99);        //语言
define('_category_code', 29);        //分类编号 
define('_category_cip', 99);        //CIP分类
define('_target_user', 99);        //读者对象 
define('_package_count', 99);        //每包册数 
//抓取指定行数
define('_startrow', 0);            //row from 1
define('_endrow', 5000);            //  
define('_isbnarr', '');            //指定isbn列表
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

if (strpos(_OPENFILE, '.csv') !== false) {
    $csv = array();
    $file = fopen(_OPENFILE, 'r');

    while (($result = fgetcsv($file)) !== false) {
        $csv[] = $result;
    }
    fclose($file);
} else {

    $csv = readXLXSarray(_OPENFILE);
}
//preg_match_all('/\d+/',  $csv[_header][_isbn],$match);
$isbn_pre = isset($match[0]) ? implode($match[0], "") : _ISBNPRE;
for ($i = _startrow; $i < count($csv) && $i <= _endrow; $i++) {
    $value = $csv[$i];
    echo $isbnstr = preg_replace(_math_isbn, '', $value[_isbn]);
    $isbn = trim($isbn_pre . $isbnstr);
    if (strlen($isbn) < 10 || (_isbnarr && strpos(_isbnarr, $isbn) === false)) {
        continue;
    }
    if (_INITIALVAL) {//读取默认值 
        $defaultVal['cid'] = empty(_CIDCODE) ? trim($value[_cid]) : _CIDCODE;
        $defaultVal['title'] = trim(iconv("GBK", "UTF-8", $value[_title]));
        $defaultVal['title'] = preg_replace(['/《/', '/》/'], ['', ''], $defaultVal['title']);
        if (_BOOKDATA) {
            $defaultVal['publisher'] = trim(iconv("GBK", "UTF-8", $value[_publisher]));
            $defaultVal['author'] = trim(iconv("GBK", "UTF-8", $value[_author]));
            $defaultVal['paper'] = trim(iconv("GBK", "UTF-8", $value[_paper]));
            $defaultVal['series'] = trim(iconv("GBK", "UTF-8", $value[_series]));

            $defaultVal['abstract'] = trim(iconv("GBK", "UTF-8", $value[_abstract]));
            $defaultVal['directory'] = trim(iconv("GBK", "UTF-8", $value[_directory]));
            $defaultVal['author_intro'] = trim(iconv("GBK", "UTF-8", $value[_author_intro]));
            $defaultVal['trial_chapter'] = trim(iconv("GBK", "UTF-8", $value[_trial_chapter]));
            $defaultVal['other_info'] = trim(iconv("GBK", "UTF-8", $value[_other_info]));

            $defaultVal['sku'] = trim(iconv("GBK", "UTF-8", $value[_sku]));
            $defaultVal['longtitle'] = trim(iconv("GBK", "UTF-8", $value[_longtitle]));
            $defaultVal['dimensions'] = trim(iconv("GBK", "UTF-8", $value[_dimensions]));
            //$defaultVal['dimensions']=explode('/',$defaultVal['dimensions'])[1]; 

            $defaultVal['price'] = preg_replace(_math_price, '', $value[_price]);
            $defaultVal['pubdate'] = $value[_pubdate];
            $defaultVal['page'] = preg_replace(_math_page, '', $value[_page]);
            $defaultVal['word_count'] = preg_replace(_math_word_count, '', $value[_word_count]);
            if (_WORDUNIT) {
                $defaultVal['word_count'] = intval($defaultVal['word_count']) * 1000;
            }
            $defaultVal['quantity'] = preg_replace(_math_quantity, '', $value[_quantity]);
            $defaultVal['package_count'] = preg_replace(_math_quantity, '', $value[_package_count]);
            $defaultVal['weight'] = preg_replace(_math_weight, '', $value[_weight]);
            $defaultVal['edition'] = preg_replace(_math_edition, ' ', $value[_edition]);

            $defaultVal['category_code'] = trim(iconv("GBK", "UTF-8", $value[_category_code]));
            $defaultVal['category_cip'] = preg_replace(_math_cip, ' ', $value[_category_cip]);
            // $defaultVal['category_cip'] = trim(iconv("GBK", "UTF-8", $value[_category_cip]));
            $defaultVal['target_user'] = trim(iconv("GBK", "UTF-8", $value[_target_user]));

            $num = preg_replace(_math_discount, '', $value[_discount]);
            $defaultVal['discount'] = parseDiscount($num);
            $language_code = trim(iconv("GBK", "UTF-8", $value[_language_code]));
            $defaultVal['language_code'] = getLangCode($language_code);
            $pubcountry_code = trim(iconv("GBK", "UTF-8", $value[_pubcountry_code]));
            $defaultVal['pubcountry_code'] = getCountryCode($pubcountry_code);
            $defaultVal['weightunit'] = _WEIGHTUNIT;
            $defaultVal['currency_code'] = _CURRENCYCODE;
            $defaultVal['paper'] = _PAPER;
        }
    }
    $fetdata = [];
    if (!_UPDATE || _INTEGRANT) {
        $fetdata = Crawler_Dangdang($isbn);
        if (!$fetdata) {
            $fetdata = Crawler_DangdangWap($isbn);
        }
        if (!$fetdata && empty($amazon)) {
            $fetdata = Crawler_Amazon($isbn, _BOOKDATA, _PICTURE);
            if ($fetdata == 'need validate') {
                $amazon = 1;
            }
        }
        if (!$fetdata) {
            //   $fetdata = Crawler_Taoshu($isbn, _BOOKDATA, _PICTURE);
        }
    }
    if (_INTEGRANT && empty($fetdata) || $fetdata == 'need validate') {
        echo _OPENFILE . "-------- NoData:" . $i . "  " . $isbn . "\n";
        $sql_text = $isbn . "\n";
        file_put_contents(_SAVEPATH . 'nodata_isbn.txt', $sql_text, FILE_APPEND);
        $sql_text = $isbn . "<=>" . json_encode($defaultVal) . "\n";
        file_put_contents(_SAVEPATH . 'nodata.txt', $sql_text, FILE_APPEND);
    }
    if (!_INTEGRANT || !empty($fetdata)) {
        $fetdata['imagepath'] = trim($fetdata['imagepath']);
        $fetdata['sku'] = $defaultVal['sku'] ? addslashes(strip_tags(trim($defaultVal['sku']))) : trim($fetdata['sku']);
        $fetdata['author'] = $defaultVal['author'] ? addslashes(strip_tags(trim($defaultVal['author']))) : trim($fetdata['author']);
        $fetdata['title'] = $defaultVal['title'] ? addslashes(strip_tags(trim($defaultVal['title']))) : trim($fetdata['title']);
        $fetdata['longtitle'] = $defaultVal['longtitle'] ? addslashes(strip_tags(trim($defaultVal['longtitle']))) : trim($fetdata['longtitle']);
        $fetdata['longtitle'] = $fetdata['longtitle'] ? $fetdata['longtitle'] : $fetdata['title'];
        $fetdata['price'] = $defaultVal['price'] ? addslashes(strip_tags(trim($defaultVal['price']))) : trim($fetdata['price']);
        $fetdata['dimensions'] = $defaultVal['dimensions'] ? addslashes(strip_tags(trim($defaultVal['dimensions']))) : trim($fetdata['dimensions']);
        $fetdata['pubdate'] = $defaultVal['pubdate'] ? addslashes(strip_tags(trim($defaultVal['pubdate']))) : trim($fetdata['pubdate']);
        $fetdata['publisher'] = $defaultVal['publisher'] ? addslashes(strip_tags(trim($defaultVal['publisher']))) : trim($fetdata['publisher']);
        $fetdata['edition'] = $defaultVal['edition'] ? addslashes(strip_tags(trim($defaultVal['edition']))) : trim($fetdata['edition']);
        $fetdata['pages'] = $defaultVal['pages'] ? addslashes(strip_tags(trim($defaultVal['pages']))) : trim($fetdata['pages']);
        $fetdata['word_count'] = $defaultVal['word_count'] ? addslashes(strip_tags(trim($defaultVal['word_count']))) : trim($fetdata['word_count']);
        $fetdata['series'] = $defaultVal['series'] ? addslashes(strip_tags(trim($defaultVal['series']))) : trim($fetdata['series']);
        $fetdata['sku'] = $defaultVal['sku'] ? addslashes(strip_tags(trim($defaultVal['sku']))) : trim($fetdata['sku']);
 
        $fetdata['category_code'] = trim($fetdata['category_code']); 
    }

    echo _OPENFILE . " No:" . $i . "  " . $isbn . "\n";

    if (!_UPDATE) {
        $cid = empty($defaultVal['cid']) ? "" : ' cid_code=\'' . empty($defaultVal['cid']) . '\' , ';
        $sql_text = "INSERT INTO  books set " . $cid . " isbn='" . $isbn . "', title='" . $fetdata['title'] . "', longtitle='" . $fetdata['longtitle'] . "', author='" . $fetdata['author'] .
                "' , created_at=now(), updated_at=now(); \n";

        file_put_contents(_SAVEPATH . 'bookdetails_' . _startrow . '-' . _endrow . '.sql', $sql_text, FILE_APPEND);
    } else {
        $update = "";
        $cid = empty($defaultVal['cid']) ? "" : (' cid_code=\'' . $defaultVal['cid'] . '\'  and ');
        $sql_text = "update books  set ";

        empty($fetdata['imagepath']) ? '' : $update = (" imagepath='" . $fetdata['imagepath'] . "' ");

        if (!empty($update)) {
            $sql_text = $sql_text . trim($update, ',') . " where  " . $cid . " isbn='" . $isbn . "'   ;\n";
            file_put_contents(_SAVEPATH . 'bookupdate_' . _startrow . '-' . _endrow . '.sql', $sql_text, FILE_APPEND);
        }
    }
}
