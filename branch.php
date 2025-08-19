<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php';
//include_once 'includes/code.php';
?>

<body class="horizontal light  ">
    <div class="wrapper">
        <?php include_once 'includes/header.php'; ?>
        <main role="main" class="main-content">
            <div class="container">
                <div class="row ">
                    <div class="col-12">

                        <div class="card ">
                            <div class="card-header card-bg " align="center">
                                <h4 class="card-text"> Manage Branches</h4>
                            </div>
                            <div class="card-body">
                                <form class="form-horizontal" method="POST" action="includes/code.php" id="myForm">
                                    <input type="hidden" name="action" value="add_new_branch">
                                    <input type="hidden" name="new_branch_id" value="<?= @$_REQUEST['branch_edit_id'] ?>">

                                    <div class="form-group row">
                                        <label for="clientContact" class="col-sm-2 control-label">Branch Name</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="branch_name" name="branch_name" placeholder="Name Here..." autocomplete="off" required value="<?= @$fetchusers['branch_name'] ?>" />
                                        </div>
                                        <label for="clientContact" class="col-sm-2 control-label">Branch Email</label>
                                        <div class="col-sm-4">
                                            <input type="email" class="form-control" id="branch_email" name="branch_email" placeholder="Email Here..." autocomplete="off" required value="<?= @$fetchusers['branch_email'] ?>" />
                                        </div>

                                    </div> <!--/form-group-->
                                    <div class="form-group row">
                                        <label for="clientContact" class="col-sm-2 control-label">Phone Number</label>
                                        <div class="col-sm-4">
                                            <input type="number" min="0" class="form-control" id="branch_phone" name="branch_phone" placeholder="Number Here..." autocomplete="off" required value="<?= @$fetchusers['branch_phone'] ?>" />
                                        </div>
                                        <label for="clientContact" class="col-sm-2 control-label">Address</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="branch_address" name="branch_address" placeholder="Address Here" autocomplete="off" required value="<?= @$fetchusers['branch_address'] ?>" />
                                        </div>

                                    </div> <!--/form-group-->



                                    <div class="form-group row">
                                        <label for="clientContact" class="col-sm-2 control-label">Status </label>

                                        <div class="col-sm-4">
                                            <select class="form-control" name="branch_status">
                                                <option <?= @($fetchusers['status'] == "1") ? "seleted" : "" ?> value="1">Active</option>
                                                <option <?= @($fetchusers['status'] == "0") ? "seleted" : "" ?> value="0">Not Active</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 ml-auto">
                                            <?= $users_button; ?>
                                        </div>

                                    </div> <!--/form-group-->

                                </form>
                                <br><br>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12">
                        <div class="card mt-2">
                            <div class="card-header cyan-bgcolor" align="center">
                                <h4>Branch List</h4>
                            </div>
                            <div class="card-body">
                                <table class="table example1" id="myTable">
                                    <thead>
                                        <tr>
                                            <th class="">Sr</th>
                                            <th class="">Branch Name</th>
                                            <th class="">Branch Email</th>
                                            <th class="">Branch Phone</th>
                                            <th class="">Branch Address</th>
                                            <th class="">Branch Status</th>
                                            <th class="">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php



                                        $sql = "SELECT * FROM `branch` WHERE branch_status = '1' ";

                                        $result = mysqli_query($dbc, $sql);

                                        if (mysqli_num_rows($result) > 0) {
                                            $a= 0;
                                            while ($row = mysqli_fetch_array($result)) {
                                                $a++;
                                        ?>
                                                <tr>
                                                    <td><?= $a?></td>
                                                    <td class="text-capitalize"><?= $row['branch_name']; ?></td>
                                                    <td class="text-capitalize"><?= $row['branch_email']; ?></td>
                                                    <!-- <td>Encrypted </td> -->
                                                    <td><?= $row['branch_phone'] ?></td>
                                                    <td class="text-capitalize"><?= $row['branch_address']; ?></td>

                                                    <td>
                                                        <?php
                                                        if ($row['branch_status'] == '1') {
                                                        ?>
                                                            <span class="label label-lg label-info" style="font-size: ">Active</span>
                                                        <?php
                                                            # code...
                                                        } else {
                                                        ?>
                                                            <span class="label label-lg label-danger" style="font-size: "> Not Active</span>
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <!-- <td><?= date('D, d-M-Y', strtotime($row['adddatetime'])); ?> -->


                                                    </td>
                                                    <td class="d-flex">
                                                        <?php if (@$userPrivileges['nav_delete'] == 1 || $fetchedUserRole == "admin"): ?>
                                                            <form action="branch.php" method="POST">
                                                                <input type="hidden" name="branch_del_id" value="<?= $row['branch_id'] ?>">
                                                                <button type="submit" class="btn btn-admin2 btn-sm m-1">Delete</button>
                                                            </form>
                                                        <?php endif ?>

                                                        <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin"): ?>
                                                            <form action="branch.php" method="POST">
                                                                <input type="hidden" name="branch_edit_id" value="<?= $row['branch_id'] ?>">
                                                                <button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
                                                            </form>
                                                        <?php endif ?>
                                                    </td>


                                                </tr>
                                    </tbody>
                            <?php
                                            }
                                        }
                            ?>


                                </table>

                            </div>
                        </div>
                    </div>
                </div> <!-- .row -->
            </div> <!-- .container-fluid -->

        </main> <!-- main -->
    </div> <!-- .wrapper -->

</body>

</html>
<?php include_once 'includes/foot.php'; ?>