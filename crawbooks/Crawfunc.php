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
class Crawfunc {

    private static $noImgDir = './img/';
    private static $tmp = './tmp/';
    private static $saveDIR = './bookimg/';
    public static $resultDIR = '';

    /**
     * 
     * @param type $isbn 抓取的ISBN
     * @param type $bookinfo  已有的图书信息
     * @param type $bookdata 是否抓取图书基本信息
     * @param type $picture  是否获取图片信息 
     * @return type
     */
    static function Crawler_Dangdang($isbn, $bookinfo, $bookdata = true, $picture = true, $saveimg = false) {

        $crawlURL = 'http://search.dangdang.com/?key=' . $isbn . '&act=input';
        $fetchData = self::file_get_contents_curl($crawlURL, 1);

        preg_match_all('/<li ddt-pit=".*" class="line1" id="p\d+">.*<a.*title="(.*)".*href="(.*)".*>(.*)<\/a>.*<\/li>/isU', $fetchData, $getLink, PREG_SET_ORDER);
        preg_match_all('/<li ddt-pit=".*" class="line1" id="p\d+">.*<p.*class="price".*>(.*)<span.*class="search_now_price".*>(.*)<\/span>.*<span.*class="search_pre_price".*>(.*)<\/span>/isU', $fetchData, $price, PREG_SET_ORDER);
        preg_match_all('/<li ddt-pit=".*" class="line1" id="p\d+">.*<a.*name=\'itemlist-author\'.*>(.*)<\/a>/isU', $fetchData, $author, PREG_SET_ORDER); //作者
        preg_match_all('/<li ddt-pit=".*" class="line1" id="p\d+">.*<a.*name=\'P_cbs\'.*>(.*)<\/a>/isU', $fetchData, $press, PREG_SET_ORDER); //出版社
        preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $getLink[0][3], $imageLink, PREG_SET_ORDER); //图

        $title_fet = $getLink[0][1];
        $pLink = $getLink[0][2];

