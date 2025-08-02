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
                <b class="text-center card-text">Quotation List</b>


              </div>
            </div>

          </div>
          <div class="card-body">
            <?php
            $branches = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status= 1");
            $selected_branch_id = $_GET['branch_id'] ?? $_SESSION['branch_id'];
            if ($_SESSION['user_role'] == 'admin') {
              ?>

              <form method="GET" class="form-inline my-3 ml-4">
                <label for="branch_id" class="mr-2">Filter by Branch:</label>
                <select name="branch_id" id="branch_id" class="form-control text-capitalize mr-2"
                  onchange="this.form.submit()">
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
            <table class="table  dataTable" id="view_orders_tb">

              <thead>
                <tr>
                  <th class="text-dark">Date</th>
                  <th class="text-dark">Quotation Id</th>
                  <th class="text-dark">Customer Name</th>
                  <th class="text-dark">Amount</th>
                  <th class="text-dark">Comment</th>
                  <th class="text-dark">Type</th>
                  <th class="text-dark">File</th>
                  <th class="text-dark">Status</th>
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
                $q = mysqli_query($dbc, "SELECT * FROM quotations $branch_filter ORDER BY quotation_id DESC");


                $c = 0;
                while ($r = mysqli_fetch_assoc($q)) {
                  $c++;
                  ?>
                  <tr class="text-capitalize">
                    <td><?= $r['quotation_date'] ?></td>
                    <td>SF25-Q-<?= $r['quotation_id'] ?></td>
                    <td><?= ucfirst($r['client_name']) ?></td>
                    <td><?= $r['total_amount'] ?></td>
                    <td class="text-capitalize"><?= $r['quotation_narration'] ?></td>
                    <td class="text-capitalize">
                      <?php if ($r['is_delivery_note'] == 1) {
                        echo "Delivery Note";
                      } else {
                        echo "Quotation";
                      } ?>
                    </td>
                    <td>
                      <?php if (!empty($r['quotation_file'])): ?>
                        <a href="img/uploads/<?= htmlspecialchars($r['quotation_file']) ?>" target="_blank">
                          <button class="btn btn-admin btn-sm m-1">View File</button>
                        </a>
                      <?php endif; ?>

                    </td>
                    <td>
                      <?php if (($r['is_delivery_note'] == 1)) {
                        if ($r['payment_status'] == 1): ?>
                          <span class="bg-success btn p-1 rounded text-dark">Paid</span>
                        <?php else: ?>
                          <span class="bg-danger p-1 rounded text-white">Unpaid</span>
                        <?php endif;
                      } ?>
                    </td>
                    <td class="d-flex">
                      <button type="button" class="btn btn-admin2 btn-sm m-1 d-inline-block view-stock-btn"
                        onclick="getdata(<?= $r['quotation_id'] ?> , 'quotation')" data-toggle="modal"
                        data-target="#view_print_modal">
                        Detail
                      </button>
                      <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin" and $r['payment_type'] == "quotation"): ?>
                        <form action="quotation.php" method="POST">

                          <input type="hidden" name="edit_order_id" value="<?= base64_encode($r['quotation_id']) ?>">
                          <input type="hidden" name="credit_type" value="quotation">

                          <?php if ($r['payment_status'] != 1): ?>
                            <button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
                          <?php endif; ?>
                        </form>
                      <?php endif; ?>
                      <form action="quotation.php" method="POST">

                        <input type="hidden" name="edit_order_id" value="<?= base64_encode($r['quotation_id']) ?>">
                        <input type="hidden" name="credit_type" value="quotation">
                        <input type="hidden" name="paid_status" value="pending">


                        <?php if ($r['is_delivery_note'] == 1): ?>
                          <button type="submit" class="btn btn-primary btn-sm m-1">Pay</button>
                        <?php endif; ?>
                      </form>
                      <?php if (@$userPrivileges['nav_delete'] == 1 || $fetchedUserRole == "admin"): ?>
                        <a href="#"
                          onclick="deleteAlert('<?= $r['quotation_id'] ?>','quotations','quotation_id','view_orders_tb')"
                          class="btn btn-danger btn-sm m-1">Delete</a>


                      <?php endif; ?>


                      <a target="_blank" href="print_sale.php?type=quotation&id=<?= $r['quotation_id'] ?>"
                        class="btn btn-admin2 btn-sm m-1">Print</a>
                    </td>
                  </tr>
                <?php } ?>
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