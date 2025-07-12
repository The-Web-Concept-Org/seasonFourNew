<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php';

if (!empty($_REQUEST['edit_purchase_id'])) {
    # code...
    $fetchGatepass = fetchRecord($dbc, "gatepass", "gatepass_id", base64_decode($_REQUEST['edit_purchase_id']));
    // print_r($fetchGatepass);
}
?>


<body class="horizontal light  ">
    <div class="wrapper">
        <?php include_once 'includes/header.php'; ?>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header card-bg" align="center">

                    <div class="row">
                        <div class="col-12 mx-auto h4">
                            <b class="text-center card-text">Gatepass Out</b>


                            <!-- <a href="credit_purchase.php" class="btn btn-admin float-right btn-sm">Add New</a> -->
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <form action="php_action/custom_action.php" method="POST" id="sale_order_fm">
                        <input type="hidden" name="product_purchase_id"
                            value="<?= @empty($_REQUEST['edit_purchase_id']) ? "" : base64_decode($_REQUEST['edit_purchase_id']) ?>">
                        <input type="hidden" name="payment_type" id="payment_type" value="credit_sale">
                        <input type="hidden" name="gatepass" id="gatepass" value="gatepass">
                        <input type="hidden" name="price_type" id="price_type" value="sale">
                        <input type="hidden" name="user_id"
                            value="<?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '' ?>">


                        <div class="row form-group">
                            <!-- Gatepass ID -->
                            <div class="col-md-3">
                                <div class="row">
                                    <!-- ID# (approx 50%) -->
                                    <div class="col-6 pr-1">
                                        <label>ID#</label>
                                        <?php
                                        $result = mysqli_query($dbc, "SHOW TABLE STATUS LIKE 'gatepass'");
                                        $data = mysqli_fetch_assoc($result);
                                        $next_increment = $data['Auto_increment'];
                                        ?>
                                        <input type="text" name="next_increment" id="next_increment"
                                            value="SF25-G-<?= empty($_REQUEST['edit_purchase_id']) ? $next_increment : htmlspecialchars($fetchGatepass['gatepass_id']) ?>"
                                            readonly class="form-control">
                                    </div>

                                    <!-- Gatepass Date (approx 50%) -->
                                    <div class="col-6 pl-1">
                                        <label>Gatepass Date</label>
                                        <input type="text" name="gatepass_date" id="gatepass_date"
                                            value="<?= empty($_REQUEST['edit_purchase_id']) ? date('Y-m-d') : htmlspecialchars($fetchGatepass['gatepass_date']) ?>"
                                            readonly class="form-control">
                                    </div>
                                </div>
                            </div>


                            <!-- From Branch -->
                            <div class="col-sm-2">
                                <label for="from_branch" class="control-label">From Branch</label>
                                <select class="form-control searchableSelect" name="from_branch" id="branch_id"
                                    required>
                                    <option selected disabled value="">Select Branch</option>
                                    <?php
                                    $branch = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status = 1");
                                    while ($row = mysqli_fetch_array($branch)) {
                                        $selected = ($fetchGatepass['from_branch'] == $row['branch_id']) ? "selected" : "";
                                        echo '<option ' . $selected . ' value="' . htmlspecialchars($row['branch_id']) . '">' . htmlspecialchars($row['branch_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- To Branch -->
                            <div class="col-sm-2">
                                <label for="to_branch" class="control-label">To Branch</label>
                                <select class="form-control searchableSelect" name="to_branch" id="to_branch" required>
                                    <option selected disabled value="">Select Branch</option>
                                    <?php
                                    $branch = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status = 1");
                                    while ($row = mysqli_fetch_array($branch)) {
                                        $selected = ($fetchGatepass['to_branch'] == $row['branch_id']) ? "selected" : "";
                                        echo '<option ' . $selected . ' value="' . htmlspecialchars($row['branch_id']) . '">' . htmlspecialchars($row['branch_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Narration -->
                            <div class="col-sm-2">
                                <label>Comment</label>
                                <input type="text" autocomplete="off" name="gatepass_narration"
                                    value="<?= htmlspecialchars(@$fetchGatepass['gatepass_narration']) ?>"
                                    class="form-control">
                            </div>

                            <!-- File Upload -->
                            <div class="col-sm-2">
                                <label>Attach File
                                    <?php if (!empty($fetchGatepass['gatepass_file'])): ?>
                                        <a href="img/uploads/<?= htmlspecialchars($fetchGatepass['gatepass_file']) ?>"
                                            target="_blank">
                                            <p type="button" class="d-inline p-0 m-0">View File</p>
                                        </a>
                                    <?php endif; ?>
                                </label>
                                <input type="file" autocomplete="off" class="form-control" name="gatepass_file">
                            </div>
                        </div> <!-- end of form-group -->

                        <div class="form-group row mb-3">
                            <div class="col-4 col-md-2">
                                <label>Product Code</label>
                                <input type="text" autocomplete="off" name="product_code" id="get_product_code"
                                    class="form-control">
                            </div>
                            <div class="col-6 col-md-4">
                                <label>Products</label>
                                <input type="hidden" id="add_pro_type" value="add">
                                <select class="form-control searchableSelect" id="get_product_name" name="product_id">
                                    <option value="">Select Product</option>
                                    <?php
                                    $result = mysqli_query($dbc, "SELECT * FROM product WHERE status=1 ");
                                    while ($row = mysqli_fetch_array($result)) {
                                        $getBrand = fetchRecord($dbc, "brands", "brand_id", $row['brand_id']);
                                        $getCat = fetchRecord($dbc, "categories", "categories_id", $row['category_id']);
                                        ?>

                                        <option data-price="<?= $row["current_rate"] ?>" <?= empty($r['product_id']) ? "" : "selected" ?> value="<?= $row["product_id"] ?>"
                                            style="text-transform: capitalize;">
                                            <?= $getCat["categories_name"] ?> - <?= $row["product_name"] ?> -
                                            <?= @$getBrand["brand_name"] ?>
                                        </option>

                                        <!-- <option data-price="<?= $row["current_rate"] ?>" <?= empty($r['product_id']) ? "" : "selected" ?> value="<?= $row["product_id"] ?>">
                                            <?= $row["product_name"] ?> </option> -->


                                    <?php } ?>
                                </select>
                                <span class="text-center w-100" id="instockQty"></span>
                            </div>
                            <div class="col-1 col-md-1">
                                <label class="invisible d-block">.</label>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                    data-target="#add_product_modal"> <i class="fa fa-plus"></i> </button>
                            </div>
                            <!-- <div class="col-6 col-sm-2 col-md-2">
                                <label>Product Details</label>
                                <input type="text" class="form-control" id="get_product_detail">
                            </div> -->
                            <!-- <div class="col-6 col-sm-1 col-md-1">
                                <label>Final Price</label>
                                <input type="number" 
                                    class="form-control" id="get_product_price">
                            </div> -->
                            <div class="col-6 col-sm-2 col-md-1">
                                <label>Quantity</label>
                                <input type="number" class="form-control" id="get_product_quantity" value="" min="1"
                                    name="quantity">
                            </div>
                            <!-- <div class="col-6 col-sm-1 col-md-1">
                                <label>Amount</label>
                                <input type="number" <?= ($_SESSION['user_role'] == "admin") ? "" : "readonly" ?>
                                    class="form-control" id="get_product_sale_price">
                            </div> -->

                            <div class="col-sm-1">
                                <br>
                                <button type="button" class="btn btn-success btn-sm mt-2 float-right"
                                    id="addProductPurchase">
                                    <span class="btn-text"><i class="fa fa-plus"></i> <b>Add</b></span>
                                    <span class="spinner-border spinner-border-sm text-light ms-2 d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-12">

                                <table class="table  saleTable" id="myDiv">
                                    <thead class="table-bordered">
                                        <tr>
                                            <th class="text-dark">Code</th>
                                            <th class="text-dark">Product Name</th>
                                            <!-- <th class="text-dark">Product Details</th> -->
                                            <th class="text-dark">Unit Price</th>
                                            <th class="text-dark">Quantity</th>
                                            <th class="text-dark" style="width: 18%;">Amount</th>
                                            <th class="text-dark" style="width: 30%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table table-bordered" id="purchase_product_tb">
                                        <?php if (isset($_REQUEST['edit_purchase_id'])):
                                            $q = mysqli_query($dbc, "SELECT  product.*,brands.*,gatepass_item.* FROM gatepass_item INNER JOIN product ON product.product_id=gatepass_item.product_id INNER JOIN brands ON product.brand_id=brands.brand_id   WHERE gatepass_item.gatepass_id='" . base64_decode($_REQUEST['edit_purchase_id']) . "'");

                                            while ($r = mysqli_fetch_assoc($q)) {

                                                ?>
                                                <tr id="product_idN_<?= $r['product_id'] ?>">
                                                    <input type="hidden" data-price="<?= $r['rate'] ?>"
                                                        data-quantity="<?= $r['quantity'] ?>"
                                                        id="product_ids_<?= $r['product_id'] ?>" class="product_ids"
                                                        name="product_ids[]" value="<?= $r['product_id'] ?>">
                                                    <input type="hidden" id="product_quantites_<?= $r['product_id'] ?>"
                                                        name="product_quantites[]" value="<?= $r['quantity'] ?>">
                                                    <input type="hidden" id="product_rate_<?= $r['product_id'] ?>"
                                                        name="product_rates[]" value="<?= $r['rate'] ?>">

                                                    <input type="hidden" id="product_totalrate_<?= $r['product_id'] ?>"
                                                        name="product_totalrates[]" value="<?= $r['rate'] ?>">
                                                    <input type="hidden" id="product_salerate_<?= $r['product_id'] ?>"
                                                        name="product_salerates[]" value="<?= $r['sale_rate'] ?>">
                                                    <td><?= $r['product_code'] ?></td>
                                                    <td><?= $r['product_name'] ?></td>

                                                    <td><?= $r['rate'] ?></td>
                                                    <td><?= $r['quantity'] ?></td>
                                                    <td><?= (float) $r['rate'] * (float) $r['quantity'] ?></?>
                                                    </td>
                                                    <td>

                                                        <button type="button"
                                                            onclick="removeByid(`#product_idN_<?= $r['product_id'] ?>`)"
                                                            class="fa fa-trash text-danger" href="#"></button>
                                                        <button type="button"
                                                            onclick="editByid(<?= $r['product_id'] ?>,`<?= $r['product_code'] ?>`,<?= $r['rate'] ?>,<?= $r['quantity'] ?>)"
                                                            class="fa fa-edit text-success ml-2 "></button>

                                                    </td>
                                                </tr>
                                            <?php }
                                        endif ?>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="table-bordered"></td>

                                            <td class="table-bordered"> Sub Total :</td>
                                            <td class="table-bordered" id="product_total_amount">
                                                <?= @$fetchGatepass['total_amount'] ?>
                                            </td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="table-bordered"></td>
                                            <td class="table-bordered"> Discount :</td>
                                            <td class="table-bordered" id="getDiscount"><input onkeyup="getOrderTotal()"
                                                    type="number" id="ordered_discount"
                                                    class="form-control form-control-sm"
                                                    value="<?= @empty($_REQUEST['edit_order_id']) ? $fetchGatepass['discount'] : "0" ?>"
                                                    min="0" name="ordered_discount">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="table-bordered"></td>
                                            <td class="table-bordered"> <strong>Net Total :</strong> </td>
                                            <td class="table-bordered" id="product_grand_total_amount">
                                                <?= @$fetchGatepass['grand_total'] ?>
                                            </td>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 d-flex justify-content-end">
                                <a href="credit_purchase.php" class="btn btn-dark  pb-2btn-sm">Cancel</a>
                                <button class="btn btn-admin float-right ml-2 " name="sale_order_btn" value="print"
                                    type="submit" id="sale_order_btn">
                                    <span class="btn-text">Save and Print</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> <!-- .row -->
        </div> <!-- .container-fluid -->


    </div> <!-- .wrapper -->

</body>

</html>
<?php include_once 'includes/foot.php'; ?>

<script>
    setTimeout(function () {
        $("purchase_type").change();
        $('#product_grand_total_amount').text("<?= @$fetchGatepass['grand_total'] ?>");
        $('#product_total_amount').text("<?= @$fetchGatepass['total_amount'] ?>");
        $('#remaining_ammount').val("<?= @$fetchGatepass['due'] ?>");
        $('#ordered_discount').val("<?= @$fetchGatepass['discount'] ?>");
        $('#paid_ammount').val("<?= @$fetchGatepass['paid'] ?>");
    }, 500);
</script>