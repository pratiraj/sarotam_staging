<?php
include '/var/www/weikfield_kiosks/it_config.php';
//include '/home/weikfield/public_html/kiosks/it_config.php';
require_once "lib/core/Constants.php";
require_once "lib/db/DBConn.php";
require_once "session_check.php";
?>


<html>
    <head>
        <meta charset="UTF-8">
        <title>Weikfield Kiosk</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- Bootstrap 3.3.4 -->
        <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- Font Awesome Icons -->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="../../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="../../dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
        <!-- AdminLTE Skins. Choose a skin from the css/skins 
             folder instead of downloading all of them to reduce the load. -->
        <link href="../../dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- daterange picker -->
        <link href="../../plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
        <!-- Bootstrap time Picker -->
        <link href="../../plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet"/>




    </head>
    <body class="skin-green sidebar-mini">
        <div class="wrapper">

            <header class="main-header">
                <!-- Logo -->
                <a href="../../index2.html" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><b>WF</b></span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><b>WeikField</b></span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <span style = "font-size: 25px"><Strong>Chirag</strong></span>
                                </a>
                                <ul class="dropdown-menu">
                                </ul>
                            </li>

                        </ul>
                    </div>
                </nav>
            </header>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->

                <div class="box box-success">
                    <div class="box-header">
                        <section class="content-header">

                            <h2>
                                Item Wise Sale
                            </h2>
                        </section>  
                        <hr style="display: block;margin-top: 0.5em;margin-bottom: 0.5em;margin-left: auto;margin-right: auto;border-style: inset;border-width: 1px;">
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-4" style="margine-left:5em">
                                <label>Select Store:</label>
                                <select id="store" name="store" class="form-control" onchange="loadStoreWise(this.value);" >
                                    <option value = "-1" selected="true">Select Store</option>
                                    <?php
                                    $db = new DBConn();
                                    $store_id = "";
                                    $store_id = $_GET["sid"];
                                    $date = "";
                                    $date = $_GET["date"];
                                    $query = "select id,name from it_stores";
                                    $obj = $db->fetchObjectArray($query);
                                    foreach ($obj as $store) {
                                        if (isset($store_id) && $store_id == $store->id && $store_id != "") {
                                            ?>
                                            <option value = "<?php echo $store->id; ?>" selected="true"><?php echo $store->name; ?></option>
                                        <?php } else {
                                            ?>
                                            <option value = "<?php echo $store->id; ?>"><?php echo $store->name; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>   
                                </select>
                            </div>
                            <div class="col-xs-5" style="margin-left: 2em">
                                <div class="form-group">
                                    <label>Select Date range:</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <?php
                                        if (isset($date) && $date != "") {
                                            ?>

                                            <input type="text" class="form-control pull-right" id="reservation" onchange="loadDateWise(this.value);" value="<?php echo $date; ?>"/>
                                            <?php
                                        } else {
                                            ?>
                                            <input type="text" class="form-control pull-right" id="reservation" onchange="loadDateWise(this.value);"/>
                                            <?php
                                        }
                                        ?>
                                    </div> <!--/.input group -->
                                </div> <!--/.form group   --> 
                            </div>
                        </div>
                    </div> <!--/.box-body -->
                </div> <!--/.box -->
                <!--  Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Menu</th>
                                                <th>Item Name</th>
                                                <th>Price</th>
                                                <th>Unit Sold</th>
                                                <th>Sales Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $total_qty = 0;
                                            $total_value = 0;
                                            if (isset($store_id) && $store_id != "") {
                                                if (isset($date) && $date != "") {
                                                    list($date_1, $date_2) = preg_split("/-/", $date);
                                                    list($mm, $dd, $yy) = preg_split("/\//", $date_1);
                                                    $date_1 = $db->safe($yy . "-" . $mm . "-" . $dd);
                                                    $date_1 = str_replace(' ', '', $date_1);
                                                    list($mm, $dd, $yy) = preg_split("/\//", $date_2);
                                                    $date_2 = $db->safe($yy . "-" . $mm . "-" . $dd);
                                                    $date_2 = str_replace(' ', '', $date_2);
                                                    $query = "select c.name as menu,p.name as item,o.price as price,sum(o.item_qty) as qty, sum(o.price*o.item_qty) as total from it_products p,it_categories c,it_store_products s,it_order_items o where transaction_types&1 = 1 and p.ctg_id = c.id and s.product_id = p.id and s.id = o.product_id and s.store_id = $store_id  and date(o.createtime)  between $date_1 and $date_2 group by o.product_id,o.price;";
                                                } else {
                                                    $query = "select c.name as menu,p.name as item,o.price as price,sum(o.item_qty) as qty, sum(o.price*o.item_qty) as total from it_products p,it_categories c,it_store_products s,it_order_items o where transaction_types&1 = 1 and p.ctg_id = c.id and s.product_id = p.id and s.id = o.product_id and s.store_id = $store_id group by o.product_id,o.price;";
                                                }
                                            } else {
                                                $query = "select c.name as menu,p.name as item,o.price as price,sum(o.item_qty) as qty, sum(o.price*o.item_qty) as total from it_products p,it_categories c,it_store_products s,it_order_items o where transaction_types&1 = 1 and p.ctg_id = c.id and s.product_id = p.id and s.id = o.product_id and s.store_id = -1 group by o.product_id,o.price;";
                                            }
                                            $obj = $db->fetchObjectArray($query);
                                            $count = 0;
                                            foreach ($obj as $user) {
                                                ?>  
                                                <tr>
                                                    <td><?php echo $user->menu; ?></td>
                                                    <td><?php echo $user->item ?></td>
                                                    <td><?php echo $user->price; ?></td>
                                                    <td><?php echo $user->qty; ?></td>
                                                    <td><?php echo round($user->total, 2);?></td>
                                                </tr>
                                                <?php
                                                $total_qty = $total_qty + $user->qty;
                                                $total_value = $total_value + $user->total;
                                                $count ++;
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>Total</th>
                                                <th><?php echo $total_qty; ?></th>
                                                <th><?php echo $total_value; ?></th>
                                            </tr>

                                        </tfoot>
                                    </table>
                                </div> <!--/.box-body -->
                            </div> <!--/.box -->
                        </div> <!--/.col -->
                    </div> <!--/.row -->
                </section> <!--/.content -->
            </div><!-- /.content-wrapper -->


            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <!-- search form -->
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="treeview">
                            <a href="../sales/sales.php">
                                <i class="fa fa-file-text-o"></i> <span>Sales</span> <i class="fa fa-angle-right pull-right"></i>
                            </a>
                        </li>    
                         <li class="treeview">

                            <a href="../sales/sale_item.php">
                                <i class="fa fa-file-text-o"></i><span>Item wise Sales</span> <i class="fa fa-angle-right pull-right"></i>
                            </a>
                        </li>  
                        <li class="treeview">

                            <a href="../sales/expenses.php">
                                <i class="fa fa-file-text-o"></i> <span>Expense</span> <i class="fa fa-angle-right pull-right"></i>
                            </a>
                        </li>  
                        <li class="treeview">

                            <a href="../sales/grn.php">
                                <i class="fa fa-file-text-o"></i> <span>GRN</span> <i class="fa fa-angle-right pull-right"></i>
                            </a>
                        </li>  
                        <li class="treeview">

                            <a href="../sales/wastage.php">
                                <i class="fa fa-file-text-o"></i> <span>Wastage</span> <i class="fa fa-angle-right pull-right"></i>
                            </a>
                        </li>  
                        </li>  
                           <li class="treeview">
                            <a href="../sales/idealVSactual.php">
                                <i class="fa fa-file-text-o"></i> <span>ConSumption Report</span> <i class="fa fa-angle-right pull-right"></i>
                            </a>
                        </li>  
                       
                       
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>



            <footer class="main-footer">
                <strong>Copyright &copy; 2014-2015 <a href="http://onintouch.com">  OnIntouch</a>.</strong> All rights reserved.
            </footer>

            <!-- /.control-sidebar -->
            <!-- Add the sidebar's background. This div must be placed
                 immediately after the control sidebar -->
            <div class='control-sidebar-bg'></div>
        </div><!-- ./wrapper -->

        <!-- jQuery 2.1.4 -->
        <script src="../../plugins/jQuery/jQuery-2.1.4.min.js"></script>
        <!-- Bootstrap 3.3.2 JS -->
        <script src="../../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <!-- DATA TABES SCRIPT -->
        <script src="../../plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="../../plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
        <!-- SlimScroll -->
        <script src="../../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <!-- FastClick -->
        <script src='../../plugins/fastclick/fastclick.min.js'></script>
        <!-- AdminLTE App -->
        <script src="../../dist/js/app.min.js" type="text/javascript"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="../../dist/js/demo.js" type="text/javascript"></script>
        <!-- date-range-picker -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
        <script src="../../plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
        <!-- bootstrap color picker -->
        <script src="../../plugins/colorpicker/bootstrap-colorpicker.min.js" type="text/javascript"></script>
        <!-- bootstrap time picker -->
        <script type="text/javascript">
                                                $(function () {
                                                    $("#example1").dataTable();                 //Date range picker
                                                    $('#reservation').daterangepicker();
                                                    //Date range as a button
                                                    $('#daterange-btn').daterangepicker(
                                                            {
                                                                ranges: {
                                                                    'Today': [moment(), moment()],
                                                                    'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                                                                    'Last 7 Days': [moment().subtract('days', 6), moment()],
                                                                    'Last 30 Days': [moment().subtract('days', 29), moment()],
                                                                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                                                                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                                                                },
                                                                startDate: moment().subtract('days', 29),
                                                                endDate: moment()
                                                            },
                                                    function (start, end) {
                                                        $('#reportrange-span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                                                    }

                                                    );
                                                });


                                                function loadStoreWise(store_id) {
                                                    var date = document.getElementById('reservation').value;
                                                    window.location.href = "?sid=" + store_id + "&date=" + date;

                                                }
                                                function loadDateWise(date) {
                                                    var store = document.getElementById('store').value;
                                                    window.location.href = "?date=" + date + "&sid=" + store;

                                                }
        </script>

    </body>
</html>
