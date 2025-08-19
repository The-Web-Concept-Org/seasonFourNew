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
                                        $display_id = 'SF25-CI-' . $fetchOrder['order_id'];
                                    } else {
                                        $result = mysqli_query($dbc, 'SHOW TABLE STATUS LIKE "manual_bill"');
                                        $data = mysqli_fetch_assoc($result);
                                        $next_increment = $data['Auto_increment'];
                                        $display_id = 'SF25-CI-' . $next_increment;
                                    }
                                    ?>
                                    <input type="text" id="display_id" value="<?= htmlspecialchars($display_id) ?>"
                                        class="form-control" readonly>
                                    <input type="hidden" name="next_increment" id="next_increment"
                                        value="<?= htmlspecialchars($display_id) ?>">
                                </div>
                                <div class="w-100 pe-1 pl-1">
                                    <label>Date</label>
                                    <input type="text" name="order_date" id="order_date"
                                        value="<?= empty($_REQUEST['edit_order_id']) ? date('Y-m-d') : date('Y-m-d', strtotime($fetchOrder['timestamp'])) ?>"
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
                            <div class="col-sm-2">
                                <label>Type</label>
                                <select name="type" class="form-control" id="type">
                                    <option value="Sale_Invoice" <?= (!isset($_REQUEST['edit_order_id']) || @$fetchOrder['type'] == "Manual Bill") ? "selected" : "" ?>>
                                        Manual Bill
                                    </option>

                                    <option value="lpo" <?= @$fetchOrder['type'] == "lpo" ? "selected" : "" ?>>LPO</option>
                                    <option value="quotation" <?= @$fetchOrder['type'] == "quotation" ? "selected" : "" ?>>
                                        Quotation</option>
                                    <option value="delivery_note" <?= @$fetchOrder['type'] == "delivery_note" ? "selected" : "" ?>>Delivery Note</option>
                                </select>
                            </div>

                        </div>

                        <div class="form-group row mb-3">
                            <div class="col-6 col-md-4">
                                <label>Products</label>
                                <input type="text" id="get_product_name" name="product_name" autocomplete="off"
                                    class="form-control" list="product_list">
                                <input type="hidden" id="product_id" name="product_id">
                                <datalist id="product_list">
                                    <?php
                                    $result = mysqli_query($dbc, "SELECT * FROM product WHERE status=1 ");
                                    while ($row = mysqli_fetch_array($result)) {
                                        $getBrand = fetchRecord($dbc, "brands", "brand_id", $row['brand_id']);
                                        $getCat = fetchRecord($dbc, "categories", "categories_id", $row['category_id']);

                                        // Combine product info into value string
                                        $productLabel = $getCat["categories_name"] . " - " . $row["product_name"] . " - " . @$getBrand["brand_name"];
                                        ?>
                                        <option value="<?= htmlspecialchars($productLabel) ?>"
                                            data-id="<?= $row['product_id'] ?>" data-price="<?= $row['current_rate'] ?>"
                                            data-category="<?= $getCat["categories_name"] ?>"
                                            data-brand="<?= @$getBrand["brand_name"] ?>">
                                        </option>
                                    <?php } ?>
                                </datalist>

                                <span class="text-center w-100" id="instockQty"></span>
                            </div>
                            <div class="col-6 col-sm-2 col-md-2">
                                <label>Final Price</label>
                                <input type="number" min="0" class="form-control" id="get_final_rate" name="final_rate">
                            </div>
                            <div class="col-6 col-sm-2 col-md-2">
                                <label>Quantity</label>
                                <input type="number" class="form-control" id="get_product_quantity" value="" min="1"
                                    name="quantity">
                            </div>
                            <div class="col-6 col-sm-1 col-md-2">
                                <label>Amount</label>
                                <input type="number" class="form-control" id="get_product_sale_price">
                            </div>
                            <div class="col-sm-1">
                                <br>
                                <button type="button" class="btn btn-success btn-sm mt-2 float-right" id="manualSale">
                                    <span class="btn-text"><i class="fa fa-plus"></i> <b>Add</b></span>
                                    <span class="spinner-border spinner-border-sm text-light ms-2 d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <table class="table saleTable" id="myDiv">
                                    <thead class="table-bordered">
                                        <tr>
                                            <th class="">Product Name</th>
                                            <th class="">Final Rate</th>
                                            <th class="">Quantity</th>
                                            <th class="" style="width: 20%;">Amount</th>
                                            <th class="" style="width: 20%;">Action</th>
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
                                                $product_name_js = json_encode($r['product_name']); // for JS
                                                $product_name_html = htmlspecialchars($r['product_name']); // for display
                                                ?>
                                                <tr id="product_idN_<?= $unique_id ?>">
                                                        <input type="hidden" data-price="<?= $r['final_rate'] ?>"
                                                        data-quantity="<?= $r['quantity'] ?>"
                                                        id="product_ids_<?= $unique_id ?>" class="product_ids"
                                                        name="product_ids[]" value="<?= $r['product_id'] ?>">
                                                    <input type="hidden" id="product_quantites_<?= $unique_id ?>"
                                                        name="product_quantites[]" value="<?= $r['quantity'] ?>">

                                                    <input type="hidden" id="product_final_rates_<?= $unique_id ?>"
                                                        name="product_final_rates[]" value="<?= $r['final_rate'] ?>">
                                                    <input type="hidden" id="product_names_<?= $unique_id ?>"
                                                        name="product_names[]" value="<?= $product_name_html ?>">
                                                    <input type="hidden" id="product_actions_<?= $unique_id ?>"
                                                        name="product_actions[]" value="update">
                                                    <input type="hidden" id="product_uids_<?= $unique_id ?>"
                                                        name="product_uids[]" value="<?= $product_uid ?>">
                                                    <td><?= $product_name_html ?></td>
                                                    <td><?= number_format((float) $r['final_rate'], 2) ?></td>
                                                    <td><?= $r['quantity'] ?></td>
                                                    <td><?= number_format((float) $r['final_rate'] * (float) $r['quantity'], 2) ?>
                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                            onclick="removeByid('#product_idN_<?= $unique_id ?>')"
                                                            class="fa fa-trash text-danger"></button>
                                                        <button type="button" onclick='editByid(
                                                                    <?= json_encode($r["product_id"]) ?>,
                                                                    <?= $product_name_js ?>,
                                                                    <?= (int) $r["quantity"] ?>,
                                                                    <?= (float) $r["final_rate"] ?>,
                                                                    <?= json_encode($product_uid) ?>
                                                                )' class="fa fa-edit text-success ml-2"></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td class="table-bordered"> Sub Total :</td>
                                            <td class="table-bordered" id="product_total_amount">
                                                <?= @$fetchOrder['total_amount'] ? number_format((float) $fetchOrder['total_amount'], 2) : '0' ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"></td>
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
                                        </tr>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td class="table-bordered"> <strong>Net Total :</strong> </td>
                                            <td class="table-bordered" id="product_grand_total_amount">
                                                <?= @$fetchOrder['grand_total'] ? number_format((float) $fetchOrder['grand_total'], 2) : '0' ?>
                                            </td>
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
                                    id="sale_btn">
                                    <span class="btn-text">Save and Print</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'includes/foot.php'; ?>
