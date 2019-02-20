<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once 'lib/db/DBLogic.php';

$error = array();
$dbl = new DBLogic();
extract($_POST);
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$db = new DBConn();
$dbl = new DBLogic();

try{
    if(trim($amount) == "" || trim($description) == ""){
        $error['missing_parameters'] = "Please Enter All Required Fields";
    }
    
    if(count($error) == 0){
        $voucher_num = $dbl->getVoucherNum();
        $userid = $user->id;
        $crdetails = $dbl->getCRDetailsByUserId($userid);
        $stateobj = $dbl->getStateInfo($crdetails->state);
        $stateTin = $stateobj->TIN;
        $crid = $crdetails->id;
        $voucher_no = strtoupper("IM".$dbl->getCRCode($userid) . "/" . $dbl->getActiveFinancialYear() . "-" . $stateTin . "/" . $voucher_num);
        $obj = $dbl->insertIntoImprestDetails($amount,$description,$voucher_no, $userid, $crid);
        if($obj != 0){
            $result = $dbl->updateVoucherNum();
            $success = "Data Entered Successfully.";
        }
    }
    
    

    
    

} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'imprest/register';
} else {

    
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'imprest/register';
}
session_write_close();
 header("Location: " . DEF_SITEURL . $redirect);
exit;