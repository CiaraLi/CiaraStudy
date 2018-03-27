<?php

function readXLXSarray($newfilename) {
    $inputFileName = $newfilename;
    //  Read your Excel workbook
    try {
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);
    } catch (Exception $e) {
        die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                . '": ' . $e->getMessage());
    }

    //  Get worksheet dimensions
    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    for ($row = _startrow; $row <= _endrow && $row <= $highestRow; $row++) {
        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
        $need[$row] = $rowData[0];
    }
    return $need;
}

//当当   http://search.dangdang.com/?key=9787542936219&act=input
function Crawler_Dangdang($isbn, $bookdata = true, $picture = true) {
    $fetdata = [];
    $crawlURL = 'http://search.dangdang.com/?key=' . $isbn . '&act=input';
    $fetchData = get_contents_curl($crawlURL, 1);
    preg_match_all('/<li ddt-pit=".*" class="line1" id="p\d+">.*<a.*title="(.*)".*href="(.*)".*>(.*)<\/a>.*<\/li>/isU', $fetchData, $getLink, PREG_SET_ORDER);
    preg_match_all('/<li ddt-pit=".*" class="line1" id="p\d+">.*<p.*class="price".*>(.*)<span.*class="search_now_price".*>(.*)<\/span>.*<span.*class="search_pre_price".*>(.*)<\/span>/isU', $fetchData, $price, PREG_SET_ORDER);
    preg_match_all('/<li ddt-pit=".*" class="line1" id="p\d+">.*<a.*name=\'itemlist-author\'.*>(.*)<\/a>/isU', $fetchData, $author, PREG_SET_ORDER); //作者
    preg_match_all('/<li ddt-pit=".*" class="line1" id="p\d+">.*<a.*name=\'P_cbs\'.*>(.*)<\/a>/isU', $fetchData, $press, PREG_SET_ORDER); //出版社
    preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $getLink[0][3], $imageLink, PREG_SET_ORDER); //图

    $title_fet = $getLink[0][1];
    $pLink = $getLink[0][2];
    $imageSmall = $imageLink[0][2];
    if (!empty($title_fet) && $bookdata) {
        $fetchProData = file_get_contents_curl($pLink, 1);

        $proID = str_replace(".html", "", str_replace("http://product.dangdang.com/", "", $pLink));
        $prJson = 'http://product.dangdang.com/?r=callback%2Fdetail&productId=' . $proID . '&templateType=publish&describeMap=&shopId=0&categoryPath=01.41.41.05.00.00';
        $getJson = file_get_contents($prJson);
        $detailJson = json_decode($getJson, true);

        preg_match_all('/<h2>.*<span.*class="head_title_name" title="(.*)">/isU', $fetchProData, $longtitle_fet, PREG_SET_ORDER);
        preg_match_all('/<div.*id="detail_all".*>.*<li>.*版 次.*：(.*)<\/li>/isU', $fetchProData, $edition_fet, PREG_SET_ORDER);
        preg_match_all('/<div.*id="detail_all".*>.*<li>.*页 数.*：(.*)<\/li>/isU', $fetchProData, $pages_fet, PREG_SET_ORDER);
        preg_match_all('/<div.*id="detail_all".*>.*<li>.*字 数.*：(.*)<\/li>/isU', $fetchProData, $word_fet, PREG_SET_ORDER);
        preg_match_all('/<div.*id="detail_all".*>.*<li>.*印刷时间.*：(.*)<\/li>/isU', $fetchProData, $printime_fet, PREG_SET_ORDER);
        preg_match_all('/<div.*id="detail_all".*>.*<li>.*开 本.*：(.*)<\/li>/isU', $fetchProData, $size_fet, PREG_SET_ORDER);
        preg_match_all('/<div.*id="detail_all".*>.*<li>.*纸 张.*：(.*)<\/li>/isU', $fetchProData, $paper_fet, PREG_SET_ORDER);
        preg_match_all('/id="detail_all".*>.*<li>.*丛书名.*：(.*)<\/li>/isU', $fetchProData, $series_fet, PREG_SET_ORDER);
        preg_match_all('/cp.*?cp.*?cp01.(\d{2}).(\d{2}).(\d{2}).(\d{2}).(\d{2})/isU', $fetchProData, $category_fet, PREG_SET_ORDER);


        //简介
        preg_match_all('/<span id="content-all"><\/span>(.*)<\/div><\/div>(<div id=")?/isU', $detailJson['data']['html'], $content_fet, PREG_SET_ORDER);
        if (!$content_fet) {
            preg_match_all('/content-textarea.*>(.*)textarea/isU', addslashes($myJson['data']['html']), $content_fet, PREG_SET_ORDER);
        }
        //目录
        preg_match_all('/<textarea.*id = "catalog-textarea">(.*)<\/textarea><div class="section_show_more/isU', $detailJson['data']['html'], $book_directory_fet, PREG_SET_ORDER);
        //作者
        preg_match_all('/<span id="authorIntroduction-all"><\/span>(.*)<\/div><\/div>(<div id=")?/isU', $detailJson['data']['html'], $author_intro_fet, PREG_SET_ORDER);

        //试读章节
        preg_match_all('/<textarea.*id = "extract-textarea">(.*)<\/textarea>/isU', $detailJson['data']['html'], $trial_chapter_fet, PREG_SET_ORDER);
        //其他
        preg_match_all('/<span id="abstract-all"><\/span>(.*)<\/div><\/div>(<div id=")?/isU', $detailJson['data']['html'], $other_info_fet, PREG_SET_ORDER);

        $fetdata['author'] = addslashes(strip_tags(html_entity_decode(trim($author[0][1]))));
        $fetdata['title'] = addslashes(strip_tags(trim(html_entity_decode($title_fet))));
        $fetdata['title'] = preg_replace(_math_longtitle, '', $fetdata['title']);
        $fetdata['longtitle'] = addslashes(strip_tags(trim(html_entity_decode($longtitle_fet[0][1]))));
        preg_match_all('/(好评|店铺|礼券)/isU', $fetdata['longtitle'], $ad, PREG_SET_ORDER);
        if (trim($fetdata['longtitle']) || $ad) {
            $fetdata['longtitle'] = $fetdata['title'];
        }
        $fetdata['longtitle'] = preg_replace(_math_longtitle, '', $fetdata['longtitle']);
        $fetdata['price'] = addslashes(strip_tags(preg_replace(_math_price, '', trim($price[0][2]))));
        $fetdata['dimensions'] = addslashes(strip_tags(trim($size_fet[0][1])));
        $fetdata['pubdate'] = addslashes(strip_tags(trim($printime_fet[0][1])));
        $fetdata['publisher'] = addslashes(strip_tags(trim($press[0][1])));
        $fetdata['edition'] = addslashes(strip_tags(trim($edition_fet[0][1])));
        $fetdata['pages'] = addslashes(strip_tags(trim($pages_fet[0][1])));
        $fetdata['word_count'] = addslashes(strip_tags(trim($word_fet[0][1])));
        $fetdata['series'] = addslashes(strip_tags(trim($series_fet[0][1])));

        $fetdata['abstract'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($content_fet[0][1])))));
        $fetdata['directory'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($book_directory_fet[0][1])))));
        $fetdata['author_intro'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($author_intro_fet[0][1])))));
        $fetdata['trial_chapter'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($trial_chapter_fet[0][1])))));
        $fetdata['other_info'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($other_info_fet[0][1])))));
        //分类
        $category1 = intval(addslashes(strip_tags(trim($category_fet[0][1]))));
        $category2 = intval(addslashes(strip_tags(trim($category_fet[0][2]))));
        $category_trans = (string) $category1 . "_" . (string) $category2;
        $fetdata['category_code'] = addslashes(strip_tags(trim($category_trans)));