</body>
<script>
    $(document).ready(function () {
        $('#order_date').datepicker({ dateFormat: 'yy-mm-dd' });

        let debounceTimeout;
        $('#get_product_name').on('input change', function () {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                let productName = $(this).val().trim();
                let editRowId = $('#edit_row_id').val();

                if (!editRowId) {
                    let selectedOption = $('#product_list option[value="' + productName.replace(/"/g, '\\"') + '"]');
                    if (selectedOption.length) {
                        let productId = selectedOption.data('id');
                        let price = selectedOption.data('price');
                        $('#product_id').val(productId);
                        $('#get_final_rate').val(price);
                        $('#add_pro_type').val('add');
                    } else {
                        $('#product_id').val('');
                        $('#get_final_rate').val('');
                        $('#add_pro_type').val('add');
                    }
                }
                updateSalePrice();
            }, 300);
        });

        // Update sale price
        $('#get_product_quantity, #get_final_rate').on('input', updateSalePrice);

        function updateSalePrice() {
            let finalRate = parseFloat($('#get_final_rate').val()) || 0;
            let quantity = parseInt($('#get_product_quantity').val()) || 1;
            $('#get_product_sale_price').val((finalRate * quantity));
        }

        function sanitizeId(name) {
            return name.replace(/\s+/g, '_').replace(/[^\w\-]/g, '').toLowerCase();
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        $('#manualSale').on('click', function () {
            let $btn = $(this);
            let $spinner = $btn.find('.spinner-border');
            let $text = $btn.find('.btn-text');

            // Show loader and disable button
            $spinner.removeClass('d-none');
            $text.addClass('d-none');
            $btn.prop('disabled', true);

            setTimeout(() => {
                let productName = $('#get_product_name').val().trim();
                let finalRate = parseFloat($('#get_final_rate').val()) || 0;
                let quantity = parseInt($('#get_product_quantity').val()) || 1;
                let amount = finalRate * quantity;
                let addProType = $('#add_pro_type').val();
                let editRowId = $('#edit_row_id').val();
                let productUid = $('#product_uid').val() || 'p_' + Date.now();

                if (!productName) {
                    alert('Please enter or select a product name.');
                    resetButton();
                    return;
                }

                if (finalRate <= 0) {
                    alert('Final rate must be greater than 0.');
                    resetButton();
                    return;
                }

                if (quantity <= 0) {
                    alert('Quantity must be greater than 0.');
                    resetButton();
                    return;
                }

                const rowId = editRowId || productUid;
                const escapedName = escapeHtml(productName);

                if (addProType === 'add') {
                    // Check if product already exists in the table using safer filter
                    let existingRow = $('input[name="product_names[]"]').filter(function () {
                        return $(this).val().trim() === productName;
                    }).closest('tr');

                    if (existingRow.length) {
                        let existingQuantity = parseInt(existingRow.find('input[name="product_quantites[]"]').val()) || 0;
                        let newQuantity = existingQuantity + quantity;
                        let newAmount = finalRate * newQuantity;

                        existingRow.replaceWith(generateRowHTML(rowId, escapedName, finalRate, newQuantity, newAmount, 'add'));
                    } else {
                        $('#purchase_product_tb').append(generateRowHTML(rowId, escapedName, finalRate, quantity, amount, 'add'));

                        // Add new option to datalist
                        $('#product_list').append(`<option value="${escapedName}" data-id="${rowId}" data-price="${finalRate}"></option>`);
                    }
                } else if (addProType === 'update') {
                    let existingRow = $(`#product_idN_${editRowId}`).length
                        ? $(`#product_idN_${editRowId}`)
                        : $('input[name="product_names[]"]').filter(function () {
                            return $(this).val().trim() === productName;
                        }).closest('tr');

                    if (existingRow.length) {
                        let newQuantity = quantity;
                        let newAmount = finalRate * newQuantity;

                        existingRow.replaceWith(generateRowHTML(rowId, escapedName, finalRate, newQuantity, newAmount, 'update'));
                    } else {
                        $('#purchase_product_tb').append(generateRowHTML(rowId, escapedName, finalRate, quantity, amount, 'update'));
                    }
                }

                // Reset fields
                $('#get_product_name').val('').focus();
                $('#product_id').val('');
                $('#get_final_rate').val('');
                $('#get_product_quantity').val('');
                $('#get_product_sale_price').val('');
                $('#add_pro_type').val('add');
                $('#edit_row_id').val('');
                $('#product_uid').val('');
                updateTotals();
                resetButton();

                function resetButton() {
                    $spinner.addClass('d-none');
                    $text.removeClass('d-none');
                    $btn.prop('disabled', false);
                }
            }, 300);
        });

        function generateRowHTML(rowId, productName, finalRate, quantity, amount, action) {
            return `
        <tr id="product_idN_${rowId}">
            <input type="hidden" data-price="${finalRate}" data-quantity="${quantity}" 
                   id="product_ids_${rowId}" class="product_ids" name="product_ids[]" value="${rowId}">
            <input type="hidden" id="product_quantites_${rowId}" name="product_quantites[]" value="${quantity}">
            <input type="hidden" id="product_rates_${rowId}" name="product_rates[]" value="${finalRate}">
            <input type="hidden" id="product_final_rates_${rowId}" name="product_final_rates[]" value="${finalRate}">
            <input type="hidden" id="product_names_${rowId}" name="product_names[]" value="${productName}">
            <input type="hidden" id="product_actions_${rowId}" name="product_actions[]" value="${action}">
            <input type="hidden" id="product_uids_${rowId}" name="product_uids[]" value="${rowId}">
            <td>${productName}</td>
            <td>${finalRate.toFixed(2)}</td>
            <td>${quantity}</td>
            <td>${amount.toFixed(2)}</td>
            <td>
                <button type="button" onclick="removeByid('#product_idN_${rowId}')" class="fa fa-trash text-danger"></button>
                <button type="button" onclick="editByid('${rowId}', \`${productName.replace(/`/g, '\\`')}\`, ${quantity}, ${finalRate}, '${rowId}')" class="fa fa-edit text-success ml-2"></button>
            </td>
        </tr>
    `;
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

            $('#product_total_amount').text(subtotal.toFixed(2));
            $('#product_grand_total_amount').text(grandTotal.toFixed(2));
        }

        window.removeByid = function (selector) {
            let row = $(selector);
            row.find('input[name="product_actions[]"]').val('delete');
            row.remove();
            updateTotals();
        };

        window.editByid = function (rowId, productName, quantity, finalRate, productUid) {
            $('#get_product_name').val(productName);
            $('#get_final_rate').val(parseFloat(finalRate));
            $('#get_product_quantity').val(quantity);
            $('#get_product_sale_price').val((parseFloat(finalRate) * quantity));
            $('#add_pro_type').val('update');
            $('#edit_row_id').val(rowId);
            $('#product_uid').val(productUid);
            updateSalePrice();
            // console.log('Editing:', { rowId, productName, quantity, finalRate, productUid }); 
        };

        window.getOrderTotal = function () {
            updateTotals();
        };

        $('#sale_btn').on('click', function (e) {
            e.preventDefault();

            const $btn = $(this);
            const $spinner = $btn.find('.spinner-border');
            const $text = $btn.find('.btn-text');

            if ($('#purchase_product_tb tr').length === 0) {
                alert('Please add at least one product to the order.');
                return;
            }

            if ($('#branch_id').val() === null || $('#branch_id').val() === '') {
                alert('Please select a branch.');
                return;
            }

            // Show loader
            $spinner.removeClass('d-none');
            $text.addClass('d-none');
            $btn.prop('disabled', true);

            let formData = $('#sale_order_fm').serializeArray();
            // console.log(formData);

            $.ajax({
                url: $('#sale_order_fm').attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    // Hide loader
                    resetButton();

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
                    resetButton();
                    console.log('Status:', status, 'Error:', error, 'Response:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Request Failed',
                        text: 'An error occurred while saving the order.'
                    });
                }
            });

            function resetButton() {
                $spinner.addClass('d-none');
                $text.removeClass('d-none');
                $btn.prop('disabled', false);
            }
        });
    });
</script>

</html>