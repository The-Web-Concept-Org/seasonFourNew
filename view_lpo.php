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
                <b class="text-center card-text">LPO List</b>


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
            <table class="table  dataTable" id="view_purchase_tb">
              <thead>
                <tr>
                  <th class="text-dark"> Date</th>
                  <th class="text-dark">Supplier Name</th>
                  <th class="text-dark">Phone</th>
                  <th class="text-dark">Comment</th>
                  <th class="text-dark">Amount</th>
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
                $q = mysqli_query($dbc, "SELECT * FROM lpo $branch_filter ORDER BY lpo_date DESC");


                $c = 0;
                while ($r = mysqli_fetch_assoc($q)) {
                  $c++;
                  ?>
                  <tr class="text-capitalize">
                    <td><?= $r['lpo_date'] ?></td>
                    <td><?= ucfirst($r['client_name']) ?></td>
                    <td><?= $r['client_contact'] ?></td>

                    <td class="text-capitalize"><?= $r['lpo_narration'] ?></td>
                    <td><?= $r['grand_total'] ?></td>
                    <td>
                      <img src="img/uploads/" alt="">
                      <?php if (!empty($r['lpo_file'])): ?>
                        <a href="img/uploads/<?= htmlspecialchars($r['lpo_file']) ?>" target="_blank">
                          <button class="btn btn-admin btn-sm m-1">View File</button>
                        </a>
                      <?php endif; ?>

                    </td>


                    <td class="d-flex">
                      <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin" and $r['payment_type'] == "cash_purchase"): ?>
                        <form action="cash_purchase.php" method="POST">
                          <input type="hidden" name="edit_purchase_id" value="<?= base64_encode($r['lpo_id']) ?>">
                          <button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
                        </form>


                      <?php endif; ?>
                      <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin" and $r['payment_type'] == "lpo"): ?>
                        <form action="lpo.php" method="POST">
                          <input type="hidden" name="edit_purchase_id" value="<?= base64_encode($r['lpo_id']) ?>">
                          <button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
                        </form>


                      <?php endif; ?>
                      <?php if (@$userPrivileges['nav_delete'] == 1 || $fetchedUserRole == "admin"): ?>
                        <a href="#" onclick="deleteAlert('<?= $r['lpo_id'] ?>','lpo','lpo_id','view_purchase_tb')"
                          class="btn btn-danger btn-sm m-1">Delete</a>


                      <?php endif; ?>


                      <a target="_blank" href="print_sale.php?id=<?= $r['lpo_id'] ?>&type=lpo"
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

</body>

</html>
<?php include_once 'includes/foot.php'; ?>