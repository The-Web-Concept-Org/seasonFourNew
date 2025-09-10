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
                <b class="text-center card-text">Purchase List</b>


              </div>
            </div>

          </div>
          <?php
          // Only show branch filter for admin
          if ($_SESSION['user_role'] == 'admin') {
            $branches = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status = 1");
            $selected_branch_id = $_GET['branch_id'] ?? ''; // admin: default is all branches
            ?>

            <form method="GET" class="form-inline my-3 ml-4">
              <label for="branch_id" class="mr-2">Filter by Branch:</label>
              <select name="branch_id" id="branch_id" class="form-control text-capitalize mr-2"
                onchange="this.form.submit()">
                <option value="">All Branches</option>
                <?php
                while ($b = mysqli_fetch_assoc($branches)) {
                  $selected = ($selected_branch_id == $b['branch_id']) ? 'selected' : '';
                  echo "<option value='{$b['branch_id']}' class='text-capitalize' $selected>{$b['branch_name']}</option>";
                }
                ?>
              </select>
            </form>

            <?php
          }
          ?>

          <div class="card-body">
            <table class="table  dataTable" id="view_purchase_tb">
              <thead>
                <tr>
                  <th class=""> Date</th>
                  <th class="">Bill ID</th>
                  <th class="">Supplier Name</th>
                  <th class="">Comment</th>
                  <th class="">Amount</th>
                  <th class="">Purchase Type</th>
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
                } elseif (!empty($selected_branch_id)) {
                  $branch_filter = "WHERE branch_id = '$selected_branch_id'";
                }

                // Fetch purchases
                $q = mysqli_query($dbc, "SELECT * FROM purchase $branch_filter ORDER BY purchase_id DESC");

                $c = 0;
                while ($r = mysqli_fetch_assoc($q)) {
                  $c++;
                  ?>

                  <tr>
                    <td><?= $r['purchase_date'] ?></td>
                    <td>
                      <?= ($r['payment_type'] == "cash_purchase" ? "SF25-CP-" : "SF-CRP-") . $r['purchase_id'] ?>
                    </td>

                    <td><?= ucfirst($r['client_name']) ?></td>
                    <td class="text-capitalize"><?= $r['purchase_narration'] ?></td>
                    <td><?= $r['grand_total'] ?></td>
                    <td class="text-capitalize"><?= $r['payment_type'] ?></td>
                    <td>
                      <img src="img/uploads/" alt="">
                      <?php if (!empty($r['purchase_file'])): ?>
                        <a href="img/uploads/<?= htmlspecialchars($r['purchase_file']) ?>" target="_blank">
                          <button class="btn btn-admin btn-sm m-1">View File</button>
                        </a>
                      <?php endif; ?>

                    </td>

                    <td class="d-flex">
                      <button type="button" class="btn btn-admin2 btn-sm m-1 d-inline-block view-stock-btn"
                        onclick="getdata(<?= $r['purchase_id'] ?> , 'purchase')" data-toggle="modal" data-target="#view_print_modal">
                        Detail
                      </button>
                      <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin" and $r['payment_type'] == "cash_purchase"): ?>
                        <form action="credit_purchase.php" method="POST">
                          <input type="hidden" name="edit_purchase_id" value="<?= base64_encode($r['purchase_id']) ?>">
                          <button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
                        </form>


                      <?php endif; ?>
                      <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin" and $r['payment_type'] == "credit_purchase"): ?>
                        <form action="credit_purchase.php" method="POST">
                          <input type="hidden" name="edit_purchase_id" value="<?= base64_encode($r['purchase_id']) ?>">
                          <button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
                        </form>


                      <?php endif; ?>
                      <?php if (@$userPrivileges['nav_delete'] == 1 || $fetchedUserRole == "admin"): ?>
                        <a href="#"
                          onclick="deleteAlert('<?= $r['purchase_id'] ?>','purchase','purchase_id','view_purchase_tb')"
                          class="btn btn-danger btn-sm m-1">Delete</a>


                      <?php endif; ?>


                      <a target="_blank" href="print_sale.php?id=<?= $r['purchase_id'] ?>&type=purchase"
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

</script>