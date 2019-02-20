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

                $prods_obj = $dbl->getCollectionRegisterByDate($datetime);

                if ($prods_obj) {
                    $envelope = new SimpleXMLElement('<ENVELOPE/>');
                    $name = "COLLECTIONREGISTER_" . $datetime . "xml";
                    $header = $envelope->addChild("HEADER");
                    $header->addChild("TALLYREQUEST", "Import Data");
                    $body = $envelope->addChild("BODY");
                    $importdata = $body->addChild("IMPORTDATA");
                    $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                    $reqdesc->addChild("REPORTNAME", "Collection Register");
                    $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                    $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                    //echo "gererrere";
                    $reqdata = $importdata->addChild("REQUESTDATA");
                    foreach ($prods_obj as $obj) {
                        if (isset($obj) && !empty($obj) && $obj != null) {
                            
                            $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                            $voucher = $tallymsg->addChild("COLLECTIONREGISTER");
                            //$CRObj = $dbl->getCRInfoById($obj->crid);
                            //$CRCODE=strtoupper($CRObj->crcode);
                            //temporary
                            $CRCODE='CR270001';
                            $voucher->addChild("CRCODE",$CRCODE );
                            $voucher->addChild("INVOICENO",$obj->invoice_no);
                            $invoiceDate = date("d-m-Y h:i:s", strtotime($obj->saledate));
                            $voucher->addChild("INVOICEDATE", $invoiceDate);
                            $TOTALAMOUNT= $obj->total_amount;
                            //$voucher->addChild("TOTALAMOUNT", sprintf ("%.2f",TOTALAMOUNT));
                            $roundTotalPOVal= round($TOTALAMOUNT);
                            //$roundoff = $roundTotalPOVal- TOTALAMOUNT;
                            //$voucher->addChild("ROUNDOFF", sprintf ("%.2f",$roundoff));
                            $voucher->addChild("TOTALAMOUNT", $roundTotalPOVal);
                          
                           $paymentMode=strtoupper($obj->chargetypedesc);
                           $pmode=str_replace('CHARGES',' ',$paymentMode);
                            $voucher->addChild("PAYMENTMODE", $pmode);
//                            $voucher->addChild("DEPOSITINBANK", $obj->deposit_in_bank);
//                            $userObj = $dbl->getUserInfoById($obj->user);
//                            $voucher->addChild("BYUSER", $userObj->name);
                            $createDate = date("d-m-Y h:i:s", strtotime($obj->createtime));
                            $voucher->addChild("CREATETIME", $createDate);
                            
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




