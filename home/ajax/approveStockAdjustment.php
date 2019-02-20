<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/email/EmailHelper.php";

extract($_GET);
$userid = getCurrStoreId();
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    $dcid = "NULL";
    $reason=1;
    $sign=1;
    $crid = isset($crid) && trim($crid) != "" ? $crid : NULL;
    $uploaddate = isset($uploaddate) && trim($uploaddate) != "" ? $uploaddate : NULL;
    $prodid = isset($prodid) && trim($prodid) != "" ? $prodid : NULL;
    $id = isset($id) && trim($id) != "" ? $id : NULL;
    $addedstock = isset($addedstock) && trim($addedstock) != "" ? $addedstock : NULL;
    $tranid=$id;
$qty=$addedstock;
$rate=0;
    if($crid == NULL){ 
        $resp = array(
            "error" => "1",
            "msg" => "Please Select CR / Select All"
        );
        echo json_encode($resp);
        
    }else{
        $dbl->approveStockAdjustment($uploaddate,$crid,$userid,$prodid);
        $obj= $dbl->getLatestBatchCodeByProdID($prodid,$crid);
        $batchcode=$obj->batchcode;
        $dbl->updateStock($crid, $dcid, $prodid, $batchcode, $reason, $sign,$qty, $rate, $tranid);
        $resp = array(
            "error" => "0",
            "msg" => "success"
        );
        
       
        $currDate = $uploaddate;
        error_log("\n currDate : ".$currDate."\n",3,"../ajax/tmp.txt");   
        $usertype = UserType::HO;
        $obj_user = $dbl->getUserInfoByType($usertype);
        $crname = "";
        if($crid > 0){
            $objcr = $dbl->getCRInfoById($crid);
            if($objcr != NULL){
                $crname = $objcr->crcode;
            }
        }else{
            $crname = "All CR";
        }
          
        $emailid = "";
        if($obj_user != NULL && isset($obj_user)){
            $emailid = $obj_user->email;
        }
         
        //email sending
        
        $subject = "Approved Product Stock : Stock Adjustment Approved (".ddmmyy($currDate).")";
        $body = '<p>New stock adjustment Approved for '.strtoupper($crname).'<br>'
              . 'Please Check</p>';
        
        $emailHelper = new EmailHelper();
        $emailHelper->send(array($emailid), $subject, $body);
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
