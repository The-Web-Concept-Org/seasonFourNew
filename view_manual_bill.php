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
                <b class="text-center card-text">Manual Bill List</b>


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
          <div class="card-body">
            <table class="table  dataTable" id="view_orders_tb">
              <thead>
                <tr>
                  <th class="text-dark"> Date</th>
                  <th class="text-dark">Bill Id</th>
                  <th class="text-dark">Customer Name</th>
                  <th class="text-dark">Amount</th>
                  <th class="text-dark">Comment</th>
                  <th class="text-dark">Type</th>
                  <!-- <th class="text-dark">File</th> -->
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
                $q = mysqli_query($dbc, "SELECT * FROM manual_bill $branch_filter ORDER BY order_id DESC");


                $c = 0;
                while ($r = mysqli_fetch_assoc($q)) {
                  $c++;
                  ?>



                  <tr>
                    <td><?= $r['timestamp'] ?></td>
                    <td>SF25-CI-<?= $r['order_id'] ?></td>
                    <td><?= ucfirst($r['customer_name']) ?></td>
                    <td><?= $r['grand_total'] ?></td>
                    <td class="text-capitalize"><?= $r['order_narration'] ?></td>
                    <td class="text-capitalize"><?= $r['type'] ?></td>



                    <td class="d-flex">

                      <form action="manual_bill.php" method="POST">
                        <input type="hidden" name="edit_order_id" value="<?= base64_encode($r['order_id']) ?>">
                        <button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
                      </form>

                      <form class="delete-form" data-id="<?= $r['order_id'] ?>">
                        <button type="button" class="btn btn-admin btn-sm m-1 delete-btn">Delete</button>
                      </form>

                      <a target="_blank" href="print_sale.php?type=manualbill&id=<?= $r['order_id'] ?>"
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
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script>
  $(document).ready(function () {
    $(document).on('click', '.delete-btn', function () {
      const button = $(this);
      const form = button.closest('.delete-form');
      const orderId = form.data('id');

      Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.post('php_action/custom_action.php', {
            edit_order_id: orderId,
            delete_manualbill: true
          }, function (response) {
            if (response.trim() === "Deleted successfully.") {
              Swal.fire('Deleted!', 'Record has been deleted.', 'success');
              // Remove the row visually
              form.closest('tr').remove();
            } else {
              Swal.fire('Error!', response, 'error');
              console.error("Delete error: ", response);
            }
          }).fail(function (xhr, status, error) {
            Swal.fire('Error!', 'AJAX request failed.', 'error');
            console.error("AJAX failed: ", error);
          });
        }
      });
    });
  });
</script>