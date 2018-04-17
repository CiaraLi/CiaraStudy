<?php

if (!function_exists('dd')) {

    function dd($param = '', $exit = true) {
        echo '<pre>';
        var_dump($param);
        $exit ? exit() : null;
    }

}
error_reporting(E_ERROR);

require_once './Crawfunc.php';
require_once './crawbooks.php';
require_once './ImageHash.php';
require_once './FileReader.php';


$map = [
    'isbn' => 6, /* isbn */
    'title' => 1, /* title */
    'author' => 11, /* author */
    'price' => 5, /* Price */
    'dimensions' => 4, /* book Size 16K */
    'pubdate' => 8, /* pubdate 2017-09-09 */
    'publisher' => 2, /* publisher */
    'cip' => 7, 'package_num' => 9
];
$bookpath = "./book.csv";
$savedir = "./result";
$cid = "";
$start = "1";
$end = "10";

$reader = new \FileReader($bookpath, $savedir, $cid, $start, $end, $map);
$reader->readfiledata();
for ($index = $reader->headrow; $index < count($reader->filedata) && $index <= $reader->endrow; $index++) {

    $data = $reader->fetchfiledata($index);
    if (!empty($data['isbn'])) {
        $isbn = $data['isbn'];
        $test = new \Test($isbn, $data, $savedir);
        $bookdata = $test->run();
        $sql = $test->getSql($isbn, $cid, false);
        $reader->pushSQL($data['isbn'], $index, $sql);
    }
}

class Test {

    public $bookdata;
    public $isbn;
    public $cid;
    public $crawdata;
    public $resultdir;

    function __construct($isbn = '', $bookdata = [], $resultdir = '') {
        empty($isbn) ? null : $this->isbn = $isbn;
        empty($bookdata) ? null : $this->bookdata = $bookdata;
        empty($resultdir) ? null : $this->resultdir = $resultdir;
    }

    function run() {

        $craw = new \crawbooks();
        Crawfunc::$resultDIR = $this->resultdir;
        $crawdata = $craw->craw($this->isbn, $this->bookdata, true, true, true, true, true);
        return $this->crawdata = $this->_parseBookdata($crawdata);
    }

    function getSql($isbn, $cidcode, $isupdate = false) {
        $fetdata = $this->crawdata;
        if ($isupdate != true) {
            $setdata = ["isbn='{$fetdata['isbn']}'", "title='{$fetdata['title']}'", "price='{$fetdata['price']}'"];

            $setdata[] = "discount='" . $fetdata['discount'] . "'";
            $setdata[] = "status_related_price='{$fetdata['sell_price']}'";
            empty($cidcode) ? null : $setdata[] = "cid_code='$cidcode'";
            $setdata[] = "longtitle='{$fetdata['longtitle']}'";
            $setdata[] = "imagepath='{$fetdata['imagepath']}'";
            $setdata[] = "publisher_code='{$fetdata['publisher']}'";
            $setdata[] = "edition='{$fetdata['edition']}'";
            $setdata[] = "pubdate='{$fetdata['pubdate']}'";
            
            $setdata[] = "pages='{$fetdata['pages']}'";
            $setdata[] = "word_count='{$fetdata['word_count']}'";
            $setdata[] = "dimensions='{$fetdata['dimensions']}'";
            $setdata[] = "author='{$fetdata['author']}'";
            $setdata[] = "series='{$fetdata['series']}'";
            /* 简介 */
            $setdata[] = "abstract='{$fetdata['abstract']}'";
            $setdata[] = "directory='{$fetdata['directory']}'";
            $setdata[] = "author_intro='{$fetdata['author_intro']}'";
            $setdata[] = "trial_chapter='{$fetdata['trial_chapter']}'";
            $setdata[] = "other_info='{$fetdata['other_info']}'";

            return $sql_text = "INSERT INTO books set  " . implode(',', $setdata) . " ,created_at=now() , updated_at=now()  ; \n";
        } else {
            $update = [];
            $where = ["isbn='$isbn'", "cid_code='$cidcode'"];
            empty($fetdata['author']) ? null : $update[] = "author='{$fetdata['author']}'";
            empty($fetdata['abstract']) ? null : $update[] = "abstract='{$fetdata['abstract']}'";
            empty($fetdata['directory']) ? null : $update[] = "directory='{$fetdata['directory']}'";
            empty($fetdata['author_intro']) ? null : $update[] = "author_intro='{$fetdata['author_intro']}'";
            empty($fetdata['trial_chapter']) ? null : $update[] = "trial_chapter='{$fetdata['trial_chapter']}'";
            empty($fetdata['other_info']) ? null : $update[] = "other_info='{$fetdata['other_info']}'";
            if (!empty($update)) {
                return $sql_text = "update books set " . implode(',', $update) . " where " . implode(' and ', $where) . "; \n";
            }
        }
    }

    private function _parseBookdata($bookdata) {
        if (!empty($bookdata['image_url']) && !empty($bookdata['imagepath'])) {
            
        }
        if (empty($bookdata['category_code']) || $bookdata['category_code'] = '0_0') {
            $bookdata['category_code'] = '74';
        }
        return $bookdata;
    }

}
