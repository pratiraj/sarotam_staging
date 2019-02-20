<?php
$menu = array(  
    "Manage" => array(                
        // "region" => array("Regions", "regions"),
        "stores" => array("Stores", "stores"),
    ),  
    "Instances" => array(
       "pinst" => arraY("POS Instances", "pos/instances"),
       // "tinst" => arraY("Tagging Instances","tagging/instances")
    ),
//    "Users" => array(
//       "users" => array("Users","users")       
//    ), 
    " Manage Supplier" => array(
        "msupp" => array("Supplier", "supplier"),
        "msuppro" => array("Supplier Product", "supplier/product"),
    ),
    "Sales" => array(
        "sorders" => array("Search Orders", "search/orders"),
        "cust" => array("Customers Master", "customers"),
    ),
    "Purchase" => array(
        "puorders" => array("Purchase Orders", "purchase/orders"),
    ),
    "Reports" => array(
        "cstock" => array("Current Stock", "current/stock"),
        "dsales" => array("Transactions Summary","tran/summary"),
        "fb" => array("Feedback","feedback")
    ),
    "Transaction" => array(
        "tran" => array("Transaction", "transaction"),
       // "ttypes" => array("Transaction Types", "transaction/types"),
    ),
    "Catalog" => array(
        "categories" => array("Categories", "categories"),
        "masterProd" => array("Master Products", "master/products"),
        "storeProd" => array("Store Products", "store/products")
    ),    
//     "Packages" => array(
//        "package" => array("Package","package"),
////        "pprod" => array("Package Product")
//    ),
    "Taxes" => array(
        "taxes" => array("Taxes","taxes")
    ),
    "Payment Types" => array(
        "ptypes" => array("Payment Types","payment/types")
    ),   
//    "Terms & Conditions" => array(
//        "tnc" => array("Terms & Conditions","tncs")
//    ),
    
//    "Wallet" => array(
//        "wallet" => array("Wallet","wallet")
//    ),
//    "Loyalty" => array(
//        "loyalty" => array("Loyalty","loyalty")
//    ),
   
//    "Tickets" => array(
//        "ticket" => array("Ticket", "region")
//    ),
//    "Orders" => array(
//        "active" => array("Active Orders", "active/order"),
//        "pending" => array("Pending Orders", "pending/order"),
//        "Complete" => array("Complete Orders", "complete/order"),
//    ),
//    
    "Manage Settings" => array(
       // "users" => array("Users", "user"),
        //"userperm" => array ("User Permissions","user/permissions"),
        "settings" => array("My Settings", "user/settings")
    )
);
?>
<div class="grid_2">
    <div id="section-menu">
        <ul class="section menu">
<?php foreach ($menu as $menuheading => $submenu) { ?>
                <li>
                    <a class="menuitem"><?php echo $menuheading; ?></a>
                    <ul class="submenu">
    <?php
    foreach ($submenu as $menukey => $menudetail) {
        if ($menukey == $menuitem) {
            $selected = 'class="menuselect"';
        } else {
            $selected = "";
        }
        ?>
                            <li><a <?php echo $selected; ?> href="<?php echo $menudetail[1]; ?>"><?php echo $menudetail[0]; ?></a></li>
                <?php } ?>
                    </ul>
                </li>
<?php } ?>
        </ul>
    </div>
</div>