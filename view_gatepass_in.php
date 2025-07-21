<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php'; ?>

<body class="horizontal light  ">
    <div class="wrapper">
        <?php include_once 'includes/header.php'; ?>
        <?php
        if (isset($_POST['approve_gatepass']) || isset($_POST['reject_gatepass'])) {
            $gatepass_id = $_POST['gatepass_id'];
            $action = isset($_POST['approve_gatepass']) ? 'approved' : 'rejected';

            if ($action == 'approved') {
                // Fetch all items
                $items = mysqli_query($dbc, "SELECT * FROM gatepass_item WHERE gatepass_id='$gatepass_id'");
                while ($item = mysqli_fetch_assoc($items)) {
                    $product_id = $item['product_id'];
                    $qty = $item['quantity'];
                    $from_branch = $item['from_branch'];
                    $to_branch = $item['to_branch'];
                    $user_id = $_SESSION['user_id'];

                    // Deduct from from_branch
                    mysqli_query($dbc, "UPDATE inventory SET quantity_instock = quantity_instock - $qty WHERE product_id='$product_id' AND branch_id='$from_branch'");
                    // Add to to_branch
                    $check_to = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='$product_id' AND branch_id='$to_branch'");
                    if (mysqli_num_rows($check_to)) {
                        mysqli_query($dbc, "UPDATE inventory SET quantity_instock = quantity_instock + $qty WHERE product_id='$product_id' AND branch_id='$to_branch'");
                    } else {
                        mysqli_query($dbc, "INSERT INTO inventory (product_id, branch_id, user_id, quantity_instock) VALUES ('$product_id', '$to_branch', '$user_id', '$qty')");
                    }
                }
            }

            // Update gatepass status
            mysqli_query($dbc, "UPDATE gatepass SET stock_status='$action' WHERE gatepass_id='$gatepass_id'");
            $_SESSION['msg'] = "Gatepass has been $action successfully.";
        }

        ?>
        <main role="main" class="main-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header card-bg" align="center">

                        <div class="row">
                            <div class="col-12 mx-auto h4">
                                <b class="text-center card-text">Gatepass In List</b>


                            </div>
                        </div>

                    </div>

                    <div class="card-body">
                        <table class="table  dataTable" id="view_purchase_tb">
                            <thead>
                                <tr>
                                    <th class="text-dark"> Date</th>
                                    <th class="text-dark"> Gatepass Id</th>
                                    <th class="text-dark">From Branch</th>
                                    <th class="text-dark">To Branch</th>
                                    <th class="text-dark">Comment</th>
                                    <!-- <th class="text-dark">File</th> -->
                                    <th class="text-dark">Status</th>
                                    <th class="text-dark">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $session_branch_id = $_SESSION['branch_id'];
                                $user_role = $_SESSION['user_role'];

                                // Apply branch filter based on role
                                if ($user_role != 'admin') {
                                    $branch_filter = "WHERE to_branch = '$session_branch_id' ";
                                } else {
                                    $branch_filter = ""; // admin sees all
                                }

                                // Fetch filtered gatepass records
                                $q = mysqli_query($dbc, "SELECT * FROM gatepass $branch_filter ORDER BY gatepass_id DESC");

                                $c = 0;
                                while ($r = mysqli_fetch_assoc($q)) {
                                    $from_branch = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM branch WHERE branch_id = '{$r['from_branch']}'"));
                                    $to_branch = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM branch WHERE branch_id = '{$r['to_branch']}'"));
                                    $c++;
                                    ?>

                                    <tr class="text-capitalize">
                                        <td><?= $r['gatepass_date'] ?></td>
                                        <td class="text-capitalize">SF25-G-<?= $r['gatepass_id'] ?></td>
                                        <td><?= $from_branch['branch_name'] ?></td>
                                        <td><?= $to_branch['branch_name'] ?></td>
                                        <td class="text-capitalize"><?= $r['gatepass_narration'] ?></td>

                                        <!-- <td>
                                            <img src="img/uploads/" alt="">
                                            <?php if (!empty($r['gatepass_file'])): ?>
                                                <a href="img/uploads/<?= htmlspecialchars($r['gatepass_file']) ?>"
                                                    target="_blank">
                                                    <button class="btn btn-admin btn-sm m-1">View File</button>
                                                </a>
                                            <?php endif; ?>

                                        </td> -->
                                        <td class="text-capitalize">
                                            <span
                                                class="badge badge-<?= $r['stock_status'] == 'approved' ? 'success' : ($r['stock_status'] == 'rejected' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($r['stock_status']) ?>
                                            </span>
                                        </td>
                                        <td class="d-flex">
                                            <?php if ($r['stock_status'] == 'pending' && $r['to_branch'] == $_SESSION['branch_id']): ?>
                                                <form method="post" action="">
                                                    <input type="hidden" name="gatepass_id" value="<?= $r['gatepass_id'] ?>">
                                                    <button name="approve_gatepass"
                                                        class="btn btn-success btn-sm m-1">Approve</button>
                                                    <button name="reject_gatepass"
                                                        class="btn btn-danger btn-sm m-1">Reject</button>
                                                </form>
                                            <?php endif; ?>
                                            <button type="button"
                                                class="btn btn-admin2 btn-sm m-1 d-inline-block view-stock-btn"
                                                onclick="getdata(<?= $r['gatepass_id'] ?>, 'gatepass')" data-toggle="modal"
                                                data-target="#view_print_modal">
                                                Detail
                                            </button>


                                            <!-- <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin" and $r['payment_type'] == "gatepass"): ?>
                                                <form action="gatepass.php" method="POST">
                                                    <input type="hidden" name="edit_purchase_id"
                                                        value="<?= base64_encode($r['gatepass_id']) ?>">
                                                    <button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
                                                </form>


                                            <?php endif; ?>
                                            <?php if (@$userPrivileges['nav_delete'] == 1 || $fetchedUserRole == "admin"): ?>
                                                <a href="#"
                                                    onclick="deleteAlert('<?= $r['gatepass_id'] ?>','gatepass','gatepass_id','view_purchase_tb')"
                                                    class="btn btn-danger btn-sm m-1">Delete</a>


                                            <?php endif; ?> -->


                                            <a target="_blank"
                                                href="print_sale.php?id=<?= $r['gatepass_id'] ?>&type=gatepass"
                                                class="btn btn-admin2 btn-sm m-1">Print</a>
                                            <!-- <?php if ($r['stock_status'] != '1'): ?>
                                                    <a href="#" onclick="approveAlert('<?= $r['gatepass_id'] ?>')" class="btn btn-danger btn-sm m-1">Approve</a>
                                                <?php endif; ?> -->
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