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

//    $username = isset($_GET['username']) ? ($_GET['username']) : false;
//    if (!$username) {
//        $error['username'] = "Not able to get User Name";
//    }
//
//    $password = isset($_GET['password']) ? ($_GET['password']) : false;
//    if (!$password) {
//        $error['password'] = "Not able to get Password";
//    }

    $datetime = isset($_GET['datetime']) ? ($_GET['datetime']) : false;
    if (!$datetime) {
        $error['datetime'] = "Not able to get date and time";
    }

    if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == 'shradhatally' && $_SERVER['PHP_AUTH_PW'] == 'intouch@2k18') {
        if (count($error) == 0) {

//            $encodedPassword = md5($password);
//            $isvaliduser = $dbl->verifyTallyLogin($username, $encodedPassword);
//            if (isset($isvaliduser) && $isvaliduser != NULL) {

                $prods_obj = $dbl->getProductMastersByDate($datetime);

                if ($prods_obj) {
                    $envelope = new SimpleXMLElement('<ENVELOPE/>');
                    $name = "ProductMasters_" . $datetime . "xml";
                    $header = $envelope->addChild("HEADER");
                    $header->addChild("TALLYREQUEST", "Import Data");
                    $body = $envelope->addChild("BODY");
                    $importdata = $body->addChild("IMPORTDATA");
                    $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                    $reqdesc->addChild("REPORTNAME", "Product Masters");
                    $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                    $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                    //echo "gererrere";
                    $reqdata = $importdata->addChild("REQUESTDATA");
                    foreach ($prods_obj as $obj) {
                        if (isset($obj) && !empty($obj) && $obj != null) {
                            $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                            $voucher = $tallymsg->addChild("PRODUCT");
                            //$dt = date('Y-m-d', strtotime($obj->createtime));
                            //$invdate = preg_replace("/[^0-9]+/", "", $dt);
                            $voucher->addChild("ID", $obj->id);
                            $voucher->addChild("CATEGORY", $obj->catname);
                            $desc1 = isset($obj->desc1) && trim($obj->desc1) != "" ? " , " . $obj->desc1 . " mm" : "";
                            $desc2 = isset($obj->desc2) && trim($obj->desc2) != "" ? " x " . $obj->desc2 . " mm" : "";
                            $thickness = isset($obj->thickness) && trim($obj->thickness) != "" ? " , " . $obj->thickness . " mm" : "";
                            $itemname = $obj->name . $desc1 . $desc2 . $thickness;
                            $voucher->addChild("PRODUCT", $itemname);
                            $voucher->addChild("SHORTNAME", $obj->shortname);
                            $voucher->addChild("HSNCODE", $obj->hsncode);
                            $voucher->addChild("TAXRATE", "18");
                            $voucher->addChild("CGST", "9");
                            $voucher->addChild("SGST", "9");
                            $voucher->addChild("GSTAPPLICABLEDATE", "2018-10-01");
                            $voucher->addChild("STANDEREDLENGTH", $obj->stdlength);
                            $voucher->addChild("UNITOFMEASURE", "KG");
                            $voucher->addChild("KGPERPIECE", $obj->kg_per_pc);
                            $voucher->addChild("CREATETIME", $obj->createtime);
                        }
                    }

                   // header('Content-Disposition: attachment;filename=' . $name);
                    header('Content-Type: application/xml; charset=utf-8');
                    $string = $envelope->saveXML();
                    echo $string;
                } else {
                    echo "No Record found for given date range";
                }
                //print_r($customer_obj);
//            } else {
//                echo '<span style="color:red;text-align:center;">Incorrect Username or Password </span>';
//            }
        } else {
            foreach ($error as $key => $value) {
                echo $value;
            }
        }
    }else {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Incorrect Username or password.';
        exit;
    }
} catch (Exception $xcp) {
    print($xcp->getMessage());
}




