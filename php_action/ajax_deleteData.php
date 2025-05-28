<?php
include_once 'db_connect.php';
require_once '../includes/functions.php';
/*Delete*/
if (!empty($_REQUEST['delete_id'])) {
	# code...
	$id = $_REQUEST['delete_id'];
	$table = $_REQUEST['table'];
	$fld = $_REQUEST['fld'];
	if (mysqli_query($dbc, "DELETE FROM $table WHERE $fld='$id'")) {
		$msg = "Data Has been deleted...";
		$sts = "success";
	} else {
		$msg = mysqli_error($dbc);
		$sts = "danger";
	}
	echo json_encode(['msg' => $msg, "sts" => $sts]);
}
if (isset($_REQUEST['delete_bymanually'])) {
	# code...
	$id = $_REQUEST['delete_bymanually'];
	$table = $_REQUEST['table'];
	$row = $_REQUEST['row'];

	if ($table == "vouchers") {
		$vouchers = fetchRecord($dbc, "vouchers", "voucher_id", $id);
		@deleteFromTable($dbc, "transactions", 'transaction_id', $vouchers['transaction_id1']);
		@deleteFromTable($dbc, "transactions", 'transaction_id', $vouchers['transaction_id2']);
		if (mysqli_query($dbc, "DELETE FROM vouchers WHERE voucher_id='$id'")) {
			$msg = "Data Has been deleted...";
			$sts = "success";
		} else {
			$msg = mysqli_error($dbc);
			$sts = "error";
		}
	} elseif ($table == "orders") {
		$orders = fetchRecord($dbc, 'orders', $row, $id);
		$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));

		if ($get_company['stock_manage'] == 1) {
			$proQ = get($dbc, "order_item WHERE order_id='" . $id . "' ");

			while ($proR = mysqli_fetch_assoc($proQ)) {
				$newqty = 0;
				$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $proR['product_id'] . "' "));
				$newqty = (int) $quantity_instock['quantity_instock'] + (int) $proR['quantity'];
				$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$newqty' WHERE product_id='" . $proR['product_id'] . "' ");
			}
		}
		deleteFromTable($dbc, "transactions", 'transaction_id', $orders['transaction_paid_id']);
		deleteFromTable($dbc, "transactions", 'transaction_id', $orders['transaction_id']);
		if (mysqli_query($dbc, "DELETE FROM orders WHERE $row='$id'")) {
			$msg = "Data Has been deleted...";
			$sts = "success";
		} else {
			$msg = mysqli_error($dbc);
			$sts = "error";
		}
	} elseif ($table == "purchase") {

		$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));

		if ($get_company['stock_manage'] == 1) {
			$proQ = get($dbc, "purchase_item WHERE purchase_id='" . $id . "' ");

			while ($proR = mysqli_fetch_assoc($proQ)) {
				$newqty = 0;
				$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $proR['product_id'] . "' "));
				$newqty = (int) $quantity_instock['quantity_instock'] - (int) $proR['quantity'];
				$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$newqty' WHERE product_id='" . $proR['product_id'] . "' ");
			}
		}
		$vouchers = fetchRecord($dbc, 'purchase', $row, $id);
		@deleteFromTable($dbc, "transactions", 'transaction_id', $vouchers['transaction_paid_id']);
		@deleteFromTable($dbc, "transactions", 'transaction_id', $vouchers['transaction_id']);
		if (mysqli_query($dbc, "DELETE FROM purchase WHERE $row='$id'")) {
			$msg = "Data Has been deleted...";
			$sts = "success";
		} else {
			$msg = mysqli_error($dbc);
			$sts = "error";
		}
	} elseif ($table == "gatepass") {

		$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));

		if ($get_company['stock_manage'] == 1) {

			$gatepass_data = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM gatepass WHERE gatepass_id = '$id'"));
			$from_branch = $gatepass_data['from_branch'];
			$to_branch = $gatepass_data['to_branch'];

			$proQ = mysqli_query($dbc, "SELECT * FROM gatepass_item WHERE gatepass_id='" . $id . "'");

			while ($proR = mysqli_fetch_assoc($proQ)) {
				$product_id = $proR['product_id'];
				$quantity = (int) $proR['quantity'];

				$fromStockRes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM inventory WHERE product_id='$product_id' AND branch_id='$from_branch'"));
				$toStockRes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM inventory WHERE product_id='$product_id' AND branch_id='$to_branch'"));

				$fromQty = isset($fromStockRes['quantity_instock']) ? (int) $fromStockRes['quantity_instock'] : 0;
				$toQty = isset($toStockRes['quantity_instock']) ? (int) $toStockRes['quantity_instock'] : 0;

				$new_fromQty = $fromQty + $quantity;
				$new_toQty = $toQty - $quantity;

				mysqli_query($dbc, "UPDATE inventory SET quantity_instock='$new_fromQty' WHERE product_id='$product_id' AND branch_id='$from_branch'");

				mysqli_query($dbc, "UPDATE inventory SET quantity_instock='$new_toQty' WHERE product_id='$product_id' AND branch_id='$to_branch'");
			}
		}

		$vouchers = fetchRecord($dbc, 'gatepass', $row, $id);
		@deleteFromTable($dbc, "transactions", 'transaction_id', $vouchers['transaction_paid_id']);
		@deleteFromTable($dbc, "transactions", 'transaction_id', $vouchers['transaction_id']);

		mysqli_query($dbc, "DELETE FROM gatepass_item WHERE gatepass_id='$id'");

		if (mysqli_query($dbc, "DELETE FROM gatepass WHERE $row='$id'")) {
			$msg = "Gatepass deleted successfully.";
			$sts = "success";
		} else {
			$msg = mysqli_error($dbc);
			$sts = "error";
		}
	} elseif ($table == "product") {
		if (mysqli_query($dbc, "UPDATE product SET status=0 WHERE $row='$id'")) {
			$msg = "Product Has been deleted...";
			$sts = "success";
		} else {
			$msg = mysqli_error($dbc);
			$sts = "error";
		}
	} elseif ($table == "customers") {
		if (mysqli_query($dbc, "UPDATE customers SET customer_status=0 WHERE $row='$id'")) {
			$msg = "Data Has been deleted...";
			$sts = "success";
		} else {
			$msg = mysqli_error($dbc);
			$sts = "error";
		}
	} elseif ($table == "quotations") {
		$quotation = fetchRecord($dbc, 'quotations', $row, $id);

		$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));

		if ($get_company['stock_manage'] == 1 && $quotation['is_delivery_note'] == '1') {
			$branch_id = $quotation['branch_id'];

			// Fetch all related items
			$itemsQ = mysqli_query($dbc, "SELECT * FROM quotation_item WHERE quotation_id = '$id'");
			while ($item = mysqli_fetch_assoc($itemsQ)) {
				$product_id = $item['product_id'];
				$quantity = (int) $item['quantity'];

				// Check if inventory record exists
				$invQ = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id = '$product_id' AND branch_id = '$branch_id'");
				if (mysqli_num_rows($invQ) > 0) {
					$inventory = mysqli_fetch_assoc($invQ);
					$newQty = (int) $inventory['quantity_instock'] + $quantity;
					mysqli_query($dbc, "UPDATE inventory SET quantity_instock = '$newQty', inventory_timestamp = NOW() WHERE inventory_id = '{$inventory['inventory_id']}'");
				} else {
					mysqli_query($dbc, "
					INSERT INTO inventory (branch_id, user_id, product_id, quantity_instock, inventory_timestamp)
					VALUES ('$branch_id', '{$quotation['user_id']}', '$product_id', '$quantity', NOW())
				");
				}
			}
		}

		// Delete quotation and its items
		if (mysqli_query($dbc, "DELETE FROM quotations WHERE $row='$id'")) {
			mysqli_query($dbc, "DELETE FROM quotation_item WHERE quotation_id='$id'");
			$msg = "Quotation and related items deleted, inventory updated.";
			$sts = "success";
		} else {
			$msg = mysqli_error($dbc);
			$sts = "error";
		}
	} elseif ($table == "lpo") {
		$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));

		if ($get_company['stock_manage'] == 1) {
			$proQ = get($dbc, "lpo_item WHERE lpo_id='" . $id . "' ");

			// while ($proR=mysqli_fetch_assoc($proQ)) {
			// 	$newqty=0;
			// 	$quantity_instock=mysqli_fetch_assoc(mysqli_query($dbc,"SELECT quantity_instock FROM  product WHERE product_id='".$proR['product_id']."' "));
			// 	$newqty=(int)$quantity_instock['quantity_instock']-(int)$proR['quantity'];
			// 	$quantity_update=mysqli_query($dbc,"UPDATE product SET  quantity_instock='$newqty' WHERE product_id='".$proR['product_id']."' ");


			// }
		}
		$vouchers = fetchRecord($dbc, 'lpo', $row, $id);
		if (mysqli_query($dbc, "DELETE FROM lpo WHERE $row='$id'")) {
			$msg = "Data Has been deleted...";
			$sts = "success";
		} else {
			$msg = mysqli_error($dbc);
			$sts = "error";
		}
	} else {
		if (deleteFromTable($dbc, $table, $row, $id)) {
			$msg = $table . " Has been deleted...";
			$sts = "success";
		} else {
			$msg = mysqli_error($dbc);
			$sts = "error";
		}
	}



	echo json_encode(['msg' => $msg, "sts" => $sts]);
}

