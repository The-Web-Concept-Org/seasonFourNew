<?php

if (isset($_POST['ajax']) && $_POST['ajax'] === 'get_stock_detail') {
  include_once 'includes/head.php';

  $product_id = $_POST['product_id'];
  $current_branch_id = $_SESSION['branch_id'] ?? 0;

  $query = "SELECT 
    b.branch_id, 
    b.branch_name, 
    SUM(i.quantity_instock) AS stock,
    p.product_name,
    br.brand_name,
    c.categories_name
FROM inventory i
JOIN branch b ON i.branch_id = b.branch_id
JOIN product p ON i.product_id = p.product_id
LEFT JOIN brands br ON p.brand_id = br.brand_id
LEFT JOIN categories c ON p.category_id = c.categories_id
WHERE i.product_id = $product_id
GROUP BY i.branch_id
HAVING stock > 0
";

  $result = mysqli_query($dbc, $query);

  if (mysqli_num_rows($result) > 0) {
    // Get the first row (contains product info)
    $row = mysqli_fetch_assoc($result);

    echo "<h5 style='padding-left :200px'>" . htmlspecialchars($row['categories_name']) . " | " . htmlspecialchars($row['product_name']) . " |  " . htmlspecialchars($row['brand_name']) . "</h5>";

    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>Branch Name</th><th>Available Stock</th></tr></thead><tbody>";

    $total_stock = $row['stock']; // first row's stock
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['branch_name']) . "</td>";
    echo "<td>" . $row['stock'] . "</td>";
    echo "</tr>";

    // Loop remaining rows
    while ($row = mysqli_fetch_assoc($result)) {
      $stock = $row['stock'];
      $total_stock += $stock;

      echo "<tr>";
      echo "<td>" . htmlspecialchars($row['branch_name']) . "</td>";
      echo "<td>" . $stock . "</td>";
      echo "</tr>";
    }

    echo "<tr>";
    echo "<td><strong>Total Stock</strong></td>";
    echo "<td><strong>" . $total_stock . "</strong></td>";
    echo "</tr>";
    echo "</tbody></table>";
  } else {
    echo "<div class='alert alert-warning text-center'>No stock available in any branch.</div>";
  }

  exit;
}

?>








<!DOCTYPE html>
<html lang="en">
<?php
include_once 'includes/head.php';
if (isset($_REQUEST['edit_product_id'])) {
  $fetchproduct = fetchRecord($dbc, "product", "product_id", base64_decode($_REQUEST['edit_product_id']));
}
$btn_name = isset($_REQUEST['edit_product_id']) ? "Update" : "Add";

?>
<style type="text/css">
  .badge {
    font-size: .9375rem;
  }

  @media print {

    /* Hide action column (last column) */
    #product_tb th:last-child,
    #product_tb td:last-child {
      display: none !important;
    }

    /* Hide print button itself */
    .d-print-none {
      display: none !important;
    }
  }
</style>

