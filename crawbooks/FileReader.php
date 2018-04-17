<?php

header('Content-Type: text/html; charset=utf-8');
ini_set('mbstring.substitute_character', "none");
ini_set('max_execution_time', -1);
ini_set('memory_limit', '-1');  
class FileReader {

    public $filedata;
    public $isbn_pre = '';
    public $openfile;
    public $savepath;
    public $cid;
    public $headrow;
    public $endrow;
    public $default = [
        'package_num' => 1, /* per number 每包册数  default 1 */
        'package_type' => '平装', /* binding 装帧 default 1 */
        'set_type' => 1, /* is suit 是否套装  default 1 */
        'weight_unit' => 'kg', /* weight_unit default kg */
        'language_code' => '', /* language_code default zh */
        'word_unit' => 'k', /* word unit default k */
        'paper' => 'p', /* paper type default p */
        'currency_code' => '', /* paper type default CNY */
    ];
    public $map;

    function __construct($filepath, $savepath, $cid, $start = '0', $end = '5000', $map = []) {
        $this->openfile = $filepath;
        $this->savepath = $savepath;
        $this->headrow = $start;
        $this->endrow = $end;
        $this->cid = $cid;
        $this->map = [
            'cid' => 99, /* cid */
            'isbn' => 99, /* isbn */
            'title' => 99, /* title */
            'author' => 99, /* author */
            'price' => 99, /* Price */
            'discount' => 99, /* discount */
            'dimensions' => 99, /* book Size 16K */
            'pubdate' => 99, /* pubdate 2017-09-09 */
            'publisher' => 99, /* publisher */
            'series' => 99, /* series */
            'edition' => 99, /* edition 1,2,3 */
            'pages' => 99, /* pages */
            'word_count' => 99, /* word_count */
            'quantity' => 99, /* quantity */
            'weight' => 99, /* weight */
            'sku' => 99, /* sku */
            'abstract' => 99, /* abstract */
            'directory' => 99, /* directory */
            'author_intro' => 99, /* author_intro */
            'trial_chapter' => 99, /* trial_chapter */
            'other_info' => 99, /* other_info */
            'category_code' => 99, /*  */
            'cip_code' => 99, /* cip code  */
            'target' => 99, /* target */
            'package_num' => 99, /* per number 每包册数  default 1 */
            'package_type' => 99, /* binding 装帧 default 1 */
            'set_type' => 99, /* is suit 是否套装  default 1 */
            'weight_unit' => 99, /* weight_unit default kg */
            'language_code' => 99, /* language_code default zh */
            'word_unit' => 99, /* word unit default k */
            'paper' => 99, /* paper type default p */
            'currency_code' => 99, /* paper type default CNY */
        ];
        if (!empty($map)) {
            $this->map = array_merge($this->map, (array) $map);
        }
    }

    function readfiledata() {
        if (strpos(strrev($this->openfile), 'vsc.') === 0) {
            $data = $this->_redCsvdata();
        } else {
            $data = $this->_readExcelData();
        }
    }

    function fetchfiledata($i) {
        $parsedata = [];
        if (!empty($this->filedata) && $i < count($this->filedata) && $i <= $this->endrow) {
            $row = $this->filedata[$i];
            $isbnstr = preg_replace('/[^0-9]/', '', explode('/', $row[$this->map['isbn']])[0]);
            $isbn = trim($this->isbn_pre . $isbnstr);

            if (strlen($isbn) != 10 && strlen($isbn) != 13) {
                return [];
            }
            foreach ($this->map as $field => $pos) {
                $cell = $row[$pos];
                switch ($field) {
                    case 'isbn':
                        $cell = preg_replace('/[^0-9]/', '', $cell);
                        break;
                    case 'package_num':
                    case 'word_count':
                    case 'page':
                    case 'quantity':
                    case 'edition':
                        $cell = preg_replace('/[^0-9]+/', '', $cell);
                        break;
                    case 'dimensions':
                        $match = [];
                        preg_match('/(\w+)开/',  trim($cell), $match);
                        $cell = empty($match[1]) ? '' : $match[1];
                        break;
                    case 'cid':
                        $cell = empty($this->cid) ? $cell : $this->cid;
                        break;
                    case 'title':
                        $cell = preg_replace(['/《/', '/》/'], '', trim(iconv("GBK", "UTF-8", $cell)));
                        break;
                    case 'cip':
                        $match = [];
                        preg_match('/[A-Z]{1,2}\d{0,4}([\.+=\-\/]\d{1,4})*/', strtoupper(trim($cell)), $match);
                        $cell = empty($match[1]) ? '' : $match[1];
                        break;
                    case 'pubdate':
                        $cell = preg_replace('/[^A-Za-z0-9.:\/-]+/', '', $cell);
                        break;
                    case 'price':
                    case 'weight':
                    case 'discount':
                        $cell = preg_replace('/[^0-9.]+/', '', $cell);
                        break;
                    default:
                        $cell = trim(iconv("GBK", "UTF-8", $cell));
                        break;
                }
                empty($cell) && !empty($this->default[$field]) ? $cell = $this->default[$field] : null;
                empty($cell) ? null : $parsedata[$field] = trim($cell);
            }
        }
        return $parsedata;
    }

    function _redCSVdata() {
        $csv = array();
        $file = fopen($this->openfile, 'r');
        while (($result = fgetcsv($file)) !== false) {
            $csv[] = $result;
        }
        fclose($file);
        $this->filedata = $csv;
    }

    function _readExcelData() {
        return [];
    }

    function pushSQL($isbn, $i, $sql_text) {
        echo $this->savepath . " No:" . $i . "  " . $isbn . "\n";
        file_put_contents($this->savepath . '/bookdetails_' . $this->headrow . '-' . $this->endrow . '.sql', $sql_text, FILE_APPEND);
    }

}
