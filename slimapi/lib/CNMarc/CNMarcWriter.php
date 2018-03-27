<?php
namespace CNMarc;  

use\CNMarc\CNMarc as CNMarc;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MarcCreator
 *
 * @author ciara
 */
class CNMarcWriter extends CNMarcWriterInterface{

    public $MarcUnit;

    function __construct(CNMarc $marc) {
        $this->MarcUnit = $marc;
    }

    function __destruct() {
        $this->MarcUnit = null;
    }

}
