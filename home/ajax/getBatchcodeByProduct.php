<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/email/EmailHelper.php";

$userid = getCurrStoreId();
$user = getCurrStore();
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    
    $stockcurrid = isset($_GET['stockcurrid']) ? ($_GET['stockcurrid']) : false;
    if(!$stockcurrid){ $error['stockcurrid'] = "Not able to get Stock Item Id"; }
    
    $fromlocid = isset($_GET['fromlocid']) ? ($_GET['fromlocid']) : false;
    if(!$fromlocid){ $error['fromlocid'] = "Not able to get From Loc Id"; }
    
    $fromloctype = isset($_GET['fromloctype']) ? ($_GET['fromloctype']) : false;
    if(!$fromloctype){ $error['fromloctype'] = "Not able to get From Loc TYPE"; }
    
    if(count($error) == 0){
        $availableqty = 0;
        $batchcode = "";
        $objBatchcodes = $dbl->getBatchCodeByProductid($stockcurrid,$fromlocid,$fromloctype);
        if($objBatchcodes != NULL){
//            $availableqty = $obj->qty;
//            $batchcode = $obj->batchcode;
            ?><?php
	foreach($objBatchcodes as $batchcodes){
		?>
        <option value="<?php echo $batchcodes->id."::".$batchcodes->batchcode."::".$batchcodes->length."::".$batchcodes->qty."::".$batchcodes->no_of_pieces; ?>"><?php echo $batchcodes->batchcode.", Qty - ".$batchcodes->qty.", Length - ".$batchcodes->length; ?></option>
        <?php
	}
//            $resp = array(
//                "error" => "0",
//                "availableqty" => $availableqty,
//                "batchcode" => $batchcode
//            );
//            echo json_encode($resp);
        }
    }
}catch(Exception $xcp){
    print($xcp->getMessage());
}