//var_dump($category_fet,$fetdata);exit;
    }
    if ($picture) {
        $fetdata['imagepath'] = http_download_image($imageSmall, $isbn);
    }
    return $fetdata;
}

//当当wap   http://search.m.dangdang.com/search.php?keyword=
function Crawler_DangdangWap($isbn, $bookdata = true, $picture = true) {
    $fetdata = [];
    $crawlURL = 'http://search.m.dangdang.com/search_ajax.php?cid=0&keyword=' . $isbn . '&have_ad=1&act=get_product_flow_search';
    $fetchData = get_contents_curl($crawlURL); //crawl,file_get_contents_curl,jsonCrawl,curlWithoutICONV
    $fetchData = json_decode($fetchData);
    if ($fetchData->products) {
        $product = $fetchData->products[0];
        if ($bookdata) {
            $detail = json_encode($product, JSON_UNESCAPED_UNICODE);
            preg_match_all('/([\x{4e00}-\x{9fa5}]+系列)/u', $detail, $series_fet, PREG_SET_ORDER);

            $pLink = $product->product_url;
            $pdetail = file_get_contents_curl($pLink);
            // pc  http://category.dangdang.com/cp01.25.11.04.00.00.html
            // wap  http://search.m.dangdang.com/category.php?cid=01.25.11.04.00.00&sid=c83c14d89336c41feca6950c3cb1a474
            preg_match_all('/category\.php\?cid=01.(\d{2}).(\d{2}).(\d{2}).(\d{2}).(\d{2})/isU', $pdetail, $category_fet, PREG_SET_ORDER);

            preg_match_all('/<a dd_name=".*" href="(.*product.m.dangdang.com\/detail.*html.*)?">详情<\/a>/isU', $pdetail, $detailurl_fet, PREG_SET_ORDER);
            $detailtext = file_get_contents_curl($detailurl_fet[0][1]);
            preg_match_all('/【内容简介】(.*)【?/isU', $detailtext, $abstract_fet, PREG_SET_ORDER);
            preg_match_all('/【目录】(.*)【?/isU', $detailtext, $book_directory_fet, PREG_SET_ORDER);
            preg_match_all('/【作者简介】(.*)【?/isU', $detailtext, $author_intro_fet, PREG_SET_ORDER);
            preg_match_all('/【精彩书摘】(.*)【?/isU', $detailtext, $trial_chapter_fet, PREG_SET_ORDER);
            preg_match_all('/【编辑推荐】(.*)【?/isU', $detailtext, $other_info_fet, PREG_SET_ORDER);


            $fetdata['title'] = $product->name;
            $fetdata['price'] = $product->price;
            $fetdata['author'] = $product->authorname;
            $fetdata['publisher'] = $product->publisher;
            $fetdata['pubdate'] = $product->publish_date;
            $fetdata['abstract'] = $product->subname;
            $fetdata['series'] = addslashes(strip_tags(trim($series_fet[0][1])));


            $category1 = intval(addslashes(strip_tags(trim($category_fet[0][1]))));
            $category2 = intval(addslashes(strip_tags(trim($category_fet[0][2]))));
            $category_trans = (string) $category1 . "_" . (string) $category2;
            $fetdata['category_code'] = addslashes(strip_tags(trim($category_trans)));


            $fetdata['abstract'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($abstract_fet[0][1])))));
            $fetdata['directory'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($book_directory_fet[0][1])))));
            $fetdata['author_intro'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($author_intro_fet[0][1])))));
            $fetdata['trial_chapter'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($trial_chapter_fet[0][1])))));
            $fetdata['other_info'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($other_info_fet[0][1])))));
        }
        if ($picture) {
            $imageSmall = $product->image_url;
            $fetdata['imagepath'] = http_download_image($imageSmall, $isbn);
        }
    }
    return $fetdata;
}

