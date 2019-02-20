<?php
$menu = array(
    "Masters" => array(
        "products" => array("Products", "products"),
        "prodprice" => array("Product Pricing","product/pricing"),
        //"productprice1" => array("Product Price Change","cr/product/price"),
       // "price" => array("price","price"),
        //"suppliers" => array("Suppliers", "suppliers"),
        //"dc" => array("Distribution Center", "distribution/center"),
        "Transport" => array("Transports", "transports"),
        //"rfc" => array("Retail Franchisee", "retail/franchisee"),
       // "tax" => array("Tax", "tax"),
       // "users" => array("Users", "users"),
       // "customers" => array("Customers", "customers"),
        //"transporters" => array("Transporters", "transporters"),
      //  "batch" => array("Batch", "batch"),
      //  "price" => array("Product Price", "product/price")
    ),
    "Reports" => array(
        "crstockreport" => array("Stock Report (CR)","cr/stock/report"),
        "crsalesreport" => array("Sales Report (CR)","cr/sales/report"),
        //"po" => array("Purchase Order","po")
/*        "poapprove" => array("Approve Purchase Order","po/awaiting/approvals"),
        "pocancel" => array("Cancel Purchase Order","po/awaiting/cancel"),*/
        
    ),
    "Transactions" => array(
       "CR sales Details" => array("CR Sales", "cr/sales/details"),
        "stocktransfer" => array("Stock Transfer","stocktransfer"),
        "CR stock Details" => array("CR stocks", "cr/itemstock/details")
    ),
    "Manage Settings" => array(
        // "users" => array("Users", "user"),
        //"userperm" => array ("User Permissions","user/permissions"),
        "settings" => array("Change Password", "user/settings")      
    )
);
// $menu['Dashboard'] = array(
//     "dash" => array("Reports", DEF_SITEURL."DashbordReport/AdminLTE-2.1.1/pages/sales/sales.php")
// );
?>

<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
     <?php foreach ($menu as $menuheading => $submenu) { ?>
     <li class="treeview">
      <a href="#">
        <i class="fa fa-dashboard"></i> <span><?php echo $menuheading; ?></span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
       <?php
       foreach ($submenu as $menukey => $menudetail) {
        if ($menukey == $menuitem) {
          $selected = 'class="active"';
        } else {
          $selected = "";
        }
        if($menukey == "dash"){
          ?>
          <li <?php echo $selected; ?> ><a href="<?php echo $menudetail[1]; ?>" target="_blank"><i class="fa fa-circle-o"></i> <?php echo $menudetail[0]; ?></a></li>
          <?php
        }else{
         ?>
         <li <?php echo $selected; ?> ><a href="<?php echo $menudetail[1]; ?>"><i class="fa fa-circle-o"></i> <?php echo $menudetail[0]; ?></a></li>
         <?php 
       }
     } ?>
   </ul>
 </li>
 <?php } ?>
</ul>
</section>
<!-- /.sidebar -->
</aside>
  <section id="page-content-wrapper" class="main-content">
  <!--<div class="sub-navigation">-->
<!--                    <ul>
                        <li>PO Tracking</li>                        
                    </ul>-->
                <!--</div>-->