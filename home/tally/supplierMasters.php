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
        // the user is authenticated and handle the rest api call here
        //echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
        if (count($error) == 0) {

                $customer_obj = $dbl->getSupplierMastersByDate($datetime);
                if ($customer_obj) { 
                    $envelope = new SimpleXMLElement('<ENVELOPE/>');
                    $name = "SupplierMaster" . $datetime . "xml";
                    $header = $envelope->addChild("HEADER");
                    $header->addChild("TALLYREQUEST", "Import Data");
                    $body = $envelope->addChild("BODY");
                    $importdata = $body->addChild("IMPORTDATA");
                    $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                    $reqdesc->addChild("REPORTNAME", "Supplier Masters");
                    $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                    $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                    //echo "gererrere";
                    $reqdata = $importdata->addChild("REQUESTDATA");
                    foreach ($customer_obj as $obj) {
                        if (isset($obj) && !empty($obj) && $obj != null) {
                            $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                            $voucher = $tallymsg->addChild("SUPPLIER");
                            //$dt = date('Y-m-d', strtotime($obj->createtime));
                            //$invdate = preg_replace("/[^0-9]+/", "", $dt);
                            $voucher->addChild("ID", $obj->id);
                            $voucher->addChild("SUPPLIERCODE", $obj->supplier_code);
                            $voucher->addChild("DATEOFENTRY", $obj->date_of_entry);
                            $voucher->addChild("KYCNUMBER", $obj->kyc_number);
                            $voucher->addChild("COMPANYNAME", $obj->company_name);
                            $voucher->addChild("BANKNAME", $obj->bank_name);
                            $voucher->addChild("BANKACCOUNTNUMBER", $obj->bank_ac_no);
                            $voucher->addChild("BANKBRANCH", $obj->bank_branch);
                            $cleanString = str_replace('<br/>', ' ',$obj->address); 
                            $split = explode(' ', $cleanString); // Split up the whole string
                            $chunks = array_chunk($split, 6); // Make groups of 3 words
                            $address = array_map(function($chunk) {
                                return implode(' ', $chunk);
                            }, $chunks); // Put each group back together
                            $addCount = 1;
                            $length = count($address);
                            for($i=0 ; $i<$length; $i++){
                                $voucher->addChild("ADDRESS".$addCount, $address[$i]);
                                $addCount++;
                            }
                            $voucher->addChild("COUNTRY", "India");
                            $voucher->addChild("STATE", $obj->sstate);
                            $voucher->addChild("DISTRICT", $obj->district);
                            $voucher->addChild("PINCODE", $obj->pincode);
                            $voucher->addChild("PANNUMBER", $obj->pan_no);
                            $voucher->addChild("CINNUMBER", $obj->cin_no);
                            $voucher->addChild("GSTIN", $obj->gst_no);
                            $voucher->addChild("CONTACTPERSON", $obj->contact_person1);
                            $voucher->addChild("PHONENUMBER", $obj->phone1);
                            $voucher->addChild("EMAIL", $obj->email1);
                            $voucher->addChild("CREATETIME", $obj->createtime);
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