//亚马逊 https://www.amazon.cn/gp/search/ref=sr_adv_b/?field-isbn=9787542951113
function Crawler_Amazon($isbn, $bookdata = true, $picture = true) {
    $fetdata = [];
    $crawlURL = 'https://www.amazon.cn/gp/search/ref=sr_adv_b/?field-isbn=' . $isbn;
    preg_match_all('/action="\/(.*)\/validateCaptcha"/i', $fetchProData, $error1, PREG_SET_ORDER);
    $thread = get_contents_curl($crawlURL);

    preg_match_all('/<li id="result_0".*<a class="[^>]*? s-access-detail-page .*".* title="(.*)" .*href="(.*)"><h2 .* s-access-title[^>]*?>.*<\/h2><\/a>/isU', $thread, $getLink, PREG_SET_ORDER);
    preg_match_all('/<span class="[^>]* s-price [^>]*">￥([0-9.]*)<\/span>/i', $thread, $price, PREG_SET_ORDER);

    $title_fet = $getLink[0][1];
    $pLink = urldecode($getLink[0][2]);
    preg_match_all('/dp\/([^\/]*+)/isU', $pLink, $asin_fet, PREG_SET_ORDER);

    $asin = urldecode($asin_fet[0][1]);

    $fetchProData = file_get_contents_curl($pLink); //图书详情页
    preg_match_all('/action="\/(.*)\/validateCaptcha"/i', $fetchProData, $error2, PREG_SET_ORDER);
    if ($error1 || $error2) {
        echo "need validate";
        return "need validate";
    }
    if ($title_fet && empty($error) && $bookdata) {
        preg_match_all('/field-author=.*">(.*)<\/a>/isU', $fetchProData, $author, PREG_SET_ORDER);
        preg_match_all('/<li><b>出版社:<\/b>([^>]*); 第([^>]*)版 \(([^>]*)\)<\/li>/isU', $fetchProData, $press, PREG_SET_ORDER);
        preg_match_all('/<span id="productTitle"[^>]*>(.*)<\/span>/isU', $fetchProData, $longtitle_fet, PREG_SET_ORDER);
        preg_match_all('/<li><b>([^>]*装|[^>]*页[^>]*):<\/b> ([^>]*)页?<\/li>/isU', $fetchProData, $pages_fet, PREG_SET_ORDER);
        preg_match_all('/<li><b>开本:<\/b>([^>]*)<\/li>/isU', $fetchProData, $size_fet, PREG_SET_ORDER);
        preg_match_all('/<li><b>.*字 数:[^<]*<\/b>(.*)<\/li>/isU', $fetchProData, $word_fet, PREG_SET_ORDER); //nodata
        preg_match_all('/<li><b>.*纸 张:[^<]*<\/b>(.*)<\/li>/isU', $fetchProData, $paper_fet, PREG_SET_ORDER);
        preg_match_all('/<li><b>.*丛书名:[^<]*<\/b>(.*)<\/li>/isU', $fetchProData, $series_fet, PREG_SET_ORDER);
        preg_match_all('/bookDescEncodedData = "([%&#a-zA-z0-9]*)"/is', $fetchProData, $content_fet, PREG_SET_ORDER);

        $authorLink = 'https://www.amazon.cn/gp/product-description/ajaxGetProuductDescription.html?ref_=dp_apl_pc_loaddesc&asin=' . $asin . '&deviceType=web';
        $authorData = file_get_contents_curl($authorLink); //作者简介页
        //目录
        preg_match_all('/<div id="s_content_2".*<\/h3>(.*)<\/div>(<div id=")?/isU', $authorData, $book_directory_fet, PREG_SET_ORDER);
        //作者
        preg_match_all('/<div id="s_content_1".*<\/h3>(.*)<\/div>(<div id=")?/isU', $authorData, $author_intro_fet, PREG_SET_ORDER);
        //试读章节
        preg_match_all('/<div id="s_content_3".*<\/h3>(.*)<\/div>(<div id=")?/isU', $authorData, $trial_chapter_fet, PREG_SET_ORDER);
        //其他
        preg_match_all('/<div id="s_content_0".*<\/h3>(.*)<\/div>(<div id=")?/isU', $authorData, $other_info_fet, PREG_SET_ORDER);

        $fetdata['author'] = addslashes(strip_tags(html_entity_decode(trim($author[0][1]))));
        $fetdata['title'] = addslashes(strip_tags(trim(html_entity_decode($title_fet))));
        $fetdata['longtitle'] = addslashes(strip_tags(trim(html_entity_decode($longtitle_fet[0][1]))));
        $fetdata['longtitle'] = $fetdata['longtitle'] ? $fetdata['longtitle'] : $fetdata['title'];
        $fetdata['price'] = addslashes(strip_tags(preg_replace(_math_price, '', trim($price[0][1]))));
        $fetdata['dimensions'] = addslashes(strip_tags(trim($size_fet[0][1])));
        $fetdata['pubdate'] = addslashes(strip_tags(trim($press[0][3])));
        $fetdata['publisher'] = addslashes(strip_tags(trim($press[0][1])));
        $fetdata['edition'] = addslashes(strip_tags(trim($press[0][2])));
        $fetdata['pages'] = addslashes(strip_tags(trim($pages_fet[0][2])));
        $fetdata['word_count'] = addslashes(strip_tags(trim($word_fet[0][1])));
        $fetdata['series'] = addslashes(strip_tags(trim($series_fet[0][1])));
        $fetdata['abstract'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($content_fet[0][1])))));


        $fetdata['directory'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($book_directory_fet[0][1])))));
        $fetdata['author_intro'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($author_intro_fet[0][1])))));
        $fetdata['trial_chapter'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($trial_chapter_fet[0][1])))));
        $fetdata['other_info'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($other_info_fet[0][1])))));
    }
    if ($picture) {
        preg_match_all('/<img[^>]*src=(\'|\")([^"\']*)\\1[^>]*id="[^>]*imgBlkFront"[^>]*>/i', $fetchProData, $imageLink, PREG_SET_ORDER);
        $imageSmall = addslashes(strip_tags(trim($imageLink[0][2])));
        $fetdata['imagepath'] = http_download_image($imageSmall, $isbn);
    }
    return $fetdata;
}

