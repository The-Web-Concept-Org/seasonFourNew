<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php'; ?>
<style type="text/css">
  th {
    font-size: 18px;

  }
</style>

<body class="horizontal light  ">
  <div class="wrapper">
    <?php include_once 'includes/header.php'; ?>
    <main role="main" class="main-content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header card-bg" align="center">

            <div class="row">
              <div class="col-12 mx-auto h4">
                <h2 class="text-center card-text">Analytics</h2>


              </div>
            </div>

          </div>
          <div class="card-body">


            <?php
            $selected_branch_id = null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['branch_id'])) {
              // Admin selected a branch via form
              $selected_branch_id = (int) $_POST['branch_id'];
            } elseif ($_SESSION['user_role'] === 'admin') {
              // First page load for admin → get first active branch
              $branch_query = mysqli_query($dbc, "SELECT branch_id FROM branch WHERE branch_status = 1 ORDER BY branch_id ASC LIMIT 1");
              if ($branch_row = mysqli_fetch_assoc($branch_query)) {
                $selected_branch_id = (int) $branch_row['branch_id'];
              }
            } else {
              // Non-admin user → use their assigned branch
              $selected_branch_id = (int) $_SESSION['branch_id'];
            }
            ?>





            <form action="" method="post" class="">


              <div class="row d-print-none ">
                <?php if ($_SESSION['user_role'] == 'admin') { ?>
                  <div class="form-group col-sm-3">
                    <label for="">Branch</label>
                    <select name="branch_id" id="branch_id" onchange="fetchAccounts(this.value)"
                      class="form-control text-capitalize" required>
                      <?php
                      $branch = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status = 1");
                      while ($row = mysqli_fetch_array($branch)) { ?>
                        <option class="text-capitalize" value="<?= $row['branch_id'] ?>"
                          <?= ($row['branch_id'] == $selected_branch_id ? 'selected' : '') ?>>
                          <?= $row['branch_name'] ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>
                <?php } ?>
                <!-- <div class="form-group col-sm-2"></div>group -->
                <div class="form-group col-sm-3 ">



                  <label for="">From Date</label>



                  <input type="date" name="from_date" class="form-control">







                </div>
                <div class="form-group col-sm-3 ">



                  <label for="">To Date</label>



                  <input type="date" name="to_date" class="form-control">







                </div>





                <div class="form-group col-sm-3 d-print-none">



                  <br />



                  <button class="mt-2 btn btn-admin float-right" name="genealledger" type="submit">Search</button>
                  <button class="mt-2 btn btn-admin2 float-right" onclick="window.print();"
                    style="margin-right: 15px;">Print Report</button>


                </div><!-- group -->



              </div>



            </form>
            <hr>



            <?php
            // Get branch filter if available
            // $branch_filter_orders = '';
            // $branch_filter_purchase = '';
            // $branch_filter_vouchers = '';
            
            // if (isset($_SESSION['branch_id']) && !empty($_SESSION['branch_id'])) {
            //   $branch_id = (int) $_SESSION['branch_id'];
            //   $branch_filter_orders = " AND orders.branch_id = $branch_id";
            //   $branch_filter_purchase = " AND purchase.branch_id = $branch_id";
            //   $branch_filter_vouchers = " AND vouchers.branch_id = $branch_id";
            // }
            
            $branch_filter_orders = " AND orders.branch_id = $selected_branch_id";
            $branch_filter_orders_return = " AND orders_return.branch_id = $selected_branch_id";
            $branch_filter_purchase = " AND purchase.branch_id = $selected_branch_id";
            $branch_filter_purchase_return = " AND purchase_return.branch_id = $selected_branch_id";
            $branch_filter_vouchers = " AND vouchers.branch_id = $selected_branch_id";
            $branch_filter_inventory = "branch_id = $selected_branch_id";



            // From date and to date both present
            if (!empty($_REQUEST['from_date']) && !empty($_REQUEST['to_date'])) {
              $from = $_REQUEST['from_date'];
              $to = $_REQUEST['to_date'];

              $sales = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS total_order, SUM(grand_total) AS total_sales 
     FROM orders 
     WHERE order_date BETWEEN '$from' AND '$to' $branch_filter_orders"
              ));

              $sales_return = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS total_order_return, SUM(grand_total) AS total_sales_return 
     FROM orders_return 
     WHERE order_date BETWEEN '$from' AND '$to' $branch_filter_orders_return"
              ));

              $salesGet = mysqli_query(
                $dbc,
                "SELECT * FROM orders 
     WHERE order_date BETWEEN '$from' AND '$to' $branch_filter_orders"
              );

              $purchases = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS total_purchase, SUM(grand_total) AS total_amount 
     FROM purchase 
     WHERE purchase_date BETWEEN '$from' AND '$to' $branch_filter_purchase"
              ));

              $purchases_return = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS total_purchase_return, SUM(grand_total) AS total_amount_return 
     FROM purchase_return 
     WHERE purchase_date BETWEEN '$from' AND '$to' $branch_filter_purchase_return"
              ));

              $purchases_items = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT SUM(quantity) AS total_items, SUM(quantity*rate) AS total_rate 
     FROM purchase_item 
     INNER JOIN purchase ON purchase.purchase_id = purchase_item.purchase_id 
     WHERE purchase_date BETWEEN '$from' AND '$to' $branch_filter_purchase"
              ));

              $sales_items = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT SUM(quantity) AS total_items, SUM(quantity*rate) AS total_rate 
     FROM order_item 
     INNER JOIN orders ON orders.order_id = order_item.order_id 
     WHERE order_date BETWEEN '$from' AND '$to' $branch_filter_orders"
              ));

              $instock = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT SUM(quantity_instock) AS total_stock 
     FROM inventory 
     WHERE 1 AND $branch_filter_inventory"
              ));

              $expense = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT SUM(voucher_amount) AS total_amount 
     FROM vouchers  
     WHERE voucher_group='expense_voucher' 
     AND voucher_date BETWEEN '$from' AND '$to' $branch_filter_vouchers"
              ));

              $cash_in_hand_sale = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS cash_in_hand, SUM(grand_total) AS cash_in_hand_amount 
     FROM orders 
     WHERE order_date BETWEEN '$from' AND '$to' 
     AND payment_type='cash' $branch_filter_orders"
              ));

              $credit_sale = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS credit_sale, SUM(grand_total) AS credit_sale_amount 
     FROM orders 
     WHERE order_date BETWEEN '$from' AND '$to' 
     AND payment_type='credit' $branch_filter_orders"
              ));
            }

            // Only from_date present
            else if (!empty($_REQUEST['from_date']) && empty($_REQUEST['to_date'])) {
              $from = $_REQUEST['from_date'];

              $sales = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS total_order, SUM(grand_total) AS total_sales 
     FROM orders 
     WHERE order_date = '$from' $branch_filter_orders"
              ));

              $sales_return = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS total_order_return, SUM(grand_total) AS total_sales_return 
     FROM orders_return 
     WHERE order_date = '$from' $branch_filter_orders_return"
              ));

              $salesGet = mysqli_query(
                $dbc,
                "SELECT * FROM orders 
     WHERE order_date = '$from' $branch_filter_orders"
              );

              $purchases = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS total_purchase, SUM(grand_total) AS total_amount 
     FROM purchase 
     WHERE purchase_date = '$from' $branch_filter_purchase"
              ));

              $purchases_return = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS total_purchase_return, SUM(grand_total) AS total_amount_return 
     FROM purchase_return 
     WHERE purchase_date = '$from' $branch_filter_purchase_return"
              ));

              $purchases_items = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT SUM(quantity) AS total_items, SUM(quantity*rate) AS total_rate 
     FROM purchase_item 
     INNER JOIN purchase ON purchase.purchase_id = purchase_item.purchase_id 
     WHERE purchase_date = '$from' $branch_filter_purchase"
              ));

              $sales_items = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT SUM(quantity) AS total_items, SUM(quantity*rate) AS total_rate 
     FROM order_item 
     INNER JOIN orders ON orders.order_id = order_item.order_id 
     WHERE order_date = '$from' $branch_filter_orders"
              ));

              $instock = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT SUM(quantity_instock) AS total_stock 
     FROM inventory 
     WHERE 1 AND $branch_filter_inventory"
              ));

              $expense = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT SUM(voucher_amount) AS total_amount 
     FROM vouchers  
     WHERE voucher_group='expense_voucher' 
     AND voucher_date = '$from' $branch_filter_vouchers"
              ));

              $cash_in_hand_sale = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS cash_in_hand, SUM(grand_total) AS cash_in_hand_amount 
     FROM orders 
     WHERE order_date = '$from' 
     AND payment_type='cash' $branch_filter_orders"
              ));

              $credit_sale = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS credit_sale, SUM(grand_total) AS credit_sale_amount 
     FROM orders 
     WHERE order_date = '$from' 
     AND payment_type='credit' $branch_filter_orders"
              ));
            }

            // No date filters
            else {

              $sales = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) AS total_order, order_id, SUM(grand_total) AS total_sales FROM orders WHERE 1 $branch_filter_orders"));
              $sales_return = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) AS total_order_return, order_id, SUM(grand_total) AS total_sales_return FROM orders_return WHERE 1 $branch_filter_orders_return"));
              $salesGet = mysqli_query($dbc, "SELECT * FROM orders WHERE 1 $branch_filter_orders");

              $purchases = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) AS total_purchase, SUM(grand_total) AS total_amount FROM purchase WHERE 1 $branch_filter_purchase"));
              $purchases_return = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) AS total_purchase_return, SUM(grand_total) AS total_amount_return FROM purchase_return WHERE 1 $branch_filter_purchase_return"));

              $purchases_items = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT SUM(quantity) AS total_items, SUM(quantity*rate) AS total_rate, purchase.*, purchase_item.* 
                                  FROM purchase_item 
                                  INNER JOIN purchase ON purchase.purchase_id = purchase_item.purchase_id 
                                  WHERE 1 $branch_filter_purchase"
              ));

              $sales_items = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT SUM(quantity) AS total_items, SUM(quantity*rate) AS total_rate, orders.*, order_item.* 
                              FROM order_item 
                              INNER JOIN orders ON orders.order_id = order_item.order_id 
                              WHERE 1 $branch_filter_orders"
              ));

              $instock = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(quantity_instock) AS total_stock FROM inventory WHERE 1 AND $branch_filter_inventory"));
              // $instock = $purchases_items['total_items'] - $sales_items['total_items'];
              // $total_rate = $purchases_items['total_rate'] - $sales_items['total_rate'];
            
              $expense = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(voucher_amount) AS total_amount FROM vouchers WHERE voucher_group='expense_voucher' $branch_filter_vouchers"));

              $cash_in_hand_sale = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS cash_in_hand, SUM(grand_total) AS cash_in_hand_amount 
                                    FROM orders 
                                    WHERE payment_type='cash' $branch_filter_orders"
              ));

              $credit_sale = mysqli_fetch_array(mysqli_query(
                $dbc,
                "SELECT COUNT(*) AS credit_sale, SUM(grand_total) AS credit_sale_amount 
                              FROM orders 
                              WHERE payment_type='credit' $branch_filter_orders"
              ));
            }
            $total_revenue = $sales['total_sales'] - $sales_return['total_sales_return'];
            // Final computed value
            $total_expense = !empty($expense['total_amount']) ? abs($expense['total_amount']) : 0;
            ?>


            <?php
            $total_stock_value = 0;

            // Fetch all inventory rows per product for the branch
            $inventory_query = mysqli_query($dbc, "SELECT product_id, SUM(quantity_instock) as total_instock 
                                                                  FROM inventory 
                                                                  WHERE  $branch_filter_inventory 
                                                                  GROUP BY product_id
                                                                   ");

            while ($inventory = mysqli_fetch_assoc($inventory_query)) {
              $product_id = $inventory['product_id'];
              $quantity = $inventory['total_instock'];

              // Now get the latest purchase rate for this product
              $rate_query = mysqli_query($dbc, "SELECT rate FROM purchase_item WHERE product_id = '$product_id' AND $branch_filter_inventory ORDER BY purchase_item_id DESC LIMIT 1");
              $rate_row = mysqli_fetch_assoc($rate_query);
              $purchase_rate = $rate_row['rate'] ?? 0;
              $total_stock_value += $quantity * $purchase_rate;
            }

            $net_profit = 0;

            // 1. Calculate profit from actual sales
            while ($fetchOrder = mysqli_fetch_assoc($salesGet)) {
              $order_id = $fetchOrder['order_id'];
              $sql = "SELECT * FROM order_item WHERE order_id = '$order_id' AND order_item_status = 1";
              $query = $dbc->query($sql);

              while ($result = $query->fetch_assoc()) {
                $product_id = $result['product_id'];
                $sold_quantity = $result['quantity'];
                $sold_rate = $result['rate'];

                // Get latest purchase rate for the product
                $purchase_sql = "SELECT rate FROM purchase_item WHERE product_id = '$product_id' ORDER BY purchase_item_id DESC LIMIT 1";
                $purchase_result = mysqli_fetch_assoc(mysqli_query($dbc, $purchase_sql));
                $purchase_rate = $purchase_result['rate'] ?? 0;

                $sold_income = $sold_quantity * $sold_rate;
                $purchase_cost = $sold_quantity * $purchase_rate;

                $net_profit += ($sold_income - $purchase_cost);
              }
            }

            // 2. Subtract losses from returned orders
            $return_loss = 0;

            $return_orders = mysqli_query(
              $dbc,
              "SELECT * FROM orders_return WHERE 1 $branch_filter_orders_return"
            );

            while ($return = mysqli_fetch_assoc($return_orders)) {
              $return_order_id = $return['order_id'];
              $return_items = mysqli_query($dbc, "SELECT * FROM order_return_item WHERE order_id = '$return_order_id' AND order_item_status = 1");

              while ($item = mysqli_fetch_assoc($return_items)) {
                $product_id = $item['product_id'];
                $return_qty = $item['quantity'];
                $return_rate = $item['rate'];

                // Get latest purchase rate again
                $purchase_sql = "SELECT rate FROM purchase_item WHERE product_id = '$product_id' ORDER BY purchase_item_id DESC LIMIT 1";
                $purchase_result = mysqli_fetch_assoc(mysqli_query($dbc, $purchase_sql));
                $purchase_rate = $purchase_result['rate'] ?? 0;

                $return_income = $return_qty * $return_rate;
                $return_cost = $return_qty * $purchase_rate;

                $return_loss += ($return_income - $return_cost);
              }
            }

            // 3. Final Net Profit
            $final_net_profit = $net_profit - $return_loss;
            ?>



            <div class="row">
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-primary text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-shopping-cart text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Total Orders</p>
                        <span class="h3 mb-0 text-white">
                          <?= isset($sales['total_order']) ? abs($sales['total_order']) : "0"; ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-secondary text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-shopping-bag text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Total Purchase</p>
                        <span class="h3 mb-0 text-white">
                          <?= isset($purchases['total_purchase']) ? abs($purchases['total_purchase']) : "0"; ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-primary text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-dollar-sign text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Total Sales</p>
                        <span class="h3 mb-0 text-white">
                          <?= isset($sales['total_sales']) ? abs($sales['total_sales']) : "0"; ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-secondary text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-dollar-sign text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Total Purchase</p>
                        <span class="h3 mb-0 text-white">
                          <?= $pur = isset($purchases['total_amount']) ? abs($purchases['total_amount']) : "0";
                          ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-secondary text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-shopping-cart text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Total Orders Return</p>
                        <span class="h3 mb-0 text-white">
                          <?= isset($sales_return['total_order_return']) ? abs($sales_return['total_order_return']) : "0"; ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-primary text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-shopping-bag text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Total Purchase Return</p>
                        <span class="h3 mb-0 text-white">
                          <?= isset($purchases_return['total_purchase_return']) ? abs($purchases_return['total_purchase_return']) : "0"; ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-secondary text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-dollar-sign text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Total Sales Return</p>
                        <span class="h3 mb-0 text-white">
                          <?= isset($sales_return['total_sales_return']) ? abs($sales_return['total_sales_return']) : "0"; ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-primary text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-dollar-sign text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Total Purchase Return</p>
                        <span class="h3 mb-0 text-white">
                          <?= $pur = isset($purchases_return['total_amount_return']) ? abs($purchases_return['total_amount_return']) : "0";
                          ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>

              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-success text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-plus-square text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Purchased Quantity</p>
                        <span class="h3 mb-0 text-white">

                          <?= $pur = isset($purchases_items['total_items']) ? abs($purchases_items['total_items']) : "0";
                          ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-info text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-minus-square text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Saled Quantity</p>
                        <span class="h3 mb-0 text-white">
                          <?= isset($sales_items['total_items']) ? abs($sales_items['total_items']) : "0"; ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-success text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-package text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">inStock Quantity</p>
                        <span class="h3 mb-0 text-white">
                          <?php
                          echo abs(@$instock['total_stock']);
                          ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-info text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-dollar-sign text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">inStock Amount</p>
                        <span class="h3 mb-0 text-white">
                          <?php

                          echo $total_rate = isset($total_stock_value) ? abs(@$total_stock_value) : "0";
                          ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-dark text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-dollar-sign text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Total Revenue</p>
                        <span class="h3 mb-0 text-white">
                          <?php
                          echo $revenue = isset($total_revenue) ? abs($total_revenue) : "0";
                          ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-warning text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-dollar-sign text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Total Income</p>
                        <span class="h3 mb-0 text-white">
                          <?= $final_net_profit ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-dark text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-dollar-sign text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Total Expense</p>
                        <span class="h3 mb-0 text-white">
                          <?= $total_expense;
                          ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-warning text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-dollar-sign text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Total Profit</p>
                        <span class="h3 mb-0 text-white">
                          <?= $final_net_profit - $total_expense;
                          ?>
                        </span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>


            </div>
            <hr>
            <div class="row">
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-warning text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-shopping-cart text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Cash in Hand (no.)</p>
                        <span class="h3 mb-0 text-white"><?= @(int) $cash_in_hand_sale['cash_in_hand'] ?></span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-dark text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-dollar-sign text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Cash in Hand (pkr)</p>
                        <span class="h3 mb-0 text-white"><?= @(int) $cash_in_hand_sale['cash_in_hand_amount'] ?></span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-warning text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-shopping-cart text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Credit Sale (no.)</p>
                        <span class="h3 mb-0 text-white"><?= @(int) $credit_sale['credit_sale'] ?></span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---------------------------end of box------------------------------------------------------>
              <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow bg-dark text-white border-0">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-3 text-center">
                        <span class="circle circle-sm bg-white">
                          <i class="fe fe-16 fe-dollar-sign text-default mb-0"></i>
                        </span>
                      </div>
                      <div class="col pr-0">
                        <p class="small text-white mb-0">Credit Sale (pkr)</p>
                        <span class="h3 mb-0 text-white"><?= @(int) $credit_sale['credit_sale_amount'] ?></span>
                        <!--   <span class="small text-white">+5.5%</span> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div> <!-- end of row -->
            <hr>
            <div class="row mt-2">
              <div class="col-sm-12">
                <h2 class="text-center">Bank Balance</h2>
              </div>
            </div>
            <div class="row">
              <div class="table col-sm-12 text-center">
                <table width="100%">
                  <thead>
                    <th class="" width="33.3%">Sr No.</th>
                    <th class="" width="33.3%">Account Details</th>
                    <th class="" width="33.3%">Balance</th>
                  </thead>
                  <tbody>
                    <?php
                    $branch_filter = $selected_branch_id ? " AND branch_id = $selected_branch_id" : "";

                    $q = mysqli_query($dbc, "SELECT * FROM customers WHERE customer_type='bank' AND customer_status=1 $branch_filter");
                    $c = 0;

                    while ($r = mysqli_fetch_assoc($q)):
                      $c++;
                      ?>
                      <tr>
                        <td><?= $c ?></td>
                        <td><?= $r['customer_name'] ?></td>
                        <td><?= getcustomerBlance($dbc, $r['customer_id']) ?></td>
                      </tr>
                    <?php endwhile; ?>


                  </tbody>
                </table>
              </div>
            </div>
            <script>



              $(function () {



                var dateFormat = "yy-mm-dd";



                from = $("#from")



                  .datepicker({



                    changeMonth: true,



                    numberOfMonths: 1,



                    dateFormat: "yy-mm-dd",



                  })



                  .on("change", function () {



                    to.datepicker("option", "minDate", getDate(this));



                  }),



                  to = $("#to").datepicker({



                    changeMonth: true,



                    numberOfMonths: 1,



                    dateFormat: "yy-mm-dd",



                  })



                    .on("change", function () {



                      from.datepicker("option", "maxDate", getDate(this));



                    });







                function getDate(element) {



                  var date;



                  try {



                    date = $.datepicker.parseDate(dateFormat, element.value);



                  } catch (error) {



                    date = null;



                  }







                  return date;



                }



              });



            </script>
          </div>
        </div> <!-- .row -->
      </div> <!-- .container-fluid -->

    </main> <!-- main -->
  </div> <!-- .wrapper -->

</body>

</html>
<?php include_once 'includes/foot.php'; ?>


<style type="text/css">
  @media print {
    .h3 {
      font-size: 40px !important;
      overflow: hidden !important;
      color: black !important;
    }

    .small {
      font-size: 30px !important;
      color: black !important;
    }

    .table {
      font-size: 30px !important;
      text-align: center !important;
      width: 100% !important;
    }

  }
</style>