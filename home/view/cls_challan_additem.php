 <?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_challan_additem extends cls_renderer {

    var $currStore;
    var $userid;
    var $dtrange;
    var $params;
    var $poid = "";

    function __construct($params = null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        $this->currStore = getCurrStore();
        $this->params = $params;
        if (isset($this->params["transferid"]) != "") {
            $this->transferid = $this->params["transferid"];
            
        }
    }

    function extraHeaders() {
        ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" /> 
<style type="text/css" title="currentStyle">
    /*  @import "js/datatables/media/css/demo_page.css";
      @import "js/datatables/media/css/demo_table.css";*/
    @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
    @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
    div.alert *
    {
        color: red;
    }
</style>
<script type="text/javaScript">   

$(function () {  
            $('#batchcodes').on('change', function(){  
                var selected = $(this).find("option:selected");
                var arrSelected = []; 
                var count = 0; 
                var totQty = 0; 
                var pieces = 0; 
                selected.each(function(){ 
                    var v = $(this).val().split("::"); 
                    var qt = parseFloat(v[3]);
                    //alert("qty"+qt);
                    var prodsel = $("#transferitem").val();
                    var length = parseFloat(v[2]);
                    //alert("length"+length);
                    var arr = prodsel.split("::"); 
                    var prodid = arr[1];
                    var kgperpc = parseFloat(arr[2]); 
                    if(kgperpc > 0 && qt > 0 && length > 0){
                         var piece = qt / ((length / 1000) * kgperpc); 
                         piece = Math.round(piece);
                    }
                    pieces = pieces + piece;
                    count++;
                    if(count > 1){
                        document.getElementById('qty').readOnly = true;
                        document.getElementById('pieces').readOnly = true;
                    }else{
                        document.getElementById('qty').readOnly = false;
                        document.getElementById('pieces').readOnly = false;
                    }
                    totQty = totQty + qt;
                    $("#qty").val(totQty);
                    $("#length").val(length);
                    $("#pieces").val(pieces);
                    $("#availableqty").val(totQty);
                });
                //alert(count);
                $("#selectedbatches").val(count);
                $("#qty").val(totQty);
                var qt = parseFloat($("#qty").val());
                var selectedBatches = $("#selectedbatches").val();
             
             });
             
//              $('#batchcodes1').on('change', function(){
//                 alert(0);
//                 var selected = $(this).find("option:selected");
//                 var arrSelected = [];
//                 var count = 0;
//                 var totQty = 0;
//                 var pieces = 0;
//                 selected.each(function(){
//                     var v = $(this).val().split("::");
//                     alert("V"+v)
//                     var qt = parseFloat(v[3]);
//                     var prodsel = $("#transferitem").val();
//                     var length = parseFloat(v[2]); 
//                     // alert(length);batchcodes1
//                     var arr = prodsel.split("::"); 
//                     alert('alert'+arr)
//                     var prodid = arr[1];
//                     var batchcodepieces = parseFloat(v[4]);

//                     var kgperpc = parseFloat(arr[2]);
// //                    if(kgperpc > 0 && batchcodepieces > 0 && length > 0){
// //                         var piece = batchcodepieces * length * (kgperpc/1000);  
// //                         alert("pieces "+piece);
// //                         piece = Math.round(piece);
// //                    }
//                     pieces = pieces + batchcodepieces;
//                     count++;
//                     if(count > 1){
//                         document.getElementById('qty2').readOnly = true;
//                         document.getElementById('pieces2').readOnly = true;
//                     }else{
//                         document.getElementById('qty2').readOnly = false;
//                         document.getElementById('pieces2').readOnly = false;
//                     }
//                     totQty = totQty + qt;
//                     $("#length").val(length);
//                     // alert('tqty'+totQty)
//                     $("#qty2").val(totQty);
//                     $("#pieces2").val(pieces);
//                     $("#availablepcs").val(pieces);
//                     $("#availableqty").val(totQty);
//                 });
//                 //alert(count);
//                 //$("#selectedbatches").val(count);
//                 //$("#qty").val(totQty);
//                 //var qt = parseFloat($("#qty").val());
//                 //var selectedBatches = $("#selectedbatches").val();
             
//              }); 
             
             
      });        

  function calcValue(v){ 
                var prodsel = $("#poitem").val(); 
                var length = parseFloat($("#length").val());    
                var arr = prodsel.split("::");   
                var prodid = arr[1]; 
                var kgperpc = parseFloat(arr[2]);
                var qt = parseFloat($("#qty").val());
                var rate = parseFloat($("#rate").val());
                var lccharge = parseFloat($("#lcrate").val());   
                if(isNaN(lccharge)){
                     lccharge = 0;
                }
                
                if(rate > 0){
                    //var lcrate = rate + 0.15;
                    var lcrate = rate + lccharge; 
                    var sgst = lcrate * 0.09;
                    sgst = roundToTwo(sgst);
                    $("#sgst").val(sgst);
                    var cgst = lcrate * 0.09;
                    cgst = roundToTwo(cgst);
                    $("#cgst").val(cgst);
                    var totalrate = parseFloat(lcrate) + parseFloat(sgst) + parseFloat(cgst);
                    //var totalrate = lcrate + sgst + cgst;
                    totalrate = roundToTwo(totalrate);
                    $("#totalrate").val(totalrate);
                    var value = qt * totalrate;
                    //alert(value);
                    $("#value").val(roundToTwo(value));
                }

                if(kgperpc > 0 && qt > 0 && length > 0){
                    var piece = qt / ((length / 1000) * kgperpc);
                    piece = Math.round(piece);
                    $("#pieces").val(piece);
                }
            }
//
//    function deletePOItem(itemid){ 
//      var r = confirm("Are you sure you want to delete this item");
//         if(r){ 
//          var remarks = $('#remarks').val();  
//          var ajaxURL = "ajax/deletePOitem.php?itemid=" + itemid;  
//              //alert(ajaxURL);
//              $.ajax({
//              url:ajaxURL,
//                  dataType: 'json',qty
//                  success:function(data){
//                      //alert(data.error);
//                      if (data.error == "1") {
//                          alert(data.msg);
//                      } else {
//                          alert("PO item deleted successfully.")
//                          window.location.href = "po/additem/poid="+<?php echo $this->poid;?>;  
//                      }
//                  }
//              });
//        }
//    }
//
    function roundToThree(num) {    
      return +(Math.round(num + "e+3")  + "e-3");
    }

    function roundToFour(num) {    
      return +(Math.round(num + "e+4")  + "e-4");
    } 
            

    function selectStockItem(id){
 // alert(id);
        var arr = id.split("::");
        var stockid = arr[0];
        var length = arr[2];
        var fromlocid = $("#frmlocid").val();
        var fromloctype = $("#frmloctype").val();
        var ajaxURL = "ajax/getStockItemValue.php?stockcurrid=" + stockid+"&fromlocid="+fromlocid+"&fromloctype="+fromloctype;  
        $.ajax({
        url:ajaxURL,
            dataType: 'json',
            success:function(data){
                if (data.error == "1") { 
                    alert(data.msg);
                } else {
                    $("#availableqty").val(data.availableqty);  
                    $("#length").val(length);
                    $("#batchcode").val(data.batchcode);
                }
            }
        });
    }
    
    function selectBatchCode(stockcurrid){ 
        var arr = stockcurrid.split("::");
        var stockid = arr[0];
        var prodid = arr[1];
        var fromlocid = $("#frmlocid").val();
        var fromloctype = $("#frmloctype").val();
        /*var ajaxURL = "ajax/getGRNItemValue.php?prodid=" + prodid +"&grnid="+grnid; */
        var ajaxURL = "ajax/getBatchcodeByProduct.php?stockcurrid=" + prodid+"&fromlocid="+fromlocid+"&fromloctype="+fromloctype; 
        $.ajax({
        url:ajaxURL,
            //dataType: 'json',
            cache: false,
            success:function(html){
                //alert(html);
                    $('#batchcodes').append(html); 
                    $('#batchcodes1').append(html);
                    $("#batchcodes").selectpicker('refresh');
                    $("#batchcodes1").selectpicker('refresh'); 
                    $("#qty").val(arr[3]);
                    
//                     $(document).on('change', '.selectpicker', function () {
//                       $('.selectpicker').selectpicker('refresh'); 
//                     });
            }
        });
    }
    
    function checkqty(){ 
        var prodsel = $("#transferitem").val();

        var length = parseFloat($("#length").val()); 
        var arr = prodsel.split("::"); 
        var prodid = arr[1];
        var kgperpc = parseFloat(arr[2]);
        var enterqty = parseFloat($("#qty2").val());
        var availableqty = parseFloat($("#availableqty").val());
        if(kgperpc > 0 && enterqty > 0 && length > 0){
            var piece = (enterqty*1000) / ((length / 1000) * kgperpc); 
            piece = Math.round(piece);
            $("#pieces2").val(piece);
        }
        if(enterqty > availableqty){
            alert("Please enter qty less than available qty");
            $("#pieces2").val("");
            $("#qty2").val("");
            return false;
        }else{ 
            return true;
        }
    }
    
    function checkpieces(){
        var prodsel = $("#transferitem").val();
        var length = parseFloat($("#length").val()); 
        var arr = prodsel.split("::"); 
        var prodid = arr[1];
        var kgperpc = parseFloat(arr[2]);
        var pieces = parseFloat($("#pieces2").val());
        var availablepcs = parseFloat($("#availablepcs").val());
        if(pieces > availablepcs ){
            alert("Please enter pieces less than available pieces"); 
            $("#pieces2").val("");
            $("#qty2").val("");
        }
        var availableqty = parseFloat($("#availableqty").val()); 
        if(kgperpc > 0 && pieces > 0 && length > 0){
            
            var qty = pieces * length * (kgperpc/1000);  
             qty = roundToTwo(qty);
            $("#qty2").val(qty);
        }
        var calculatedqty = parseFloat($("#qty2").val());
        if(calculatedqty > availableqty){
            alert("Please enter qty less than available qty"); 
            $("#pieces2").val("");
            $("#qty2").val("");
            return false;
        }else{ 
            return true;
        }
    }
    
    function receiveinKg(){
    var checkBox1 = document.getElementById("check1");
    var div1 = document.getElementById("checkbox1");
        if (checkBox1.checked == true){
            div1.style.display = "block";
        }else{
            div1.style.display = "none";
        }
    }
    
    function receiveinPieces(){
    var checkBox2 = document.getElementById("check2");
    var div2 = document.getElementById("checkbox2");
        if (checkBox2.checked == true){
            div2.style.display = "block";
        }else{
            div2.style.display = "none";
        }
    }
    
    function roundToTwo(num) {    
      return +(Math.round(num + "e+2")  + "e-2"); 
    }
    

        </script>
        <link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
    }

    public function pageContent() {
        $menuitem = "stocktransfer"; //pagecode
        include "sidemenu." . $this->currStore->usertype . ".php";
        $formResult = $this->getFormResult();
        $dbl = new DBLogic();
        $obj_states = $dbl->getStates();

        $obj_categories = $dbl->getAllActiveCategories();
        $obj_products = $dbl->getAllActiveProducts();
        $obj_colors = $dbl->getAllColors();
        $obj_GstPer = $dbl->getAllGSTPer();
        $obj_transports = $dbl->getAllTransports();
        $obj_brands = $dbl->getAllBrands();
        $obj_manufacturers = $dbl->getAllManufacturers();
        $obj_suppliers = $dbl->getAllActiveSuppliers();
        $obj_specifications = $dbl->getAllActiveSpecifications();
        $obj_sttrns = $dbl->getStockTransferDetails($this->transferid);
        $obj_challan = $dbl->getChallanInfo($this->transferid);
        $obj_stockitems = $dbl->getStockTransferItems($this->transferid);
        $obj_challanitems = $dbl->getChallanItems($obj_challan->id);
        $obj_stockcurrent = $dbl->getStockcurrentDetails($obj_sttrns->from_location_id,$obj_sttrns->from_location_type);
        //print_r($obj_stockcurrent);
   
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Stock Transfer Details</h2>
                            <div class="common-content-block">
                                <input type="hidden" name="transferid" id="transferid" value="<?php echo $this->transferid; ?>"/>
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <label>Stock Transfer No : <?php echo $obj_sttrns->transferno; ?></label>
                                        </div>
                                         <div class="col-md-3">
                                            <label>Stock Transfer Date : <?php echo $obj_sttrns->transferdate; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>From Location : <?php echo $obj_sttrns->fromloc; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>To Location : <?php echo $obj_sttrns->toloc; ?></label>
                                        </div>
                                    </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Challan Details</h2>
                            <div class="common-content-block">
                                <input type="hidden" name="transferid" id="transferid" value="<?php echo $this->transferid; ?>"/>
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <label>Challan No : <?php echo $obj_challan->challan_no; ?></label>
                                        </div>
                                         <div class="col-md-6">
                                            <label>Challan Date : <?php echo $obj_challan->ctime; ?></label>
                                        </div>
                                    </div>
                                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Add Challan Items</h2>
                            <div class="common-content-block">
                                <form role="form" id="challanadditem" name="challanadditem" enctype="multipart/form-data" method="post" action="formpost/challanadditem.php">
                               <input type="hidden" name="transferid" id="transferid" value="<?php echo $this->transferid; ?>"/>
                                        <input type="hidden" name="frmlocid" id="frmlocid" value="<?php echo $obj_sttrns->from_location_id; ?>"/>
                                         <input type="hidden" name="challanid" id="challanid" value="<?php echo $obj_challan->id; ?>"/>
                                        <input type="hidden" name="length" id="length" value=""/>
                                         <input type="hidden" name="availableqty" id="availableqty" value=""/>
                                           <input type="hidden" name="batchcode" id="batchcode" value=""/>
                                        <input type="hidden" name="frmloctype" id="frmloctype" value="<?php echo $obj_sttrns->from_location_type; ?>"/>
                                            <div class="col-md-12">
                                                <label>Select Item</label>
                                                <select id="transferitem" name="transferitem" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="selectBatchCode(this.value);">
                                                    <option value="-1">Select Item</option>
                                                    <?php
                                                    foreach ($obj_stockitems as $item) {
                                                        $desc1 = isset($item->desc_1) && trim($item->desc_1) != "" ? " , ".$item->desc_1." mm" : "";
                                                        $desc2 = isset($item->desc_2) && trim($item->desc_2) != "" ? " x ".$item->desc_2." mm" : "";
                                                        $thickness = isset($item->thickness) && trim($item->thickness) != "" ? " , ".$item->thickness." mm" : "";
                                                        $itemname = $item->prod.$desc1.$desc2.$thickness;
                                                        ?>
                                                        <option value="<?php echo $item->id . "::" .$item->prodid ."::".$item->kg_per_pc."::".$item->qty; ?>"><?php echo $itemname; ?></option>
                                                    <?php } ?>
                                                </select>
                                                
                                            </div>
                                        <div class="col-md-12"><br/>
                                        <div class="col-md-4" id="checkbox1">
                                                <label> Required Qty</label>
                                               <input type="text" id="qty" name="qty" class="form-control" placeholder="Qty"  value="<?php echo $this->getFieldValue("qty"); ?>" onkeyup="" readonly autocomplete="off"> 
                                        </div> 
                                        </div>
                                         <div class="col-md-6"><br/>
                                                <label>Select Batch Code</label><br>  
                                                <select id="batchcodes1" name="batchcodes1"  class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="selectStockItem(this.value);">
                                                    <option value = "selected">Select Batch Code :</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3"><br/>
                                                <label>Enter Batch Qty</label><br>
                                                <input type="text" id="qty2" name="qty2" class="form-control" placeholder="Qty (MT)"onkeyup="checkqty(this.value);" value="<?php echo $this->getFieldValue("qty2"); ?>">
                                            </div>
                                            <div class="col-md-3"><br/>
                                                <label>Enter number of pieces</label><br>
                                                <input type="text" id="pieces2" name="pieces2" class="form-control" readonly placeholder="no of pieces" value="<?php echo $this->getFieldValue("pieces2"); ?>"  autocomplete="off">
                                            </div> 
                                            
                                        <div class="col-md-12">
                                            <br>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary">Add Item</button>
                                            </div>
                                        </div>
                                         <?php //if ($formResult->form_id == 'createdc') {  ?>
                                        <div class="alert" style="display:<?php echo $formResult->showhide; ?>;"<?php echo $formResult->status; ?>>
                                            <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h4> <?php echo $formResult->status; ?>
                                        </div>
                                            <?php //}  ?>
                                    </form>                                 
                                 
                                <div class="col-md-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td>Sl.No.</td>
                                                <td>Product</td>
                                                <td>Batchcode</td>
                                                <td>Qty (MT)</td>
                                        
<!--                                                <td>Action</td>-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $srno = 1;
                                            $tot_qty = 0;
                                            $itemCount = 0;
                                            setlocale(LC_MONETARY,"en_IN");
                                            $total_value = 0;
                                            if ($obj_challanitems != NULL) {
                                                foreach ($obj_challanitems as $item) {
                                                    $itemCount++;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $srno; ?></td>
                                                        <td><?php
                                                             $desc1 = isset($item->desc_1) && trim($item->desc_1) != "" ? " , ".$item->desc_1." mm" : "";
                                                             $desc2 = isset($item->desc_2) && trim($item->desc_2) != "" ? " x ".$item->desc_2." mm" : "";
                                                             $thickness = isset($item->thickness) && trim($item->thickness) != "" ? " , ".$item->thickness." mm" : "";
                                                             $itemname = $item->prod.$desc1.$desc2.$thickness;
                                                               echo $itemname;
                                                            ?>
                                                        </td>
                                                        <td><?php echo $item->batchcode; ?></td>
                                                        <td><?php echo $item->qty; ?></td>
                                                        <?php $itemtotalval =  $item->qty *1;?>
                                                        
<!--                                                        <td><input class="btn btn-primary" type="button" name="deleteItem" id="deleteItem" value="Delete" onclick="deletePOItem(<?php echo $item->id; ?>);" /></td>-->
                                                    </tr>
                                                            <?php
                                                            $srno = $srno + 1;
                                                            $tot_qty = $tot_qty + $item->qty;
                                                            
                                                }
                                            }
                                            ?>
                                        </tbody> 
                                        <tfoot>
                                            <tr>
                                                <td>Total</td>
                                                <td></td>
                                                <td></td>
                                                <td><?php echo $tot_qty; ?></td>
                                                <td></td>
                                               
<!--                                                <td></td>-->
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                               <div class="col-md-12">
                                   <form role="form" id="submitpo" name="submitpo" enctype="multipart/form-data" method="post" action="formpost/submitChallan.php">
                                       <input type="hidden" name="itemcount" id="itemcount" value="<?php echo $itemCount; ?>"/>
                                       <input type="hidden" name="transferid" id="transferid" value="<?php echo $this->transferid; ?>"/>
                                       <input type="hidden" name="challanid" id="challanid" value="<?php echo $obj_challan->id; ?>"/>
                                        
                                        <div class="col-md-6">
                                            <label>E-Way Bill No :</label>
                                            <input type="text" id="ewaybill" name="ewaybill" class="form-control" placeholder="E-Way Bill No"  value="<?php echo $this->getFieldValue("qty"); ?>" onkeyup="" autocomplete="off" required>
                                        </div>
                                         <div class="col-md-6">
                                            <label>Vehicle No : </label>
                                            <input type="text" id="vehicleno" name="vehicleno" class="form-control" placeholder="Vehicle No"  value="<?php echo $this->getFieldValue("qty"); ?>" onkeyup="" autocomplete="off" required>
                                        </div>                                       
                                        <div class="col-md-12">
                                            <label>&nbsp;</label><br/>
                                            <button type="submit" class="btn btn-primary">Save and Submit</button>
                                        </div>
<!--                                        <input type="hidden" name="pid" id="pid" value="<?php echo $this->poid; ?>">-->

                                    </form>
                                </div> 
    
                            </div>
                        </div>
                    </div> <!--Add Items>   
                </div>
            </div> 
        </div><!-- end -->
<!--
               <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>      -->


        <?php
        // }else{ print "You are not authorized to access this page";}
    }

}
?>