function http_download_image($url, $isbn, $save = true) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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
        $tmp = 'tmp.' . $info['extension'];
        $fps = @fopen(_SAVEPATH . $tmp, "w");
        @fwrite($fps, $return_content);
        if (!comparePicture(_SAVEPATH . $tmp)) {
            $filename = trim($isbn) . '.' . $info['extension'];
            if ($save) {
                $fps = @fopen(_SAVEPATH . "s/" . $filename, "a");
                $fpb = @fopen(_SAVEPATH . "b/" . $filename, "a");
                @fwrite($fps, $return_content);
                @fwrite($fpb, $return_content);
            }
        }
    }
    return $filename;
}

function get_contents_curl($url, $utf = 0, $refer = '') {
    $headers[] = UserAgent(); //<-- this is user agent
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

function comparePicture($pic) {
    $nolist = explode(';', FIND_PIC);
    foreach ($nolist as $key => $val) {
        $size1 = filesize($pic);
        $size2 = filesize($val);
        if ($size1 == $size2) {
            $fp1 = fopen($pic, 'rb');
            $image1 = bin2hex(fread($fp1, $size1));

            $fp2 = fopen($val, 'rb');
            $image2 = bin2hex(fread($fp2, $size2));

            if ($image1 == $image2) {
                return true;
            }
        }
    }

    return false;
}

//淘书网 http://www.taoshu.com/AdvanceSearch.aspx?isbn=9787541211218
function Crawler_Taoshu($isbn, $bookdata = true, $picture = true) {
    $fetdata = [];
    $crawlURL = 'http://www.taoshu.com/AdvanceSearch.aspx?isbn=' . $isbn;
    preg_match_all('/action="\/(.*)\/validateCaptcha"/i', $fetchProData, $error1, PREG_SET_ORDER);
    $thread = file_get_contents_curl($crawlURL);

    preg_match_all('/<a id="lbooks_bookname_[\w]+" href="([^"]+)">([^<]+)<\/a>/isU', $thread, $getLink, PREG_SET_ORDER);
    preg_match_all('/<div class="authors">.*target="_blank">(.*)<\/a>[^<]*[^<]\/\s<a href=.*target="_blank">(.*)<\/a>\s*[^<]\/\s*([a-zA-Z0-9.-]+)<\/div>/isU', $thread, $authors, PREG_SET_ORDER);
    preg_match_all('/<div\s*class="price"><span class="new-price">([0-9.]+)<\/span><\/div>/i', $thread, $price, PREG_SET_ORDER);

    $title_fet = $getLink[0][2];
    $pLink = urldecode($getLink[0][1]);

    $fetchProData = file_get_contents_curl("http://www.taoshu.com" . $pLink); //图书详情页

    if ($title_fet && empty($error) && $bookdata) {
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
        //preg_match_all('/<div id="content_text".*>(.*)<\/div>/isU', $fetchProData, $trial_chapter_fet, PREG_SET_ORDER );
        //preg_match_all('/<div id="content_text".*>(.*)<\/div>/isU', $fetchProData, $other_info_fet, PREG_SET_ORDER );

        $fetdata['author'] = addslashes(strip_tags(html_entity_decode(trim($author_fet))));
        $fetdata['title'] = addslashes(strip_tags(trim(html_entity_decode($title_fet))));
        $fetdata['longtitle'] = addslashes(strip_tags(trim(html_entity_decode($longtitle_fet[0][1]))));
        $fetdata['longtitle'] = $fetdata['longtitle'] ? $fetdata['longtitle'] : $fetdata['title'];
        $fetdata['price'] = addslashes(strip_tags(preg_replace(_math_price, '', trim($price[0][1]))));
        $fetdata['dimensions'] = addslashes(strip_tags(trim($size_fet[0][1])));
        $fetdata['pubdate'] = addslashes(strip_tags(trim($pubdate_fet)));
        $fetdata['publisher'] = addslashes(strip_tags(trim($publisher_fet)));
        $fetdata['pages'] = addslashes(strip_tags(trim($pages_fet[0][1])));
        $fetdata['word_count'] = addslashes(strip_tags(trim($word_fet[0][1])));
        $fetdata['series'] = addslashes(strip_tags(trim($series_fet[0][1])));

        $fetdata['abstract'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($abstract_fet[0][1])))));
        $fetdata['directory'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($book_directory_fet[0][1])))));
        $fetdata['author_intro'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($author_intro_fet[0][1])))));
        //$fetdata['trial_chapter'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($trial_chapter_fet[0][1])))));
        //$fetdata['other_info'] = addslashes(strip_tags(html_entity_decode(trim(urldecode($other_info_fet[0][1]))))); 
    }
    if ($picture) {
        preg_match_all('/<div class="pic".*<img src="(.*)"[^>]*><\/a><\/div>/isU', $fetchProData, $imageLink, PREG_SET_ORDER);
        $imageLink = addslashes(strip_tags(trim($imageLink[0][1])));
        $fetdata['imagepath'] = http_download_image($imageSmall, $isbn);
    }
    return $fetdata;
}

