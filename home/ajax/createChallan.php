<?php
include "../../it_config.php";
require_once "session_check.php";
// require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";

$userid = getCurrStoreId();
$error = array();
try{
    // $db = new DBConn();
    $dbl = new DBLogic();
    
    $transferid = isset($_GET['transferid']) ? ($_GET['transferid']) : false;
    if(!$transferid){ $error['transferid'] = "Not able to get Stock Transfer Id"; }
          

    if(count($error) == 0){
          $obj_stocktransfer = $dbl->getStockTransferDetails($transferid);

   if(isset($obj_stocktransfer)){
            $fromname =$obj_stocktransfer->fromloc; 
            $stateid = 0;
            if($obj_stocktransfer->from_location_type == LocationType::DC){
                 $objdc = $dbl->getDCInfo($obj_stocktransfer->from_location_id); 
                 $stateid = $objdc->state; 
            }else{
                $crdc = $dbl->getCRInfoById($obj_stocktransfer->from_location_id);
                $stateid = $crdc->state; 
            }
        
         $stcnum = "STC-".$fromname."/".$dbl->getActiveFinancialYear()."-".$dbl->fetchNextChallanNumber($stateid);
         $dbl->insertStockTransferChallan($obj_stocktransfer->id, $stcnum, StockTransferChallanStatus::BeingCreated, $userid,$stateid);
         // $dbl->updatetockTransfer($obj_stocktransfer->id, StockTransferStatus::Completed, $userid);
        }
        $resp = array(
            "error" => "0",
            "msg" => "success"
        );
        echo json_encode($resp);
    }else{
        $resp = array(
            "error" => "1",
            "msg" => "Not able to Generate Challan"
        );
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
