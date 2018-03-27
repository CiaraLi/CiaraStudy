<?php
if (empty($_REQUEST['hid'])) {
    ?>
    <html>
        <header>
            <meta http-equiv="content-type" content="text/html;charset=utf-8">
        </header>
        <body> 
            <form action="" method="post" enctype="multipart/form-data">
                <div>
                    开始行<input type="text" value="0" name="startrow"> 
                    结束行<input type="text" value="10" name="endrow"> 
                    开始列<input type="text" value="0" name="startcol"> 
                    结束列<input type="text" value="10" name="endcol"> 
                </div>
                <div>
                    <input type="file" name="fileToUpload"> 
                    <input type="checkbox" name="print" value='1' checked="checked">页面输出 
                    <select name="hid">
                        <option value="1">测试上传</option>
                        <option value="2">下载excel</option>
                        <option value="3">查看单元格格式</option>
                        <option value="4">下载远程excel</option>
                    </select> 
                    <input type="submit" value="submit" name="submit"> 
                </div>
            </form>  
        </body>
    </html>

    <?php
} else {
    
}
?>
<?php
include('./PHPExcel.php');

define('startRow', empty($_REQUEST['startrow']) ? 0 : intval($_REQUEST['startrow']));
define('endRow', empty($_REQUEST['endrow']) ? 10 : intval($_REQUEST['endrow']));
define('startCol', empty($_REQUEST['startcol']) ? 0 : intval($_REQUEST['startcol']));
define('endCol', empty($_REQUEST['endcol']) ? 10 : intval($_REQUEST['endcol']));

if (!empty($_FILES['fileToUpload'])) {
    $file = pathinfo($_FILES['fileToUpload']['name']);
    mess($_FILES);
    $newfilename = 'file/upload.' . (empty($file['extension']) ? "tmp" : $file['extension']);
    mess("<br><br><u><H2>Upload File</H2></u><br><br>");
    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $newfilename)) {
        mess("Uploaded");
    } else {
        mess("File was not uploaded");
    }
} else {
    $newfilename = "";
}
if (@$_REQUEST['hid'] == '1' && is_file($newfilename)) {
    //print data
    $res = readXLXSarray($newfilename);
    var_dump($res);
} elseif (@$_REQUEST['hid'] == '2') {
    //create new
    $excel = new PHPExcel();
    $excel->getProperties()->setTitle("1")->setDescription("none");
    $sheet = $excel->setActiveSheetIndex(0);
    for ($row = 1; $row < 3; $row++) {
        $sheet->setCellValueByColumnAndRow(0, $row, 'row:' . $row . ' col:1');
        $sheet->setCellValueByColumnAndRow(1, $row, 'row:' . $row . ' col:2');
        $sheet->setCellValueByColumnAndRow(2, $row, 'row:' . $row . ' col:3');
        $sheet->getCellByColumnAndRow(0, $row)->getStyle()->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
        $sheet->getCellByColumnAndRow(1, $row)->getStyle()->getFont()->setBold('40');
        $sheet->getCellByColumnAndRow(2, $row)->getStyle()->getFont()->setBold('40')->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
    }
    exportXLXSarray($excel, 'output', 'xls');
} elseif (@$_REQUEST['hid'] == '3') {
    $newfilename = is_file($newfilename) ? './read.xls' : $newfilename;
    //read formate
    $res = readXLXSformate($newfilename);
} elseif (@$_REQUEST['hid'] == '4') {
    //read and write
    $newfilename = empty($newfilename) ? './read.xls' : $newfilename;
    $excel = readFileList($newfilename);
    exportXLXSarray($excel, 'output', 'xls');
}

