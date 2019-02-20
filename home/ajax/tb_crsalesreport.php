<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
$aColumns = array('invoice_no','cname','cphone','batchcode','qty','mrp','total','createtime');
$sColumns = array('c.invoice_no','c.cname','c.cphone','cl.batchcode','cl.qty','cl.mrp','cl.tptal','cl.createtime');


/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$crid = isset($_GET['crid']) ? $_GET['crid'] : false;

//$crcode = $crobj->crcode;
//error_log("\nMSL crrcode: ".$crid."\n",3,"tmp.txt");
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
$sOrder = " order by cl.createtime desc ";
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
 * SQL queriespageLength: 10
 * Get data to display
 */
// select l.name,b.bin,p.name,s.qty from it_stock_current s ,it_locations l ,it_bins b ,it_products p  where l.id= s.createdat_locationid and b.id =s.bin_id and p.id= s.product_id;
if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}

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
//error_log("\nMSL salesquery: "."herrererer"."\n",3,"tmp.txt");
$queryy = "select * from it_rfc_master where id = $crid";
//error_log("\nMSL salesquery: ".$queryy."\n",3,"tmp.txt");
$crobj = $db->fetchObject($queryy);
$crcode = $crobj->crcode;
$sWhere .= " c.id = cl.invoice_id and c.status = 1";
$sQuery = "select SQL_CALC_FOUND_ROWS c.invoice_no,c.cname,c.cphone,cl.batchcode,cl.qty,cl.mrp,cl.total as total,cl.createtime from it_".$crcode." c,it_".$crcode."_items cl
        $sWhere 
	$sOrder
	$sLimit
";
//echo $sQuery;
error_log("\nMSL salesquery: ".$sQuery."\n",3,"tmp.txt");
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;
//$aColumns = array('invoice_no','cname','cphone','batchcode','qty','mrp','tptal','createtime');
$rows = array(); $iTotal=0;
foreach ($objs as $obj)
{      
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            if ($aColumns[$i] == 'invoice_no') {
                //show in ddmmyy
                $row[] = $obj->invoice_no;
            }else if($aColumns[$i] == 'cname'){
                //show in ddmmyy
                $row[] = $obj->cname;
            }else if($aColumns[$i] == 'cphone'){
                 $row[] = $obj->cphone;
//                $row[] = $obj->pono; 
            }else if($aColumns[$i] == 'batchcode'){
               $row[] = $obj->batchcode;
            }else if($aColumns[$i] == 'qty'){
               $row[] = $obj->qty;
            }else if($aColumns[$i] == 'mrp'){
               $row[] = $obj->mrp;
            }else if($aColumns[$i] == 'total'){
               $row[] = $obj->total;
            }else if($aColumns[$i] == 'createtime'){
               $row[] = $obj->createtime;
            }   
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