<body class="horizontal light  ">
  <div class="wrapper">
    <?php include_once 'includes/header.php'; ?>
    <main role="main" class="main-content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header card-bg" align="center">

            <div class="row">
              <div class="col-12 mx-auto h4">
                <b class="text-center card-text">Product Management</b>

                <!-- <a href="stockreport.php" class="btn btn-admin float-right btn-sm mx-1">Print Stock (Advance)</a>
                <a href="stock.php?type=simple" class="btn btn-admin float-right btn-sm mx-1">Print Stock</a>
                <a href="stock.php?type=amount" class="btn btn-admin float-right btn-sm mx-1">Print Stock With Amount</a> -->

                <!-- <a href="product.php?act=add" class="btn btn-admin float-right btn-sm mx-1">Add New</a> -->
              </div>
            </div>

          </div>
          <?php if (@$_REQUEST['act'] == "add"): ?>
            <div class="card-body">
              <form action="php_action/custom_action.php" id="add_product_fm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="product_module">
                <input type="hidden" name="product_id" value="<?= @base64_encode($fetchproduct['product_id']) ?>">
                <input type="hidden" id="product_add_from" value="page">


                <div class="form-group row">
                  <div class="col-md-2 mt-3">
                    <label>Product ID#</label>
                    <?php
                    $result = mysqli_query($dbc, "SELECT product_code FROM product WHERE product_code LIKE 'SF%' ORDER BY CAST(SUBSTRING(product_code, 3) AS UNSIGNED) DESC LIMIT 1");
                    $data = mysqli_fetch_assoc($result);

                    if ($data && isset($data['product_code'])) {
                      $latest_code = $data['product_code'];
                      $number_only = (int) substr($latest_code, 2);
                      $number_only = $number_only + 1;
                    }
                    ?>
                    <input type="text" name="next_increment" id="next_increment"
                      value="SF25-PROD-<?= @empty($_REQUEST['edit_product_id']) ? $number_only : preg_replace('/\D/', '', $fetchproduct['product_code']) ?>"
                      readonly class="form-control">
                  </div>

                  <div class="col-sm-2 mt-3">
                    <label for="">Product Category</label>
                    <div id="categoryDropdownContainer">
                      <select class="form-control searchableSelect categorydropdown" name="category_id" id="tableData1" size="1">
                        <option value="">Select Category</option>
                        <?php
                        $result = mysqli_query($dbc, "select * from categories");
                        while ($row = mysqli_fetch_array($result)) {
                          ?>
                          <option data-price="<?= $row["category_price"] ?>" <?= @($fetchproduct['category_id'] != $row["categories_id"]) ? "" : "selected" ?> value="<?= $row["categories_id"] ?>">
                            <?= $row["categories_name"] ?>-<?= $row["category_price"] ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                    <!-- <div id="newCategoryDiv" style="display:none;">
                      <input type="text" class="form-control " id="new_category_name" name="new_category_name"
                        placeholder="Add New Category">
                      <input type="hidden" id="new_category_status" name="new_category_status" value="1">
                    </div> -->
                  </div>
                  <div class="col-1 col-md-1 mt-3">
                    <label class="invisible d-block">.</label>
                    <button type="button" class="btn btn-danger btn-sm hide_btn_forModal" data-toggle="modal"
                      data-target="#add_category_modal">
                      <i class="fa fa-plus"></i>
                    </button>
                    <!-- <button type="button" class="btn btn-danger btn-sm " style="display: none;"
                      id="cancelCategoryBtn">Cancel</button> -->
                  </div>

                  <div class="col-sm-2 mt-3">
                    <label for="">Product Brand</label>
                    <div id="brandDropdownContainer">
                      <select class="form-control searchableSelect tableData brandAccordingCategory" name="brand_id" id="brandSelect" size="1">
                        <option value="">Select Brand</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-1 col-md-1 mt-3">
                    <label class="invisible d-block">.</label>
                    <button type="button" class="btn btn-success btn-sm hide_btn_forModal" id="addBrandBtn"
                      data-toggle="modal" data-target="#add_brand_modal">
                      <i class="fa fa-plus"></i>
                    </button>
                  </div>

                  <div class="col-sm-2 mb-3 mt-3 mb-sm-0">
                    <?php
                    // Fetch latest product code starting with 'SF'
                    $result = mysqli_query($dbc, "SELECT product_code FROM product WHERE product_code LIKE 'SF%' ORDER BY CAST(SUBSTRING(product_code, 3) AS UNSIGNED) DESC LIMIT 1");
                    $data = mysqli_fetch_assoc($result);

                    // Parse and increment
                    if ($data && isset($data['product_code'])) {
                      $latest_code = strtoupper($data['product_code']); // Ensure the prefix is uppercase
                      $prefix = 'SF';
                      $number_part = (int) substr($latest_code, strlen($prefix));
                      $next_number = $number_part + 1;
                      $next_product_code = $prefix . $next_number;
                    }


                    // Use existing code if editing
                    $input_value = !empty($_REQUEST['edit_product_id']) ? htmlspecialchars($fetchproduct['product_code']) : $next_product_code;
                    ?>

                    <label for="product_code">Product Code</label>
                    <input type="text" class="form-control text-uppercase" id="product_code" name="product_code"
                      value="<?= $input_value ?>" readonly required>
                  </div>

                  <div class="col-sm-2 mb-3 mt-3 mb-sm-0">
                    <label for="">Product Name</label>
                    <input type="text" class="form-control" id="product_name" placeholder="Product Name"
                      name="product_name" required
                      value="<?= htmlspecialchars(@$fetchproduct['product_name'], ENT_QUOTES) ?>">

                  </div>

                  <div class="col-sm-3 mt-3 mb-sm-0">
                    <label for="">Product Description</label>

                    <textarea class="form-control" name="product_description"
                      placeholder="Product Description"><?= @$fetchproduct['product_description'] ?></textarea>
                  </div>
                  <div class="col-sm-2 mt-3 mb-sm-0">
                    <label for="">Purchase Rate</label>
                    <input type="text" class="form-control" id="purchase_rate" placeholder=" Rate" name="purchase_rate"
                      required value="<?= @$fetchproduct['purchase_rate'] ?>">
                  </div>
                  <div class="col-sm-2 mt-3 mb-sm-0">
                    <label for="">Sale Rate</label>
                    <input type="text" class="form-control" id="" placeholder=" Rate" name="current_rate" required
                      value="<?= @$fetchproduct['current_rate'] ?>">
                  </div>
                  <div class="col-sm-2 mt-3 mb-sm-0">
                    <label for="">Final Rate</label>
                    <input type="text" class="form-control" id="final_rate" placeholder=" Rate" name="final_rate" required
                      value="<?= @$fetchproduct['final_rate'] ?>">
                  </div>
                  <div class="col-sm-1 mt-3">
                    <label for="">MOQ Alert</label>
                    <input type="text" required class="form-control"
                      value="<?= (empty($fetchproduct)) ? 5 : $fetchproduct['alert_at'] ?>" id="alert_at"
                      placeholder="Product Stock Alert" name="alert_at">
                  </div>

                  <div class="col-sm-2 mt-3 mb-sm-0">

                    <label for="">Status</label>
                    <select class="form-control" required name="availability" id="availability">
                      <option value="1">Available</option>
                      <option value="0">Not Available</option>
                    </select>

                  </div>
                </div>

                <div class="form-group row">
                  <!-- Product Brand Section -->


                </div>
                <button class="btn btn-admin float-right" type="submit" id="add_product_btn"><?= $btn_name ?></button>
              </form>
            </div>
          <?php else: ?>
            <div class="card-body">

              <div class="d-flex justify-content-between  mb-3 ">
                <div>
                  <?php
                  // make sure the session is started
                  $user_role = $_SESSION['user_role'] ?? '';
                  $session_branch_id = $_SESSION['branch_id'] ?? '';
                  ?>

                  <?php if ($user_role === 'admin'): ?>
                    <form method="GET" class="form-inline mb-3">
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
                  <?php else: ?>
                    <?php

                    $_GET['branch_id'] = $session_branch_id;
                    ?>
                  <?php endif; ?>

                </div>
                <div class="d-flex align-items-center ml-auto">
                  <form action="php_action/download_products.php?action=upload_products" method="POST"
                    enctype="multipart/form-data">
                    <input type="file" name="excel_file" accept=".xlsx, .xls" required>
                    <button type="submit" class="btn btn-success mr-3">Upload Products</button>
                  </form>
                  <a href="php_action/download_products.php?action=download_products"
                    class="btn btn-primary mr-3">Download Products</a>
                  <a href="php_action/download_products.php?action=download_example" class="btn btn-danger mr-3">Download
                    Example</a>
                  <button onclick="printTable()" class="btn btn-success text-white d-print-none">Print Table</button>
                </div>
              </div>

              <div id="loader" class="text-center my-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <p>Loading products...</p>
              </div>

              <div id="productTableWrapper" style="display: none;">
                <table class="table dataTable col-12" style="width: 100%" id="product_tb">
                  <thead>
                    <tr>
                      <th class="text-dark">Code</th>
                      <th class="text-dark">Name</th>
                      <th class="text-dark" style="width: 20%;">Description</th>
                      <th class="text-dark" style="min-width: 5%;">Category</th>
                      <th class="text-dark" style="width: 15%;">Brand</th>
                      <?php if ($UserData['user_role'] == 'admin'): ?>
                        <th class="text-dark" style="width: 20%;">Purchase Rate</th>
                      <?php endif; ?>
                      <th class="text-dark" style="width: 15%;">Sale Rate</th>
                      <th class="text-dark" style="width: 15%;">Final Rate</th>
                      <th class="text-dark" style="width: 15%;">Quantity</th>
                      <th class="d-print-none text-dark" style="width: 15%;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $branch_id = ($_SESSION['user_role'] == 'admin') ? ($_GET['branch_id'] ?? '') : $_SESSION['branch_id'];

                    $query = "
        SELECT 
          p.*, 
          b.brand_name, 
          c.categories_name, 
          IFNULL(SUM(i.quantity_instock), 0) AS quantity_instock
        FROM product p
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN categories c ON p.category_id = c.categories_id
        LEFT JOIN inventory i ON p.product_id = i.product_id";

                    if (!empty($branch_id)) {
                      $query .= " AND i.branch_id = '$branch_id'";
                    }

                    $query .= "
        WHERE p.status = 1
        GROUP BY p.product_id
        ORDER BY p.product_code DESC";

                    $result = mysqli_query($dbc, $query);

                    while ($r = mysqli_fetch_assoc($result)) {
                      $quantity = $r['quantity_instock'];
                      $alert_at = !empty($r['alert_at']) ? $r['alert_at'] : 5;
                      $badge_class = ($quantity <= $alert_at) ? 'bg-danger text-white p-1 rounded' : 'bg-success text-white p-1 rounded';
                      ?>
                      <tr>
                        <td class="text-uppercase"><?= $r['product_code'] ?></td>
                        <td class="text-capitalize"><?= $r['product_name'] ?></td>
                        <td class="text-capitalize"><?= $r['product_description'] ?></td>
                        <td class="text-capitalize"><?= $r['categories_name'] ?? 'N/A' ?></td>
                        <td class="text-capitalize"><?= $r['brand_name'] ?? 'N/A' ?></td>

                        <?php if ($UserData['user_role'] == 'admin'): ?>
                          <td><?= $r['purchase_rate'] ?></td>
                        <?php endif; ?>

                        <td><?= $r['current_rate'] ?></td>
                        <td><?= $r['final_rate'] ?></td>

                        <td><span class='<?= $badge_class ?>'><?= $quantity ?></span></td>

                        <td class="d-flex">
                          <?php if (@$userPrivileges['nav_edit'] == 1 || $_SESSION['user_role'] == 'admin'): ?>
                            <form action="product.php?act=add" method="POST">
                              <input type="hidden" name="edit_product_id" value="<?= base64_encode($r['product_id']) ?>">
                              <button type="submit" class="btn btn-admin btn-sm m-1 d-inline-block">Edit</button>
                            </form>
                          <?php endif ?>

                          <?php
                          $product_id = $r['product_id'];

                          $query_for_delete = "SELECT SUM(quantity_instock) as total_stock FROM inventory WHERE product_id = $product_id";
                          $result_for_delete = mysqli_query($dbc, $query_for_delete);

                          if (!$result_for_delete) {
                            echo "Query error: " . mysqli_error($dbc);
                            exit;
                          }

                          $row_for_delete = mysqli_fetch_assoc($result_for_delete);
                          $total_stock_for_delete = (float) $row_for_delete['total_stock'];

                          // Only show delete button if total stock is exactly 0 (no + or - stock)
                          if ($total_stock_for_delete == 0) {
                            if (@$userPrivileges['nav_delete'] == 1 || $_SESSION['user_role'] == 'admin') {
                              ?>
                              <button type="button"
                                onclick="deleteAlert('<?= $r['product_id'] ?>','product','product_id','product_tb')"
                                class="btn btn-admin2 btn-sm m-1 d-inline-block">Delete</button>
                              <?php
                            }
                          }
                          ?>


                          <a href="print_barcode.php?id=<?= base64_encode($r['product_id']) ?>"
                            class="btn btn-primary btn-sm m-1">Barcode</a>

                          <button type="button" class="btn btn-admin2 btn-sm m-1 d-inline-block view-stock-btn"
                            onclick="getdata(<?= $r['product_id'] ?>)" data-toggle="modal" data-target="#view_stock_modal">
                            Detail
                          </button>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

            </div>

          <?php endif ?>
        </div>
      </div> <!-- .row -->
  </div> <!-- .container-fluid -->

  </main> <!-- main -->
  </div> <!-- .wrapper -->
  <div class="modal fade" id="view_stock_modal" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="defaultModalLabel">Stock Detail</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <div class="form-group row">



          </div>

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
<script>
  function printTable() {
    // If using DataTables
    const table = $('#product_tb').DataTable();

    // Save current page length
    const oldLength = table.page.len();

    // Show all rows
    table.page.len(-1).draw();

    setTimeout(() => {
      // Open new print window
      const tableHTML = document.getElementById('product_tb').outerHTML;
      const printWindow = window.open('', '', 'height=700,width=1000');
      printWindow.document.write('<html><head><title>Print Table</title>');
      printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
      printWindow.document.write('<style>');
      printWindow.document.write('@media print { .d-print-none { display: none !important; }');
      printWindow.document.write('#product_tb th:last-child, #product_tb td:last-child { display: none !important; } }');
      printWindow.document.write('</style></head><body>');
      printWindow.document.write(tableHTML);
      printWindow.document.write('</body></html>');
      printWindow.document.close();
      printWindow.focus();

      // Print and close
      setTimeout(() => {
        printWindow.print();
        printWindow.close();

        // Restore previous pagination
        table.page.len(oldLength).draw();
      }, 500);
    }, 500);
  }

  function getdata(id) {
    $.ajax({
      url: '', // same file
      method: 'POST',
      data: {
        ajax: 'get_stock_detail',
        product_id: id
      },
      success: function (response) {
        // console.log('Response:', response); 
        $('#view_stock_modal .modal-body .form-group').html(response);
        $('#view_stock_modal').modal('show');
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', status, error);
      }
    });

  }

  $(document).ready(function () {
    // In edit mode
    <?php if (isset($_REQUEST['edit_product_id'])): ?>
      loadBrands('<?= $fetchproduct['category_id'] ?>', '<?= $fetchproduct['brand_id'] ?>');
<?php endif; ?>
      // loader
      $('#loader').hide();
    $('#productTableWrapper').show();
  });
</script>