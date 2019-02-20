<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_cr_stock_report extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $cid;
        var $uid;
        var $pid;
        var $sid = -1;
        var $grnstatus = "";
       
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            $this->params = $params;
            $dbl = new DBLogic();
            if(isset($this->params["crid"]) != ""){
                $this->crid = $this->params["crid"];
            }else if($this->currStore->usertype == UserType::RFC){
                 $objcr = $dbl->getCRDetailsByUserId($this->currStore->id);
                 $this->crid = $objcr->id;
            }else{
                $this->crid = 0;
            }
//            else if($this->currStore->usertype == UserType::DC){
//                $this->dcid = $this->currStore->id;
//            }else{
//                $this->dcid = 0;
//            }
                
        }

function extraHeaders() { ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style type="text/css" title="currentStyle">
    /*  @import "js/datatables/media/css/demo_page.css";
      @import "js/datatables/media/css/demo_table.css";*/
      @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
      @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
</style>
<script type="text/javaScript">    
$(function(){ 
    var uploaddate = $("#dates").val();
    var url = "ajax/tb_crstockreport.php?crid="+<?php echo $this->crid;?>+"&uploaddate="+uploaddate;       
    //alert(url);
    oTable = $('#tb_crstockreporttable').dataTable( {     
	"bProcessing": true,  
	"bServerSide": true, 
        "aoColumns": [null,null,null,null,null,null,null,null,null,null], 
	"sAjaxSource": url,
        "aaSorting": [], 
        "iDisplayLength" : 50
    } );
// search on pressing Enter key only
    $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){ 
	if (e.which == 13){                     
		oTable.fnFilter($(this).val(), null, false, true);
	}
    });    
}); 

function getstock(crid){
    //alert("hereeee");
    window.location.href = "cr/stock/report/crid="+crid; 
}

function genAggExcelRep(){
   var uploaddate = $("#dates").val(); 
   var crid = <?php echo $this->crid;?>;
   //alert(uploaddate); 
    //var dtrange = $("#dateselect").val();
    if(crid == 0){
        alert("Please select CR first");
    }else{
        window.location.href="formpost/genAggCRStockSummayExcel.php?crid="+crid+"&uploaddate="+uploaddate;
    }   
}

function genExcelRep(){
   var uploaddate = $("#dates").val(); 
   var crid = <?php echo $this->crid;?>;
   //alert(uploaddate);
    //var dtrange = $("#dateselect").val();
    if(crid == 0){
        alert("Please select CR first");
    }else{
        window.location.href="formpost/genCRStockSummayExcel.php?crid="+crid+"&uploaddate="+uploaddate;
    }   
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "stockreport";
            include "sidemenu.".$this->currStore->usertype.".php";
            $dbl = new DBLogic();
            $obj_cr_master = $dbl->getCRList();
            $obj_dates = $dbl->getPriceApprovalDates($this->crid);
            //$array = StockTransferStatus::getAll();
            //print_r($array);
?>

<div class="container-section">
    <div class="row">
        <?php if($this->currStore->usertype != UserType::RFC) {?>
        <div class="col-md-3">
            <select id="dccode" name="dccode" class="selectpicker form-control" data-show-subtext="true"
                    data-live-search="true" onchange="getstock(this.value);" >
                <option value="-1">Select Consignment Retailer</option>
                  <?php foreach($obj_cr_master as $crmaster){
                     $selected = "";
                    if($crmaster->id == $this->crid){ $selected = "selected"; }?>
                     <option value="<?php echo $crmaster->id;?>" <?php echo $selected;?>"><?php echo $crmaster->crcode;?></option>
                  <?php } ?>
               </select>
        </div>
        <?php }?>
        <div class="col-md-3">
            <select id="dates" name="dates" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" 
                    onchange="statusChange(this.value);">
                <?php foreach($obj_dates as $date){ ?>
                    <option value="<?php echo ($date->applicable_date);?>"><?php echo $date->applicable_date;?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" onclick="genAggExcelRep();">Aggregate Stock Report</button>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" onclick="genExcelRep();">Export to Excel</button>
        </div>
        

    </div>
    
    <br/>
    <div class="row"> 
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;CR Stock Report</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_crstockreporttable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>Category</th>
                                <th>Product</th>
                                <th>Desc1</th>
                                <th>Desc2</th>
                                <th>Thickness</th>
                                <th>HSN</th>
                                <th>Batchcode</th>
                                <th>Qty(MT)</th>
                                <th>Rate(Rs./MT)</th>
                                <th>Value(Rs)</th>
<!--                                <th>Createtime</th>-->
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                             <td colspan="10" class="dataTables_empty">Loading data from server</td>
                         </tr>
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 </div>         
<!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


