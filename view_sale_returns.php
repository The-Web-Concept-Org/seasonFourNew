<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php'; ?>

<body class="horizontal light  ">
  <div class="wrapper">
    <?php include_once 'includes/header.php'; ?>
    <main role="main" class="main-content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header card-bg" align="center">

            <div class="row">
              <div class="col-12 mx-auto h4">
                <b class="text-center card-text">Order Returns List</b>


              </div>
            </div>

          </div>
          <?php
          $branches = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status= 1");
          $selected_branch_id = $_GET['branch_id'] ?? $_SESSION['branch_id'];
          if ($_SESSION['user_role'] == 'admin') {
          ?>

            <form method="GET" class="form-inline my-3 ml-4">
              <label for="branch_id" class="mr-2">Filter by Branch:</label>
              <select name="branch_id" id="branch_id" class="form-control text-capitalize mr-2" onchange="this.form.submit()">
                <option value="">All Branches</option>
                <?php
                $branches = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status = 1");
                while ($b = mysqli_fetch_assoc($branches)) {
                  $selected = ($_GET['branch_id'] ?? '') == $b['branch_id'] ? 'selected' : '';
                  echo "<option value='{$b['branch_id']}' class='text-capitalize' $selected>{$b['branch_name']}</option>";
                }
                ?>
              </select>
            </form>
          <?php } ?>
          <div class="card-body">
            <table class="table  dataTable" id="view_orders_tb">
              <thead>
                <tr>
                  <th class="text-dark"> Date</th>
                  <th class="text-dark">Return Id</th>
                  <th class="text-dark">Customer Name</th>
                  <th class="text-dark">Amount</th>
                  <th class="text-dark">Comment</th>
                  <th class="text-dark">Sale Type</th>
                  <th class="text-dark">File</th>
                  <th class="text-dark">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $branch_filter = "";

                // Check role and apply branch filter
                if ($_SESSION['user_role'] != 'admin') {
                  $session_branch_id = $_SESSION['branch_id'];
                  $branch_filter = "WHERE branch_id = '$session_branch_id'";
                } elseif ($_SESSION['user_role'] == 'admin' && !isset($_GET['branch_id'])) {
                  $branch_filter = "";
                } elseif (!empty($selected_branch_id)) {
                  $branch_filter = "WHERE branch_id = '$selected_branch_id'";
                }

                // Fetch purchases
                $q = mysqli_query($dbc, "SELECT * FROM orders_return $branch_filter ORDER BY order_id DESC");


                $c = 0;
                while ($r = mysqli_fetch_assoc($q)) {
                  $c++;
                ?>

                  <tr>
                    <td><?= $r['order_date'] ?></td>
                    <td>SF25-SR-<?= $r['order_id'] ?></td>
                    <td><?= ucfirst($r['client_name']) ?></td>
                    <td><?= $r['grand_total'] ?></td>
                    <td class="text-capitalize"><?= $r['order_narration'] ?></td>
                    <td class="text-capitalize"><?= $r['payment_type'] ?></td>

                    <td>
                      <?php if (!empty($r['order_file'])): ?>
                        <a href="img/uploads/<?= htmlspecialchars($r['order_file']) ?>" target="_blank">
                          <button class="btn btn-admin btn-sm m-1">View File</button>
                        </a>
                      <?php endif; ?>
                    </td>

                    <td class="d-flex">
                      <?php if (@$get_company['sale_interface'] == "barcode") {
                        $cash_sale_url = "cash_salebarcode.php";
                        $credit_sale_url = "credit_sale.php";
                      } elseif ($get_company['sale_interface'] == "keyboard") {
                        $cash_sale_url = "cash_salegui.php";
                        $credit_sale_url = "credit_sale.php";
                      } else {
                        $cash_sale_url = "cash_sale.php";
                        $credit_sale_url = "credit_sale.php";
                      }
                      ?>
                      <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin" and $r['payment_type'] == "cash"): ?>
                        <form action="sale_return.php" method="POST">
                          <input type="hidden" name="edit_order_id" value="<?= base64_encode($r['order_id']) ?>">
                          <button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
                        </form>


                      <?php endif; ?>
                      <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin" and $r['payment_type'] == "credit"): ?>
                        <form action="sale_return.php" method="POST">

                          <input type="hidden" name="edit_order_id" value="<?= base64_encode($r['order_id']) ?>">
                          <input type="hidden" name="credit_type" value="<?= $r['credit_sale_type'] ?>">



                          <button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
                        </form>


                      <?php endif; ?>
                      <?php if (@$userPrivileges['nav_delete'] == 1 || $fetchedUserRole == "admin"): ?>
                        <a href="#" onclick="deleteAlert('<?= $r['order_id'] ?>','orders_return','order_id','view_orders_tb')" class="btn btn-danger btn-sm m-1">Delete</a>


                      <?php endif; ?>


                      <a target="_blank" href="print_sale.php?type=order_return&id=<?= $r['order_id'] ?>" class="btn btn-admin2 btn-sm m-1">Print</a>
                    </td>
                  </tr>
                <?php  } ?>
              </tbody>
            </table>
          </div>
        </div> <!-- .row -->
      </div> <!-- .container-fluid -->

    </main> <!-- main -->
  </div> <!-- .wrapper -->

</body>

</html>
<?php include_once 'includes/foot.php'; ?>