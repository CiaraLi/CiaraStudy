<?php
namespace CNMarc;  

use \CNMarc\Structure\CNMarcTable as CNMarcTable;
use \CNMarc\Structure\CNMarcHeader as CNMarcHeader;
use \CNMarc\Structure\CNMarcFields as CNMarcFields;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CNMarc
 *
 * @author ciara
 */
class CNMarc extends CNMarcInterface{

    protected $Header;
    protected $Table;
    protected $Fields;
    protected $HashTable;

    function __construct(CNMarcHeader $header, CNMarcTable $table, CNMarcFields $fields) {
        $this->Header = $header;
        $this->Table = $table;
        $this->Fields = $fields;
    }

}
