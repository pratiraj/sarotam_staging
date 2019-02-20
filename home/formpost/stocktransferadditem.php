<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
//print_r($_POST);
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try {

    $transferid = isset($transferid) && trim($transferid) != "" ? intval($transferid) : false;
    if (!$transferid) {
        $error['missing_grnid'] = "Not able to get Stock Transfer Reference";
    }
    
    if(isset($check1)){
        $transferIn = 0;
    }
    
    if(isset($check2)){
        $transferIn = 1;
    }
    
    $arr = explode("::", $transferitem);
    $grnlineid = $arr[0];
    $prodid = $arr[1];
  
 
    $qty = isset($qty) && trim($qty) != "" ? trim($qty) : false;
    if (!$qty) {
        $qty  = isset($qty2) && trim($qty2) != "" ? trim($qty2) : false;
        if(!$qty){
        $error['missing_qty'] = "Enter qty to Transfer";
        }
    }
    
    

    if (count($error) == 0) {
        $stocktransferitem_id = $dbl->insertStockTransferItem($transferid,$prodid,$qty);

    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'stocktransfer/additem/transferid=' . $transferid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "stocktransfer/additem/transferid=" . $transferid;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
