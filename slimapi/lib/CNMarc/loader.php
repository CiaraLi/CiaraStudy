<?php
// readdir_phpfile(BASEPATH.'lib/CNMarc',true);
 
require_once __DIR__.'/Structure/CNMarcFieldsInterface.php';
require_once __DIR__.'/Structure/CNMarcHeaderInterface.php';
require_once __DIR__.'/Structure/CNMarcTableInterface.php';
require_once __DIR__.'/CNMarcInterface.php';
require_once __DIR__.'/CNMarcWriterInterface.php';
require_once __DIR__.'/Structure/CNMarcHeader.php';
require_once __DIR__.'/Structure/CNMarcTable.php';
require_once __DIR__.'/Structure/CNMarcFields.php';
require_once __DIR__.'/CNMarc.php';
require_once __DIR__.'/CNMarcWriter.php';

use \CNMarc\CNMarc;
use CNMarc\Structure\CNMarcHeader;
use CNMarc\Structure\CNMarcTable;
use CNMarc\Structure\CNMarcFields;

$header=new CNMarcHeader();
$table=new CNMarcTable();
$field=new CNMarcFields();
$marc=new CNMarc($header,$table,$field);