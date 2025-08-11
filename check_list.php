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
                <b class="text-center card-text">Check List</b>


              </div>
            </div>

          </div>
          <div class="card-body">
            <table class="table  dataTable" id="check_tb">
              <thead>
                <tr>
                  <th class="text-dark">#</th>
                  <th class="text-dark">From Account</th>
                  <th class="text-dark">Bank</th>
                  <th class="text-dark">Check No.</th>
                  <th class="text-dark">Amount</th>
                  <th class="text-dark">Voucher Type</th>
                  <th class="text-dark">Check Date</th>
                  <th class="text-dark">Check Status</th>
                  <th class="text-dark">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php $q = mysqli_query($dbc, "SELECT * FROM checks ORDER BY check_status ASC ");
                $c = 0;
                while ($r = mysqli_fetch_assoc($q)) {
                  $c++;


                  @$vouchers = fetchRecord($dbc, "vouchers", "voucher_id", $r['voucher_id']);
                  @$customer_id1 = fetchRecord($dbc, "customers", "customer_id", @$vouchers['customer_id1'])['customer_name'];
                  @$customer_id2 = fetchRecord($dbc, "customers", "customer_id", @$vouchers['customer_id2'])['customer_name'];
                  @$username = fetchRecord($dbc, "users", "user_id", @$vouchers['addby_user_id'])['username'];



                  ?>
                  <tr>
                    <td><?= $c ?></td>
                    <td class="text-capitalize"><?= $customer_id1 ?></td>
                    <td class="text-capitalize"><?= $r['check_bank_name'] ?></td>
                    <td><?= $r['check_no'] ?></td>
                    <td><?= @$vouchers['voucher_amount'] ?></td>
                    <td class="text-capitalize"><?= $r['check_type'] ?></td>
                    <td><?= $r['check_expiry_date'] ?></td>
                    <td>
                      <?php if ($r['check_status'] == 1) { ?>
                        <span class="badge badge-success">Passed</span>

                      <?php } elseif ($r['check_status'] == 3) { ?>
                        <span class="badge badge-danger">Failed</span>
                      <?php } else { ?>
                        <span class="badge badge-info">Pending</span>

                      <?php } ?>
                    </td>
                    <td>
                      <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin"): ?>
                        <form action="voucher.php" method="POST">
                          <input type="hidden" name="id" value="<?= base64_encode($r['voucher_id']) ?>">
                          <input type="hidden" name="act" value="<?= $vouchers['voucher_group'] ?>">
                          <button type="submit" class="btn m-1 btn-admin btn-sm">Edit</button>
                        </form>


                      <?php endif ?>
                      <a target="_blank"
                        href="print_voucher_custom.php?voucher_id= <?= base64_encode($r['voucher_id']) ?>"
                        class="btn btn-primary btn-sm m-1">Print</a>
                      <?php //if (@$userPrivileges['nav_delete']==1 || $fetchedUserRole=="admin"): ?>

                      <button type="button" onclick="setCheckStatus(<?= $r['check_id'] ?>)"
                        class="btn btn-admin2 btn-sm m-1">Paid</button>
                      <?php //  endif ?>
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