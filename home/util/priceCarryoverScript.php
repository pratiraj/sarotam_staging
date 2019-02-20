<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';
require_once "lib/email/EmailHelper.php";


$error = array();
$db = new DBConn();
$dbl = new DBLogic(); 

try {
  
   
   
    $query = "select max(applicable_date)as applicable_date,product_id from it_product_price  where  is_approved=1 group by product_id"; 
    print_r($query);
    $qryObj = $db->fetchObjectArray($query);
    $newDate=date("Y-m-d H:i", time());
    //$newDate="2019-02-01";
    $yesterday=date('jS F Y',strtotime("-1 days"));
    $today=date('jS F Y');
    print_r($newDate);
    $rowUpdated = 0;
    //$newApplicableTime=date("H:i", time());
    if(isset($qryObj)){
        foreach ($qryObj as $obj){
            
            
            $applicableDate = $obj->applicable_date;
            $prodid = $obj->product_id;
            //print_r($applicableDate);
             //print_r($applicableTime);
              //print_r($productID);
           $prodDetailsQry="select price,lastprice,createdby,crid from it_product_price where applicable_date='".$applicableDate."' and product_id=$prodid";
           //print_r($prodDetailsQry);
           $qryObj1 = $db->fetchObjectArray($prodDetailsQry); 
           foreach ($qryObj1 as $obj2){
            $price=$obj2->price;
            $userid=$obj2->createdby;
            $cr=$obj2->crid;
            $last_price=$obj2->lastprice;
            $uploaddate = $newDate;
            //$time=$newApplicableTime;
              
           $id=$dbl->uploadYesterdaysPrices($prodid,$price,$userid,$cr,$last_price,$uploaddate);
            $rowUpdated++;
            }
        }
        echo "Total Rows Updated ". $rowUpdated;
//         $objDirector = $dbl->getUserInfoByType(UserType::Director);
//
//    if ($rowUpdated > 0) {
//        //if ($objpo->po_status == $postatus) {
//        if (isset($objDirector->email) && trim($objDirector->email) != "") {
//            $arr_to = explode(",", $objDirector->email);
//
//            //$arr_to = $objpo->email;
//            foreach ($arr_to as $to) {
//                echo $to;
//                $subject = "Price Carryover "; //." Date ". ddmmyy_date($objpo->submitdate);
//               $body = '<p>The price on ' . $yesterday . '. is being carried over to ' . $today . '. at All Consignment Retail Outlets:</p>
//                     
//                                <p>Thanks & Regards,</p>
//                                <p>Sarotam</p>
//                                <p><b>Note : This is computer generated email do not reply.  </b></p>';
//
//                print_r($body);
//                $emailHelper = new EmailHelper();
//                $success = $emailHelper->send(array($to), $subject, $body);
//                //$dbl->updateEmailStatus($objpo->id);
//            }
//        }
//        
//        
//    }
//    $objHO = $dbl->getUserInfoByType(UserType::HO);
//    if ($rowUpdated > 0) {
        //if ($objpo->po_status == $postatus) {
//        if (isset($objHO->email) && trim($objHO->email) != "") {
//            $arr_to = explode(",", $objHO->email);
//
//            //$arr_to = $objpo->email;
//            foreach ($arr_to as $to) {
//                //echo $to;
//                $subject = "Price Carryover "; //." Date ". ddmmyy_date($objpo->submitdate);
//                $body = '<p>The price on ' . $yesterday . '. is being carried over to ' . $today . '. at All Consignment Retail Outlets:</p>
//                     
//                                <p>Thanks & Regards,</p>
//                                <p>Sarotam</p>
//                                <p><b>Note : This is computer generated email do not reply.  </b></p>';
//
//                $emailHelper = new EmailHelper();
//                $success = $emailHelper->send(array($to), $subject, $body);
//                //$dbl->updateEmailStatus($objpo->id);
//            }
//        }
//        
//        
//    }
    
    }
    
} catch (Exception $ex) {
    
}


