<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
try {
    $db = new DBConn();
    $dbl = new DBLogic();


    if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == 'shradhatally' && $_SERVER['PHP_AUTH_PW'] == 'intouch@2k18') {

        $datetime = isset($_GET['datetime']) ? ($_GET['datetime']) : false;
        if (!$datetime) {
            $error['datetime'] = "Not able to get date and time";
        }

        if (count($error) == 0) {

            $po_obj = $dbl->getPODetailsByDate($datetime);

            if ($po_obj) {
                $envelope = new SimpleXMLElement('<ENVELOPE/>');
                $name = "PurchaseOrderVoucher_" . $datetime . "xml";
                $header = $envelope->addChild("HEADER");
                $header->addChild("TALLYREQUEST", "Import Data");
                $body = $envelope->addChild("BODY");
                $importdata = $body->addChild("IMPORTDATA");
                $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                $reqdesc->addChild("REPORTNAME", "Purchase Order Voucher");
                $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                //echo "gererrere";
                $reqdata = $importdata->addChild("REQUESTDATA");
                foreach ($po_obj as $obj) {
                    if (isset($obj) && !empty($obj) && $obj != null) {
                        //$crdetails = $dbl->getCRInfoById($obj->crid);
                        $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                        $salesdata = $tallymsg->addChild("PURCHASEORDER");
                        //$dt = date('Y-m-d', strtotime($obj->createtime));
                        //$invdate = preg_replace("/[^0-9]+/", "", $dt);
                        $voucher = $salesdata->addChild("PURCHASEORDERINFO");
                        $voucher->addChild("ID", $obj->id);
                        $voucher->addChild("PONUMBER", $obj->pono);
                        $voucher->addChild("SUPPLIER", $obj->supplierName);
                        $voucher->addChild("QUANTITY", sprintf("%.4f",$obj->tot_qty));
                        $polines_obj = $dbl->getPOItems($obj->id);
                        
                        $gstTotal = 0;
                        $taxable_value = 0;
                        $cgst_value = 0;
                        $sgst_value = 0;
                        $total_value = 0;
                        $tot_value = 0;
                        foreach($polines_obj as $itemobj) {

                            $taxable_value = round($line->rate * $line->qty,2);
                            $cgst_value = round($taxable_value * $line->cgstpct,2);
                            $sgst_value = round($taxable_value * $line->sgstpct,2);
                            $gstTotal = $gstTotal + ($itemobj->cgstval + $itemobj->cgstval) * $itemobj->qty;
                            $tot_value = $taxable_value + $cgst_value + $sgst_value;
                        }
                        $voucher->addChild("GSTTOTAL", sprintf("%.2f", $gstTotal));
                        $totalValue = $tot_value;
                        $voucher->addChild("TOTALVALUE", sprintf("%.2f", $totalValue));
                        $roundTotalPOVal = round($totalValue);
                        $roundoff = $roundTotalPOVal - $totalValue;
                        $voucher->addChild("ROUNDOFF", sprintf("%.2f", $roundoff));
                        $voucher->addChild("GRANDTOTALVALUE", $roundTotalPOVal);
                        $voucher->addChild("PAYMENTTERM", $obj->paymentterm);
                        //print($obj->createtime);
                        $newDate = date("d-m-Y h:i:s", strtotime($obj->createtime));

                        $voucher->addChild("DATE", $newDate);
                        //$polines_obj2 = $dbl->getPOItems($obj->id);

                        foreach ($polines_obj as $obj) {
                            $taxable_value = round($line->rate * $line->qty,2);
                            $cgst_value = round($taxable_value * $line->cgstpct,2);
                            $sgst_value = round($taxable_value * $line->sgstpct,2);
                            $total_value = $taxable_value + $cgst_value + $sgst_value; 
                            
                            $voucher2 = $salesdata->addChild("POITEM");
                            $voucher2->addChild("CATEGORY", $obj->category);
                            $desc1 = isset($obj->desc_1) && trim($obj->desc_1) != "" ? " , " . $obj->desc_1 . " mm" : "";
                            $desc2 = isset($obj->desc_2) && trim($obj->desc_2) != "" ? " x " . $obj->desc_2 . " mm" : "";
                            $thickness = isset($obj->thickness) && trim($obj->thickness) != "" ? " , " . $obj->thickness . " mm" : "";
                            $itemname = $obj->prod . $desc1 . $desc2 . $thickness;
                            $voucher2->addChild("PRODUCT", $itemname);
                            $voucher2->addChild("HSNCODE", $obj->hsncode);
                            $voucher2->addChild("COLOR", $obj->color);
                            $voucher2->addChild("MANUFACTURER", $obj->manufacturer);
                            $voucher2->addChild("BRAND", $obj->brand);
                            $voucher2->addChild("SKU", $obj->sku);
                            $voucher2->addChild("LENGTH", $obj->length);
                            $voucher2->addChild("QUANTITY", sprintf("%.4f",$obj->qty));
                            $voucher2->addChild("NUMBEROFPIECES", round($obj->no_of_pieces, 2));
                            $voucher2->addChild("BASERATE", $obj->rate);
                            $voucher2->addChild("TAXABLEVALUE", sprintf("%.2f",$obj->rate * $obj->qty));
                            //$voucher2->addChild("LCRATE", $obj->lcrate);
                            $voucher2->addChild("CGSTPERCENTAGE", $obj->cgstpct);
                            $voucher2->addChild("CGSTAMOUNT", sprintf("%.2f", $obj->cgstval * $obj->qty));
                            $voucher2->addChild("SGSTPERCENTAGE", $obj->sgstpct);
                            $voucher2->addChild("SGSTAMOUNT", sprintf("%.2f", $obj->sgstval * $obj->qty));
                            $voucher2->addChild("TOTALRATE", sprintf("%.2f", $obj->totalrate));
                            $voucher2->addChild("TOTALVALUE", sprintf("%.2f", $obj->totalvalue));
                            //$newDate1 = date("d-m-Y h:i:s", strtotime($obj->createtime));
                            //$voucher2->addChild("CREATETIME", $newDate1);
                        }
                        
//                        print_r($voucher2);
////                        echo $gstTotal;
//                        return;
                        
                    }
                }
//                return;
                header('Content-Disposition: attachment;filename=' . $name);
                header('Content-Type: application/xml; charset=utf-8');
                $string = $envelope->saveXML();
                echo $string;
            } else {
                echo "No Record found for given date range";
            }
        } else {
            foreach ($error as $key => $value) {
                echo $value;
            }
        }
    } else {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Incorrect Username or password.';
        exit;
    }
} catch (Exception $xcp) {
    print($xcp->getMessage());
}




