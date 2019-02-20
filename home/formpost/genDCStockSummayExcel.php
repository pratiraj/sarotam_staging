<?php
ini_set('max_execution_time', 300);
ini_set('memory_limit', '1024M');
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once 'lib/db/DBLogic.php';
require_once "lib/core/strutil.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once  'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

extract($_GET);
$curr = getCurrStore();
$currusertype = $curr->usertype;
$dcid = isset($_GET['dcid'])?$_GET['dcid']:false;
$errors = array();
try{
    
$db = new DBConn();
$dbl = new DBLogic();

$sheetIndex=0;
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Create a first sheet, representing points details data
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('Stock Details');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr. no');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Category');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Product');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Desc1');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Desc2');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Thickness');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'HSN Code');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Batch Code');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Qty (Kg.)');
$objPHPExcel->getActiveSheet()->setCellValue('J1', 'No Of Pieces(For reference Only)');
$objPHPExcel->getActiveSheet()->setCellValue('K1', 'Rate (Rs./Kg)');
$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Value (Rs.)');
$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Createtime');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);   
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);




$styleArray = array(
    'font'  => array(
        'bold'  => false,
//        'color' => array('rgb' => 'FF0000'),
        'size'  => 10,
    ));
$objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);  
$objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('K')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('L')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('M')->applyFromArray($styleArray);


$rowCount=2;
//c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,s.batchcode,s.qty,s.createtime
$result = $dbl->getDCStockSummery($dcid);
    $sr_no = 1;
    foreach ($result as $obj) {
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $sr_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->ctg);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->desc1);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->desc2);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->thickness);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->hsncode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $obj->batchcode);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $obj->qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $obj->noofpcs);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $obj->rate);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, $obj->value);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $obj->createtime);
          

        $createtime = "";
        if(isset($obj->createtime) && $obj->createtime != NULL){
            $createtime = $obj->createtime;
        }
        //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(33, $rowCount, $createtime);


        $created_by = "";
        if(isset($obj->created_by) && $obj->created_by != NULL){
            $obj_user = $dbl->getUserInfoById($obj->created_by);
            if(isset($obj_user) && $obj_user != NULL){
                $created_by = $obj_user->name;
            }
        }
        //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(34, $rowCount, $created_by);
        
        $updated_by = "";
        if(isset($obj->updated_by) && $obj->updated_by != NULL){
            $obj_user = $dbl->getUserInfoById($obj->updated_by);
            if(isset($obj_user) && $obj_user != NULL){
                $updated_by = $obj_user->name;
            }
        }
        
        if($updated_by != ""){
            //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(35, $rowCount, $updated_by);
            $updatetime = "";
            if(isset($obj->updatetime) && $obj->updatetime != NULL){
                $updatetime = $obj->updatetime;
            }
            //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(36, $rowCount, $updatetime);
        }
        
        $sr_no++;    
        $rowCount++;
    }
 
 // Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="DC Stock Report.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}catch(Exception $xcp){
    print $xcp->getMessage();
}
?>