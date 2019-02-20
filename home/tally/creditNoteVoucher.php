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

    $datetime = isset($_GET['datetime']) ? ($_GET['datetime']) : false;
    if (!$datetime) {
        $error['datetime'] = "Not able to get date and time";
    }

    if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == 'shradhatally' && $_SERVER['PHP_AUTH_PW'] == 'intouch@2k18') {
        if (count($error) == 0) {

                $creditnote_obj = $dbl->getCreditNoteDetailsByDate($datetime);
               // print_r($creditnote_obj);
//                 [0] => stdClass Object ( [id] => 6 [crid] => 1 [cnno] => CN-27/1819-7 [invoiceid] => 545 
//                         [invoice_no] => CR270001/1819-27/507 [invoice_date] => 2019-01-12 00:00:00 [customerid] => 102 
//                     [cphone] => 9326225033 [cname] => Satish Borade [cndate] => 2019-01-22 [discount] => 10 [status] => 1 
//                     [inactive] => 0 [tot_qty] => 7 [tot_value] => 289.17 [createdby] => 14 [createtime] => 2019-01-22 17:10:16 
//                     [updatedby] => [updatetime] => 0000-00-00 00:00:00 
               // return;
                if ($creditnote_obj) {
                    $envelope = new SimpleXMLElement('<ENVELOPE/>');
                    $name = "CreditNoteVoucher_" . $datetime . "xml";
                    $header = $envelope->addChild("HEADER");
                    $header->addChild("TALLYREQUEST", "Import Data");
                    $body = $envelope->addChild("BODY");
                    $importdata = $body->addChild("IMPORTDATA");
                    $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                    $reqdesc->addChild("REPORTNAME", "Sales Voucher");
                    $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                    $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                    //echo "gererrere";
                    $reqdata = $importdata->addChild("REQUESTDATA");
                    foreach ($creditnote_obj as $obj) {
                        if (isset($obj) && !empty($obj) && $obj != null) {
                            $crdetails = $dbl->getCRInfoById($obj->crid);
                            $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                            $salesdata = $tallymsg->addChild("CREDITNOTE");
                            //$dt = date('Y-m-d', strtotime($obj->createtime));
                            //$invdate = preg_replace("/[^0-9]+/", "", $dt);
                            $voucher = $salesdata->addChild("CREDITNOTEINFO");
                            $voucher->addChild("ID", $obj->id);
                            $voucher->addChild("CREDITNOTENUMBER", $obj->cnno);
                            $voucher->addChild("CRID", $obj->crid);
                            $voucher->addChild("CRCODE", strtoupper($crdetails->crcode));
                            //$voucher->addChild("REFERENCENUMBER", $obj->reference_no);
                            $date = date('d-m-Y', strtotime($obj->cndate));
                            $voucher->addChild("CREDITNOTEDATE", $date);
                            $voucher->addChild("CUSTOMERID", $obj->customerid);
                            $voucher->addChild("CUSTOMERNAME", $obj->cname);
                            $voucher->addChild("CUSTOMERPHONE", $obj->cphone);
                            $totalValue = $obj->tot_value;
                            $voucher->addChild("TOTALVALUE", sprintf("%.2f", $totalValue));
                            $roundTotalPOVal = round($totalValue);
                            $roundoff = $roundTotalPOVal - $totalValue;
                            $voucher->addChild("ROUNDOFF", sprintf("%.2f", $roundoff));
                            $voucher->addChild("GRANDTOTALVALUE", $roundTotalPOVal);
                            $invoiceitems_obj = $dbl->getCNItems($obj->id);
                            
                            foreach ($invoiceitems_obj as $objs) {
                                $newDate1 = date("d-m-Y h:i:s", strtotime($objs->createtime));
                            }
                            $voucher->addChild("CREATETIME", $newDate1);
                            //print_r($voucher);
                            //return;
                            foreach ($invoiceitems_obj as $obj) {
                                $voucher2 = $salesdata->addChild("CREDITNOTEITEM");
                                $desc1 = isset($obj->desc_1) && trim($obj->desc_1) != "" ? " , " . $obj->desc_1 . " mm" : "";
                                $desc2 = isset($obj->desc_2) && trim($obj->desc_2) != "" ? " x " . $obj->desc_2 . " mm" : "";
                                $thickness = isset($obj->thickness) && trim($obj->thickness) != "" ? " , " . $obj->thickness . " mm" : "";
                                $itemname = $obj->product . $desc1 . $desc2 . $thickness;
                                $voucher2->addChild("PRODUCT", $itemname);
                                $voucher2->addChild("BATCHCODE", $obj->batchcode);
                                $voucher2->addChild("QUANTITY", sprintf("%.2f",$obj->qty));
                                $voucher2->addChild("ACTUALRATE", $obj->actualrate);
                                $voucher2->addChild("MRP", $obj->mrp);
                                //$voucher2->addChild("CUTTINGCHARGES", $obj->cuttingcharges);
//                                if (isset($obj->paymentcharges)) {
//                                    $voucher2->addChild("PAYMENTCHARGES", $obj->paymentcharges);
//                                }
//                                $voucher2->addChild("RATE", $obj->rate);
                                $voucher2->addChild("TAXABLEVALUE", sprintf("%.2f",$obj->rate * $obj->qty));
                                if ($crdetails->state == "22") {
                                    $voucher2->addChild("CGSTPERCENTAGE", $obj->cgstpct);
                                    $voucher2->addChild("CGSTAMOUNT", sprintf("%.2f",$obj->cgstval *  $obj->qty));
                                    $voucher2->addChild("SGSTPERCENTAGE", $obj->sgstpct);
                                    $voucher2->addChild("SGSTAMOUNT", sprintf("%.2f",$obj->sgstval * $obj->qty));
                                } else {
                                    $voucher2->addChild("IGSTPERCENTAGE", sprintf("%.2f",$obj->igstpct * $obj->qty));
                                    $voucher2->addChild("IGSTAMOUNT", $obj->igstval);
                                }
                                $voucher2->addChild("TOTAL", sprintf("%.2f",$obj->total));
                                $newDate1 = date("d-m-Y h:i:s", strtotime($obj->createtime));
                                //$voucher2->addChild("CREATETIME", $newDate1);
                            }
                        }
                    }

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