// Approve Gatepass

if (isset($_REQUEST['approve_bymanually'])) {
	$gatepass_id = $_REQUEST['approve_bymanually'];

	$gatepass = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM gatepass WHERE gatepass_id='$gatepass_id'"));
	while ($gatepass) {
		$gatepass_item = mysqli_query($dbc, "SELECT * FROM gatepass_item WHERE gatepass_id='$gatepass_id'");
		while ($item = mysqli_fetch_assoc($gatepass_item)) {
			$product_id = $item['product_id'];
			$quantity = $item['quantity'];
			$from_branch = $item['from_branch'];
			$to_branch = $item['to_branch'];

			$check_from = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='$product_id' AND branch_id='$from_branch'");
			if (mysqli_num_rows($check_from) > 0) {
				mysqli_query($dbc, "UPDATE inventory SET quantity_instock = quantity_instock - $quantity WHERE product_id='$product_id' AND branch_id='$from_branch'");
			} else {
				mysqli_query($dbc, "INSERT INTO inventory (product_id, branch_id, quantity_instock) VALUES ('$product_id', '$from_branch', -$quantity)");
			}

			$check_to = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='$product_id' AND branch_id='$to_branch'");
			if (mysqli_num_rows($check_to) > 0) {
				mysqli_query($dbc, "UPDATE inventory SET quantity_instock = quantity_instock + $quantity WHERE product_id='$product_id' AND branch_id='$to_branch'");
			} else {
				mysqli_query($dbc, "INSERT INTO inventory (product_id, branch_id, quantity_instock) VALUES ('$product_id', '$to_branch', $quantity)");
			}
		}
		break;
	}

	$update_data = [
		'stock_status' => 1,
	];

	if (update_data($dbc, 'gatepass', $update_data, 'gatepass_id', $gatepass_id)) {
		$msg = "Gatepass has been approved successfully.";
		$sts = "success";
	} else {
		$msg = mysqli_error($dbc);
		$sts = "danger";
	}

	echo json_encode(['msg' => $msg, 'sts' => $sts]);
}