        if (!empty($title_fet) && $bookdata) {
            $fetchProData = self::file_get_contents_curl($pLink, 1);

            $proID = str_replace(".html", "", str_replace("http://product.dangdang.com/", "", $pLink));
            $prJson = 'http://product.dangdang.com/?r=callback%2Fdetail&productId=' . $proID . '&templateType=publish&describeMap=&shopId=0&categoryPath=01.41.41.05.00.00';
            $getJson = file_get_contents($prJson);
            $detailJson = json_decode($getJson, true);
            preg_match_all('/<h2>.*<span.*class="head_title_name" title="(.*)">/isU', $fetchProData, $longtitle_fet, PREG_SET_ORDER);
            preg_match_all('/<div.*id="detail_all".*>.*<li>.*版[^次]*?次.*：(.*)<\/li>/isU', $fetchProData, $edition_fet, PREG_SET_ORDER);
            preg_match_all('/<div.*id="detail_all".*>.*<li>.*包[^装]*?装.*：(.*)<\/li>/isU', $fetchProData, $package_type_fet, PREG_SET_ORDER);
            preg_match_all('/<div.*id="detail_all".*>.*<li>.*是否套装.*：(.*)<\/li>/isU', $fetchProData, $set_type_fet, PREG_SET_ORDER);
            preg_match_all('/<div.*id="detail_all".*>.*<li>.*页[^数]*?数.*：(.*)<\/li>/isU', $fetchProData, $pages_fet, PREG_SET_ORDER);
            preg_match_all('/<div.*id="detail_all".*>.*<li>.*字[^数]*?数.*：(.*)<\/li>/isU', $fetchProData, $word_fet, PREG_SET_ORDER);
            preg_match_all('/<span.*>出版时间:(.*)(&nbsp;)*<\/span>/isU', $fetchProData, $pubdate_fet, PREG_SET_ORDER);
            preg_match_all('/<div.*id="detail_all".*>.*<li>.*印刷时间.*：(.*)<\/li>/isU', $fetchProData, $printime_fet, PREG_SET_ORDER);
            preg_match_all('/<div.*id="detail_all".*>.*<li>.*开[^本]*?本.*：(.*)<\/li>/isU', $fetchProData, $size_fet, PREG_SET_ORDER);
            preg_match_all('/<div.*id="detail_all".*>.*<li>.*纸[^张]*?张.*：(.*)<\/li>/isU', $fetchProData, $paper_fet, PREG_SET_ORDER);
            preg_match_all('/id="detail_all".*>.*<li>.*丛书名.*：(.*)<\/li>/isU', $fetchProData, $series_fet, PREG_SET_ORDER);
            preg_match_all('/cp.*?cp.*?cp01.(\d{2}).(\d{2}).(\d{2}).(\d{2}).(\d{2})/isU', $fetchProData, $category_fet, PREG_SET_ORDER);

            $maps = $detailJson['data']['navigationLabels'];

            //简介
            preg_match_all('/id\s*=\s*"content-textarea">(.*)<\/textarea>/isU', ($detailJson['data']['html']), $content_fet, PREG_SET_ORDER);
            if (!$content_fet) {
                preg_match_all('/id\s*=\s*"content-all"><\/span>(.*)?<\/div><\/div>/isU', $detailJson['data']['html'], $content_fet, PREG_SET_ORDER);
            }
            //目录
            preg_match_all('/id\s*=\s*"catalog-textarea">(.*)<\/textarea>/isU', ($detailJson['data']['html']), $book_directory_fet, PREG_SET_ORDER);
            if (!$book_directory_fet) {
                preg_match_all('/id\s*=\s*"catalog-all"><\/span>(.*)<\/div>/isU', $detailJson['data']['html'], $book_directory_fet, PREG_SET_ORDER);
            }
            //作者
            preg_match_all('/id\s*=\s*"authorIntroduction-all"><\/span>(.*)<\/div>/isU', ($detailJson['data']['html']), $author_intro_fet, PREG_SET_ORDER);
            if (!$author_intro_fet) {
                preg_match_all('/id\s*=\s*"authorIntroduction">(.*)<\/p>/isU', $detailJson['data']['html'], $author_intro_fet, PREG_SET_ORDER);
            }
            //试读章节
            preg_match_all('/id\s*=\s*"extract-textarea">(.*)<\/textarea>/isU', $detailJson['data']['html'], $trial_chapter_fet, PREG_SET_ORDER);
            if (!$trial_chapter_fet) {
                preg_match_all('/id\s*=\s*"extract-show-all"><\/span>(.*)<\/div>/isU', ($detailJson['data']['html']), $trial_chapter_fet, PREG_SET_ORDER);
            }

            //feednack
            preg_match_all('/id\s*=\s*"mediaFeedback-show-all"[^>]*>(.*)<\/textarea>/isU', $detailJson['data']['html'], $media_fet, PREG_SET_ORDER);
            if (!$media_fet) {
                preg_match_all('/id\s*=\s*"mediaFeedback"><\/span>(.*)<\/div>/isU', ($detailJson['data']['html']), $media_fet, PREG_SET_ORDER);
            }
            //其他
            preg_match_all('/id\s*=\s*"abstract-all"><\/span>(.*)<\/div>/isU', $detailJson['data']['html'], $other_info_fet, PREG_SET_ORDER);
            if (!$other_info_fet) {
                preg_match_all('/id\s*=\s*"abstractl"><\/span>(.*)<\/div>/isU', ($detailJson['data']['html']), $other_info_fet, PREG_SET_ORDER);
            }

            $bookinfo = self::getDecodeString((array) $bookinfo, 'author', $author[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'title', $title_fet, false);
            $bookinfo['title'] = preg_replace(_math_longtitle, '', $bookinfo['title']);
            $bookinfo = self::getDecodeString($bookinfo, 'longtitle', $longtitle_fet[0][1], true);
            $bookinfo['longtitle'] = preg_replace(_math_longtitle, '', $bookinfo['longtitle']);
            if (!empty($bookinfo['longtitle'])) {
                preg_match_all('/(好评|店铺|礼券)/isU', $bookinfo['longtitle'], $ad, PREG_SET_ORDER);
                if ($ad) {
                    $bookinfo['longtitle'] = $bookinfo['title'];
                }
            }
            $bookinfo = self::getDecodeString($bookinfo, 'price', preg_replace(_math_price, '', trim($price[0][3])), false);
            $bookinfo = self::getDecodeString($bookinfo, 'dimensions', $size_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'pubdate', $pubdate_fet[0][1]);
            empty($bookinfo['pubdate']) ? null : $bookinfo = self::getDecodeString($bookinfo, 'pubdate', $printime_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'publisher', $press[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'edition', $edition_fet[0][1]);

            $bookinfo = self::getDecodeString($bookinfo, 'package_type', $package_type_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'set_type', $set_type_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'pages', $pages_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'word_count', $word_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'series', $series_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'weight', $weight_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'weight_unit', $weight_fet[0][2]);
            $bookinfo = self::getDecodeString($bookinfo, 'language_code', $lang_fet[0][1]);


            $bookinfo = self::getDecodeContent($bookinfo, 'abstract', $content_fet[0][1]);
            $bookinfo = self::getDecodeContent($bookinfo, 'directory', $book_directory_fet[0][1]);
            $bookinfo = self::getDecodeContent($bookinfo, 'author_intro', $author_intro_fet[0][1]);
            $bookinfo = self::getDecodeContent($bookinfo, 'trial_chapter', $trial_chapter_fet[0][1]);
            $bookinfo = self::getDecodeContent($bookinfo, 'other_info', $other_info_fet[0][1]);
            !empty($bookinfo['other_info']) ? null : $bookinfo = self::getDecodeContent($bookinfo, 'other_info', $media_fet[0][1]);

            //only dangdang save category_code
            $category1 = intval(addslashes(strip_tags(trim($category_fet[0][1]))));
            $category2 = intval(addslashes(strip_tags(trim($category_fet[0][2]))));
            $category_trans = (string) $category1 . "_" . (string) $category2;
            $bookinfo['category_code'] = empty($category1) ? '' : addslashes(strip_tags(trim($category_trans)));
        }
        if ($picture && !empty($imageLink[0][2])) {
            $imageSmall = $imageLink[0][2];
            !empty($bookinfo['imagepath']) ? null : $bookinfo['imagepath'] = self::http_compare_image($imageSmall, $isbn, $saveimg);
            !empty($bookinfo['image_url']) ? null : $bookinfo['image_url'] = empty($bookinfo['imagepath']) ? '' : $imageSmall;
        }

        return $bookinfo;
    }

//当当wap   http://search.m.dangdang.com/search.php?keyword=
    static function Crawler_DangdangWap($isbn, $bookinfo = [], $bookdata = true, $picture = true, $saveimg = false) {
        $crawlURL = 'http://search.m.dangdang.com/search_ajax.php?cid=0&keyword=' . $isbn . '&have_ad=1&act=get_product_flow_search';
        $fetchData = self::file_get_contents_curl($crawlURL); //crawl,file_get_contents_curl,jsonCrawl,curlWithoutICONV
        $fetchData = json_decode($fetchData);
        if ($fetchData->products) {
            $product = $fetchData->products[0];
            if ($bookdata) {
                $detail = json_encode($product, JSON_UNESCAPED_UNICODE);
                preg_match_all('/([\x{4e00}-\x{9fa5}]+系列)/u', $detail, $series_fet, PREG_SET_ORDER);

                $pLink = $product->product_url;
                $pdetail = self::file_get_contents_curl($pLink);
                // pc  http://category.dangdang.com/cp01.25.11.04.00.00.html
                // wap  http://search.m.dangdang.com/category.php?cid=01.25.11.04.00.00&sid=c83c14d89336c41feca6950c3cb1a474
                preg_match_all('/category\.php\?cid=01.(\d{2}).(\d{2}).(\d{2}).(\d{2}).(\d{2})/isU', $pdetail, $category_fet, PREG_SET_ORDER);
                preg_match_all('/<a dd_name=".*" href="(.*product.m.dangdang.com\/detail.*html.*)?">详情<\/a>/isU', $pdetail, $detailurl_fet, PREG_SET_ORDER);
                $detailtext = self::file_get_contents_curl($detailurl_fet[0][1]);
                preg_match_all('/【内容简介】([^【]*)?/isU', $detailtext, $abstract_fet, PREG_SET_ORDER);
                preg_match_all('/【目录】([^【]*)?/isU', $detailtext, $book_directory_fet, PREG_SET_ORDER);
                preg_match_all('/【作者简介】([^【]*)?/isU', $detailtext, $author_intro_fet, PREG_SET_ORDER);
                preg_match_all('/【精彩书摘】([^【]*)?/isU', $detailtext, $trial_chapter_fet, PREG_SET_ORDER);
                preg_match_all('/【编辑推荐】([^【]*)?/isU', $detailtext, $other_info_fet, PREG_SET_ORDER);

                $bookinfo = self::getDecodeString($bookinfo, 'title', $product->name);
                $bookinfo = self::getDecodeString($bookinfo, 'price', $product->price);
                $bookinfo = self::getDecodeString($bookinfo, 'author', $product->authorname);
                $bookinfo = self::getDecodeString($bookinfo, 'publisher', $product->publisher);
                $bookinfo = self::getDecodeString($bookinfo, 'pubdate', $product->publish_date);

                $bookinfo = self::getDecodeContent($bookinfo, 'abstract', $abstract_fet[0][1]);
                $bookinfo = self::getDecodeContent($bookinfo, 'directory', $book_directory_fet[0][1]);
                $bookinfo = self::getDecodeContent($bookinfo, 'author_intro', $author_intro_fet[0][1]);
                $bookinfo = self::getDecodeContent($bookinfo, 'trial_chapter', $trial_chapter_fet[0][1]);
                $bookinfo = self::getDecodeContent($bookinfo, 'other_info', $other_info_fet[0][1]);

                $category1 = intval(addslashes(strip_tags(trim($category_fet[0][1]))));
                $category2 = intval(addslashes(strip_tags(trim($category_fet[0][2]))));
                $category_trans = (string) $category1 . "_" . (string) $category2;
                $bookinfo['category_code'] = addslashes(strip_tags(trim($category_trans)));
            }
            if ($picture && !empty($product->image_url)) {
                $imageSmall = $product->image_url;
                !empty($bookinfo['imagepath']) ? null : $bookinfo['imagepath'] = self::http_compare_image($imageSmall, $isbn, $saveimg);
                !empty($bookinfo['image_url']) ? null : $bookinfo['image_url'] = empty($bookinfo['imagepath']) ? '' : $imageSmall;
            }
        }
        return $bookinfo;
    }

    /**
     * 
     * @param type $isbn 抓取的ISBN
     * @param type $bookinfo  已有的图书信息
     * @param type $bookdata 是否抓取图书基本信息
     * @param type $picture  是否获取图片信息 
     * @return type
     */
    static function Crawler_Taoshu($isbn, $bookinfo, $bookdata = true, $picture = true, $saveimg = false) {
        $crawlURL = 'http://www.taoshu.com/AdvanceSearch.aspx?isbn=' . $isbn;
        preg_match_all('/action="\/(.*)\/validateCaptcha"/i', $fetchProData, $error1, PREG_SET_ORDER);
        $thread = self::file_get_contents_curl($crawlURL);

        preg_match_all('/<a id="lbooks_bookname_[\w]+" href="([^"]+)">([^<]+)<\/a>/isU', $thread, $getLink, PREG_SET_ORDER);
        preg_match_all('/<div class="authors">.*target="_blank">(.*)<\/a>[^<]*[^<]\/\s<a href=.*target="_blank">(.*)<\/a>\s*[^<]\/\s*([a-zA-Z0-9.-]+)<\/div>/isU', $thread, $authors, PREG_SET_ORDER);
        preg_match_all('/<div\s*class="price"><span class="new-price">([0-9.]+)<\/span><\/div>/i', $thread, $price, PREG_SET_ORDER);

        $title_fet = $getLink[0][2];
        $pLink = urldecode($getLink[0][1]);

        $fetchProData = self::file_get_contents_curl("http://www.taoshu.com" . $pLink); //图书详情页

        if ($title_fet && $bookdata) {
            preg_match_all('/<p>作　者：<a href="\/author.*>(.*)<\/a>.*<\/p>/isU', $fetchProData, $authorss, PREG_SET_ORDER);
            preg_match_all('/<p>出版社：.*\/press_.*>(.*)<\/a><\/p>/isU', $fetchProData, $press, PREG_SET_ORDER);
            preg_match_all('/<span>出版时间：([0-9A-Za-z-]+)<\/span>/isU', $fetchProData, $pubdate, PREG_SET_ORDER);
            $author_fet = $authors[0][1];
            $publisher_fet = $authors ? $authors[0][2] : $press[0][1];
            $pubdate_fet = $authors ? $authors[0][3] : $pubdate[0][1];
            $price_fet = $price[0][1];

            preg_match_all('/<span>页数：([^<]*)<\/span>/isU', $fetchProData, $pages_fet, PREG_SET_ORDER);
            preg_match_all('/<span>开本：([^<]*)<\/span>/isU', $fetchProData, $size_fet, PREG_SET_ORDER);
            preg_match_all('/<span>字数：([0-9]+)<\/span>/isU', $fetchProData, $word_fet, PREG_SET_ORDER); //nodata 

            preg_match_all('/<p>丛书名：.*<a href="\/series_[^>]*target="_blank">(.*)<\/a><\/p>/isU', $fetchProData, $series_fet, PREG_SET_ORDER);

            preg_match_all('/<div id="content_text".*>(.*)<\/div>/isU', $fetchProData, $abstract_fet, PREG_SET_ORDER);
            preg_match_all('/<div id="catalog_text".*>(.*)<\/div>/isU', $fetchProData, $book_directory_fet, PREG_SET_ORDER);
            preg_match_all('/<div id="authorintro_text".*>(.*)<\/div>/isU', $fetchProData, $author_intro_fet, PREG_SET_ORDER);


            $bookinfo = self::getDecodeString((array) $bookinfo, 'author', $author_fet);
            $bookinfo = self::getDecodeString($bookinfo, 'title', $title_fet, false);

            $bookinfo = self::getDecodeString($bookinfo, 'price', preg_replace(_math_price, '', trim($price[0][1])), false);
            $bookinfo = self::getDecodeString($bookinfo, 'dimensions', $size_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'pubdate', $pubdate_fet);
            $bookinfo = self::getDecodeString($bookinfo, 'publisher', $publisher_fet);
            $bookinfo = self::getDecodeString($bookinfo, 'edition', $edition_fet[0][1]);

            $bookinfo = self::getDecodeString($bookinfo, 'package_type', $package_type_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'set_type', $set_type_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'pages', $pages_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'word_count', $word_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'series', $series_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'weight', $weight_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'weight_unit', $weight_fet[0][2]);
            $bookinfo = self::getDecodeString($bookinfo, 'language_code', $lang_fet[0][1]);


            $bookinfo = self::getDecodeContent($bookinfo, 'abstract', $abstract_fet[0][1]);
            $bookinfo = self::getDecodeContent($bookinfo, 'directory', $book_directory_fet[0][1]);
            $bookinfo = self::getDecodeContent($bookinfo, 'author_intro', $author_intro_fet[0][1]);
            $bookinfo = self::getDecodeContent($bookinfo, 'trial_chapter', $trial_chapter_fet[0][1]);
            $bookinfo = self::getDecodeContent($bookinfo, 'other_info', $other_info_fet[0][1]);
            !empty($bookinfo['other_info']) ? null : $bookinfo = self::getDecodeContent($bookinfo, 'other_info', $media_fet[0][1]);
        }
        if ($picture) {
            preg_match_all('/<div class="pic".*<img src="(.*)"[^>]*><\/a><\/div>/isU', $fetchProData, $imageLink, PREG_SET_ORDER);
            $imageLink = addslashes(strip_tags(trim($imageLink[0][1])));
            empty($imageLink) || !empty($bookinfo['imagepath']) ? null : $bookinfo['imagepath'] = self::http_compare_image($imageLink, $isbn, $saveimg);
            !empty($bookinfo['image_url']) ? null : $bookinfo['image_url'] = empty($bookinfo['imagepath']) ? '' : $imageLink;
        }


        return $bookinfo;
    }

//亚马逊 https://www.amazon.cn/gp/search/ref=sr_adv_b/?field-isbn=9787542951113
    static function Crawler_Amazon($isbn, $bookinfo, $bookdata = true, $picture = true, $saveimg = false) {

        $crawlURL = 'https://www.amazon.cn/gp/search/ref=sr_adv_b/?field-isbn=' . $isbn;
        preg_match_all('/action="\/(.*)\/validateCaptcha"/i', $fetchProData, $error1, PREG_SET_ORDER);
        $thread = self::file_get_contents_curl($crawlURL);

        preg_match_all('/<li id="result_0".*<a class="[^>]*? s-access-detail-page .*".* title="(.*)" .*href="(.*)"><h2 .* s-access-title[^>]*?>.*<\/h2><\/a>/isU', $thread, $getLink, PREG_SET_ORDER);
        preg_match_all('/<span class="[^>]* s-price [^>]*">￥([0-9.]*)<\/span>/i', $thread, $price, PREG_SET_ORDER);

        $title_fet = $getLink[0][1];
        $pLink = urldecode($getLink[0][2]);
        preg_match_all('/dp\/([^\/]*+)/isU', $pLink, $asin_fet, PREG_SET_ORDER);

        $asin = urldecode($asin_fet[0][1]);

        $fetchProData = self::file_get_contents_curl($pLink); //图书详情页
        preg_match_all('/action="\/(.*)\/validateCaptcha"/i', $fetchProData, $error2, PREG_SET_ORDER);
        if ($error1 || $error2) {
            return $bookinfo;
        }
        if ($title_fet && empty($error) && $bookdata) {
            preg_match_all('/field-author=.*">(.*)<\/a>/isU', $fetchProData, $author_fet, PREG_SET_ORDER);
            preg_match_all('/<b>出版社[^<]*<\/b>([^<]+); 第([^<]+)版\(?([^<]*)\)?<\/li>/isU', $fetchProData, $press, PREG_SET_ORDER);
            preg_match_all('/<span id="productTitle"[^>]]*>(.*)<\/span>/isU', $fetchProData, $longtitle_fet, PREG_SET_ORDER);
            preg_match_all('/<b>([^<]*装)[^<]*<\/b>(([^<]+)页)?<\/li>/isU', $fetchProData, $pages_fet, PREG_SET_ORDER);
            preg_match_all('/<b>开本[^<]*<\/b>([^<]*)<\/li>/isU', $fetchProData, $size_fet, PREG_SET_ORDER);
            preg_match_all('/<b>.*字 数[^<]*<\/b>([^<]*)<\/li>/isU', $fetchProData, $word_fet, PREG_SET_ORDER); //nodata
            preg_match_all('/<b>.*商品重量[^<]*<\/b>([^<]*)(k?g?[^<\w]*)<\/li>/isU', $fetchProData, $weight_fet, PREG_SET_ORDER); //nodata
            preg_match_all('/<b>.*纸 张[^<]*<\/b>([^<]*)<\/li>/isU', $fetchProData, $paper_fet, PREG_SET_ORDER);
            preg_match_all('/<b>.*丛书名[^<]*<\/b>([^<]*)<\/li>/isU', $fetchProData, $series_fet, PREG_SET_ORDER);
            preg_match_all('/<b>语种[^<]*<\/b>([^<]*)<\/li>/isU', $fetchProData, $lang_fet, PREG_SET_ORDER);
            preg_match_all('/bookDescEncodedData = "([^"]*)"/is', $fetchProData, $content_fet, PREG_SET_ORDER);

            $authorLink = 'https://www.amazon.cn/gp/product-description/ajaxGetProuductDescription.html?ref_=dp_apl_pc_loaddesc&asin=' . $asin . '&deviceType=json';
            $authorData = self::file_get_contents_curl($authorLink); //作者简介页
            //目录
            preg_match_all('/<h3>(目录|&#30446;&#24405;)<\/h3>(.*)<\/div>/isU', $authorData, $book_directory_fet, PREG_SET_ORDER);
            //作者
            preg_match_all('/<h3>(作者|&#20316;&#32773;&#31616;&#20171;)<\/h3>(.*)<\/div>/isU', $authorData, $author_intro_fet, PREG_SET_ORDER);
            //试读章节
            preg_match_all('/<h3>(试读章节|&#30446;&#24405;)<\/h3>(.*)<\/div>/isU', $authorData, $trial_chapter_fet, PREG_SET_ORDER);
            //其他
            preg_match_all('/<h3>(编辑推荐|&#32534;&#36753;&#25512;&#33616;)<\/h3>(.*)<\/div>/isU', $authorData, $other_info_fet, PREG_SET_ORDER);


            $bookinfo = self::getDecodeString((array) $bookinfo, 'author', $author_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'title', $title_fet, false);

            $bookinfo = self::getDecodeString($bookinfo, 'price', preg_replace(_math_price, '', trim($price[0][1])), false);
            $bookinfo = self::getDecodeString($bookinfo, 'dimensions', $size_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'pubdate', $press[0][3]);
            $bookinfo = self::getDecodeString($bookinfo, 'publisher', $press[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'edition', $press[0][2]);

            $bookinfo = self::getDecodeString($bookinfo, 'package_type', $pages_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'set_type', $set_type_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'pages', $pages_fet[0][3]);
            $bookinfo = self::getDecodeString($bookinfo, 'word_count', $word_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'series', $series_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'weight', $weight_fet[0][1]);
            $bookinfo = self::getDecodeString($bookinfo, 'weight_unit', $weight_fet[0][2]);
            $bookinfo = self::getDecodeString($bookinfo, 'language_code', $lang_fet[0][1]);


            $bookinfo = self::getDecodeContent($bookinfo, 'abstract', $content_fet[0][1]);
            $bookinfo = self::getDecodeContent($bookinfo, 'directory', $book_directory_fet[0][2]);
            $bookinfo = self::getDecodeContent($bookinfo, 'author_intro', $author_intro_fet[0][2]);
            $bookinfo = self::getDecodeContent($bookinfo, 'trial_chapter', $trial_chapter_fet[0][2]);
            $bookinfo = self::getDecodeContent($bookinfo, 'other_info', $other_info_fet[0][2]);
            !empty($bookinfo['other_info']) ? null : $bookinfo = self::getDecodeContent($bookinfo, 'other_info', $media_fet[0][2]);
        }
        if ($picture) {
            preg_match_all('/<img[^>]*src=(\'|\")([^"\']*)\\1[^>]*id="[^>]*imgBlkFront"[^>]*>/i', $fetchProData, $imageLink, PREG_SET_ORDER);
            $imageSmall = addslashes(strip_tags(trim($imageLink[0][2])));
            empty($imageSmall) || !empty($bookinfo['imagepath']) ? null : $bookinfo['imagepath'] = self::http_compare_image($imageSmall, $isbn, $saveimg);
            !empty($bookinfo['image_url']) ? null : $bookinfo['image_url'] = empty($bookinfo['imagepath']) ? '' : $imageSmall;
        }

        return $bookinfo;
    }

    static function file_get_contents_curl($url, $utf = 0) {
        $headers[] = self::UserAgent(); //<-- this is user agent
        $headers[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $headers[] = "Accept-Language:en-us,en;q=0.5";
        $headers[] = "Accept-Encoding:gzip,deflate";
        $headers[] = "Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $headers[] = "Keep-Alive:115";
        $headers[] = "Connection:keep-alive";
        $headers[] = "Cache-Control:max-age=0";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_ENCODING, "UTF-8");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        $data = curl_exec($curl);

        curl_close($curl);
        usleep(100000);
        if ($utf) {
            $data = iconv("GBK", "UTF-8", $data);
        }
        return $data;
    }

    static function getDecodeString($info, $key, $str, $recover = false) {
        if (!empty($str) && (empty(trim($info[$key])) || $recover)) {
            $chars = htmlentities($str);
            $chars = html_entity_decode($chars);
            $chars = str_replace("&nbsp;", "", trim($chars));
            $chars = strip_tags($chars);
            $chars = addslashes($chars);
            empty(trim($info[$key])) || ($recover && mb_strlen($info[strval($key)]) > mb_strlen(strval($chars)) / 2) ? $info[$key] = trim($chars) : null;
        }
        return $info;
    }

    static function getDecodeContent($info, $key, $str) {
        if (!empty($str) && (empty($info[$key]) || mb_strlen($info[$key]) < mb_strlen($str)) / 2) {
            $chars = urldecode($str);
            $chars = html_entity_decode($chars);
            $chars = preg_replace("/<p>((?!<\/p>)*)<\/p>/is", "\\1<br>", trim($chars));
            $chars = strip_tags($chars, '<br>');
            $chars = addslashes($chars);
            $chars = str_replace("&nbsp;", "", trim($chars));
            false !== strpos($chars, '暂缺') ? $chars = "" : null;
            false !== strpos($chars, '暂时没有内容') ? $chars = "" : null;
            $chars = preg_replace("/(\\n(\r|\\s)*)+/", "\n", trim($chars));
            $chars = str_replace(array("\r\n", "\r", "\n"), "", $chars);
            mb_strlen($info[strval($key)]) > mb_strlen(strval($str)) / 2 ? null : $info[$key] = trim($chars);
        }
        return $info;
    }

    static function UserAgent() {
        $agents = ["User-Agent:Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13",
            "Mozilla/5.0 (Windows; U; Windows NT 5.2) Gecko/2008070208 Firefox/3.0.1",
            "Mozilla/5.0 (Windows; U; Windows NT 5.1) Gecko/20070803 Firefox/1.5.0.12",
            "Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Version/3.1 Safari/525.13",
            "Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 ",
            "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; Maxthon/3.0)",
            "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.5 Safari/534.55.3",
            "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)",
        ];

        return array_rand($agents);
    }

    static function http_save_image($isbn, $url = '', $return_content = null) {
        if (empty($return_content) && !empty($url)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2); //设置超时时间
            curl_setopt($ch, CURLOPT_URL, $url);
            ob_start();
            curl_exec($ch);
            $return_content = ob_get_contents();
            ob_end_clean();
            curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
        $info = pathinfo($url);
        $filename = '';
        if (!empty($return_content) && !empty($info['extension'])) {
            $savedir = empty(self::$resultDIR) ? self::$saveDIR : self::$resultDIR;
            $filename = trim($isbn) . '.' . $info['extension'];
            $fps = @fopen($savedir . "/b/" . $filename, "w");
            $fpb = @fopen($savedir . "/s/" . $filename, "w");
            @fwrite($fps, $return_content);
            @fwrite($fpb, $return_content);
        }
        return $filename;
    }

    static function http_compare_image($url, $isbn, $save = false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2); //设置超时时间
        curl_setopt($ch, CURLOPT_URL, $url);
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $info = pathinfo($url);
        $filename = '';
        if (!empty($info['extension'])) {
            //move to tmp
            $tmpname = 'tmp' . (time() % 5) . '.' . $info['extension'];
            $fps = @fopen(self::$tmp . "/" . $tmpname, "w");
            @fwrite($fps, $return_content);
            //对比并过滤无用图片
            if (!self::comparePicture(self::$tmp . "/" . $tmpname)) {
                $filename = trim($isbn) . '.' . $info['extension'];
                if ($save == true) {
                    self:: http_save_image($isbn, $url, $return_content);
                }
            }
        }
        return $filename;
    }

    /**
     * //获取指定卖家无图片的商品isbn
     * @param type $cid
     * @param type $from
     * @param type $to
     * @return type
     */
    static function getISBN_byImage($cid, $from, $to) {
        //https://books.maishumaishu.com/ajaxload/companybooks?seller_cid=FZCBS&key_words=&showtype=image&act=details&page=10
        $url = "ajaxload/companybooks?seller_cid={$cid}&key_words=&showtype=image";
        $fetchProData = self::file_get_contents_curl("https://books.maishumaishu.com/" . $url); //图书详情页

        $isbnlist = [];
        if ($fetchProData) {
            for ($i = $from; $i <= $to; $i++) {
                $url = "ajaxload/companybooks?seller_cid={$cid}&key_words=&showtype=image&page=" . intval($i);
                $fetchProData = self::file_get_contents_curl("https://books.maishumaishu.com/" . $url); //图书详情页
                $preg = "/<script(.*)?\/script>/isU";
                $fetchProData = preg_replace($preg, "", $fetchProData);
                $preg = "/<style(.*)?\/style>/isU";
                $fetchProData = preg_replace($preg, "", $fetchProData);
                preg_match_all('/<img src="(?<image>https:\/\/booksbackend.maishumaishu.com\/upload\/products\/b\/(?<isbn>\d+).jpg)"/isU', $fetchProData, $matches, PREG_SET_ORDER);

                if (!empty($matches)) {
                    foreach ($matches as $key => $link) {
                        $imageLink = $link['image'];
                        $isbn = $link['isbn'];
                        $path = self::http_compare_image($imageLink, $isbn, false);
                        if (empty($path)) {
                            $isbnlist[] = $isbn;
                        }
                    }
                }
            }
        }
        return $isbnlist;
    }

    /**
     * 比较两个图片是否一致
     * @param type $pic
     * @return boolean
     */
    static function comparePicture($pic) {
        $nolist = scandir(self::$noImgDir);
        foreach ($nolist as $key => $val) {
            if (is_file(self::$noImgDir . $val)) {
                $same = (ImageHash::isImageFileSimilar($pic, self::$noImgDir . $val));
                if ($same) {
                    return true;
                }
            }
        }
        return false;
    }

}
