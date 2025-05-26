<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php';

if (!empty($_REQUEST['edit_order_id'])) {
    $fetchOrder = fetchRecord($dbc, "manual_bill", "order_id", base64_decode($_REQUEST['edit_order_id']));
}

?>

<body class="horizontal light">
    <div class="wrapper">
        <?php include_once 'includes/header.php'; ?>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header card-bg" align="center">
                    <div class="row">
                        <div class="col-12 mx-auto h4">
                            <b class="text-center card-text pb-3"> Manual Bill </b>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="php_action/custom_action.php" method="POST" id="sale_order_fm">
                        <input type="hidden" name="product_order_id"
                            value="<?= !isset($_REQUEST['edit_order_id']) ? '' : base64_decode($_REQUEST['edit_order_id']) ?>">
                        <input type="hidden" name="form_type" id="form_type" value="manual-bill">
                        <input type="hidden" name="user_id"
                            value="<?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '' ?>">
                        <input type="hidden" id="add_pro_type" value="add">
                        <input type="hidden" id="edit_row_id">
                        <input type="hidden" id="product_uid">

                        <?php if ($_SESSION['user_role'] == 'admin') { ?>
                            <div class="dropdown-wrapper ml-auto mb-3">
                                <select name="branch_id" id="branch_id" class="custom-dropdown text-capitalize" required>
                                    <option selected disabled>Select Branch</option>
                                    <?php
                                    $branch = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status = 1");
                                    while ($row = mysqli_fetch_array($branch)) {
                                        ?>
                                        <option <?= (@$fetchOrder['branch_id'] == $row['branch_id']) ? 'selected' : '' ?>
                                            class="text-capitalize" value="<?= $row['branch_id'] ?>">
                                            <?= $row['branch_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="branch_id" id="branch_id" value="<?= $_SESSION['branch_id'] ?>">
                        <?php } ?>

                        <div class="row form-group">
                            <div class="col-md-4 d-flex">
                                <div class="w-100 pe-1">
                                    <label>ID#</label>
                                    <?php
                                    if (!empty($_REQUEST['edit_order_id'])) {
                                        $display_id = 'MB-' . $fetchOrder['order_id'];
                                    } else {
                                        $result = mysqli_query($dbc, 'SHOW TABLE STATUS LIKE "manual_bill"');
                                        $data = mysqli_fetch_assoc($result);
                                        $next_increment = $data['Auto_increment'];
                                        $display_id = 'MB-' . $next_increment;
                                    }
                                    ?>
                                    <input type="text" id="display_id" value="<?= htmlspecialchars($display_id) ?>"
                                        class="form-control" readonly>
                                    <input type="hidden" name="next_increment" id="next_increment"
                                        value="<?= htmlspecialchars($display_id) ?>">
                                </div>
                                <div class="w-100 pe-1 pl-1">
                                    <label>Date</label>
                                    <input type="date" name="order_date" id="order_date"
                                        value="<?= empty($_REQUEST['edit_order_id']) ? date('Y-m-d') : $fetchOrder['order_date'] ?>"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-2 cash-sale-div1">
                                <label>Customer Number</label>
                                <input type="number" onchange="getCustomer_name(this.value)"
                                    value="<?= @$fetchOrder['customer_phone'] ?>" autocomplete="off" min="0"
                                    class="form-control" name="client_contact" list="phone">
                                <datalist id="phone">
                                    <?php
                                    $q = mysqli_query($dbc, 'SELECT DISTINCT customer_phone FROM customers');
                                    while ($r = mysqli_fetch_assoc($q)) {
                                        ?>
                                        <option value="<?= $r['customer_phone'] ?>"><?= $r['customer_phone'] ?></option>
                                    <?php } ?>
                                </datalist>
                            </div>
                            <div class="col-sm-2 cash-sale-div2">
                                <label>Customer Name</label>
                                <input type="text" id="client_name" value="<?= @$fetchOrder['customer_name'] ?>"
                                    class="form-control" autocomplete="off" name="client_name" list="client_names">
                                <datalist id="client_names">
                                    <?php
                                    $q = mysqli_query($dbc, 'SELECT DISTINCT customer_name FROM customers');
                                    while ($r = mysqli_fetch_assoc($q)) {
                                        ?>
                                        <option value="<?= $r['customer_name'] ?>"><?= $r['customer_name'] ?></option>
                                    <?php } ?>
                                </datalist>
                            </div>
                            <div class="col-sm-2">
                                <label>Comment</label>
                                <input type="text" autocomplete="off" name="order_narration" id="order_narration"
                                    value="<?= @$fetchOrder['order_narration'] ?>" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row mb-5">
                            <div class="col-6 col-md-2">
                                <label>Code</label>
                                <input type="text" name="product_code" autocomplete="off" id="get_product_code"
                                    class="form-control">
                            </div>
                            <div class="col-6 col-md-4">
                                <label>Products</label>
                                <input type="text" id="get_product_name" name="product_name" autocomplete="off"
                                    class="form-control" list="product_list">
                                <input type="hidden" id="product_id" name="product_id">
                                <datalist id="product_list">
                                    <?php
                                    $result = mysqli_query($dbc, 'SELECT product.*, brands.brand_name, categories.categories_name 
                                                                  FROM product 
                                                                  INNER JOIN brands ON product.brand_id = brands.brand_id 
                                                                  INNER JOIN categories ON product.category_id = categories.categories_id 
                                                                  WHERE product.status = 1');
                                    while ($row = mysqli_fetch_array($result)) {
                                        ?>
                                        <option value="<?= $row['categories_name'] ?>-<?= $row['product_name'] ?>-<?= $row['brand_name'] ?>"
                                            data-id="<?= $row['product_id'] ?>" data-code="<?= $row['product_code'] ?>"
                                            data-price="<?= $row['current_rate'] ?>" data-category="<?= $row['categories_name'] ?>"
                                            data-brand="<?= $row['brand_name'] ?>">
                                        </option>
                                    <?php } ?>
                                </datalist>
                                <span class="text-center w-100" id="instockQty"></span>
                            </div>
                            <div class="col-6 col-sm-2 col-md-1">
                                <label>Price</label>
                                <input type="number" min="0" class="form-control" id="get_product_price"
                                    name="product_price">
                            </div>
                            <div class="col-6 col-sm-1 col-md-1">
                                <label>Final Price</label>
                                <input type="number" min="0" class="form-control" id="get_final_rate" name="final_rate">
                            </div>
                            <div class="col-6 col-sm-2 col-md-1">
                                <label>Quantity</label>
                                <input type="number" class="form-control" id="get_product_quantity" value="1" min="1"
                                    name="quantity">
                            </div>
                            <div class="col-6 col-sm-1 col-md-1">
                                <label>Amount</label>
                                <input type="number" class="form-control" id="get_product_sale_price">
                            </div>
                            <div class="col-sm-1">
                                <br>
                                <button type="button" class="btn btn-success btn-sm mt-2 float-right" id="manualSale">
                                    <i class="fa fa-plus"></i> <b>Add</b>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <table class="table saleTable" id="myDiv">
                                    <thead class="table-bordered">
                                        <tr>
                                            <th class="text-dark">Code</th>
                                            <th class="text-dark">Product Name</th>
                                            <th class="text-dark">Unit Price</th>
                                            <th class="text-dark">Final Rate</th>
                                            <th class="text-dark">Quantity</th>
                                            <th class="text-dark">Amount</th>
                                            <th class="text-dark">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table table-bordered" id="purchase_product_tb">
                                        <?php
                                        if (isset($_REQUEST['edit_order_id'])):
                                            $order_id = base64_decode($_REQUEST['edit_order_id']);
                                            $manualBill = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM manual_bill WHERE order_id = '$order_id'"));
                                            $product_items = json_decode($manualBill['product_details'], true);

                                            foreach ($product_items as $r):
                                                $unique_id = uniqid('p_');
                                                $product_uid = $r['product_uid'] ?? $unique_id;
                                                ?>
                                                <tr id="product_idN_<?= $unique_id ?>">
                                                    <input type="hidden" data-price="<?= $r['rate'] ?>"
                                                        data-quantity="<?= $r['quantity'] ?>"
                                                        id="product_ids_<?= $unique_id ?>" class="product_ids"
                                                        name="product_ids[]" value="<?= $r['product_id'] ?>">
                                                    <input type="hidden" id="product_quantites_<?= $unique_id ?>"
                                                        name="product_quantites[]" value="<?= $r['quantity'] ?>">
                                                    <input type="hidden" id="product_rates_<?= $unique_id ?>"
                                                        name="product_rates[]" value="<?= $r['rate'] ?>">
                                                    <input type="hidden" id="product_totalrates_<?= $unique_id ?>"
                                                        name="product_totalrates[]" value="<?= $r['rate'] ?>">
                                                    <input type="hidden" id="product_final_rates_<?= $unique_id ?>"
                                                        name="product_final_rates[]" value="<?= $r['final_rate'] ?>">
                                                    <input type="hidden" id="product_names_<?= $unique_id ?>"
                                                        name="product_names[]" value="<?= htmlspecialchars($r['product_name']) ?>">
                                                    <input type="hidden" id="product_codes_<?= $unique_id ?>"
                                                        name="product_codes[]" value="<?= htmlspecialchars($r['product_code']) ?>">
                                                    <input type="hidden" id="product_actions_<?= $unique_id ?>"
                                                        name="product_actions[]" value="update">
                                                    <input type="hidden" id="product_uids_<?= $unique_id ?>"
                                                        name="product_uids[]" value="<?= $product_uid ?>">
                                                    <td><?= strtoupper(htmlspecialchars(@$r['product_code'])) ?></td>
                                                    <td><?= htmlspecialchars(@$r['product_name']) ?></td>
                                                    <td><?= number_format((float)$r['rate'], 2) ?></td>
                                                    <td><?= number_format((float)$r['final_rate'], 2) ?></td>
                                                    <td><?= $r['quantity'] ?></td>
                                                    <td><?= number_format((float)$r['final_rate'] * (float)$r['quantity'], 2) ?></td>
                                                    <td>
                                                        <button type="button"
                                                            onclick="removeByid('#product_idN_<?= $unique_id ?>')"
                                                            class="fa fa-trash text-danger"></button>
                                                        <button type="button"
                                                            onclick="editByid(
                                                                '<?= htmlspecialchars($r['product_id']) ?>',
                                                                '<?= htmlspecialchars($r['product_code']) ?>',
                                                                '<?= htmlspecialchars($r['product_name']) ?>',
                                                                <?= $r['rate'] ?>,
                                                                <?= $r['quantity'] ?>,
                                                                <?= $r['final_rate'] ?>,
                                                                '<?= $product_uid ?>'
                                                            )"
                                                            class="fa fa-edit text-success ml-2"></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td class="table-bordered"> Sub Total :</td>
                                            <td class="table-bordered" id="product_total_amount">
                                                <?= @$fetchOrder['total_amount'] ? number_format((float)$fetchOrder['total_amount'], 2) : '0.00' ?>
                                            </td>
                                            <td class="table-bordered"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td class="table-bordered"> Discount :</td>
                                            <td class="table-bordered" id="getDiscount">
                                                <input onkeyup="getOrderTotal()" type="number" id="ordered_discount"
                                                    class="form-control form-control-sm"
                                                    value="<?= empty($_REQUEST['edit_order_id']) ? '0' : $fetchOrder['discount'] ?>"
                                                    min="0" name="ordered_discount">
                                                <input onkeyup="getOrderTotal()" type="number" id="freight"
                                                    class="form-control form-control-sm d-none" placeholder="Freight"
                                                    value="0" min="0" name="freight">
                                            </td>
                                            <td class="table-bordered"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td class="table-bordered"> <strong>Net Total :</strong> </td>
                                            <td class="table-bordered" id="product_grand_total_amount">
                                                <?= @$fetchOrder['grand_total'] ? number_format((float)$fetchOrder['grand_total'], 2) : '0.00' ?>
                                            </td>
                                            <td class="table-bordered"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 d-flex justify-content-end">
                                <a href="credit_sale.php?credit_type=15days"
                                    class="btn btn-dark pt-2 float-right btn-sm">Cancel</a>
                                <button class="btn btn-admin ml-2" name="sale_order_btn" value="print" type="submit"
                                    id="sale_btn">Save and Print</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'includes/foot.php'; ?>
</body>
</html>

<?php include_once 'includes/foot.php'; ?>


<!-- <?php
if (!empty($_REQUEST['edit_order_id'])) {
    ?>
    <script type="text/javascript">
        var custid = $("#customer_account").val();

        //alert(custid);
        getBalance(custid, 'customer_account_exp');
    </script>
    <?php
}



?> -->

<script>
$(document).ready(function () {
    // Initialize datepicker
    $('#order_date').datepicker({ dateFormat: 'yy-mm-dd' });

    // Handle product selection
    let debounceTimeout;
    $('#get_product_name').on('input change', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            let productName = $(this).val().trim();
            let editRowId = $('#edit_row_id').val();
            // console.log('Event:', this.type, 'Product Name:', productName, 'Editing:', !!editRowId);

            if (!editRowId) {
                let selectedOption = $('#product_list option[value="' + productName.replace(/"/g, '\\"') + '"]');
                if (selectedOption.length) {
                    let productId = selectedOption.data('id');
                    let productCode = selectedOption.data('code');
                    let price = selectedOption.data('price');
                    $('#product_id').val(productId);
                    $('#get_product_code').val(productCode);
                    $('#get_product_price').val(price);
                    $('#get_final_rate').val(price);
                    $('#add_pro_type').val('add');
                } else {
                    $('#product_id').val('');
                    $('#get_product_code').val('');
                    $('#get_product_price').val('');
                    $('#get_final_rate').val('');
                    $('#add_pro_type').val('new');
                }
            }
            updateSalePrice();
        }, 300);
    });

    // Update sale price
    $('#get_product_quantity, #get_product_price, #get_final_rate').on('input', updateSalePrice);

    function updateSalePrice() {
        let finalRate = parseFloat($('#get_final_rate').val()) || parseFloat($('#get_product_price').val()) || 0;
        let quantity = parseInt($('#get_product_quantity').val()) || 1;
        $('#get_product_sale_price').val((finalRate * quantity));
    }

   

function sanitizeId(name) {
    return name.replace(/\s+/g, '_').replace(/[^\w\-]/g, '').toLowerCase();
}


$('#manualSale').on('click', function () {
    let productName = $('#get_product_name').val().trim();
    let productCode = $('#get_product_code').val().trim();
    let unitPrice = parseFloat($('#get_product_price').val()) || 0;
    let finalRate = parseFloat($('#get_final_rate').val()) || unitPrice;
    let quantity = parseInt($('#get_product_quantity').val()) || 1;
    let amount = finalRate * quantity;
    let addProType = $('#add_pro_type').val();
    let editRowId = $('#edit_row_id').val();
    let productUid = $('#product_uid').val() || 'p_' + Date.now(); // unique per item

    if (!productName) return alert('Please enter or select a product name.');
    if (unitPrice <= 0) return alert('Unit price must be greater than 0.');
    if (quantity <= 0) return alert('Quantity must be greater than 0.');

    const rowId = productUid;

    // Update if editing
 // Always remove existing product with same name if present
let existingRow = $(`input[name="product_names[]"][value="${productName}"]`).closest('tr');
if (existingRow.length) {
    existingRow.remove(); // Remove existing row completely
}

// Append new row with the updated data
$('#purchase_product_tb').append(generateRowHTML(rowId, productName, productCode, unitPrice, finalRate, quantity, amount, 'add'));

// Optionally add new option to datalist if it's a truly new product
if (addProType === 'new') {
    let option = `<option value="${productName}" data-id="${rowId}" data-code="${productCode}" data-price="${unitPrice}">New - ${productName}</option>`;
    $('#product_list').append(option);
}


    // Reset fields
    $('#get_product_name').val('');
    $('#product_id').val('');
    $('#get_product_code').val('');
    $('#get_product_price').val('');
    $('#get_final_rate').val('');
    $('#get_product_quantity').val('1');
    $('#get_product_sale_price').val('');
    $('#add_pro_type').val('add');
    $('#edit_row_id').val('');
    $('#product_uid').val('');
    updateTotals();
});

// Reusable row generator
function generateRowHTML(rowId, productName, productCode, unitPrice, finalRate, quantity, amount, action) {
    return `
        <tr id="product_idN_${rowId}">
            <input type="hidden" data-price="${unitPrice}" data-quantity="${quantity}" 
                id="product_ids_${rowId}" class="product_ids" name="product_ids[]" value="${rowId}">
            <input type="hidden" id="product_quantites_${rowId}" name="product_quantites[]" value="${quantity}">
            <input type="hidden" id="product_rates_${rowId}" name="product_rates[]" value="${unitPrice}">
            <input type="hidden" id="product_totalrates_${rowId}" name="product_totalrates[]" value="${unitPrice}">
            <input type="hidden" id="product_final_rates_${rowId}" name="product_final_rates[]" value="${finalRate}">
            <input type="hidden" id="product_names_${rowId}" name="product_names[]" value="${productName}">
            <input type="hidden" id="product_codes_${rowId}" name="product_codes[]" value="${productCode}">
            <input type="hidden" id="product_actions_${rowId}" name="product_actions[]" value="${action}">
            <input type="hidden" id="product_uids_${rowId}" name="product_uids[]" value="${rowId}">
            <td>${productCode.toUpperCase()}</td>
            <td>${productName}</td>
            <td>${unitPrice.toFixed(2)}</td>
            <td>${finalRate.toFixed(2)}</td>
            <td>${quantity}</td>
            <td>${amount.toFixed(2)}</td>
            <td>
                <button type="button" onclick="removeByid('#product_idN_${rowId}')" class="fa fa-trash text-danger"></button>
                <button type="button" onclick="editByid('${rowId}', '${productCode}', '${productName}', ${unitPrice}, ${quantity}, ${finalRate}, '${rowId}')" class="fa fa-edit text-success ml-2"></button>
            </td>
        </tr>
    `;
}


// Separate function for row update
function updateRow(rowId, productName, productCode, unitPrice, finalRate, quantity, amount, productUid, action) {
    // Update visible table row
    $(`#row_${rowId}`).html(`
        <td>${productCode.toUpperCase()}</td>
        <td>${productName}</td>
        <td>${unitPrice}</td>
        <td>${finalRate}</td>
        <td>${quantity}</td>
        <td>${amount}</td>
        <td>
            <button type="button" onclick="removeByid('#row_${rowId}'); removeByid('#hidden_inputs_row_${rowId}');" class="fa fa-trash text-danger"></button>
            <button type="button" onclick="editByid('${rowId}', '${productCode}', '${productName}', ${unitPrice}, ${quantity}, ${finalRate}, '${productUid}')" class="fa fa-edit text-success ml-2"></button>
        </td>
    `);

    // Update or insert hidden inputs
    let hiddenHtml = `
        <input type="hidden" data-price="${unitPrice}" data-quantity="${quantity}" 
            id="product_ids_${rowId}" class="product_ids" name="product_ids[]" value="${rowId}">
        <input type="hidden" id="product_quantites_${rowId}" name="product_quantites[]" value="${quantity}">
        <input type="hidden" id="product_rates_${rowId}" name="product_rates[]" value="${unitPrice}">
        <input type="hidden" id="product_totalrates_${rowId}" name="product_totalrates[]" value="${unitPrice}">
        <input type="hidden" id="product_final_rates_${rowId}" name="product_final_rates[]" value="${finalRate}">
        <input type="hidden" id="product_names_${rowId}" name="product_names[]" value="${productName}">
        <input type="hidden" id="product_codes_${rowId}" name="product_codes[]" value="${productCode}">
        <input type="hidden" id="product_actions_${rowId}" name="product_actions[]" value="${action}">
        <input type="hidden" id="product_uids_${rowId}" name="product_uids[]" value="${productUid}">
    `;

    // Append or update hidden input container
    if ($(`#hidden_inputs_${rowId}`).length) {
        $(`#hidden_inputs_${rowId}`).html(hiddenHtml);
    } else {
        $(`#row_${rowId}`).after(`
            <tr id="hidden_inputs_row_${rowId}" style="display:none;">
                <td colspan="7"><div id="hidden_inputs_${rowId}">${hiddenHtml}</div></td>
            </tr>
        `);
    }
}

function updateTotals() {
    let subtotal = 0;
    $('#purchase_product_tb tr').each(function () {
        let price = parseFloat($(this).find('input[name="product_final_rates[]"]').val()) || 0;
        let quantity = parseInt($(this).find('input[name="product_quantites[]"]').val()) || 0;
        let action = $(this).find('input[name="product_actions[]"]').val() || 'update';
        if (action !== 'delete') {
            subtotal += price * quantity;
        }
    });

    let discount = parseFloat($('#ordered_discount').val()) || 0;
    let freight = parseFloat($('#freight').val()) || 0;
    let grandTotal = subtotal - discount + freight;

    $('#product_total_amount').text(subtotal);
    $('#product_grand_total_amount').text(grandTotal);
}

// Delete product row
window.removeByid = function (selector) {
    let row = $(selector);
    row.find('input[name="product_actions[]"]').val('delete');
    row.hide(); // Hide visually but keep in DOM for form submission
    updateTotals();
};


window.editByid = function (rowId, productCode, productName, unitPrice, quantity, finalRate, productUid) {
    $('#get_product_name').val(productName);
    $('#get_product_code').val(productCode);
    $('#get_product_price').val(parseFloat(unitPrice));
    $('#get_final_rate').val(parseFloat(finalRate));
    $('#get_product_quantity').val(quantity);
    $('#get_product_sale_price').val((parseFloat(finalRate) * quantity));
    $('#add_pro_type').val(rowId.startsWith('new_') ? 'new' : 'add');
    $('#edit_row_id').val(rowId);
    $('#product_uid').val(productUid);
    updateSalePrice();
};

window.getOrderTotal = function () {
    updateTotals();
};



    // Form submission
    $('#sale_btn').on('click', function (e) {
        e.preventDefault();

        // Validation
        if ($('#purchase_product_tb tr').length === 0) {
            alert('Please add at least one product to the order.');
            return;
        }
        if ($('#branch_id').val() === null || $('#branch_id').val() === '') {
            alert('Please select a branch.');
            return;
        }

        let formData = $('#sale_order_fm').serializeArray();
        $.ajax({
            url: $('#sale_order_fm').attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showCancelButton: true,
                        confirmButtonText: 'Print Order',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
    if (result.isConfirmed) {
        window.open('print_sale.php?type=manualbill&id=' + response.order_id, '_blank');
        location.reload();
    } else {
        location.reload();
    }
});

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'An unknown error occurred.'
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log('Status:', status, 'Error:', error, 'Response:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Request Failed',
                    text: 'An error occurred while saving the order.'
                });
            }
        });
    });

    

});</script>