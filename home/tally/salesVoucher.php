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

                $sales_obj = $dbl->getSaleDetailsByDate($datetime);
                
                if ($sales_obj) {
                    $envelope = new SimpleXMLElement('<ENVELOPE/>');
                    $name = "SalesVoucher_" . $datetime . "xml";
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
                    foreach ($sales_obj as $obj) {
                        if (isset($obj) && !empty($obj) && $obj != null) {
                            $crdetails = $dbl->getCRInfoById($obj->crid);
                            $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                            $salesdata = $tallymsg->addChild("INVOICE");
                            //$dt = date('Y-m-d', strtotime($obj->createtime));
                            //$invdate = preg_replace("/[^0-9]+/", "", $dt);
                            $voucher = $salesdata->addChild("INVOICEINFO");
                            $voucher->addChild("ID", $obj->id);
                            $voucher->addChild("CRID", $obj->crid);
                            $voucher->addChild("CRCODE", strtoupper($obj->crcode));
                            $voucher->addChild("REFERENCENUMBER", $obj->reference_no);
                            $voucher->addChild("INVOICENUMBER", $obj->invoice_no);
                            $date = date('d-m-Y', strtotime($obj->invoice_date));
                            $voucher->addChild("INVOICEDATE", $date);
                            $voucher->addChild("CUSTOMERID", $obj->customer_id);
                            $voucher->addChild("CUSTOMERNAME", $obj->cname);
                            $voucher->addChild("CUSTOMERPHONE", $obj->cphone);
                            if ($obj->invoice_type == 0) {
                                $voucher->addChild("INVOICETYPE", "SALE");
                            }

                            if ($obj->paymentmode == 0) {
                                $voucher->addChild("PAYMENTMODE", "CASH");
                            } elseif ($obj->paymentmode == 1) {
                                $voucher->addChild("PAYMENTMODE", "DEBIT CARD");
                            } elseif ($obj->paymentmode == 2) {
                                $voucher->addChild("PAYMENTMODE", "CREDIT CARD");
                            }
                            $voucher->addChild("NETVALUE", $obj->netvalue);
                            $voucher->addChild("ROUNDOFF", $obj->roundoff);
                            $voucher->addChild("INVOICEVALUE", $obj->invoicevalue);
                            $invoiceitems_obj = $dbl->getInvoiceItemsByInvoiceNo($obj->invoice_no);
                            
                            foreach ($invoiceitems_obj as $objs) {
                                $newDate1 = date("d-m-Y h:i:s", strtotime($objs->createtime));
                            }
                            $voucher->addChild("CREATETIME", $newDate1);
                            
                            foreach ($invoiceitems_obj as $obj) {
                                $voucher2 = $salesdata->addChild("INVOICEITEM");
                                $desc1 = isset($obj->desc1) && trim($obj->desc1) != "" ? " , " . $obj->desc1 . " mm" : "";
                                $desc2 = isset($obj->desc2) && trim($obj->desc2) != "" ? " x " . $obj->desc2 . " mm" : "";
                                $thickness = isset($obj->thickness) && trim($obj->thickness) != "" ? " , " . $obj->thickness . " mm" : "";
                                $itemname = $obj->name . $desc1 . $desc2 . $thickness;
                                $voucher2->addChild("PRODUCT", $itemname);
                                $voucher2->addChild("BATCHCODE", $obj->batchcode);
                                $voucher2->addChild("QUANTITY", sprintf("%.2f",$obj->qty));
                                $voucher2->addChild("ACTUALRATE", $obj->actualrate);
                                $voucher2->addChild("MRP", $obj->mrp);
                                $voucher2->addChild("CUTTINGCHARGES", $obj->cuttingcharges);
                                if (isset($obj->paymentcharges)) {
                                    $voucher2->addChild("PAYMENTCHARGES", $obj->paymentcharges);
                                }
//                                $voucher2->addChild("RATE", $obj->rate);
                                $voucher2->addChild("TAXABLEVALUE", sprintf("%.2f",$obj->rate * $obj->qty));
                                if ($crdetails->state == "22") {
                                    $voucher2->addChild("CGSTPERCENTAGE", $obj->cgst_percent);
                                    $voucher2->addChild("CGSTAMOUNT", sprintf("%.2f",$obj->cgst_amt *  $obj->qty));
                                    $voucher2->addChild("SGSTPERCENTAGE", $obj->sgst_percent);
                                    $voucher2->addChild("SGSTAMOUNT", sprintf("%.2f",$obj->sgst_amt * $obj->qty));
                                } else {
                                    $voucher2->addChild("IGSTPERCENTAGE", sprintf("%.2f",$obj->igst_percent * $obj->qty));
                                    $voucher2->addChild("IGSTAMOUNT", $obj->igst_amt);
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




