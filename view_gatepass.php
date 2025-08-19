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
                                <b class="text-center card-text">Gatepass Out List</b>


                            </div>
                        </div>

                    </div>

                    <div class="card-body">
                        <table class="table  dataTable" id="view_purchase_tb">
                            <thead>
                                <tr>
                                    <th class=""> Date</th>
                                    <th class=""> Gatepass Id</th>
                                    <th class="">From Branch</th>
                                    <th class="">To Branch</th>
                                    <th class="">Comment</th>
                                    <th class="">File</th>
                                    <th class="">Status</th>
                                    <th class="">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $session_branch_id = $_SESSION['branch_id'];
                                $user_role = $_SESSION['user_role'];

                                // Apply branch filter based on role
                                if ($user_role != 'admin') {
                                    $branch_filter = "WHERE from_branch = '$session_branch_id' ";
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
                                        <td>
                                            <img src="img/uploads/" alt="">
                                            <?php if (!empty($r['gatepass_file'])): ?>
                                                <a href="img/uploads/<?= htmlspecialchars($r['gatepass_file']) ?>"
                                                    target="_blank">
                                                    <button class="btn btn-admin btn-sm m-1">View File</button>
                                                </a>
                                            <?php endif; ?>

                                        </td>
                                        <td class="text-capitalize">
                                            <span
                                                class="badge badge-<?= $r['stock_status'] == 'approved' ? 'success' : ($r['stock_status'] == 'rejected' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($r['stock_status']) ?>
                                            </span>
                                        </td>
                                        <td class="d-flex">
                                            <button type="button"
                                                class="btn btn-admin2 btn-sm m-1 d-inline-block view-stock-btn"
                                                onclick="getdata(<?= $r['gatepass_id'] ?>, 'gatepass')" data-toggle="modal"
                                                data-target="#view_print_modal">
                                                Detail
                                            </button>
                                            <?php
                                            if ($r['stock_status'] === "pending") {
                                                if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin" and $r['payment_type'] == "gatepass"): ?>
                                                    <form action="gatepass.php" method="POST">
                                                        <input type="hidden" name="edit_purchase_id"
                                                            value="<?= base64_encode($r['gatepass_id']) ?>">
                                                        <button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
                                                    </form>


                                                <?php endif;
                                                if (@$userPrivileges['nav_delete'] == 1 || $fetchedUserRole == "admin"): ?>
                                                    <a href="#"
                                                        onclick="deleteAlert('<?= $r['gatepass_id'] ?>','gatepass','gatepass_id','view_purchase_tb')"
                                                        class="btn btn-danger btn-sm m-1">Delete</a>


                                                <?php endif;
                                            }
                                            ?>


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