function getISBN_byImage($cid, $from, $to) {
    ///ajaxload/companybooks?seller_cid=FZCBS&key_words=&showtype=image&act=details&page=10
    $url = "ajaxload/companybooks?seller_cid={$cid}&key_words=&showtype=image";
    $fetchProData = file_get_contents_curl("https://books.test.com/" . $url); //图书详情页

    $isbnlist = [];
    if ($fetchProData) {
        for ($i = $from; $i <= $to; $i++) {
            $url = "ajaxload/companybooks?seller_cid={$cid}&key_words=&showtype=image&page=" . intval($i);
            $fetchProData = file_get_contents_curl("https://books.maishumaishu.com/" . $url); //图书详情页
            $preg = "/<script(.*)?\/script>/isU";
            $fetchProData = preg_replace($preg, "", $fetchProData);
            $preg = "/<style(.*)?\/style>/isU";
            $fetchProData = preg_replace($preg, "", $fetchProData);
            preg_match_all('/<img src="(?<image>https:\/\/booksbackend.maishumaishu.com\/upload\/products\/b\/(?<isbn>\d+).jpg)"/isU', $fetchProData, $matches, PREG_SET_ORDER);

            if (!empty($matches)) {
                foreach ($matches as $key => $link) {
                    $imageLink = $link['image'];
                    $isbn = $link['isbn'];
                    var_dump($isbn);
                    $path = http_download_image($imageLink, $isbn, false);
                    if (empty($path)) {
                        $isbnlist[] = $isbn;
                    }
                }
            }
        }
    }
    return $isbnlist;
}

