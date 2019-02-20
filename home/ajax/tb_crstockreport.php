<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
$aColumns = array('category','product','desc1','desc2','Thickness','HSN','batchcode','qty','price','value');
$sColumns = array('c.name','p.name','p.desc1','p.desc2','p.thickness','p.hsncode','s.batchcode','s.qty','price','value');


/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$crid = isset($_GET['crid']) ? $_GET['crid'] : false;
$uploaddate = isset($_GET['uploaddate']) ? $_GET['uploaddate'] : false;
//error_log("\nMSL stockquery: ".$dcid."\n",3,"tmp.txt");
/* 
 * Paging
 */
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
{
	$sLimit = " LIMIT ".$db->getConnection()->real_escape_string( $_GET['iDisplayStart'] ).", ".
		$db->getConnection()->real_escape_string( $_GET['iDisplayLength'] );
}


/*
 * Ordering
 */
$sOrder = " order by s.createtime desc ";
if ( isset( $_GET['iSortCol_0'] ) )
{
	$sOrder = " ORDER BY  ";
	for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
	{
		if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
		{
			$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
			 	".$db->getConnection()->real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
		}
	}
	
	$sOrder = substr_replace( $sOrder, "", -2 );
	if ( $sOrder == " ORDER BY " )
	{
		$sOrder = "";
	}
}


/* 
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */

$sWhere = "";
if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
{
	$sWhere = "WHERE (";
	for ( $i=0 ; $i<count($sColumns) ; $i++ )
	{
		$sWhere .= $sColumns[$i]." LIKE '%".$db->getConnection()->real_escape_string( $_GET['sSearch'] )."%' OR ";
	}
	$sWhere = substr_replace( $sWhere, "", -3 );
	$sWhere .= ')';
}

/* Individual column filtering */
for ( $i=0 ; $i<count($sColumns) ; $i++ )
{
	if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && isset($_GET['sSearch_'.$i]) && $_GET['sSearch_'.$i] != '' )
	{
		if ( $sWhere == "" )
		{
			$sWhere = "WHERE ";
		}
		else
		{
			$sWhere .= " AND ";
		}
		$sWhere .= $sColumns[$i]." LIKE '%".$db->getConnection()->real_escape_string($_GET['sSearch_'.$i])."%' ";
	}
}

/*
 * SQL queries
 * Get data to display
 */
// select l.name,b.bin,p.name,s.qty from it_stock_current s ,it_locations l ,it_bins b ,it_products p  where l.id= s.createdat_locationid and b.id =s.bin_id and p.id= s.product_id;
//if($sWhere==""){
//    $sWhere .= " where ";
//}else{
//    $sWhere .= " and ";
//}

//$dtClause= "";
//if(trim($purin_dt)!=""  && trim($purin_dt)!="select" && trim($purin_dt)!="Select Date" && trim($purin_dt) != null){
//    $dt = yymmdd($purin_dt);
//    $purin_dt_db = $db->safe($dt);
//    $dtClause .= " and  p.purin_dt = $purin_dt_db ";
//}else{
//    $srtdt= $db->safe(date("Y-m-01"));
//    $enddt= $db->safe(date("Y-m-d"));
//    $dtClause .= " and  p.purin_dt between $srtdt and $enddt";
//}

//////select p.pur_in_no,date(p.purin_dt),date(c.createtime),sum(ci.difference) from it_purchase_in p, it_conversions c , it_conversion_items ci where c.purchase_in_id = p.id and ci.conversion_id = c.id group by ci.conversion_id;
//$groupby = " group by ci.conversion_id";    
//$sWhere .= " where p.id = s.prodid and s.batchcode=gl.batchcode and  p.ctg_id = c.id and  s.crid = $crid";
//$sQuery = "select c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,s.batchcode,round(s.qty,2) as qty,gl.totalrate,round(s.qty * gl.totalrate,2) as value,s.createtime from it_products p,it_stockcurr s,it_categories c,it_grnitems gl
//        $sWhere 
//	$sOrder
//	$sLimit
//";
//$sWhere .= " where p.id = s.prodid and s.prodid = pr.product_id and  p.ctg_id = c.id and pr.applicable_date = date(now()) and s.crid = $crid";
//$sQuery = "select c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,s.batchcode,round(s.qty,2) as qty,pr.price as price,round(s.qty * pr.price,2) as value,s.createtime from it_products p,it_stockcurr s,it_categories c,it_product_price pr
//        $sWhere 
//	$sOrder
//	$sLimit
//";
//$sWhere .= " where p.id = s.prodid and s.prodid = pr.product_id and  p.ctg_id = c.id  and pr.applicable_date = date(now()) and s.crid = $crid";
//$sQuery = "select c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,s.batchcode,round(s.qty,2) as qty,pr.price as price,round(s.qty * pr.price,2) as value,s.createtime from it_products p,it_stockcurr s,it_categories c,it_product_price pr
//        $sWhere 
//	$sOrder
//	$sLimit
//";
$sWhere .= " where p.id = s.prodid and s.prodid = pr.product_id and  p.ctg_id = c.id  and pr.applicable_date ='".$uploaddate."'  and s.crid = $crid";
$sQuery = "select SQL_CALC_FOUND_ROWS c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,s.batchcode,round(s.qty,2) as qty,pr.price as price,round(s.qty * pr.price,2) as value,s.createtime from it_products p,it_stockcurr s,it_categories c,it_product_price pr
        $sWhere 
	$sOrder
	$sLimit
";
//echo $sQuery;
//error_log("\nMSL crstockquery: ".$sQuery."\n",3,"tmp.txt");
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;
//$aColumns = array('category','product','desc1','desc2','Thickness','HSN','batchcode','qty','createtime');
$rows = array(); $iTotal=0;
foreach ($objs as $obj)
{      
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            if ($aColumns[$i] == 'category') {
                //show in ddmmyy
                $row[] = $obj->ctg;
            }else if($aColumns[$i] == 'product'){
                //show in ddmmyy
                $row[] = $obj->name;
            }else if($aColumns[$i] == 'desc1'){
                 $row[] = $obj->desc1;
//                $row[] = $obj->pono; 
            }else if($aColumns[$i] == 'desc2'){
               $row[] = $obj->desc2;
            }else if($aColumns[$i] == 'Thickness'){
               $row[] = $obj->thickness;
            }else if($aColumns[$i] == 'HSN'){
               $row[] = $obj->hsncode;
            }else if($aColumns[$i] == 'batchcode'){
               $row[] = $obj->batchcode;
            }else if($aColumns[$i] == 'qty'){
               $row[] = $obj->qty;
            }else if($aColumns[$i] == 'price'){
               $row[] = $obj->price;
            }else if($aColumns[$i] == 'value'){
               $row[] = $obj->value;
            }
//            else if($aColumns[$i] == 'createtime'){
//               $row[] = $obj->createtime;
//            }   
	}
	$rows[] = $row;
	$iTotal++;
}

$db->closeConnection();
/*
 * Output
 */
$output = array(
	//"sEcho" => intval($_GET['sEcho']),
	"iTotalRecords" => $iTotal,
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => $rows
);

echo json_encode( $output );
?>
