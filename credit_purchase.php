<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php';

if (!empty($_REQUEST['edit_purchase_id'])) {
  # code...
  $fetchPurchase = fetchRecord($dbc, "purchase", "purchase_id", base64_decode($_REQUEST['edit_purchase_id']));
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
              <b class="text-center card-text">Purchase</b>


              <!-- <a href="credit_purchase.php" class="btn btn-admin float-right btn-sm">Add New</a> -->
            </div>
          </div>

        </div>
        <div class="card-body">
          <form action="php_action/custom_action.php" method="POST" id="sale_order_fm">
            <input type="hidden" name="product_purchase_id"
              value="<?= @empty($_REQUEST['edit_purchase_id']) ? "" : base64_decode($_REQUEST['edit_purchase_id']) ?>">
            <input type="hidden" name="payment_type" id="payment_type" value="credit_purchase">
            <input type="hidden" name="user_id" value="<?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '' ?>">

            <input type="hidden" name="lpo_form" id="lpo_form" value="">
            <input type="hidden" name="price_type" id="price_type" value="purchase">
            <?php if ($_SESSION['user_role'] == 'admin') { ?>
              <div class="dropdown-wrapper ml-auto mb-3">
                <select name="branch_id" id="branch_id" class="custom-dropdown text-capitalize" required>
                  <option selected disabled value="">Select Branch</option>
                  <?php
                  $branch = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status = 1");
                  while ($row = mysqli_fetch_array($branch)) {
                    ?>
                    <option <?= (@$fetchPurchase['branch_id'] == $row['branch_id']) ? "selected" : "" ?>
                      class="text-capitalize" value="<?= $row['branch_id'] ?>">
                      <?= $row['branch_name'] ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            <?php } else { ?>
              <!-- <input type="hidden" name="branch_id" id="branch_id" value="<?= $_SESSION['branch_id'] ?>"> -->
              <div class="dropdown-wrapper ml-auto mb-3 d-none">
                <select name="branch_id" id="branch_id" class="custom-dropdown text-capitalize" required>
                  <option selected disabled>Select Branch</option>
                  <?php
                  $branch = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_id = '" . $_SESSION['branch_id'] . "' ");
                  while ($row = mysqli_fetch_array($branch)) {
                    ?>
                    <option selected class="text-capitalize" value="<?= $row['branch_id'] ?>">
                      <?= $row['branch_name'] ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            <?php } ?>
            <div class="row form-group">
              <div class="col-md-3">
                <div class="row">
                  <div class="col-6">
                    <label>ID#</label>
                    <?php
                    $result = mysqli_query($dbc, "SHOW TABLE STATUS LIKE 'purchase'");
                    $data = mysqli_fetch_assoc($result);
                    $next_increment = $data['Auto_increment'];
                    $default_prefix = isset($_REQUEST['edit_purchase_id']) && @$fetchPurchase['payment_type'] == "cash" ? "SF25-CP-" : "SF-CRP-";
                    $default_id = isset($_REQUEST['edit_purchase_id']) ? $fetchPurchase['purchase_id'] : $next_increment;
                    ?>
                    <input type="text" name="next_increment" id="next_increment"
                      value="<?= $default_prefix . $default_id ?>" readonly class="form-control">
                  </div>

                  <div class="col-6">
                    <label>Purchase Date</label>
                    <input type="text" name="purchase_date" id="purchase_date"
                      value="<?= @empty($_REQUEST['edit_order_id']) ? date('Y-m-d') : $fetchPurchase['purchase_date'] ?>"
                      readonly class="form-control">
                  </div>
                </div>
              </div>

              <div class="col-md-2">
                <label for="purchase_type">Purchase Type</label>
                <select name="purchase_type" onchange="updatePurchaseId(this.value)" class="form-control"
                  id="purchase_type">
                  <option value="cash_purchase" <?= @$fetchPurchase['payment_type'] == "cash_purchase" ? "selected" : "" ?>>Cash</option>
                  <option value="credit_purchase" <?= @$fetchPurchase['payment_type'] == "credit_purchase" || !isset($_REQUEST['edit_purchase_id']) ? "selected" : "" ?>>Credit</option>
                </select>
              </div>

              <script>
                function updatePurchaseId(purchaseType) {
                  const idInput = document.getElementById('next_increment');
                  const currentId = idInput.value.replace(/^(SF25-CP-|SF-CRP-)/, '');
                  const newPrefix = purchaseType === 'cash_purchase' ? 'SF25-CP-' : 'SF-CRP-';
                  idInput.value = newPrefix + currentId;
                }
              </script>
              <div class="col-sm-3">
                <label>Select Supplier</label>
                <div class="input-group">
                  <select class="form-control searchableSelect" name="cash_purchase_supplier"
                    id="credit_order_client_name" required onchange="getBalance(this.value,'customer_account_exp')"
                    aria-label="Username" aria-describedby="basic-addon1">
                    <option value="">Select Supplier</option>
                    <?php
                    $q = mysqli_query($dbc, "SELECT * FROM customers WHERE customer_status =1 AND customer_type='supplier'");
                    while ($r = mysqli_fetch_assoc($q)) {
                      ?>
                      <option <?= @($fetchPurchase['customer_account'] == $r['customer_id']) ? "selected" : "" ?>
                        data-id="<?= $r['customer_id'] ?>" data-contact="<?= $r['customer_phone'] ?>"
                        value="<?= $r['customer_name'] ?>"><?= $r['customer_name'] ?> | <?= $r['customer_phone'] ?>
                      </option>
                    <?php } ?>
                  </select>
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">Balance : <span id="customer_account_exp">0</span>
                    </span>
                  </div>
                </div>
                <input type="hidden" name="customer_account" id="customer_account"
                  value="<?= @$fetchPurchase['customer_account'] ?>">
                <input type="hidden" name="client_contact" id="client_contact"
                  value="<?= @$fetchPurchase['client_contact'] ?>">

              </div>
              <div class="col-sm-1">
                <br>
                <a href="customers.php?type=supplier" class="btn btn-admin2 btn-sm mt-2">Add</a>
              </div>
              <div class="col-sm-1">
                <label>Comment</label>
                <input type="text" autocomplete="off" value="<?= @$fetchPurchase['purchase_narration'] ?>"
                  class="form-control" name="purchase_narration">

              </div>
              <div class="col-sm-2">
                <label>Attach File
                  <?php if (!empty($fetchPurchase['purchase_file'])): ?>
                    <a href="img/uploads/<?= htmlspecialchars($fetchPurchase['purchase_file']) ?>" target="_blank">
                      <p type="button" class="d-inline p-0 m-0">View File</p>
                    </a>
                  <?php endif; ?>
                </label>
                <input type="file" autocomplete="off" value="<?= @$fetchPurchase['purchase_file'] ?>"
                  class="form-control" name="purchase_file">
              </div>
            </div> <!-- end of form-group -->
            <div class="form-group row mb-5">
              <div class="col-4 col-md-2">
                <label>Product Code</label>
                <input type="text" autocomplete="off" name="product_code" id="get_product_code" class="form-control">
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

                    <!-- <option data-price="<?= $row["current_rate"] ?>" <?= empty($r['product_id']) ? "" : "selected" ?> value="<?= $row["product_id"] ?>" style="text-transform: capitalize;">
                      <?= $getCat["categories_name"] ?> - <?= $row["product_name"] ?> - <?= $getBrand["brand_name"] ?> </option> -->

                    <option class="text-capitalize" data-price="<?= $row["current_rate"] ?>" <?= empty($r['product_id']) ? "" : "selected" ?> value="<?= $row["product_id"] ?>">
                      <?= $getCat["categories_name"] ?> - <?= $row["product_name"] ?> - <?= @$getBrand["brand_name"] ?>
                    </option>


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
              <div class="col-6 col-sm-1 col-md-1">
                <label>Final Price</label>
                <input type="number" class="form-control" id="get_product_price">
              </div>
              <div class="col-6 col-sm-2 col-md-1">
                <label>Quantity</label>
                <input type="number" class="form-control" id="get_product_quantity" value="" min="1" name="quantity"
                >
              </div>
              <div class="col-6 col-sm-1 col-md-1">
                <label>Amount</label>
                <input type="number" <?= ($_SESSION['user_role'] == "admin") ? "" : "readonly" ?> class="form-control"
                  id="get_product_sale_price">
              </div>

              <div class="col-sm-1">
                <br>
                <button type="button" class="btn btn-success btn-sm mt-2 float-right" id="addProductPurchase"><i
                    class="fa fa-plus"></i> <b>Add</b></button>
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
                      <th class="text-dark" style="width: 25%;">Amount</th>
                      <th class="text-dark">Action</th>
                    </tr>
                  </thead>
                  <tbody class="table table-bordered" id="purchase_product_tb">
                    <?php if (isset($_REQUEST['edit_purchase_id'])):
                      $q = mysqli_query($dbc, "SELECT  product.*,brands.*,purchase_item.* FROM purchase_item INNER JOIN product ON product.product_id=purchase_item.product_id INNER JOIN brands ON product.brand_id=brands.brand_id   WHERE purchase_item.purchase_id='" . base64_decode($_REQUEST['edit_purchase_id']) . "'");

                      while ($r = mysqli_fetch_assoc($q)) {

                        ?>
                        <tr id="product_idN_<?= $r['product_id'] ?>">
                          <input type="hidden" data-price="<?= $r['rate'] ?>" data-quantity="<?= $r['quantity'] ?>"
                            id="product_ids_<?= $r['product_id'] ?>" class="product_ids" name="product_ids[]"
                            value="<?= $r['product_id'] ?>">
                          <input type="hidden" id="product_quantites_<?= $r['product_id'] ?>" name="product_quantites[]"
                            value="<?= $r['quantity'] ?>">
                          <input type="hidden" id="product_rate_<?= $r['product_id'] ?>" name="product_rates[]"
                            value="<?= $r['rate'] ?>">
                          <input type="hidden" id="product_totalrate_<?= $r['product_id'] ?>" name="product_totalrates[]"
                            value="<?= $r['rate'] ?>">
                          <input type="hidden" id="product_salerate_<?= $r['product_id'] ?>" name="product_salerates[]"
                            value="<?= $r['sale_rate'] ?>">
                          <td><?= $r['product_code'] ?></td>
                          <td><?= $r['product_name'] ?></td>

                          <td><?= $r['rate'] ?></td>
                          <td><?= $r['quantity'] ?></td>
                          <td><?= (float) $r['rate'] * (float) $r['quantity'] ?></?>
                          </td>
                          <td>

                            <button type="button" onclick="removeByid(`#product_idN_<?= $r['product_id'] ?>`)"
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
                      <td class="table-bordered" id="product_total_amount"><?= @$fetchPurchase['total_amount'] ?></td>
                      </td>
                      <td class="table-bordered"></td>
                    </tr>
                    <tr>
                      <td colspan="4" class="table-bordered"></td>
                      <td class="table-bordered"> Discount :</td>
                      <td class="table-bordered" id="getDiscount"><input onkeyup="getOrderTotal()" type="text"
                          id="ordered_discount" class="form-control form-control-sm"
                          value="<?= @empty($_REQUEST['edit_order_id']) ? @$fetchPurchase['discount'] : "0" ?>" min="0"
                          name="ordered_discount">
                      </td>
                      <td class="table-bordered"></td>

                    </tr>
                    <tr>
                      <td colspan="4" class="table-bordered"></td>
                      <td class="table-bordered"> <strong>Net Total :</strong> </td>
                      <td class="table-bordered" id="product_grand_total_amount"><?= @$fetchPurchase['grand_total'] ?>
                      </td>
                      </td>
                      <td class="table-bordered"></td>

                    </tr>
                    <tr>
                      <td colspan="4" class="table-bordered"></td>
                      <td class="table-bordered">Paid :</td>
                      <td class="table-bordered">
                        <div class="form-group row">
                          <div class="col-sm-6">
                            <input type="number" step="0.001" min="0" class="form-control form-control-sm"
                              id="paid_ammount" required onkeyup="getRemaingAmount()" name="paid_ammount"
                              value="<?= @$fetchPurchase['paid'] ?>">


                          </div>
                          <div class="col-sm-6">
                            <div class="custom-control custom-switch">
                              <input type="checkbox" class="custom-control-input" id="full_payment_check">
                              <label class="custom-control-label" for="full_payment_check">Full Payment</label>
                            </div>

                      </td>
                      <td class="table-bordered"></td>

                    </tr>

                    <tr>
                      <td colspan="4" class="table-bordered"></td>
                      <td class="table-bordered">Account :</td>
                      <td class="table-bordered">

                        <div class="input-group">
                          <select class="form-control" onchange="getBalance(this.value,'payment_account_bl')"
                            name="payment_account" id="payment_account" aria-label="Username"
                            aria-describedby="basic-addon1">
                            <option value="">Select Account</option>
                            <?php
                            $branch_id = $_SESSION['branch_id'];
                            $user_role = $_SESSION['user_role']; 
                            
                            if ($user_role === 'admin') {
                              $sql = "SELECT * FROM customers WHERE customer_status = 1 AND customer_type = 'bank'";
                            } else {
                              $sql = "SELECT * FROM customers WHERE customer_status = 1 AND customer_type = 'bank' AND branch_id = '$branch_id'";
                            }

                            $q = mysqli_query($dbc, $sql);

                            while ($r = mysqli_fetch_assoc($q)):
                              ?>
                              <option <?= @($fetchPurchase['payment_account'] == $r['customer_id']) ? "selected" : "" ?>
                                value="<?= $r['customer_id'] ?>">
                                <?= $r['customer_name'] ?>
                              </option>
                            <?php endwhile; ?>d
                          </select>

                          <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Balance : <span
                                id="payment_account_bl">0</span> </span>
                          </div>
                        </div>
                      </td>
                      <td class="table-bordered"></td>

                    </tr>
                    <tr>
                      <td colspan="4" class="table-bordered"></td>
                      <td class="table-bordered">Remaing Amount :</td>
                      <td class="table-bordered"><input type="number" class="form-control form-control-sm"
                          id="remaining_ammount" required readonly name="remaining_ammount"
                          value="<?= @$fetchPurchase['due'] ?>">
                      </td>
                      <td class="table-bordered"></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 d-flex justify-content-end">
                <a href="credit_purchase.php" class="btn btn-dark  pb-2btn-sm">Cancel</a>
                <button class="btn btn-admin float-right ml-2 " name="sale_order_btn" value="print" type="submit"
                  id="sale_order_btn">Save and Print</button>
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
    $('#product_grand_total_amount').text("<?= @$fetchPurchase['grand_total'] ?>");
    $('#product_total_amount').text("<?= @$fetchPurchase['total_amount'] ?>");
    $('#remaining_ammount').val("<?= @$fetchPurchase['due'] ?>");
    $('#ordered_discount').val("<?= @$fetchPurchase['discount'] ?>");
    $('#paid_ammount').val("<?= @$fetchPurchase['paid'] ?>");
  }, 500);
</script>