function file_get_contents_curl($url, $utf = 0) {
    $headers[] = UserAgent(); //<-- this is user agent
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
    usleep(500000);
    if ($utf) {
        $data = iconv("GBK", "UTF-8", $data);
    }
    return $data;
}

function parse_date($date, $outformate = '') {
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
                    $timestr.='-' . $month;
                    if (!empty($pregs['day'])) {
                        $day = $pregs['day'];
                        $timestr.='-' . $day;
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
    var_dump($date . ' => ' . $data);
    return $data;
}

function getLangCode($country) {
    if (strpos($country, '英文') !== false) {
        return 'en';
    } else if (strpos($country, '中文') !== false) {
        return 'zh';
    } else {
        return '';
    }
}

function getCountryCode($country) {
    if (strpos($country, '中国') !== false) {
        return 'CN';
    } else {
        return '';
    }
}

function getQuantityUnitCode($unit, $default = "blet1") {
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

function parseDiscount($discount) {
    preg_match("/[0][.](\d\d?)/", trim($discount), $math1);
    preg_match("/(\d\d?)\s*折/", trim($discount), $math2);
    preg_match("/(\d\d?)$/", trim($discount), $math3);
    $num = $discount * 100;
    if (!empty($math1[1])) {
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

function UserAgent() {
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
