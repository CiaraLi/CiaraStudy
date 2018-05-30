<?php

//require_once './vendor/autoload.php';
require_once './compare.php';

class compareTest extends PHPUnit_Framework_TestCase {

    public function testCompare() {
        $unit = new compare();
        $this->assertEquals(true, $unit->checkSub('ABEDACDSEVB', 'BAaD'));
//        $this->assertEquals(false, $unit->checkSub('ABEDACDSEVB', 'AAaD'));
//        $this->assertEquals(true, $unit->checkSub('ABEDACDSEVB', 'ABaD'));
//        $this->assertEquals(false, $unit->check_sub('ABEDACDSEVB', 'BAaD'));
//        $this->assertEquals(true, $unit->check_sub('ABEDACDSEVB', 'AAAD'));
//        $this->assertEquals(true, $unit->check_sub('ABEDACDSEVB', 'ABAD'));
    }

}