function readFileList($newfilename) {
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
//    $index = $objPHPExcel->getActiveSheetIndex();
//    $sheet = $objPHPExcel->getSheet($index);
//    $curcell = $objPHPExcel->getActiveSheet()->getCell('A2');
//    $curcell->get();
//    $styles = $curcell->getStyle();
//    $styles->setFont($styles->getFont());
//    $styles->setConditionalStyles($styles->getConditionalStyles()); 
//    $sheet->setCellValueByColumnAndRow(1, 1, 'test data');
//     $objPHPExcel->getActiveSheet()->getProtection()->setSheet(false); 
//    setList($objPHPExcel,'I2','二级分类!$B$3:$B$66');
//    setList($objPHPExcel,'B5','"是,否"'); 
    return $objPHPExcel;
}

function setList($objPHPExcel, $cell, $values) {
    $objValidation = $objPHPExcel->getActiveSheet()->getCell($cell)->getDataValidation();
    $curcell = $objPHPExcel->getActiveSheet()->getCell($cell);
    $type = $curcell->getStyle()->getNumberFormat()->getFormatCode();
    $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
    $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
    $objValidation->setAllowBlank(false);
    $objValidation->setShowInputMessage(true);
    $objValidation->setShowDropDown(true);
    $objValidation->setFormula1($values);
    $objPHPExcel->getActiveSheet()->getCell($cell)->setDataValidation($objValidation);
    return $objPHPExcel;
}

function readXLXSformate($newfilename) {
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
    $index = $objPHPExcel->getActiveSheetIndex();
    $sheet = $objPHPExcel->getSheet($index);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    echo ('  rowNum :' . $highestRow . ' colNum:' . $highestColumn . '  <br/>');
    echo '<table >';
    echo '<tr>';
    echo '<td>*</td>';
    for ($col = startCol; $col < endCol; $col++) {
        echo '<td>' . $col . '</td>';
    }
    echo'</tr>';
    for ($row = startRow; $row < endRow; $row++) {
        echo '<tr>';
        echo '<td>' . $row . '</td>';
        for ($col = startCol; $col < endCol; $col++) {
            echo '<td>';
            $cell = $sheet->getCellByColumnAndRow($col, $row);
            $val = $cell->getValue();
            var_dump($val);
            $type = $cell->getStyle()->getNumberFormat()->getFormatCode();
//            echo ' (<em style="color:red">' . $type . '</em>)' . mb_substr($val, 0, 20);
            $styles = $cell->getStyle();
            echo '</td>';
        }
        echo '</tr>';
    }
    echo '<table>';
}

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
    $m = $sheet->getMergeCells();
    dd($m);
    for ($row = startRow; $row <= endRow; $row++) {
        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
        $need[] = $rowData[0];
    }
    return $need;
}

function exportXLXSarray($objPHPExcel, $name = 'export', $type = 'xlsx', $oldfile = "") {
    $userBrowser = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/MSIE/i', $userBrowser) || preg_match('/Trident/i', $userBrowser)) {
        $name = urlencode($name);
        $name = iconv('UTF-8', 'GBK//IGNORE', $name);
    }
    header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    header("Content-Transfer-Encoding: binary");
    header("Pragma: no-cache");

    switch (strtolower($type)) {
        case "xls":
            $filename = $name . "." . $type;
            $writerType = !empty($oldfile) ? PHPExcel_IOFactory::autoReadType($oldfile) : 'Excel5';
            header('Content-Type: application/vnd.ms-excel.sheet.macroEnabled.12;charset=gb231');
            header('Content-Disposition: attachment;filename="' . $filename);
            break;
        default:
            $filename = $name . ".xlsx";
            $writerType = !empty($oldfile) ? PHPExcel_IOFactory::autoReadType($oldfile) : 'Excel2007';
            header('Content-Type: application/vnd.ms-excel.sheet.macroEnabled.12;charset=gb231');
            header('Content-Disposition: attachment;filename="' . $filename);
            break;
    }
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $writerType);
    $objWriter->save('php://output');
}

function dd() {
    echo '<pre>';
    $numargs = func_get_args();
    foreach ($numargs as $key => $value) {
        var_dump($value);
        echo "<br/>";
    }
    echo '</pre>';
    exit;
}

function mess($value) {
    isset($_REQUEST['print']) && $_REQUEST['print'] == 1 ? print_r($value) : null;
}
?>