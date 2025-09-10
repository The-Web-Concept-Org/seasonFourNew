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
                  <th class=""> Date</th>
                  <th class="">Bill ID</th>
                  <th class="">Customer Name</th>
                  <th class="">Amount</th>
                  <th class="">Comment</th>
                  <th class="">Sale Type</th>
                  <th class="">File</th>
                  <th class="">Action</th>
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
                      <button type="button" class="btn btn-admin2 btn-sm m-1 d-inline-block view-stock-btn"
                        onclick="getdata(<?= $r['order_id'] ?> , 'order_return')" data-toggle="modal" data-target="#view_print_modal">
                        Detail
                      </button>
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
<div class="modal fade" id="view_print_modal" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="defaultModalLabel">Detail</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div id="stock_detail_content">Loading...</div>
        </div>
        <div class="modal-footer">
          <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-dark float-right"
            id="formData_btn">Close</button>

        </div>

      </div>
    </div>
  </div>
</body>

</html>
<?php include_once 'includes/foot.php'; ?>