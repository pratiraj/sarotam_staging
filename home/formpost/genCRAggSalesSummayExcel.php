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
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

extract($_GET);
$curr = getCurrStore();
$currusertype = $curr->usertype;
$crid = isset($_GET['crid']) ? $_GET['crid'] : false;
$errors = array();
try {

    $db = new DBConn();
    $dbl = new DBLogic();

    $sheetIndex = 0;
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet, representing points details data
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Stock Details');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr. no');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Reference NO');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Invoice No');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Invoice Date');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Customer');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Cust Mo NO');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Total Qty');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'GST %');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Total of Taxable Value');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Total Of CGST');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Total Of SGST');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Net Value');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', 'Round off');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', 'Invoice Value');


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
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);



    $styleArray = array(
        'font' => array(
            'bold' => false,
//        'color' => array('rgb' => 'FF0000'),
            'size' => 10,
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
    $objPHPExcel->getActiveSheet()->getStyle('N')->applyFromArray($styleArray);


    $rowCount = 2;
    $result = $dbl->getAggCRSalesSummery($crid);
//print_r($result);
//return;
    $sr_no = 1;
    $total_total_qty = 0;
    $total_total_taxable_amt = 0;
    $total_total_cgst_val = 0;
    $total_total_sgst_val = 0;
    $total_total_invoice_val = 0;
    
    
    foreach ($result as $obj) {
        
        $invid = $obj->id;
        $userid = getCurrStoreId();
        $invnumarr = explode("-", $obj->invoice_no);
        $custInfo = $dbl->getCustomerById($obj->customer_id);
        $custName = "";
        $custPhone = "";
        if(isset($custInfo) && !empty($custInfo) && $custInfo != null){
            $custName = $custInfo->name;
            $custPhone = $custInfo->phone;
        }
            $refNum = $invnumarr[0];
            $invoicenum = $invnumarr[1];

        $saleDate = $obj->saledate;
        $compareDate = "2019-01-10";
        
        
        $total_qty = 0;
        $total_taxable_amt =0;
        $total_sgst_amt = 0;
        $total_cgst_amt = 0;
        $total_net_value = 0;
        $round_off_val = 0;
        $invoice_val = 0;
        
        $total_tot = 0;
        $tot_cgst_val = 0;
        $tot_igst_val = 0;
        $all_total_val = 0;
        $total_gst_rate = 0;
        
        if ($saleDate > $compareDate) {
            
            $itemObjs = $dbl->getInvoiceItems($invid, $userid);
            foreach ($itemObjs as $invitemobj) {
                
                
                if (isset($invitemobj) && !empty($invitemobj) && $invitemobj != null) {
                    
                    
                    $total = $invitemobj->qty * $invitemobj->rate;
                    $total_qty = $total_qty + $invitemobj->qty;
                    $total_tot = $total_tot + $total;
                    $total_disc = 0;
                    $total_gst_rate = $total_gst_rate + $invitemobj->sgst_percent;
                    $tot_cgst_val = $tot_cgst_val + $invitemobj->cgst_amt;
                    $tot_igst_val = $tot_igst_val + $invitemobj->igst_amt;
                    $lineTotal = $invitemobj->cgst_amt + $invitemobj->cgst_amt + $invitemobj->rate;
                    $roundTaxableAmt = round($invitemobj->taxable, 2);
                    $roundCgstAmt = round($invitemobj->cgst_amt, 2);
                    $roundSgstAmt = round($invitemobj->sgst_amt, 2);
                    $roundLineTotal = round($lineTotal, 2);
                    $roundQty = round($invitemobj->qty, 2);
                    $totalcgst = round(($invitemobj->cgst_amt * $roundQty), 2);
                    $totalsgst = round(($invitemobj->sgst_amt * $roundQty), 2);
                    $totaltaxable = round(($invitemobj->rate * $roundQty), 2);
                    $tot_val = $totalcgst + $totalsgst + $totaltaxable;
                    $total_taxable_amt = $total_taxable_amt + ($invitemobj->rate * $roundQty);
                    $total_sgst_amt = $total_sgst_amt + $totalsgst;
                    $total_cgst_amt = $total_cgst_amt + $totalcgst;
                    $all_total_val = $all_total_val + $tot_val;
                    
                    $round_all_total_val = round($all_total_val);
                    $invoice_val = $round_all_total_val;
                    $round_off_val = round($round_all_total_val - $all_total_val ,2,PHP_ROUND_HALF_DOWN);
                    
     
                    
                }
            }
            
        } else {
            $itemObjs = $dbl->getInvoiceItems($invid, $userid);
            foreach ($itemObjs as $invitemobj) {
                if (isset($invitemobj) && !empty($invitemobj) && $invitemobj != null) {
                    
                   $total = $invitemobj->qty * $invitemobj->rate;
                   $total_qty = $total_qty + $invitemobj->qty;
                   $total_tot = $total_tot + $total;
                   $total_disc = 0;
                   //$lineTotal = 0;
                   $total_taxable_amt = $total_taxable_amt + $invitemobj->taxable;
                   $total_cgst_amt = $total_cgst_amt + $invitemobj->cgst_amt;
                   $total_sgst_amt = $total_sgst_amt + $invitemobj->sgst_amt;
                   $all_total_val = $obj->total_amount;
                   
                    $round_all_total_val = round($all_total_val);
                    $invoice_val = $round_all_total_val;
                    $round_off_val = round($round_all_total_val - $all_total_val ,2,PHP_ROUND_HALF_DOWN);

                }
            }

        }
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $sr_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $refNum);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $invoicenum);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->saledate);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $custName);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $custPhone);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, round($total_qty,2));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, "18");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, round($total_taxable_amt,2));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $total_cgst_amt);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $total_sgst_amt);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, round($all_total_val, 2));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $round_off_val);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, $invoice_val);
        
        
                    $total_total_qty = $total_total_qty+ $total_qty;
                    $total_total_taxable_amt = $total_total_taxable_amt + $total_taxable_amt;
                    $total_total_cgst_val = $total_total_cgst_val + $total_cgst_amt;
                    $total_total_sgst_val = $total_total_sgst_val + $total_sgst_amt;
                    $total_total_invoice_val = $total_total_invoice_val + $invoice_val;
//                    print_r("inv_amt : ".round($total_qty,2)."<br>");
//                    print_r("total inv_amt : ".$total_total_qty."<br>");


        $sr_no++;
        $rowCount++;

//        break;
    }
//    return;
    $headerstyleArray = array(
        'font' => array(
            'bold' => true,
            //'color' => array('rgb' => '008080'),           
            'size' => 10,
    ));
    $objPHPExcel->getActiveSheet()->getStyle('A:Y')->applyFromArray($headerstyleArray);

    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, "TOTALS");
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, "");
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, "");
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, "");
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, "");
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, "");
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $total_total_qty);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, "");
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, round($total_total_taxable_amt, 2));
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, round($total_total_cgst_val, 2));
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, round($total_total_sgst_val, 2));
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, "");
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, "");
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, round($total_total_invoice_val, 2));

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="CR Sales Report.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>