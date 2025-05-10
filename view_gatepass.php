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
                                    <b class="text-center card-text">Gatepass List</b>


                                </div>
                            </div>

                        </div>

                        <div class="card-body">
                            <table class="table  dataTable" id="view_purchase_tb">
                                <thead>
                                    <tr>
                                        <th class="text-dark"> Date</th>
                                        <th class="text-dark">From Branch</th>
                                        <th class="text-dark">To Branch</th>
                                        <th class="text-dark">Comment</th>
                                        <th class="text-dark">Type</th>
                                        <th class="text-dark">File</th>
                                        <th class="text-dark">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $branch_filter = "";
                                    $session_branch_id = $_SESSION['branch_id'];

                                    // Check role and apply branch filter
                                    if ($_SESSION['user_role'] != 'admin') {
                                        $session_branch_id = $_SESSION['branch_id'];
                                        $branch_filter = "WHERE to_branch = '$session_branch_id'";
                                    } elseif (!empty($selected_branch_id)) {
                                        $branch_filter = "WHERE to_branch = '$selected_branch_id'";
                                    }

                                    // Fetch purchases
                                    $q = mysqli_query($dbc, "SELECT * FROM gatepass WHERE to_branch = '$session_branch_id' ORDER BY gatepass_date DESC");

                                    $c = 0;
                                    while ($r = mysqli_fetch_assoc($q)) {
                                        $from_branch = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM branch WHERE branch_id = '{$r['from_branch']}'"));
                                        $to_branch = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM branch WHERE branch_id = '{$r['to_branch']}'"));
                                        $c++;
                                    ?>

                                        <tr class="text-capitalize">
                                            <td><?= $r['gatepass_date'] ?></td>
                                            <td><?= $from_branch['branch_name'] ?></td>
                                            <td><?= $to_branch['branch_name'] ?></td>
                                            <td class="text-capitalize"><?= $r['gatepass_narration'] ?></td>
                                            <td class="text-capitalize"><?= $r['payment_type'] ?></td>
                                            <td>
                                                <img src="img/uploads/" alt="">
                                                <?php if (!empty($r['gatepass_file'])): ?>
                                                    <a href="img/uploads/<?= htmlspecialchars($r['gatepass_file']) ?>" target="_blank">
                                                        <button class="btn btn-admin btn-sm m-1">View File</button>
                                                    </a>
                                                <?php endif; ?>

                                            </td>

                                            <td class="d-flex">

                                                <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin" and $r['payment_type'] == "gatepass"): ?>
                                                    <form action="gatepass.php" method="POST">
                                                        <input type="hidden" name="edit_purchase_id" value="<?= base64_encode($r['gatepass_id']) ?>">
                                                        <button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
                                                    </form>


                                                <?php endif; ?>
                                                <?php if (@$userPrivileges['nav_delete'] == 1 || $fetchedUserRole == "admin"): ?>
                                                    <a href="#" onclick="deleteAlert('<?= $r['gatepass_id'] ?>','gatepass','gatepass_id','view_purchase_tb')" class="btn btn-danger btn-sm m-1">Delete</a>


                                                <?php endif; ?>


                                                <a target="_blank" href="print_sale.php?id=<?= $r['gatepass_id'] ?>&type=gatepass" class="btn btn-admin2 btn-sm m-1">Print</a>
                                                <?php if ($r['stock_status'] != '1'): ?>
                                                    <a href="#" onclick="approveAlert('<?= $r['gatepass_id'] ?>')" class="btn btn-danger btn-sm m-1">Approve</a>
                                                <?php endif; ?>
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

    </script>