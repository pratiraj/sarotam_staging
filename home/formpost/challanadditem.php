<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';


$error = array();
extract($_POST);
print_r($_POST);
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try {

    $transferid = isset($transferid) && trim($transferid) != "" ? intval($transferid) : false;
    if (!$transferid) {
        $error['missing_grnid'] = "Not able to get Transfer id";
    }
        $qty  = isset($qty2) && trim($qty2) != "" ? trim($qty2) : false;
        if(!$qty){
            $error['missing_qty'] = "Enter qty to Transfer";
        }
        $challan  = isset($challanid) && trim($challanid) != "" ? trim($challanid) : false;
        if(!$challan){
            $error['missing_challan'] = "Missing Challan Reference";
        }
        $stitemdata  = isset($transferitem) && trim($transferitem) != "" ? trim($transferitem) : false;
        if(!$stitemdata){
            $error['missing_stockitems'] = "Missing Stock Transfer Item Reference";
        }
        $batchcodedata  = isset($batchcodes1) && trim($batchcodes1) != "" ? trim($batchcodes1) : false;
        if(!$batchcodedata){
            $error['missing_batchcode'] = "Missing Batch Reference";
        }
        $pcs  = isset($pieces2) && trim($pieces2) != "" ? trim($pieces2) : false;
        if(!$pcs){
            $error['missing_pices'] = "Missing Pieces info";
        }

    if (count($error) == 0) {

         $stockitemarr = explode("::", $stitemdata);
         $stitemid =$stockitemarr[0]; 
         $prodid =$stockitemarr[1];
         $batcharr = explode("::", $batchcodedata);
         $batchcode = $batcharr[1];
         $length = $batcharr[2];
         // echo $batchcodedata;
         // echo 'test'; 
        $stocktransferitem_id = $dbl->insertChallanItem($challan, $stitemid,$prodid,  $qty, $batchcode, $pcs, $userid, $length);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'challan/additem/transferid=' . $transferid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "challan/additem/transferid=" . $transferid;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
