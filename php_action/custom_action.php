<?php
require_once '../php_action/db_connect.php';
require_once '../includes/functions.php';
if (isset($_REQUEST['add_manually_user'])) {
	$data = [
		'customer_name' => @$_REQUEST['customer_name'],
		'customer_phone' => @$_REQUEST['customer_phone'],
		'customer_email' => @$_REQUEST['customer_email'],
		'customer_address' => @$_REQUEST['customer_address'],
		'customer_type' => @$_REQUEST['customer_type'],
		'customer_status' => @$_REQUEST['customer_status'],
		'customer_type' => @$_REQUEST['add_manually_user'],
		'customer_limit' => @$_REQUEST['check_amount'],
		'representatives' => @$_REQUEST['representative_values'],
		'branch_id' => @$_REQUEST['branch_id'],
	];
	if ($_REQUEST['customer_id'] == "") {

		if (insert_data($dbc, "customers", $data)) {


			$res = ['msg' => ucfirst($_REQUEST['add_manually_user']) . " Added Successfully", 'sts' => 'success'];
		} else {

			$res = ['msg' => mysqli_error($dbc), 'sts' => 'error'];
		}
	} else {
		if (update_data($dbc, "customers", $data, "customer_id", $_REQUEST['customer_id'])) {


			$res = ['msg' => ucfirst($_REQUEST['add_manually_user']) . " Updated Successfully", 'sts' => 'success'];
		} else {

			$res = ['msg' => mysqli_error($dbc), 'sts' => 'error'];
		}
	}
	echo json_encode($res);
}

if (isset($_REQUEST['new_voucher_date'])) {

	if ($_REQUEST['voucher_id'] == "") {
		if ($_REQUEST['voucher_group'] == "general_voucher") {
			$data = [
				'customer_id1' => @$_REQUEST['voucher_from_account'],
				'customer_id2' => @$_REQUEST['voucher_to_account'],
				'voucher_date' => @$_REQUEST['new_voucher_date'],
				'voucher_hint' => @$_REQUEST['voucher_hint'],
				'voucher_type' => @$_REQUEST['voucher_type'],
				'voucher_amount' => @$_REQUEST['voucher_debit'],
				'voucher_group' => @$_REQUEST['voucher_group'],
				'td_check_no' => @$_REQUEST['td_check_no'],
				'voucher_bank_name' => @$_REQUEST['voucher_bank_name'],
				'td_check_date' => @$_REQUEST['td_check_date'],
				'check_type' => @$_REQUEST['check_type'],
				'addby_user_id' => @$_SESSION['userId'],
			];
		} else {
			$data = [
				'customer_id1' => @$_REQUEST['voucher_from_account'],
				'customer_id2' => @$_REQUEST['voucher_to_account'],
				'voucher_date' => @$_REQUEST['new_voucher_date'],
				'voucher_hint' => @$_REQUEST['voucher_hint'],
				'voucher_type' => @$_REQUEST['voucher_type'],
				'voucher_amount' => @$_REQUEST['voucher_debit'],
				'voucher_group' => @$_REQUEST['voucher_group'],
				'addby_user_id' => @$_SESSION['userId'],
			];
		}
		if (insert_data($dbc, "vouchers", $data)) {
			$last_id = mysqli_insert_id($dbc);
			if ($_REQUEST['voucher_group'] == "expense_voucher") {
				$voucher_to_account = fetchRecord($dbc, "customers", "customer_id", $_REQUEST['voucher_to_account']);
				$budget = [
					'budget_amount' => @$_REQUEST['voucher_debit'],
					'budget_type' => "expense",
					'budget_date' => $_REQUEST['new_voucher_date'],
					'voucher_id' => $last_id,
					'voucher_type' => @$_REQUEST['voucher_type'],
					'budget_name' => @"expense added to " . @$voucher_to_account['customer_name'],
				];
				insert_data($dbc, "budget", $budget);
			} elseif ($_REQUEST['voucher_group'] == "general_voucher" and !empty($_REQUEST['td_check_no'])) {
				$data_checks = [
					'check_no' => $_REQUEST['td_check_no'],
					'check_bank_name' => $_REQUEST['voucher_bank_name'],
					'check_expiry_date' => $_REQUEST['td_check_date'],
					'check_type' => $_REQUEST['check_type'],
					'voucher_id' => $last_id,
					'check_status' => 0,
				];
				insert_data($dbc, "checks", $data_checks);
			}


			$debit = [
				'debit' => @$_REQUEST['voucher_debit'],
				'credit' => 0,
				'customer_id' => @$_REQUEST['voucher_from_account'],
				'transaction_from' => 'voucher',
				'transaction_type' => @$_REQUEST['voucher_type'],
				'transaction_remarks' => @$_REQUEST['voucher_hint'],
				'transaction_date' => @$_REQUEST['new_voucher_date'],
			];
			insert_data($dbc, "transactions", $debit);
			$transaction_id1 = mysqli_insert_id($dbc);
			$credit = [
				'credit' => @$_REQUEST['voucher_debit'],
				'debit' => 0,
				'customer_id' => @$_REQUEST['voucher_to_account'],
				'transaction_from' => 'voucher',
				'transaction_type' => @$_REQUEST['voucher_type'],
				'transaction_remarks' => @$_REQUEST['voucher_hint'],
				'transaction_date' => @$_REQUEST['new_voucher_date'],
			];

			insert_data($dbc, "transactions", $credit);
			$transaction_id2 = mysqli_insert_id($dbc);
			$newData = ['transaction_id1' => $transaction_id1, 'transaction_id2' => $transaction_id2];
			if (update_data($dbc, "vouchers", $newData, "voucher_id", $last_id)) {
				$res = ['msg' => "Voucher Added Successfully", 'sts' => 'success', 'voucher_id' => base64_encode($last_id)];
			} else {
				$res = ['msg' => mysqli_error($dbc), 'sts' => 'error'];
			}
		} else {

			$res = ['msg' => mysqli_error($dbc), 'sts' => 'error'];
		}
	} else {
		if ($_REQUEST['voucher_group'] == "general_voucher") {
			$data = [
				'customer_id1' => @$_REQUEST['voucher_from_account'],
				'customer_id2' => @$_REQUEST['voucher_to_account'],
				'voucher_date' => @$_REQUEST['new_voucher_date'],
				'voucher_hint' => @$_REQUEST['voucher_hint'],
				'voucher_type' => @$_REQUEST['voucher_type'],
				'voucher_amount' => @$_REQUEST['voucher_debit'],
				'voucher_group' => @$_REQUEST['voucher_group'],
				'editby_user_id' => @$_SESSION['userId'],
				'td_check_no' => @$_REQUEST['td_check_no'],
				'voucher_bank_name' => @$_REQUEST['voucher_bank_name'],
				'check_type' => @$_REQUEST['check_type'],
				'td_check_date' => @$_REQUEST['td_check_date'],
			];
		} else {
			$data = [
				'customer_id1' => @$_REQUEST['voucher_from_account'],
				'customer_id2' => @$_REQUEST['voucher_to_account'],
				'voucher_date' => @$_REQUEST['new_voucher_date'],
				'voucher_hint' => @$_REQUEST['voucher_hint'],
				'voucher_type' => @$_REQUEST['voucher_type'],
				'voucher_amount' => @$_REQUEST['voucher_debit'],
				'voucher_group' => @$_REQUEST['voucher_group'],
				'editby_user_id' => @$_SESSION['userId'],
			];
		}
		if (update_data($dbc, "vouchers", $data, "voucher_id", $_REQUEST['voucher_id'])) {
			$last_id = $_REQUEST['voucher_id'];

			$transactions = fetchRecord($dbc, "vouchers", "voucher_id", $_REQUEST['voucher_id']);


			if ($_REQUEST['voucher_group'] == "expense_voucher") {
				$voucher_to_account = fetchRecord($dbc, "customers", "customer_id", $_REQUEST['voucher_to_account']);
				$budget = [
					'budget_amount' => @$_REQUEST['voucher_debit'],
					'budget_type' => "expense",
					'budget_date' => $_REQUEST['new_voucher_date'],
					'voucher_id' => $last_id,
					'voucher_type' => @$_REQUEST['voucher_type'],
					'budget_name' => @"expense added to " . @$voucher_to_account['customer_name'],
				];

				update_data($dbc, "budget", $budget, "voucher_id", $_REQUEST['voucher_id']);
			} elseif ($_REQUEST['voucher_group'] == "general_voucher") {
				$data_checks = [
					'check_no' => $_REQUEST['td_check_no'],
					'check_bank_name' => $_REQUEST['voucher_bank_name'],
					'check_expiry_date' => $_REQUEST['td_check_date'],
					'check_type' => $_REQUEST['check_type'],
					'voucher_id' => $last_id,
				];
				update_data($dbc, "checks", $data_checks, "voucher_id", $_REQUEST['voucher_id']);
			}

			$debit = [
				'debit' => @$_REQUEST['voucher_debit'],
				'credit' => 0,
				'customer_id' => @$_REQUEST['voucher_from_account'],
				'transaction_from' => 'voucher',
				'transaction_type' => @$_REQUEST['voucher_type'],
				'transaction_remarks' => @$_REQUEST['voucher_hint'],
				'transaction_date' => @$_REQUEST['new_voucher_date'],
			];

			update_data($dbc, "transactions", $debit, "transaction_id", $transactions['transaction_id1']);

			$credit = [
				'credit' => @$_REQUEST['voucher_debit'],
				'debit' => 0,
				'customer_id' => @$_REQUEST['voucher_to_account'],
				'transaction_from' => 'voucher',
				'transaction_type' => @$_REQUEST['voucher_type'],
				'transaction_remarks' => @$_REQUEST['voucher_hint'],
				'transaction_date' => @$_REQUEST['new_voucher_date'],
			];

			update_data($dbc, "transactions", $credit, "transaction_id", $transactions['transaction_id2']);

			$res = ['msg' => "Voucher Updated Successfully", 'sts' => 'success', 'voucher_id' => base64_encode($last_id)];
		} else {

			$res = ['msg' => mysqli_error($dbc), 'sts' => 'error'];
		}
	}
	echo json_encode($res);
}
if (isset($_REQUEST['new_sin_voucher_date'])) {
	if (!empty($_REQUEST['voucher_debit'])) {
		$amount = $_REQUEST['voucher_debit'];
	} else {
		$amount = $_REQUEST['voucher_credit'];
	}
	if ($_REQUEST['voucher_id'] == "") {
		$data = [
			'customer_id1' => @$_REQUEST['voucher_from_account'],
			'voucher_date' => @$_REQUEST['new_sin_voucher_date'],
			'voucher_hint' => @$_REQUEST['voucher_hint'],
			'voucher_amount' => $amount,
			'voucher_group' => @$_REQUEST['voucher_group'],
			'addby_user_id' => @$_SESSION['userId'],
		];
		if (insert_data($dbc, "vouchers", $data)) {
			$last_id = mysqli_insert_id($dbc);

			if (!empty($_REQUEST['voucher_debit'])) {
				$debit = [
					'debit' => $amount,
					'credit' => 0,
					'customer_id' => @$_REQUEST['voucher_from_account'],
					'transaction_from' => 'voucher',
					'transaction_type' => "single_voucher",
					'transaction_remarks' => @$_REQUEST['voucher_hint'],
					'transaction_date' => @$_REQUEST['new_sin_voucher_date'],
				];
				insert_data($dbc, "transactions", $debit);
			} else {
				$credit = [
					'credit' => $amount,
					'debit' => 0,
					'customer_id' => @$_REQUEST['voucher_from_account'],
					'transaction_from' => 'voucher',
					'transaction_type' => "single_voucher",
					'transaction_remarks' => @$_REQUEST['voucher_hint'],
					'transaction_date' => @$_REQUEST['new_sin_voucher_date'],
				];
				insert_data($dbc, "transactions", $credit);
			}



			$transaction_id1 = mysqli_insert_id($dbc);

			$newData = ['transaction_id1' => $transaction_id1];
			if (update_data($dbc, "vouchers", $newData, "voucher_id", $last_id)) {
				$res = ['msg' => "Voucher Added Successfully", 'sts' => 'success', 'voucher_id' => base64_encode($last_id)];
			} else {
				$res = ['msg' => mysqli_error($dbc), 'sts' => 'error'];
			}
		} else {

			$res = ['msg' => mysqli_error($dbc), 'sts' => 'error'];
		}
	} else {
		$data = [
			'customer_id1' => @$_REQUEST['voucher_from_account'],
			'voucher_date' => @$_REQUEST['new_sin_voucher_date'],
			'voucher_hint' => @$_REQUEST['voucher_hint'],
			'voucher_amount' => $amount,
			'voucher_group' => @$_REQUEST['voucher_group'],
			'editby_user_id' => @$_SESSION['userId'],
		];

		if (update_data($dbc, "vouchers", $data, "voucher_id", $_REQUEST['voucher_id'])) {
			$last_id = $_REQUEST['voucher_id'];

			$transactions = fetchRecord($dbc, "vouchers", "voucher_id", $_REQUEST['voucher_id']);

			if (!empty($_REQUEST['voucher_debit'])) {
				$debit = [
					'debit' => @$_REQUEST['voucher_debit'],
					'credit' => 0,
					'customer_id' => @$_REQUEST['voucher_from_account'],
					'transaction_from' => 'voucher',
					'transaction_type' => "single_voucher",
					'transaction_remarks' => @$_REQUEST['voucher_hint'],
					'transaction_date' => @$_REQUEST['new_sin_voucher_date'],
				];
				update_data($dbc, "transactions", $debit, "transaction_id", $transactions['transaction_id1']);
			} else {
				$credit = [
					'credit' => @$_REQUEST['voucher_credit'],
					'debit' => 0,
					'customer_id' => @$_REQUEST['voucher_from_account'],
					'transaction_from' => 'voucher',
					'transaction_type' => "single_voucher",
					'transaction_remarks' => @$_REQUEST['voucher_hint'],
					'transaction_date' => @$_REQUEST['new_sin_voucher_date'],
				];
				update_data($dbc, "transactions", $credit, "transaction_id", $transactions['transaction_id1']);;
			}


			$res = ['msg' => "Voucher Updated Successfully", 'sts' => 'success', 'voucher_id' => base64_encode($last_id)];
		} else {

			$res = ['msg' => mysqli_error($dbc), 'sts' => 'error'];
		}
	}
	echo json_encode($res);
}
if (!empty($_REQUEST['action']) and $_REQUEST['action'] == "product_module") {
	$purchase_rate = $total = 0;
	$category_price = fetchRecord($dbc, "categories", "categories_id", $_REQUEST['category_id']);

	$total = (float)@$_REQUEST['product_mm'] * (float)@$_REQUEST['product_inch'] * (float)@$_REQUEST['product_meter'];
	$purchase_rate = ($total * (float)@$category_price['category_purchase']) / 54;
	$purchase_rate = round($purchase_rate);

	$brand_id = $_REQUEST['brand_id'];
	if (empty($brand_id) && isset($_REQUEST['new_brand_name'])) {
		$newBrandName = $_REQUEST['new_brand_name'];
		$newBrandStatus = 1;

		$insertBrandQuery = "INSERT INTO brands (brand_name, brand_status) VALUES ('$newBrandName', $newBrandStatus)";
		if (mysqli_query($dbc, $insertBrandQuery)) {
			$brand_id = mysqli_insert_id($dbc);
		} else {
			echo json_encode([
				"msg" => "Failed to add new brand: " . mysqli_error($dbc),
				"sts" => "error"
			]);
			exit;
		}
	}

	$category_id = $_REQUEST['category_id'];
	if (empty($category_id) && isset($_REQUEST['new_category_name'])) {
		$newCategoryName = $_REQUEST['new_category_name'];
		$newCategoryStatus = 1;

		$insertCategoryQuery = "INSERT INTO categories (categories_name, categories_status) VALUES ('$newCategoryName', $newCategoryStatus)";
		if (mysqli_query($dbc, $insertCategoryQuery)) {
			$category_id = mysqli_insert_id($dbc);
		} else {
			echo json_encode([
				"msg" => "Failed to add new category: " . mysqli_error($dbc),
				"sts" => "error"
			]);
			exit;
		}
	}

	$data_array = [
		'product_name' => $_REQUEST['product_name'],
		'product_code' => @$_REQUEST['product_code'],
		'brand_id' => $brand_id,
		'category_id' => $category_id,
		'product_mm' => @$_REQUEST['product_mm'],
		'product_inch' => @$_REQUEST['product_inch'],
		'product_meter' => @$_REQUEST['product_meter'],
		'current_rate' => @$_REQUEST['current_rate'],
		'final_rate' => @$_REQUEST['final_rate'],
		'product_description' => @$_REQUEST['product_description'],
		't_days' => @$_REQUEST['t_days'],
		'f_days' => @$_REQUEST['f_days'],
		'alert_at' => @$_REQUEST['alert_at'],
		'availability' => @$_REQUEST['availability'],
		'purchase_rate' => @$_REQUEST['purchase_rate'],
		'status' => 1,
	];

	if ($_REQUEST['product_id'] == "") {
		if (insert_data($dbc, "product", $data_array)) {
			$last_id = mysqli_insert_id($dbc);

			if (@$_FILES['product_image']['tmp_name']) {
				upload_pic(@$_FILES['product_image'], '../img/uploads/');
				$product_image = $_SESSION['pic_name'];
				$data_image = [
					'product_image' => $product_image,
				];
				update_data($dbc, "product", $data_image, "product_id", $last_id);
			}

			$response = [
				"msg" => "Product Has Been Added",
				"sts" => "success",
				"id" => base64_encode($last_id),
				"link" => base64_encode('add_stock'),
			];
		} else {
			$response = [
				"msg" => mysqli_error($dbc),
				"sts" => "error"
			];
		}
	} else {
		// Update existing product
		if (update_data($dbc, "product", $data_array, "product_id", base64_decode($_REQUEST['product_id']))) {
			$last_id = $_REQUEST['product_id'];

			if (@$_FILES['product_image']['tmp_name']) {
				upload_pic($_FILES['product_image'], '../img/uploads/');
				$product_image = $_SESSION['pic_name'];
				$data_image = [
					'product_image' => $product_image,
				];
				update_data($dbc, "product", $data_image, "product_id", $last_id);
			}

			$response = [
				"msg" => "Product Updated",
				"sts" => "success",
				"id" => base64_encode($last_id),
				"link" => base64_encode('add_stock'),
			];
		} else {
			$response = [
				"msg" => mysqli_error($dbc),
				"sts" => "error"
			];
		}
	}
	echo json_encode($response);
}


if (!empty($_REQUEST['action']) and $_REQUEST['action'] == "inventory_module") {

	$data_array = [
		'product_name' => $_REQUEST['product_name'],
		'product_code' => rand(),
		'brand_id' => 0,
		'category_id' => 0,
		'current_rate' => @$_REQUEST['current_rate'],
		'alert_at' => 5,
		'availability' => 1,
		'purchase_rate' => $_REQUEST['current_rate'],
		'status' => 1,
		'inventory' => 1,
	];
	if ($_REQUEST['product_id'] == "") {

		if (insert_data($dbc, "product", $data_array)) {
			$last_id = mysqli_insert_id($dbc);

			$response = [
				"msg" => "Inventory Product Has Been Added",
				"sts" => "success",
			];
		} else {
			$response = [
				"msg" => mysqli_error($dbc),
				"sts" => "error"
			];
		}
	} else {
		if (update_data($dbc, "product", $data_array, "product_id", base64_decode($_REQUEST['product_id']))) {
			$last_id = $_REQUEST['product_id'];



			$response = [
				"msg" => "Product Updated",
				"sts" => "success",
			];
		} else {
			$response = [
				"msg" => mysqli_error($dbc),
				"sts" => "error"
			];
		}
	}
	echo json_encode($response);
}
if (isset($_REQUEST['get_products_list'])) {

	if ($_REQUEST['type'] == "code") {
		$q = mysqli_query($dbc, "SELECT * FROM product WHERE product_code LIKE '%" . $_REQUEST['get_products_list'] . "%' AND status=1 ");
		if (mysqli_num_rows($q) > 0) {
			while ($r = mysqli_fetch_assoc($q)) {
				$getBrand = fetchRecord($dbc, "brands", "brand_id", $r['brand_id']);
				$getCat = fetchRecord($dbc, "categories", "categories_id", $r['category_id']);
				echo '<option value="' . $r['product_id'] . '">' . $r["product_name"] . ' - ' . @$getBrand["brand_name"] . ' </option>';
			}
		} else {
			echo '<option value="">Not Found</option>';
		}
	}
	if ($_REQUEST['type'] == "product") {
		$q = mysqli_query($dbc, "SELECT * FROM product WHERE product_id='" . $_REQUEST['get_products_list'] . "' AND status=1 ");
		if (mysqli_num_rows($q) > 0) {
			$r = mysqli_fetch_assoc($q);
			echo $r['product_code'];
		}
	}
}
if (!empty($_REQUEST['getPrice'])) {
	if ($_REQUEST['type'] == "product") {
		$record = fetchRecord($dbc, "product", "product_id", $_REQUEST['getPrice']);
		// $inventory = fetchRecord($dbc, "inventory", "product_id", $_REQUEST['getPrice']);
		if (isset($_REQUEST['branch_id'])) {
			$branch_id = $_REQUEST['branch_id'];
		} else {
			$branch_id = $_SESSION['branch_id'];
		}
		$product_id = $_REQUEST['getPrice'];
		$query = "SELECT * FROM inventory WHERE product_id='$product_id' AND branch_id='$branch_id'";
		$inventory = mysqli_fetch_assoc(mysqli_query($dbc, $query));
	} else {
		$record = fetchRecord($dbc, "product", "product_code", $_REQUEST['getPrice']);
		$inventory = fetchRecord($dbc, "inventory", "product_id", $_REQUEST['getPrice']);
	}
	if ($_REQUEST['price_type'] == "purchase") {
		$price = @$record['purchase_rate'];
	} else {
		$price = @$record['current_rate'];
	}





	$response = [
		"price" => isset($price) ? $price : 0,
		"qty" => @(float)$inventory['quantity_instock'],
		"description" => $record['product_description'],
		"final_rate" => $record['final_rate'],
		"sts" => "success",
		"type" => @$_REQUEST['credit_sale_type'],
	];


	echo json_encode($response);
}

/*---------------------- cash sale-order   -------------------------------------------------------------------*/
if (isset($_REQUEST['sale_order_client_name']) && empty($_REQUEST['order_return'])) {
	$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));
	if (!empty($_REQUEST['product_ids'])) {
		# code...
		//print_r(json_encode($_REQUEST));
		$total_ammount = $total_grand = 0;

		$data = [
			'order_date' => $_REQUEST['order_date'],
			'client_name' => $_REQUEST['sale_order_client_name'],
			'client_contact' => $_REQUEST['client_contact'],
			'paid' => @$_REQUEST['paid_ammount'],
			'payment_account' => @$_REQUEST['payment_account'],
			'payment_type' => 'cash',
			'vehicle_no' => @$_REQUEST['vehicle_no'],
			'order_narration' => @$_REQUEST['order_narration'],
			'freight' => @$_REQUEST['freight'],
			'branch_id' => $_REQUEST['branch_id'],
		];

		if ($_REQUEST['product_order_id'] == "") {

			if (insert_data($dbc, 'orders', $data)) {
				$last_id = mysqli_insert_id($dbc);
				if (!empty($_FILES['order_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['order_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['order_file']['tmp_name'], $uploadPath)) {
						$data = [
							'order_file' => $fileName,
						];

						update_data($dbc, "orders", $data, "order_id", $last_id);
					}
				}
				$paidAmount = @(float)$_REQUEST['paid_ammount'];
				if ($paidAmount > 0) {
					$debit = [
						'credit' => @$_REQUEST['paid_ammount'],
						'debit' => 0,
						'customer_id' => @$_REQUEST['payment_account'],
						'transaction_from' => 'Sale',
						'transaction_type' => "cash_in_hand",
						'transaction_remarks' => "cash_sale by order id#" . $last_id,
						'transaction_date' => $_REQUEST['order_date'],
					];
					insert_data($dbc, 'transactions', $debit);
					$transaction_paid_id = mysqli_insert_id($dbc);
				}

				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {

					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$total = (float)$product_quantites * $product_rates;
					$total_ammount += (float)$total;
					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'final_rate' => $_REQUEST['product_final_rates'][$x],
						'rate' => $product_rates,
						'total' => $total,
						'order_id' => $last_id,
						'quantity' => $product_quantites,
						'product_detail' => @$_REQUEST['product_detail'][$x],
						'order_item_status' => 1,
						'branch_id' => $_REQUEST['branch_id'],
					];
					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
						@$qty = (float)$quantity_instock['quantity_instock'] - (float)$product_quantites;
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] - $product_quantites;
							if ($inventory_qty <= 0) {
								$msg = "Not Efficient Inventory";
								$sts = 'error';

								echo json_encode(['msg' => $msg, 'sts' => $sts]);
								exit;
							}
							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						} else {
							$msg = "Not Efficient Inventory";
							$sts = 'error';
							echo json_encode(['msg' => $msg, 'sts' => $sts]);
							exit;
						}
					}
					insert_data($dbc, 'order_item', $order_items);

					$x++;
				} //end of foreach
				$total_grand =  $total_ammount - $_REQUEST['ordered_discount'];

				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];

				if ($due_amount > 0) {
					$payment_status = 0; //pending

				} else {
					$payment_status = 1; //completed

				}
				$newOrder = [
					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'payment_status' => $payment_status,
					'due' => $due_amount,
					'order_status' => 1,
					'transaction_paid_id' => @$transaction_paid_id,
				];
				if (update_data($dbc, 'orders', $newOrder, 'order_id', $last_id)) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Order Has been Added";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		} else {
			if (update_data($dbc, 'orders', $data, 'order_id', $_REQUEST['product_order_id'])) {
				$last_id = $_REQUEST['product_order_id'];
				if (!empty($_FILES['order_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['order_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['order_file']['tmp_name'], $uploadPath)) {
						$data = [
							'order_file' => $fileName,
						];

						update_data($dbc, "orders", $data, "order_id", $last_id);
					}
				}

				if ($get_company['stock_manage'] == 1) {
					$proQ = get($dbc, "order_item WHERE order_id='" . $last_id . "' ");

					while ($proR = mysqli_fetch_assoc($proQ)) {
						$newqty = 0;
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $proR['product_id'] . "' "));
						$newqty = (float)$quantity_instock['quantity_instock'] + (float)$proR['quantity'];
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$newqty' WHERE product_id='" . $proR['product_id'] . "' ");

						$branch_id = $_SESSION['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $proR['product_id'] . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {
							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] + $proR['quantity'];
							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $proR['product_id'] . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						} else {
							$msg = "Not Efficient Inventory";
							$sts = 'error';
							echo json_encode(['msg' => $msg, 'sts' => $sts]);
							exit;
						}
					}


					deleteFromTable($dbc, "order_item", 'order_id', $_REQUEST['product_order_id']);

					$x = 0;
					foreach ($_REQUEST['product_ids'] as $key => $value) {
						$total = $qty = 0;
						$product_quantites = (float)$_REQUEST['product_quantites'][$x];
						$product_rates = (float)$_REQUEST['product_rates'][$x];
						$total = $product_quantites * $product_rates;
						$total_ammount += (float)$total;

						$order_items = [
							'product_id' => $_REQUEST['product_ids'][$x],
							'rate' => $product_rates,
							'total' => $total,
							'order_id' => $_REQUEST['product_order_id'],
							'quantity' => $product_quantites,
							'product_detail' => $_REQUEST['product_detail'][$x],
							'order_item_status' => 1,
							'branch_id' => $_REQUEST['branch_id'],
						];
						if ($get_company['stock_manage'] == 1) {
							$product_id = $_REQUEST['product_ids'][$x];
							$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
							$qty = (float)$quantity_instock['quantity_instock'] - $product_quantites;
							$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");

							$branch_id = $_SESSION['branch_id'];
							$user_id = $_SESSION['user_id'];
							$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
							if (mysqli_num_rows($inventory) > 0) {
								$inventory = mysqli_fetch_assoc($inventory);
								$inventory_qty = (float)$inventory['quantity_instock'] - $product_quantites;
								if ($inventory_qty <= 0) {
									$msg = "Not Efficient Inventory";
									$sts = 'error';

									echo json_encode(['msg' => $msg, 'sts' => $sts]);
									exit;
								}
								$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
							} else {
								$msg = "Not Efficient Inventory";
								$sts = 'error';
								echo json_encode(['msg' => $msg, 'sts' => $sts]);
								exit;
							}
						}
						//update_data($dbc,'order_item', $order_items , 'order_id',$_REQUEST['product_order_id']);
						insert_data($dbc, 'order_item', $order_items);

						$x++;
					} //end of foreach
					$total_grand =  $total_ammount - $_REQUEST['ordered_discount'];
					$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];
					if ($due_amount > 0) {
						$payment_status = 0; //pending

					} else {
						$payment_status = 1; //completed

					}
					$newOrder = [

						'total_amount' => $total_ammount,
						'discount' => $_REQUEST['ordered_discount'],
						'grand_total' => $total_grand,
						'payment_status' => $payment_status,
						'due' => $due_amount,
					];
					$paidAmount = @(float)$_REQUEST['paid_ammount'];
					if ($paidAmount > 0) {
						$credit1 = [
							'credit' => @$_REQUEST['paid_ammount'],
							'debit' => 0,
							'customer_id' => @$_REQUEST['payment_account'],

						];
						$transactions = fetchRecord($dbc, "orders", "order_id", $_REQUEST['product_order_id']);
						update_data($dbc, "transactions", $credit1, "transaction_id", $transactions['transaction_paid_id']);
					}
					if (update_data($dbc, 'orders', $newOrder, 'order_id', $_REQUEST['product_order_id'])) {
						# code...
						//echo "<script>alert('company Updated....!')</script>";
						$msg = "Data Has been Updated";
						$sts = 'success';
					} else {
						$msg = mysqli_error($dbc);
						$sts = "danger";
					}
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			}
		}
	} else {
		$msg = "Please Add Any Product";
		$sts = 'error';
	}
	echo json_encode(['msg' => $msg, 'sts' => $sts, 'order_id' => @$last_id, 'type' => "order", 'subtype' => $_REQUEST['payment_type']]);
}
/*---------------------- credit sale-order   -------------------------------------------------------------------*/
if (isset($_REQUEST['credit_order_client_name']) && empty($_REQUEST['quotation_form']) && empty($_REQUEST['order_return'])) {
	$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));
	if (!empty($_REQUEST['product_ids'])) {
		# code...
		$total_ammount = $total_grand = 0;

		$data = [
			'order_date' => $_REQUEST['order_date'],
			'client_name' => $_REQUEST['credit_order_client_name'],
			'client_contact' => $_REQUEST['client_contact'],
			'paid' => @$_REQUEST['paid_ammount'],
			'order_narration' => @$_REQUEST['order_narration'],
			'payment_account' => @$_REQUEST['payment_account'],
			'customer_account' => @$_REQUEST['customer_account'],
			'payment_type' => 'credit',
			'credit_sale_type' => @$_REQUEST['credit_sale_type'],
			'vehicle_no' => @$_REQUEST['vehicle_no'],
			'return_days' => @$_REQUEST['return_days'],
			'freight' => @$_REQUEST['freight'],
			'branch_id' => $_REQUEST['branch_id'],
		];
		//'payment_status'=>1,
		if ($_REQUEST['product_order_id'] == "") {

			if (insert_data($dbc, 'orders', $data)) {
				$last_id = mysqli_insert_id($dbc);
				if (!empty($_FILES['order_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['order_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['order_file']['tmp_name'], $uploadPath)) {
						$data = [
							'order_file' => $fileName,
						];

						update_data($dbc, "orders", $data, "order_id", $last_id);
					}
				}

				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {
					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$total = $product_quantites * $product_rates;
					$total_ammount += (float)$total;
					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'final_rate' => $_REQUEST['product_final_rates'][$x],
						'rate' => $product_rates,
						'total' => $total,
						'order_id' => $last_id,
						'quantity' => $product_quantites,
						'product_detail' => $_REQUEST['product_detail'][$x],
						'order_item_status' => 1,
						'branch_id' => $_REQUEST['branch_id'],
					];

					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
						$qty = (float)$quantity_instock['quantity_instock'] - $product_quantites;
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] - $product_quantites;
							if ($inventory_qty <= 0) {
								$msg = "Not Efficient Inventory";
								$sts = 'error';

								echo json_encode(['msg' => $msg, 'sts' => $sts]);
								exit;
							}
							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						} else {
							$msg = "Not Efficient Inventory";
							$sts = 'error';
							echo json_encode(['msg' => $msg, 'sts' => $sts]);
							exit;
						}
					}
					insert_data($dbc, 'order_item', $order_items);

					$x++;
				} //end of foreach

				$total_grand =  $total_ammount - $_REQUEST['ordered_discount'];
				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];

				$credit = [
					'credit' => $due_amount,
					'debit' => 0,
					'customer_id' => @$_REQUEST['customer_account'],
					'transaction_from' => 'Sale',
					'transaction_type' => "credit_sale",
					'transaction_remarks' => "credit_sale by order id#" . $last_id,
					'transaction_date' => $_REQUEST['order_date'],
				];
				if ($due_amount > 0) {
					$payment_status = 0; //pending
					insert_data($dbc, 'transactions', $credit);
					$transaction_id = mysqli_insert_id($dbc);
				} else {
					$payment_status = 1; //completed
					$transaction_id = 0;
				}
				$paidAmount = @(float)$_REQUEST['paid_ammount'];
				if ($paidAmount > 0) {
					$credit1 = [
						'credit' => @$_REQUEST['paid_ammount'],
						'debit' => 0,
						'customer_id' => @$_REQUEST['payment_account'],
						'transaction_from' => 'Sale',
						'transaction_type' => "credit_sale",
						'transaction_remarks' => "credit_sale by order id#" . $last_id,
						'transaction_date' => $_REQUEST['order_date'],
					];
					insert_data($dbc, 'transactions', $credit1);
					$transaction_paid_id = mysqli_insert_id($dbc);
				}


				$newOrder = [
					'payment_status' => $payment_status,
					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'due' => $due_amount,
					'order_status' => 1,
					'transaction_id' => @$transaction_id,
					'transaction_paid_id' => @$transaction_paid_id,
				];
				if (update_data($dbc, 'orders', $newOrder, 'order_id', $last_id)) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Order Has been Added";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		} else {
			if (update_data($dbc, 'orders', $data, 'order_id', $_REQUEST['product_order_id'])) {
				$last_id = $_REQUEST['product_order_id'];
				if (!empty($_FILES['order_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['order_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['order_file']['tmp_name'], $uploadPath)) {
						$data = [
							'order_file' => $fileName,
						];

						update_data($dbc, "orders", $data, "order_id", $last_id);
					}
				}
				if ($get_company['stock_manage'] == 1) {
					$proQ = get($dbc, "order_item WHERE order_id='" . $last_id . "' ");

					while ($proR = mysqli_fetch_assoc($proQ)) {
						$newqty = 0;
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $proR['product_id'] . "' "));
						$newqty = (float)$quantity_instock['quantity_instock'] + (float)$proR['quantity'];
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$newqty' WHERE product_id='" . $proR['product_id'] . "' ");

						$branch_id =  $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $proR['product_id'] . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {
							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] + $proR['quantity'];
							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $proR['product_id'] . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						} else {
							$msg = "Not Efficient Inventory";
							$sts = 'error';
							echo json_encode(['msg' => $msg, 'sts' => $sts]);
							exit;
						}
					}
				}
				deleteFromTable($dbc, "order_item", 'order_id', $_REQUEST['product_order_id']);

				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {
					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$total = $product_quantites * $product_rates;
					$total_ammount += (float)$total;
					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'rate' => $product_rates,
						'total' => $total,
						'order_id' => $_REQUEST['product_order_id'],
						'quantity' => $product_quantites,
						'product_detail' => $_REQUEST['product_detail'][$x],
						'order_item_status' => 1,
						'branch_id' => $_REQUEST['branch_id'],
					];
					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
						$qty = (float)$quantity_instock['quantity_instock'] - $product_quantites;
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {
							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] - $product_quantites;
							if ($inventory_qty <= 0) {
								$msg = "Not Efficient Inventory";
								$sts = 'error';

								echo json_encode(['msg' => $msg, 'sts' => $sts]);
								exit;
							}
							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						} else {
							$msg = "Not Efficient Inventory";
							$sts = 'error';
							echo json_encode(['msg' => $msg, 'sts' => $sts]);
							exit;
						}
					}
					insert_data($dbc, 'order_item', $order_items);

					$x++;
				} //end of foreach
				$total_grand =  $total_ammount - $_REQUEST['ordered_discount'];
				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];

				$transactions = fetchRecord($dbc, "orders", "order_id", $_REQUEST['product_order_id']);
				@deleteFromTable($dbc, "transactions", 'transaction_id', $transactions['transaction_id']);
				@deleteFromTable($dbc, "transactions", 'transaction_id', $transactions['transaction_paid_id']);

				$credit = [
					'credit' => $due_amount,
					'debit' => 0,
					'customer_id' => @$_REQUEST['customer_account'],
					'transaction_from' => 'Sale',
					'transaction_type' => "credit_sale",
					'transaction_remarks' => "credit_sale by order id#" . $last_id,
					'transaction_date' => $_REQUEST['order_date'],
				];
				if ($due_amount > 0) {
					$payment_status = 0; //pending
					insert_data($dbc, 'transactions', $credit);
					$transaction_id = mysqli_insert_id($dbc);
				} else {
					$payment_status = 1; //completed
					$transaction_id = 0;
				}
				$paidAmount = @(float)$_REQUEST['paid_ammount'];
				if ($paidAmount > 0) {
					$credit1 = [
						'credit' => @$_REQUEST['paid_ammount'],
						'debit' => 0,
						'customer_id' => @$_REQUEST['payment_account'],
						'transaction_from' => 'Sale',
						'transaction_type' => "credit_sale",
						'transaction_remarks' => "credit_sale by order id#" . $last_id,
						'transaction_date' => $_REQUEST['order_date'],
					];
					insert_data($dbc, 'transactions', $credit1);
					$transaction_paid_id = mysqli_insert_id($dbc);
				}

				$newOrder = [
					'payment_status' => $payment_status,
					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'due' => $due_amount,
					'transaction_id' => @$transaction_id,
					'transaction_paid_id' => @$transaction_paid_id,
				];


				if (update_data($dbc, 'orders', $newOrder, 'order_id', $_REQUEST['product_order_id'])) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Data Has been Updated";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		}
	} else {
		$msg = "Please Add Any Product";
		$sts = 'error';
	}
	echo json_encode(['msg' => $msg, 'sts' => $sts, 'order_id' => @$last_id, 'type' => "order", 'subtype' => $_REQUEST['payment_type']]);
}

if (isset($_REQUEST['getProductPills'])) {
	$q = mysqli_query($dbc, "SELECT * FROM product WHERE brand_id='" . $_REQUEST['getProductPills'] . "' ");
	if (mysqli_num_rows($q) > 0) {
		while ($r = mysqli_fetch_assoc($q)) {
			echo '<li class="nav-item text-capitalize"  ><button type="button" onclick="addProductOrder(' . $r["product_id"] . ',' . $r["quantity_instock"] . ',`plus`)" class="btn btn-primary  m-1 ">' . $r["product_name"] . '</button></li>';
		}
	} else {
		echo '<li class="nav-item text-capitalize ">No Product Has Been Added</li>';
	}
}
if (isset($_REQUEST['getCustomer_name'])) {
	$q = mysqli_query($dbc, "SELECT DISTINCT client_name FROM  orders WHERE client_contact='" . $_REQUEST['getCustomer_name'] . "' ");
	if (mysqli_num_rows($q) > 0) {
		$r = mysqli_fetch_assoc($q);
		echo $r['client_name'];
	} else {
		echo '';
	}
}
if (isset($_REQUEST['getProductDetails'])) {
	$product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT  product.*,brands.* FROM product INNER JOIN brands ON product.brand_id=brands.brand_id   WHERE product.product_id='" . $_REQUEST['getProductDetails'] . "' AND product.status=1  "));
	echo json_encode($product);
}
if (isset($_REQUEST['getProductDetailsBycode'])) {
	$product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT  product.*,brands.* FROM product INNER JOIN brands ON product.brand_id=brands.brand_id   WHERE product.product_code='" . $_REQUEST['getProductDetailsBycode'] . "' AND product.status=1  "));
	echo json_encode($product);
}
/*---------------------- cash purchase   -------------------------------------------------------------------*/
if (isset($_REQUEST['cash_purchase_supplier']) && empty($_REQUEST['lpo_form']) && empty($_REQUEST['purchase_return'])) {
	if (!empty($_REQUEST['product_ids'])) {
		# code...
		$total_ammount = $total_grand = 0;
		$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));

		$data = [
			'purchase_date' => $_REQUEST['purchase_date'],
			'client_name' => @$_REQUEST['cash_purchase_supplier'],
			'client_contact' => @$_REQUEST['client_contact'],
			'purchase_narration' => @$_REQUEST['purchase_narration'],
			'payment_account' => @$_REQUEST['payment_account'],
			'customer_account' => @$_REQUEST['customer_account'],
			'paid' => $_REQUEST['paid_ammount'],
			'payment_status' => 1,
			'payment_type' => $_REQUEST['payment_type'],
			'branch_id' => $_REQUEST['branch_id'],
		];

		if ($_REQUEST['product_purchase_id'] == "") {

			if (insert_data($dbc, 'purchase', $data)) {
				$last_id = mysqli_insert_id($dbc);

				if (!empty($_FILES['purchase_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['purchase_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['purchase_file']['tmp_name'], $uploadPath)) {
						$data = [
							'purchase_file' => $fileName,
						];

						update_data($dbc, "purchase", $data, "purchase_id", $last_id);
					}
				}

				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {
					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$product_salerates = (float)$_REQUEST['product_salerates'][$x];
					$total = (float)$product_quantites * $product_rates;
					$total_ammount += (float)$total;

					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'rate' => $product_rates,
						'sale_rate' => $product_salerates,
						'total' => $total,
						'purchase_id' => $last_id,
						'product_detail' => @$_REQUEST['product_detail'][$x],
						'quantity' => $product_quantites,
						'purchase_item_status' => 1,
						'branch_id' => $_REQUEST['branch_id'],
					];

					insert_data($dbc, 'purchase_item', $order_items);

					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
						$qty = (float)$quantity_instock['quantity_instock'] + $product_quantites;
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] + $product_quantites;
							$update_inventory = mysqli_query($dbc, "UPDATE inventory SET quantity_instock='" . $inventory_qty . "' WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						} else {
							$insert_inventory = [
								'product_id' => $_REQUEST['product_ids'][$x],
								'quantity_instock' => $product_quantites,
								'branch_id' => $_REQUEST['branch_id'],
								'user_id' => $_SESSION['user_id'],
							];
							insert_data($dbc, 'inventory', $insert_inventory);
						}
					}
					// if (isset($_REQUEST['product_salerates'][$x])) {
					// 	$product_id = $_REQUEST['product_ids'][$x];
					// 	$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT current_rate FROM  product WHERE product_id='" . $product_id . "' "));
					// 	$current_rate = $_REQUEST['product_salerates'][$x];
					// 	$quantity_update = mysqli_query($dbc, "UPDATE product SET  current_rate='$current_rate' WHERE product_id='" . $product_id . "' ");
					// }



					$x++;
				} //end of foreach
				$total_grand = (float)$total_ammount - (float)@$_REQUEST['ordered_discount'];

				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];
				if ($_REQUEST['payment_type'] == "credit_purchase") :
					if ($due_amount > 0) {
						$debit = [
							'debit' => $due_amount,
							'credit' => 0,
							'customer_id' => @$_REQUEST['customer_account'],
							'transaction_from' => 'purchase',
							'transaction_type' => $_REQUEST['payment_type'],
							'transaction_remarks' => "purchased on  purchased id#" . $last_id,
							'transaction_date' => $_REQUEST['purchase_date'],
						];
						insert_data($dbc, 'transactions', $debit);
						$transaction_id = mysqli_insert_id($dbc);
					}
				endif;
				$paidAmount = @(float)$_REQUEST['paid_ammount'];
				if ($paidAmount > 0) {
					$credit = [
						'debit' => @$_REQUEST['paid_ammount'],
						'credit' => 0,
						'customer_id' => @$_REQUEST['payment_account'],
						'transaction_from' => 'purchase',
						'transaction_type' => $_REQUEST['payment_type'],
						'transaction_remarks' => "purchased by purchased id#" . $last_id,
						'transaction_date' => $_REQUEST['purchase_date'],
					];
					insert_data($dbc, 'transactions', $credit);
					$transaction_paid_id = mysqli_insert_id($dbc);
				}

				$newOrder = [

					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'due' => $due_amount,
					'transaction_paid_id' => @$transaction_paid_id,
					'transaction_id' => @$transaction_id,
				];
				if (update_data($dbc, 'purchase', $newOrder, 'purchase_id', $last_id)) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Purchase Has been Added";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		} else {
			if (update_data($dbc, 'purchase', $data, 'purchase_id', $_REQUEST['product_purchase_id'])) {
				$last_id = $_REQUEST['product_purchase_id'];

				if (!empty($_FILES['purchase_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['purchase_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['purchase_file']['tmp_name'], $uploadPath)) {
						$data = [
							'purchase_file' => $fileName,
						];

						update_data($dbc, "purchase", $data, "purchase_id", $last_id);
					}
				}
				if ($get_company['stock_manage'] == 1) {
					$proQ = get($dbc, "purchase_item WHERE purchase_id='" . $last_id . "' ");

					while ($proR = mysqli_fetch_assoc($proQ)) {
						$newqty = 0;
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $proR['product_id'] . "' "));
						$newqty = (float)$quantity_instock['quantity_instock'] - (float)$proR['quantity'];
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$newqty' WHERE product_id='" . $proR['product_id'] . "' ");

						$branch_id = $_SESSION['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $proR['product_id'] . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] - $proR['quantity'];
							$update_inventory = mysqli_query($dbc, "UPDATE inventory SET quantity_instock='" . $inventory_qty . "' WHERE product_id='" . $proR['product_id'] . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						} else {
							$insert_inventory = [
								'product_id' => $_REQUEST['product_ids'][$x],
								'quantity_instock' => $product_quantites,
								'branch_id' => $_SESSION['branch_id'],
								'user_id' => $_SESSION['user_id'],
							];
							insert_data($dbc, 'inventory', $insert_inventory);
						}
					}
				}
				deleteFromTable($dbc, "purchase_item", 'purchase_id', $_REQUEST['product_purchase_id']);
				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {


					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$product_salerates = (float)$_REQUEST['product_salerates'][$x];
					$total = $product_quantites * $product_rates;
					$total_ammount += (float)$total;
					$purchase_item = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'rate' => $product_rates,
						'sale_rate' => $product_salerates,
						'total' => $total,
						'purchase_id' => $_REQUEST['product_purchase_id'],
						'product_detail' => @$_REQUEST['product_detail'][$x],
						'quantity' => $product_quantites,
						'purchase_item_status' => 1,
						'branch_id' => $_REQUEST['branch_id'],
					];

					//update_data($dbc,'order_item', $order_items , 'purchase_id',$_REQUEST['product_purchase_id']);
					insert_data($dbc, 'purchase_item', $purchase_item);

					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
						$qty = (float)$quantity_instock['quantity_instock'] + $product_quantites;
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");

						$branch_id = $_SESSION['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] + $product_quantites;
							$update_inventory = mysqli_query($dbc, "UPDATE inventory SET quantity_instock='" . $inventory_qty . "' WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						} else {
							$insert_inventory = [
								'product_id' => $_REQUEST['product_ids'][$x],
								'quantity_instock' => $product_quantites,
								'branch_id' => $_SESSION['branch_id'],
								'user_id' => $_SESSION['user_id'],
							];
							insert_data($dbc, 'inventory', $insert_inventory);
						}
					}

					$x++;
				} //end of foreach
				$total_grand = (float)$total_ammount - (float)@$_REQUEST['ordered_discount'];
				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];


				$transactions = fetchRecord($dbc, "purchase", "purchase_id", $_REQUEST['product_purchase_id']);
				@deleteFromTable($dbc, "transactions", 'transaction_id', $transactions['transaction_id']);
				@deleteFromTable($dbc, "transactions", 'transaction_id', $transactions['transaction_paid_id']);


				if ($_REQUEST['payment_type'] == "credit_purchase") :
					if ($due_amount > 0) {
						$debit = [
							'debit' => $due_amount,
							'credit' => 0,
							'customer_id' => @$_REQUEST['customer_account'],
							'transaction_from' => 'purchase',
							'transaction_type' => $_REQUEST['payment_type'],
							'transaction_remarks' => "purchased on  purchased id#" . $last_id,
							'transaction_date' => $_REQUEST['purchase_date'],
						];
						insert_data($dbc, 'transactions', $debit);
						$transaction_id = mysqli_insert_id($dbc);
					}
				endif;
				$paidAmount = @(float)$_REQUEST['paid_ammount'];
				if ($paidAmount > 0) {
					$credit = [
						'debit' => @$_REQUEST['paid_ammount'],
						'credit' => 0,
						'customer_id' => @$_REQUEST['payment_account'],
						'transaction_from' => 'purchase',
						'transaction_type' => $_REQUEST['payment_type'],
						'transaction_remarks' => "purchased by purchased id#" . $last_id,
						'transaction_date' => $_REQUEST['purchase_date'],
					];
					insert_data($dbc, 'transactions', $credit);
					$transaction_paid_id = mysqli_insert_id($dbc);
				}

				$newOrder = [

					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'due' => $due_amount,
					'transaction_paid_id' => @$transaction_paid_id,
					'transaction_id' => @$transaction_id,
				];

				if (update_data($dbc, 'purchase', $newOrder, 'purchase_id', $_REQUEST['product_purchase_id'])) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Purchase Has been Updated";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		}
	} else {
		$msg = "Please Add Any Product";
		$sts = 'error';
	}
	echo json_encode(['msg' => $msg, 'sts' => $sts, 'order_id' => @$last_id, 'type' => "purchase", 'subtype' => $_REQUEST['payment_type']]);
}
/*---------------------- credit Purchase-order  end -------------------------------------------------------------------*/
if (isset($_REQUEST['get_products_code'])) {
	$q = mysqli_query($dbc, "SELECT *  FROM product WHERE product_code='" . $_REQUEST['get_products_code'] . "' AND status=1 ");
	if (mysqli_num_rows($q) > 0) {
		$r = mysqli_fetch_assoc($q);
		$response = [
			"msg" => "This Product Code Already Assign to " . $r['product_name'],
			"sts" => "error",
		];
	} else {
		$response = [
			"msg" => "",
			"sts" => "success"
		];
	}
	echo json_encode($response);
}
if (isset($_REQUEST['getBalance'])) {
	$customer_id = mysqli_real_escape_string($dbc, $_REQUEST['getBalance']);

	$balance_query = mysqli_query($dbc, "
        SELECT SUM(credit - debit) AS from_balance 
        FROM transactions 
        WHERE customer_id = '$customer_id'
    ");
	$from_balance = mysqli_fetch_assoc($balance_query);

	$cust_query = mysqli_query($dbc, "
        SELECT * 
        FROM customers 
        WHERE customer_id = '$customer_id'
    ");
	$cust = mysqli_fetch_assoc($cust_query);

	$response1 = [
		'blnc' => is_numeric($from_balance['from_balance']) ? round($from_balance['from_balance']) : 0,
		'custLimit' => is_numeric($cust['customer_limit']) ? round($cust['customer_limit']) : 0,
	];

	echo json_encode($response1);
}

if (isset($_REQUEST['pending_bills_detils'])) {
	$pending_bills_detils = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM orders WHERE order_id='" . base64_decode($_REQUEST['pending_bills_detils']) . "'"));
	echo  json_encode($pending_bills_detils);
}
if (isset($_REQUEST['add_expense_name'])) {
	$data_array = [
		'expense_name' => $_REQUEST['add_expense_name'],
		'expense_status' => $_REQUEST['expense_status'],
	];
	if ($_REQUEST['expense_id'] == '') {
		if (insert_data($dbc, "expenses", $data_array)) {
			# code...
			$response = [
				"msg" => "expense Added successfully",
				"sts" => "success"
			];
		} else {
			$response = [
				"msg" => mysqli_error($dbc),
				"sts" => "danger"
			];
		}
	} else {
		if (update_data($dbc, "expenses", $data_array, "expense_id", $_REQUEST['expense_id'])) {
			# code...
			$response = [
				"msg" => "expense Updated successfully",
				"sts" => "success"
			];
		} else {
			$response = [
				"msg" => mysqli_error($dbc),
				"sts" => "error"
			];
		}
	}
	echo json_encode($response);
}

if (isset($_REQUEST['setAmountPaid'])) {
	$newOrder = [
		'payment_status' => 1,
		'paid' => $_REQUEST['paid'],
		'due' => 0,
	];
	if (update_data($dbc, 'orders', $newOrder, 'order_id', $_REQUEST['setAmountPaid'])) {

		$response = [
			'msg' => "Amount Has been Paid",
			'sts' => 'success'
		];
	} else {
		$response = [
			'msg' => mysqli_error($dbc),
			'sts' => 'error'
		];
	}
	echo json_encode($response);
}
if (isset($_REQUEST['setCheckStatus'])) {
	$newStat = [
		'check_status' => $_REQUEST['status'],
	];
	if (update_data($dbc, 'checks', $newStat, 'check_id', $_REQUEST['setCheckStatus'])) {

		$response = [
			'msg' => "Action Has been Perform Successfully",
			'sts' => 'success'
		];
	} else {
		$response = [
			'msg' => mysqli_error($dbc),
			'sts' => 'error'
		];
	}
	echo json_encode($response);
}
if (isset($_REQUEST['bill_customer_name'])) {
	$paidAmount = (float)$_REQUEST['bill_paid_ammount'] + (float)$_REQUEST['bill_paid'];


	if ($paidAmount > 0) {
		$transactions = fetchRecord($dbc, "orders", "order_id", $_REQUEST['order_id']);
		$order_date = date('Y-m-d');
		if ($transactions['transaction_paid_id'] > 0) {
			$credit1 = [
				'credit' => @$paidAmount,
				'debit' => 0,
				'customer_id' => @$_REQUEST['bill_payment_account'],
			];

			update_data($dbc, "transactions", $credit1, "transaction_id", $transactions['transaction_paid_id']);
			$transaction_paid_id = $transactions['transaction_paid_id'];
		} else {
			$credit1 = [
				'credit' => @$paidAmount,
				'debit' => 0,
				'customer_id' => @$_REQUEST['bill_payment_account'],
				'transaction_from' => 'invoice',
				'transaction_type' => "cash_in_hand",
				'transaction_remarks' => "cash_sale by order id#" . $_REQUEST['order_id'],
				'transaction_date' => $order_date,
			];
			insert_data($dbc, 'transactions', $credit1);
			$transaction_paid_id = mysqli_insert_id($dbc);
		}
	}
	$due_amount = (float)$_REQUEST['bill_grand_total'] - $paidAmount;
	if ($due_amount > 0) {
		$payment_status = 0; //pending
	} else {
		$payment_status = 1; //completed
	}
	$newOrder = [
		'payment_status' => $payment_status,
		'paid' => $paidAmount,
		'due' => $due_amount,
		'transaction_paid_id' => $transaction_paid_id,
	];
	if (update_data($dbc, 'orders', $newOrder, 'order_id', $_REQUEST['order_id'])) {

		$response = [
			'msg' => "Amount Has been Paid",
			'sts' => 'success'
		];
	} else {
		$response = [
			'msg' => mysqli_error($dbc),
			'sts' => 'error'
		];
	}
	echo json_encode($response);
}
if (isset($_REQUEST['LimitCustomer'])) {
	$data = [
		'check_no' => $_REQUEST['td_check_no'],
		'check_bank_name' => $_REQUEST['voucher_bank_name'],
		'check_expiry_date' => $_REQUEST['td_check_date'],
		'voucher_id' => 0,
		'customer_id' => $_REQUEST['LimitCustomer'],
		'check_amount' => $_REQUEST['check_amount'],
		'check_location' => $_REQUEST['location_info'],
		'check_type' => $_REQUEST['check_type'],
	];
	$cust = $_REQUEST['LimitCustomer'];
	$limitNow =  $_REQUEST['check_amount'];

	$check = mysqli_query($dbc, "SELECT * FROM checks WHERE customer_id = '$cust' AND voucher_id = 0 AND check_amount != 0");
	//echo "SELECT * FROM checks WHERE customer_id = '$cust' AND voucher_id = 0 AND check_amount != 0";

	if (mysqli_num_rows($check) > 0) {
		$qq = mysqli_fetch_assoc($check);
		//echo $qq['check_id'];
		if (update_data($dbc, 'checks', $data, 'check_id', $qq['check_id'])) {
			mysqli_query($dbc, "UPDATE customers SET customer_limit = '$limitNow' WHERE customer_id = '$cust'");

			$response = [
				'msg' => "Data Updated successfully",
				'sts' => 'success'
			];
		}
	} else {
		if (insert_data($dbc, 'checks', $data)) {
			mysqli_query($dbc, "UPDATE customers SET customer_limit = '$limitNow' WHERE customer_id = '$cust'");
			$response = [
				'msg' => "Amount Has been Paid",
				'sts' => 'success'
			];
		} else {
			$response = [
				'msg' => mysqli_error($dbc),
				'sts' => 'error'
			];
		}
	}

	echo json_encode($response);
}


if (isset($_REQUEST['LimitCustomerajax'])) {
	$cust = $_REQUEST['LimitCustomerajax'];
	$check = mysqli_query($dbc, "SELECT * FROM checks WHERE customer_id = '$cust' AND voucher_id = 0 AND check_amount != 0");
	//echo "SELECT * FROM checks WHERE customer_id = '$cust' AND voucher_id = 0 AND check_amount != 0";
	if (mysqli_num_rows($check) > 0) {
		$qq = mysqli_fetch_assoc($check);
		//print_r($qq);
		$response = [
			'check_no' => $qq['check_no'],
			'bank_name' => $qq['check_bank_name'],
			'check_date' => $qq['check_expiry_date'],
			'check_type' => $qq['check_type'],
			'check_amount' => $qq['check_amount'],
			'check_location' => $qq['check_location'],
			'sts' 			=> 'success',

		];
	} else {
		$response = '';
	}


	echo json_encode($response);
}

if (isset($_REQUEST['getCustomerLimit'])) {
	$cust = $_REQUEST['getCustomerLimit'];
	$q = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT customer_limit as customer_limit FROM customers WHERE customer_id = '$cust'"));
	echo $q['customer_limit'];
}


// Add LPO

if (isset($_REQUEST['lpo_form']) && !empty($_REQUEST['lpo_form'])) {
	if (!empty($_REQUEST['product_ids'])) {
		# code...
		$total_ammount = $total_grand = 0;
		$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));

		$data = [
			'lpo_date' => $_REQUEST['purchase_date'],
			'bill_no' => @$_REQUEST['bill_no'],
			'client_name' => @$_REQUEST['cash_purchase_supplier'],
			'client_contact' => @$_REQUEST['client_contact'],
			'lpo_narration' => @$_REQUEST['purchase_narration'],
			'payment_account' => @$_REQUEST['payment_account'],
			'customer_account' => @$_REQUEST['customer_account'],
			'paid' => @$_REQUEST['paid_ammount'],
			'payment_status' => 1,
			'payment_type' => "lpo",
		];

		if ($_REQUEST['product_purchase_id'] == "") {

			if (insert_data($dbc, 'lpo', $data)) {

				$last_id = mysqli_insert_id($dbc);

				if (!empty($_FILES['lpo_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['lpo_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['lpo_file']['tmp_name'], $uploadPath)) {
						$data = [
							'lpo_file' => $fileName,
						];

						update_data($dbc, "lpo", $data, "lpo_id", $last_id);
					}
				}


				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {
					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$product_salerates = (float)$_REQUEST['product_salerates'][$x];
					$total = (float)$product_quantites * $product_rates;
					$total_ammount += (float)$total;

					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'rate' => $product_rates,
						'total' => $total,
						'lpo_id' => $last_id,
						'product_detail' => @$_REQUEST['product_detail'][$x],
						'quantity' => $product_quantites,
						'lpo_item_status' => 1,
					];

					insert_data($dbc, 'lpo_item', $order_items);

					$x++;
				} //end of foreach
				$total_grand = $total_ammount -  ((float)$_REQUEST['ordered_discount']);

				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];


				$newOrder = [
					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'due' => $due_amount,
				];
				if (update_data($dbc, 'lpo', $newOrder, 'lpo_id', $last_id)) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "LPO Has been Added";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		} else {
			if (update_data($dbc, 'lpo', $data, 'lpo_id', $_REQUEST['product_purchase_id'])) {
				$last_id = $_REQUEST['product_purchase_id'];

				if (!empty($_FILES['lpo_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['lpo_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['lpo_file']['tmp_name'], $uploadPath)) {
						$data = [
							'lpo_file' => $fileName,
						];

						update_data($dbc, "lpo", $data, "lpo_id", $last_id);
					}
				}

				if ($get_company['stock_manage'] == 1) {
					$proQ = get($dbc, "lpo_item WHERE lpo_id='" . $last_id . "' ");
				}
				deleteFromTable($dbc, "lpo_item", 'lpo_id', $_REQUEST['product_purchase_id']);
				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {


					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$product_salerates = (float)$_REQUEST['product_salerates'][$x];
					$total = $product_quantites * $product_rates;
					$total_ammount += (float)$total;
					$purchase_item = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'rate' => $product_rates,
						'total' => $total,
						'lpo_id' => $_REQUEST['product_purchase_id'],
						'product_detail' => @$_REQUEST['product_detail'][$x],
						'quantity' => $product_quantites,
						'lpo_item_status' => 1,
					];

					//update_data($dbc,'order_item', $order_items , 'purchase_id',$_REQUEST['product_purchase_id']);
					insert_data($dbc, 'lpo_item', $purchase_item);

					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
						$qty = (float)$quantity_instock['quantity_instock'] + $product_quantites;
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");
					}

					$x++;
				} //end of foreach
				$ordered_discount = intval($_REQUEST['ordered_discount'] ?? 0);
				$total_grand = $total_ammount - $ordered_discount;
				$due_amount = $total_grand - @$_REQUEST['paid_ammount'];

				$newOrder = [
					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'due' => $due_amount,
				];

				if (update_data($dbc, 'lpo', $newOrder, 'lpo_id', $_REQUEST['product_purchase_id'])) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "LPO Has been Updated";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		}
	} else {
		$msg = "Please Add Any Product";
		$sts = 'error';
	}
	echo json_encode(['msg' => $msg, 'sts' => $sts, 'order_id' => @$last_id, 'type' => "lpo", 'subtype' => $_REQUEST['payment_type']]);
}

// cuotation

if (isset($_REQUEST['quotation_form']) && !empty($_REQUEST['quotation_form'])) {
	$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));
	if (!empty($_REQUEST['product_ids'])) {
		# code...
		$total_ammount = $total_grand = 0;

		$data = [
			'quotation_date' => $_REQUEST['order_date'],
			'client_name' => $_REQUEST['credit_order_client_name'],
			'client_contact' => $_REQUEST['client_contact'],
			'paid' => @$_REQUEST['paid_ammount'],
			'quotation_narration' => @$_REQUEST['order_narration'],
			'payment_account' => @$_REQUEST['payment_account'],
			'customer_account' => @$_REQUEST['customer_account'],
			'payment_type' => 'quotation',
			'credit_sale_type' => @$_REQUEST['credit_sale_type'],
			'freight' => @$_REQUEST['freight'],
		];
		//'payment_status'=>1,
		if ($_REQUEST['product_order_id'] == "") {

			if (insert_data($dbc, 'quotations', $data)) {
				$last_id = mysqli_insert_id($dbc);

				if (!empty($_FILES['quotation_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['quotation_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['quotation_file']['tmp_name'], $uploadPath)) {
						$data = [
							'quotation_file' => $fileName,
						];

						update_data($dbc, "quotations", $data, "quotation_id", $last_id);
					}
				}

				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {
					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$total = $product_quantites * $product_rates;
					$total_ammount += (float)$total;
					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'final_rate' => $_REQUEST['product_final_rates'][$x],
						'rate' => $product_rates,
						'total' => $total,
						'quotation_id' => $last_id,
						'quantity' => $product_quantites,
						'product_detail' => $_REQUEST['product_detail'][$x],
						'quotation_item_status' => 1,
					];

					// if ($get_company['stock_manage'] == 1) {
					// 	$product_id = $_REQUEST['product_ids'][$x];
					// 	$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
					// 	$qty = (float)$quantity_instock['quantity_instock'] - $product_quantites;
					// 	$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");
					// }
					insert_data($dbc, 'quotation_item', $order_items);

					$x++;
				} //end of foreach



				$total_grand =  $total_ammount - $_REQUEST['ordered_discount'];
				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];

				$newOrder = [
					'payment_status' => @$payment_status,
					'total_amount' => @$total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'due' => @$due_amount,
					'quotation_status' => 1,
				];
				if (update_data($dbc, 'quotations', $newOrder, 'quotation_id', $last_id)) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Quotation Has been Added";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		} else {
			if (update_data($dbc, 'quotations', $data, 'quotation_id', $_REQUEST['product_order_id'])) {
				$last_id = $_REQUEST['product_order_id'];

				if (!empty($_FILES['quotation_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['quotation_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['quotation_file']['tmp_name'], $uploadPath)) {
						$data = [
							'quotation_file' => $fileName,
						];

						update_data($dbc, "quotations", $data, "quotation_id", $last_id);
					}
				}

				if ($get_company['stock_manage'] == 1) {
					$proQ = get($dbc, "quotation_item WHERE quotation_id='" . $last_id . "' ");

					// while ($proR = mysqli_fetch_assoc($proQ)) {
					// 	$newqty = 0;
					// 	$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $proR['product_id'] . "' "));
					// 	$newqty = (float)$quantity_instock['quantity_instock'] + (float)$proR['quantity'];
					// 	$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$newqty' WHERE product_id='" . $proR['product_id'] . "' ");
					// }
				}
				deleteFromTable($dbc, "quotation_item", 'quotation_id', $_REQUEST['product_order_id']);

				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {
					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$total = $product_quantites * $product_rates;
					$total_ammount += (float)$total;
					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'final_rate' => $_REQUEST['product_final_rates'][$x],
						'rate' => $product_rates,
						'total' => $total,
						'quotation_id' => $_REQUEST['product_order_id'],
						'quantity' => $product_quantites,
						'product_detail' => $_REQUEST['product_detail'][$x],
						'quotation_item_status' => 1,
					];
					// if ($get_company['stock_manage'] == 1) {
					// 	$product_id = $_REQUEST['product_ids'][$x];
					// 	$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
					// 	$qty = (float)$quantity_instock['quantity_instock'] - $product_quantites;
					// 	$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");
					// }
					insert_data($dbc, 'quotation_item', $order_items);

					$x++;
				} //end of foreach
				$total_grand =  $total_ammount - $_REQUEST['ordered_discount'];
				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];



				$newOrder = [
					'payment_status' => @$payment_status,
					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'due' => $due_amount,
				];


				if (update_data($dbc, 'quotations', $newOrder, 'quotation_id', $_REQUEST['product_order_id'])) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Quotation Has been Updated";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		}
	} else {
		$msg = "Please Add Any Product";
		$sts = 'error';
	}
	echo json_encode(['msg' => $msg, 'sts' => $sts, 'order_id' => @$last_id, 'type' => "quotation", 'subtype' => $_REQUEST['payment_type']]);
}


/*----------------------   Purchase Return   -------------------------------------------------------------------*/
if (isset($_REQUEST['cash_purchase_supplier']) && isset($_REQUEST['purchase_return'])) {
	if (!empty($_REQUEST['product_ids'])) {
		# code...
		$total_ammount = $total_grand = 0;
		$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));

		$data = [
			'purchase_date' => $_REQUEST['purchase_date'],
			'client_name' => @$_REQUEST['cash_purchase_supplier'],
			'client_contact' => @$_REQUEST['client_contact'],
			'purchase_narration' => @$_REQUEST['purchase_narration'],
			'payment_account' => @$_REQUEST['payment_account'],
			'customer_account' => @$_REQUEST['customer_account'],
			'paid' => $_REQUEST['paid_ammount'],
			'payment_status' => 1,
			'payment_type' => $_REQUEST['payment_type'],
			'branch_id' => $_REQUEST['branch_id'],
		];

		if ($_REQUEST['product_purchase_id'] == "") {

			if (insert_data($dbc, 'purchase_return', $data)) {
				$last_id = mysqli_insert_id($dbc);

				if (!empty($_FILES['purchase_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['purchase_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['purchase_file']['tmp_name'], $uploadPath)) {
						$data = [
							'purchase_file' => $fileName,
						];

						update_data($dbc, "purchase_return", $data, "purchase_id", $last_id);
					}
				}

				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {
					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$product_salerates = (float)$_REQUEST['product_salerates'][$x];
					$total = (float)$product_quantites * $product_rates;
					$total_ammount += (float)$total;

					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'rate' => $product_rates,
						'sale_rate' => $product_salerates,
						'total' => $total,
						'purchase_id' => $last_id,
						'product_detail' => @$_REQUEST['product_detail'][$x],
						'quantity' => $product_quantites,
						'purchase_item_status' => 1,
						'branch_id' => $_REQUEST['branch_id'],
					];

					insert_data($dbc, 'purchase_return_item', $order_items);

					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
						$qty = (float)$quantity_instock['quantity_instock'] -  $product_quantites;
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] - $product_quantites;

							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						}
					}
					// if (isset($_REQUEST['product_salerates'][$x])) {
					// 	$product_id = $_REQUEST['product_ids'][$x];
					// 	$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT current_rate FROM  product WHERE product_id='" . $product_id . "' "));
					// 	$current_rate = $_REQUEST['product_salerates'][$x];
					// 	$quantity_update = mysqli_query($dbc, "UPDATE product SET  current_rate='$current_rate' WHERE product_id='" . $product_id . "' ");
					// }



					$x++;
				} //end of foreach
				$total_grand = (float)$total_ammount - (float)@$_REQUEST['ordered_discount'];

				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];

				if ($_REQUEST['payment_type'] == "credit_purchase") :
					if ($due_amount > 0) {
						$debit = [
							'debit' => 0,
							'credit' => $due_amount,
							'customer_id' => @$_REQUEST['customer_account'],
							'transaction_from' => 'purchase',
							'transaction_type' => $_REQUEST['payment_type'],
							'transaction_remarks' => "purchased on  purchased id#" . $last_id,
							'transaction_date' => $_REQUEST['purchase_date'],
						];
						insert_data($dbc, 'transactions', $debit);
						$transaction_id = mysqli_insert_id($dbc);
					}
				endif;
				$paidAmount = @(float)$_REQUEST['paid_ammount'];
				if ($paidAmount > 0) {
					$credit = [
						'debit' => 0,
						'credit' => @$_REQUEST['paid_ammount'],
						'customer_id' => @$_REQUEST['payment_account'],
						'transaction_from' => 'purchase',
						'transaction_type' => $_REQUEST['payment_type'],
						'transaction_remarks' => "purchased by purchased id#" . $last_id,
						'transaction_date' => $_REQUEST['purchase_date'],
					];
					insert_data($dbc, 'transactions', $credit);
					$transaction_paid_id = mysqli_insert_id($dbc);
				}

				$newOrder = [

					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'due' => $due_amount,
					'transaction_paid_id' => @$transaction_paid_id,
					'transaction_id' => @$transaction_id,
				];
				if (update_data($dbc, 'purchase_return', $newOrder, 'purchase_id', $last_id)) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Purchase Return Has been Added";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		} else {
			if (update_data($dbc, 'purchase_return', $data, 'purchase_id', $_REQUEST['product_purchase_id'])) {
				$last_id = $_REQUEST['product_purchase_id'];

				if (!empty($_FILES['purchase_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['purchase_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['purchase_file']['tmp_name'], $uploadPath)) {
						$data = [
							'purchase_file' => $fileName,
						];

						update_data($dbc, "purchase_return", $data, "purchase_id", $last_id);
					}
				}
				if ($get_company['stock_manage'] == 1) {
					$proQ = get($dbc, "purchase_return_item WHERE purchase_id='" . $last_id . "' ");

					while ($proR = mysqli_fetch_assoc($proQ)) {
						$newqty = 0;
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $proR['product_id'] . "' "));
						$newqty = (float)$quantity_instock['quantity_instock'] - (float)$proR['quantity'];
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$newqty' WHERE product_id='" . $proR['product_id'] . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $proR['product_id'] . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] - $proR['quantity'];

							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $proR['product_id'] . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						}
					}
				}
				deleteFromTable($dbc, "purchase_return_item", 'purchase_id', $_REQUEST['product_purchase_id']);
				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {


					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$product_salerates = (float)$_REQUEST['product_salerates'][$x];
					$total = $product_quantites * $product_rates;
					$total_ammount += (float)$total;
					$purchase_item = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'rate' => $product_rates,
						'sale_rate' => $product_salerates,
						'total' => $total,
						'purchase_id' => $_REQUEST['product_purchase_id'],
						'product_detail' => @$_REQUEST['product_detail'][$x],
						'quantity' => $product_quantites,
						'purchase_item_status' => 1,
						'branch_id' => $_REQUEST['branch_id'],
					];

					//update_data($dbc,'order_item', $order_items , 'purchase_id',$_REQUEST['product_purchase_id']);
					insert_data($dbc, 'purchase_return_item', $purchase_item);

					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
						$qty = (float)$quantity_instock['quantity_instock'] - $product_quantites;
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] - $product_quantites;
							if ($inventory_qty <= 0) {
								$msg = "Not Efficient Inventory";
								$sts = 'error';

								echo json_encode(['msg' => $msg, 'sts' => $sts]);
								exit;
							}
							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						} else {
							$msg = "Not Efficient Inventory";
							$sts = 'error';
							echo json_encode(['msg' => $msg, 'sts' => $sts]);
							exit;
						}
					}

					$x++;
				} //end of foreach
				$total_grand = (float)$total_ammount - (float)@$_REQUEST['ordered_discount'];
				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];


				$transactions = fetchRecord($dbc, "purchase_return", "purchase_id", $_REQUEST['product_purchase_id']);
				@deleteFromTable($dbc, "transactions", 'transaction_id', $transactions['transaction_id']);
				@deleteFromTable($dbc, "transactions", 'transaction_id', $transactions['transaction_paid_id']);


				if ($_REQUEST['payment_type'] == "credit_purchase") :
					if ($due_amount > 0) {
						$debit = [
							'debit' => 0,
							'credit' => $due_amount,
							'customer_id' => @$_REQUEST['customer_account'],
							'transaction_from' => 'Purchase Return ',
							'transaction_type' => $_REQUEST['payment_type'],
							'transaction_remarks' => "purchased on  purchased id#" . $last_id,
							'transaction_date' => $_REQUEST['purchase_date'],
						];
						insert_data($dbc, 'transactions', $debit);
						$transaction_id = mysqli_insert_id($dbc);
					}
				endif;
				$paidAmount = @(float)$_REQUEST['paid_ammount'];
				if ($paidAmount > 0) {
					$credit = [
						'debit' => 0,
						'credit' => @$_REQUEST['paid_ammount'],
						'customer_id' => @$_REQUEST['payment_account'],
						'transaction_from' => 'Purchase Return',
						'transaction_type' => $_REQUEST['payment_type'],
						'transaction_remarks' => "purchased by purchased id#" . $last_id,
						'transaction_date' => $_REQUEST['purchase_date'],
					];
					insert_data($dbc, 'transactions', $credit);
					$transaction_paid_id = mysqli_insert_id($dbc);
				}

				$newOrder = [

					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'due' => $due_amount,
					'transaction_paid_id' => @$transaction_paid_id,
					'transaction_id' => @$transaction_id,
				];

				if (update_data($dbc, 'purchase_return', $newOrder, 'purchase_id', $_REQUEST['product_purchase_id'])) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Purchase Return Has been Updated";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		}
	} else {
		$msg = "Please Add Any Product";
		$sts = 'error';
	}
	echo json_encode(['msg' => $msg, 'sts' => $sts, 'order_id' => @$last_id, 'type' => "purchase_return", 'subtype' => $_REQUEST['payment_type']]);
}


/*---------------------- credit sale-order-return   -------------------------------------------------------------------*/
if (isset($_REQUEST['credit_order_client_name']) && isset($_REQUEST['order_return'])) {
	$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));
	if (!empty($_REQUEST['product_ids'])) {
		# code...
		$total_ammount = $total_grand = 0;

		$data = [
			'order_date' => $_REQUEST['order_date'],
			'client_name' => $_REQUEST['credit_order_client_name'],
			'client_contact' => $_REQUEST['client_contact'],
			'paid' => @$_REQUEST['paid_ammount'],
			'order_narration' => @$_REQUEST['order_narration'],
			'payment_account' => @$_REQUEST['payment_account'],
			'customer_account' => @$_REQUEST['customer_account'],
			'payment_type' => 'credit',
			'credit_sale_type' => @$_REQUEST['credit_sale_type'],
			'vehicle_no' => @$_REQUEST['vehicle_no'],
			'return_days' => @$_REQUEST['return_days'],
			'freight' => @$_REQUEST['freight'],
			'branch_id' => $_REQUEST['branch_id'],
		];
		//'payment_status'=>1,
		if ($_REQUEST['product_order_id'] == "") {

			if (insert_data($dbc, 'orders_return', $data)) {
				$last_id = mysqli_insert_id($dbc);
				if (!empty($_FILES['order_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['order_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['order_file']['tmp_name'], $uploadPath)) {
						$data = [
							'order_file' => $fileName,
						];

						update_data($dbc, "orders_return", $data, "order_id", $last_id);
					}
				}

				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {
					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$total = $product_quantites * $product_rates;
					$total_ammount += (float)$total;
					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'final_rate' => $_REQUEST['product_final_rates'][$x],
						'rate' => $product_rates,
						'total' => $total,
						'order_id' => $last_id,
						'quantity' => $product_quantites,
						'product_detail' => $_REQUEST['product_detail'][$x],
						'order_item_status' => 1,
						'branch_id' => $_REQUEST['branch_id'],
					];

					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
						$qty = (float)$quantity_instock['quantity_instock'] + $product_quantites;
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");

						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] + $product_quantites;
							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						}
					}
					insert_data($dbc, 'order_return_item', $order_items);

					$x++;
				} //end of foreach

				$total_grand =  $total_ammount - $_REQUEST['ordered_discount'];
				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];

				$credit = [
					'credit' => 0,
					'debit' => $due_amount,
					'customer_id' => @$_REQUEST['customer_account'],
					'transaction_from' => 'Sale Return',
					'transaction_type' => "credit_sale",
					'transaction_remarks' => "credit_sale by order id#" . $last_id,
					'transaction_date' => $_REQUEST['order_date'],
				];
				if ($due_amount > 0) {
					$payment_status = 0; //pending
					insert_data($dbc, 'transactions', $credit);
					$transaction_id = mysqli_insert_id($dbc);
				} else {
					$payment_status = 1; //completed
					$transaction_id = 0;
				}
				$paidAmount = @(float)$_REQUEST['paid_ammount'];
				if ($paidAmount > 0) {
					$credit1 = [
						'credit' => 0,
						'debit' => @$_REQUEST['paid_ammount'],
						'customer_id' => @$_REQUEST['payment_account'],
						'transaction_from' => 'Sale Return',
						'transaction_type' => "credit_sale",
						'transaction_remarks' => "credit_sale by order id#" . $last_id,
						'transaction_date' => $_REQUEST['order_date'],
					];
					insert_data($dbc, 'transactions', $credit1);
					$transaction_paid_id = mysqli_insert_id($dbc);
				}


				$newOrder = [
					'payment_status' => $payment_status,
					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'due' => $due_amount,
					'order_status' => 1,
					'transaction_id' => @$transaction_id,
					'transaction_paid_id' => @$transaction_paid_id,
				];
				if (update_data($dbc, 'orders_return', $newOrder, 'order_id', $last_id)) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Order Return Has been Added";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		} else {
			if (update_data($dbc, 'orders_return', $data, 'order_id', $_REQUEST['product_order_id'])) {
				$last_id = $_REQUEST['product_order_id'];
				if (!empty($_FILES['order_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['order_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['order_file']['tmp_name'], $uploadPath)) {
						$data = [
							'order_file' => $fileName,
						];

						update_data($dbc, "orders_return", $data, "order_id", $last_id);
					}
				}
				if ($get_company['stock_manage'] == 1) {
					$proQ = get($dbc, "order_return_item WHERE order_id='" . $last_id . "' ");

					while ($proR = mysqli_fetch_assoc($proQ)) {
						$newqty = 0;
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $proR['product_id'] . "' "));
						$newqty = (float)$quantity_instock['quantity_instock'] + (float)$proR['quantity'];
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$newqty' WHERE product_id='" . $proR['product_id'] . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $proR['product_id'] . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] + $proR['quantity'];
							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $proR['product_id'] . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						}
					}
				}
				deleteFromTable($dbc, "order_return_item", 'order_id', $_REQUEST['product_order_id']);

				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {
					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$total = $product_quantites * $product_rates;
					$total_ammount += (float)$total;
					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'rate' => $product_rates,
						'total' => $total,
						'order_id' => $_REQUEST['product_order_id'],
						'quantity' => $product_quantites,
						'product_detail' => $_REQUEST['product_detail'][$x],
						'order_item_status' => 1,
						'branch_id' => $_REQUEST['branch_id'],
					];
					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
						$qty = (float)$quantity_instock['quantity_instock'] -  $product_quantites;
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] - $product_quantites;
							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						}
					}
					insert_data($dbc, 'order_item', $order_items);

					$x++;
				} //end of foreach
				$total_grand =  $total_ammount - $_REQUEST['ordered_discount'];
				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];

				$transactions = fetchRecord($dbc, "orders_return", "order_id", $_REQUEST['product_order_id']);
				@deleteFromTable($dbc, "transactions", 'transaction_id', $transactions['transaction_id']);
				@deleteFromTable($dbc, "transactions", 'transaction_id', $transactions['transaction_paid_id']);

				$credit = [
					'credit' => 0,
					'debit' => $due_amount,
					'customer_id' => @$_REQUEST['customer_account'],
					'transaction_from' => 'invoice',
					'transaction_type' => "credit_sale",
					'transaction_remarks' => "credit_sale by order id#" . $last_id,
					'transaction_date' => $_REQUEST['order_date'],
				];
				if ($due_amount > 0) {
					$payment_status = 0; //pending
					insert_data($dbc, 'transactions', $credit);
					$transaction_id = mysqli_insert_id($dbc);
				} else {
					$payment_status = 1; //completed
					$transaction_id = 0;
				}
				$paidAmount = @(float)$_REQUEST['paid_ammount'];
				if ($paidAmount > 0) {
					$credit1 = [
						'credit' => 0,
						'debit' => @$_REQUEST['paid_ammount'],
						'customer_id' => @$_REQUEST['payment_account'],
						'transaction_from' => 'Sale Return',
						'transaction_type' => "Sale Return",
						'transaction_remarks' => "credit_sale by order id#" . $last_id,
						'transaction_date' => $_REQUEST['order_date'],
					];
					insert_data($dbc, 'transactions', $credit1);
					$transaction_paid_id = mysqli_insert_id($dbc);
				}

				$newOrder = [
					'payment_status' => $payment_status,
					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'due' => $due_amount,
					'transaction_id' => @$transaction_id,
					'transaction_paid_id' => @$transaction_paid_id,
				];


				if (update_data($dbc, 'orders_return', $newOrder, 'order_id', $_REQUEST['product_order_id'])) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Data Has been Updated";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		}
	} else {
		$msg = "Please Add Any Product";
		$sts = 'error';
	}
	echo json_encode(['msg' => $msg, 'sts' => $sts, 'order_id' => @$last_id, 'type' => "order_return", 'subtype' => $_REQUEST['payment_type']]);
}

/*---------------------- cash sale--return   -------------------------------------------------------------------*/
if (isset($_REQUEST['sale_order_client_name']) && isset($_REQUEST['order_return'])) {
	$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));
	if (!empty($_REQUEST['product_ids'])) {
		# code...
		//print_r(json_encode($_REQUEST));
		$total_ammount = $total_grand = 0;

		$data = [
			'order_date' => $_REQUEST['order_date'],
			'client_name' => $_REQUEST['sale_order_client_name'],
			'client_contact' => $_REQUEST['client_contact'],
			'paid' => @$_REQUEST['paid_ammount'],
			'payment_account' => @$_REQUEST['payment_account'],
			'payment_type' => 'cash',
			'vehicle_no' => @$_REQUEST['vehicle_no'],
			'order_narration' => @$_REQUEST['order_narration'],
			'freight' => @$_REQUEST['freight'],
			'branch_id' => $_REQUEST['branch_id'],
		];

		if ($_REQUEST['product_order_id'] == "") {

			if (insert_data($dbc, 'orders_return', $data)) {
				$last_id = mysqli_insert_id($dbc);
				if (!empty($_FILES['order_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['order_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['order_file']['tmp_name'], $uploadPath)) {
						$data = [
							'order_file' => $fileName,
						];

						update_data($dbc, "orders_return", $data, "order_id", $last_id);
					}
				}
				$paidAmount = @(float)$_REQUEST['paid_ammount'];
				if ($paidAmount > 0) {
					$debit = [
						'credit' => 0,
						'debit' => @$_REQUEST['paid_ammount'],
						'customer_id' => @$_REQUEST['payment_account'],
						'transaction_from' => 'Sale Return',
						'transaction_type' => "cash_in_hand",
						'transaction_remarks' => "cash_sale by order id#" . $last_id,
						'transaction_date' => $_REQUEST['order_date'],
					];
					insert_data($dbc, 'transactions', $debit);
					$transaction_paid_id = mysqli_insert_id($dbc);
				}

				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {

					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$total = (float)$product_quantites * $product_rates;
					$total_ammount += (float)$total;
					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'final_rate' => $_REQUEST['product_final_rates'][$x],
						'rate' => $product_rates,
						'total' => $total,
						'order_id' => $last_id,
						'quantity' => $product_quantites,
						'product_detail' => @$_REQUEST['product_detail'][$x],
						'order_item_status' => 1,
						'branch_id' => $_REQUEST['branch_id'],
					];
					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
						@$qty = (float)$quantity_instock['quantity_instock'] + (float)$product_quantites;
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");

						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] + $product_quantites;
							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						}
					}
					insert_data($dbc, 'order_return_item', $order_items);

					$x++;
				} //end of foreach
				$total_grand =  $total_ammount - $_REQUEST['ordered_discount'];

				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];

				if ($due_amount > 0) {
					$payment_status = 0; //pending

				} else {
					$payment_status = 1; //completed

				}
				$newOrder = [
					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'payment_status' => $payment_status,
					'due' => $due_amount,
					'order_status' => 1,
					'transaction_paid_id' => @$transaction_paid_id,
				];
				if (update_data($dbc, 'orders_return', $newOrder, 'order_id', $last_id)) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Order Return Has been Added";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		} else {
			if (update_data($dbc, 'orders_return', $data, 'order_id', $_REQUEST['product_order_id'])) {
				$last_id = $_REQUEST['product_order_id'];
				if (!empty($_FILES['order_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['order_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['order_file']['tmp_name'], $uploadPath)) {
						$data = [
							'order_file' => $fileName,
						];

						update_data($dbc, "orders_return", $data, "order_id", $last_id);
					}
				}
				if ($get_company['stock_manage'] == 1) {
					$proQ = get($dbc, "order_return_item WHERE order_id='" . $last_id . "' ");

					while ($proR = mysqli_fetch_assoc($proQ)) {
						$newqty = 0;
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $proR['product_id'] . "' "));
						$newqty = (float)$quantity_instock['quantity_instock'] + (float)$proR['quantity'];
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$newqty' WHERE product_id='" . $proR['product_id'] . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $proR['product_id'] . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] + $proR['quantity'];
							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $proR['product_id'] . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						}
					}
				}
				deleteFromTable($dbc, "order_return_item", 'order_id', $_REQUEST['product_order_id']);

				$x = 0;
				foreach ($_REQUEST['product_ids'] as $key => $value) {
					$total = $qty = 0;
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$total = $product_quantites * $product_rates;
					$total_ammount += (float)$total;

					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'rate' => $product_rates,
						'total' => $total,
						'order_id' => $_REQUEST['product_order_id'],
						'quantity' => $product_quantites,
						'product_detail' => $_REQUEST['product_detail'][$x],
						'order_item_status' => 1,
						'branch_id' => $_REQUEST['branch_id'],
					];
					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$quantity_instock = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM  product WHERE product_id='" . $product_id . "' "));
						$qty = (float)$quantity_instock['quantity_instock'] -  $product_quantites;
						$quantity_update = mysqli_query($dbc, "UPDATE product SET  quantity_instock='$qty' WHERE product_id='" . $product_id . "' ");

						$branch_id = $_REQUEST['branch_id'];
						$user_id = $_SESSION['user_id'];
						$inventory = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						if (mysqli_num_rows($inventory) > 0) {

							$inventory = mysqli_fetch_assoc($inventory);
							$inventory_qty = (float)$inventory['quantity_instock'] -  $product_quantites;
							$inventory_update = mysqli_query($dbc, "UPDATE inventory SET  quantity_instock='$inventory_qty' WHERE product_id='" . $product_id . "' AND branch_id='" . $branch_id . "' AND user_id='" . $user_id . "' ");
						}
					}
					//update_data($dbc,'order_item', $order_items , 'order_id',$_REQUEST['product_order_id']);
					insert_data($dbc, 'order_return_item', $order_items);

					$x++;
				} //end of foreach
				$total_grand =  $total_ammount - $_REQUEST['ordered_discount'];
				$due_amount = (float)$total_grand - @(float)$_REQUEST['paid_ammount'];
				if ($due_amount > 0) {
					$payment_status = 0; //pending

				} else {
					$payment_status = 1; //completed

				}
				$newOrder = [

					'total_amount' => $total_ammount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
					'payment_status' => $payment_status,
					'due' => $due_amount,
				];
				$paidAmount = @(float)$_REQUEST['paid_ammount'];
				if ($paidAmount > 0) {
					$credit1 = [
						'credit' => 0,
						'debit' => @$_REQUEST['paid_ammount'],
						'customer_id' => @$_REQUEST['payment_account'],
					];
					$transactions = fetchRecord($dbc, "orders_return", "order_id", $_REQUEST['product_order_id']);
					update_data($dbc, "transactions", $credit1, "transaction_id", $transactions['transaction_paid_id']);
				}
				if (update_data($dbc, 'orders_return', $newOrder, 'order_id', $_REQUEST['product_order_id'])) {
					# code...
					//echo "<script>alert('company Updated....!')</script>";
					$msg = "Data Has been Updated";
					$sts = 'success';
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		}
	} else {
		$msg = "Please Add Any Product";
		$sts = 'error';
	}
	echo json_encode(['msg' => $msg, 'sts' => $sts, 'order_id' => @$last_id, 'type' => "order_return", 'subtype' => $_REQUEST['payment_type']]);
}


/*---------------------- cash purchase   -------------------------------------------------------------------*/
if (isset($_REQUEST['gatepass'])) {
	if (!empty($_REQUEST['product_ids'])) {

		$total_amount = $total_grand = $due_amount = 0;
		$get_company = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM company ORDER BY id DESC LIMIT 1"));

		$data = [
			'gatepass_date' => $_REQUEST['gatepass_date'],
			'gatepass_narration' => @$_REQUEST['gatepass_narration'],
			'payment_type' => 'gatepass',
			'from_branch' => $_REQUEST['from_branch'],
			'to_branch' => $_REQUEST['to_branch'],
		];

		// ---------------------- ADD --------------------------
		if ($_REQUEST['product_purchase_id'] == "") {
			if (insert_data($dbc, 'gatepass', $data)) {
				$last_id = mysqli_insert_id($dbc);

				// Upload file if exists
				if (!empty($_FILES['gatepass_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['gatepass_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['gatepass_file']['tmp_name'], $uploadPath)) {
						$file_data = ['gatepass_file' => $fileName];
						update_data($dbc, "gatepass", $file_data, "gatepass_id", $last_id);
					}
				}

				foreach ($_REQUEST['product_ids'] as $x => $value) {
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$product_salerates = (float)$_REQUEST['product_salerates'][$x];
					$total = $product_quantites * $product_rates;
					$total_amount += $total;

					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'rate' => $product_rates,
						'sale_rate' => $product_salerates,
						'total' => $total,
						'gatepass_id' => $last_id,
						'product_detail' => @$_REQUEST['product_detail'][$x],
						'quantity' => $product_quantites,
						'gatepass_item_status' => 1,
						'from_branch' => $_REQUEST['from_branch'],
						'to_branch' => $_REQUEST['to_branch'],
					];
					insert_data($dbc, 'gatepass_item', $order_items);

				}
				$total_ammount = isset($total_ammount) ? (float)$total_ammount : 0;

				$ordered_discount = isset($_REQUEST['ordered_discount']) ? (float)$_REQUEST['ordered_discount'] : 0;
				$paid_amount = isset($_REQUEST['paid_ammount']) ? (float)$_REQUEST['paid_ammount'] : 0;

				$total_grand = $total_ammount - $ordered_discount;
				$due_amount = $total_grand - $paid_amount;


				$newOrder = [
					'total_amount' => @$total_amount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
				];

				if (update_data($dbc, 'gatepass', $newOrder, 'gatepass_id', $last_id)) {
					$msg = "Gatepass has been added successfully.";
					$sts = "success";
				} else {
					$msg = mysqli_error($dbc);
					$sts = "danger";
				}
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		}

		// ---------------------- UPDATE --------------------------
		else {
			$last_id = $_REQUEST['product_purchase_id'];

			if (update_data($dbc, 'gatepass', $data, 'gatepass_id', $last_id)) {
				// File Upload
				if (!empty($_FILES['gatepass_file']['tmp_name'])) {
					$uploadDir = '../img/uploads/';
					$fileName = time() . '_' . basename($_FILES['gatepass_file']['name']);
					$uploadPath = $uploadDir . $fileName;

					if (move_uploaded_file($_FILES['gatepass_file']['tmp_name'], $uploadPath)) {
						$file_data = ['gatepass_file' => $fileName];
						update_data($dbc, "gatepass", $file_data, "gatepass_id", $last_id);
					}
				}

				// Rollback old items
				if ($get_company['stock_manage'] == 1) {
					$proQ = get($dbc, "gatepass_item WHERE gatepass_id='$last_id'");
					while ($proR = mysqli_fetch_assoc($proQ)) {
						$product_id = $proR['product_id'];
						$qty = $proR['quantity'];

						$from_branch = $proR['from_branch'];
						$to_branch = $proR['to_branch'];
						$user_id = $_SESSION['user_id'];

						// Revert from_branch
						$inv_from = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM inventory WHERE product_id='$product_id' AND branch_id='$from_branch' AND user_id='$user_id'"));
						mysqli_query($dbc, "UPDATE inventory SET quantity_instock=quantity_instock + $qty WHERE product_id='$product_id' AND branch_id='$from_branch' AND user_id='$user_id'");

						// Revert to_branch
						$inv_to = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM inventory WHERE product_id='$product_id' AND branch_id='$to_branch' AND user_id='$user_id'"));
						$new_qty = max(0, $inv_to['quantity_instock'] - $qty);
						mysqli_query($dbc, "UPDATE inventory SET quantity_instock='$new_qty' WHERE product_id='$product_id' AND branch_id='$to_branch' AND user_id='$user_id'");
					}
				}

				// Delete old items
				deleteFromTable($dbc, "gatepass_item", "gatepass_id", $last_id);

				// Add new items
				foreach ($_REQUEST['product_ids'] as $x => $value) {
					$product_quantites = (float)$_REQUEST['product_quantites'][$x];
					$product_rates = (float)$_REQUEST['product_rates'][$x];
					$product_salerates = (float)$_REQUEST['product_salerates'][$x];
					$total = $product_quantites * $product_rates;
					$total_amount += $total;

					$order_items = [
						'product_id' => $_REQUEST['product_ids'][$x],
						'rate' => $product_rates,
						'sale_rate' => $product_salerates,
						'total' => $total,
						'gatepass_id' => $last_id,
						'product_detail' => @$_REQUEST['product_detail'][$x],
						'quantity' => $product_quantites,
						'gatepass_item_status' => 1,
						'from_branch' => $_REQUEST['from_branch'],
						'to_branch' => $_REQUEST['to_branch'],
					];
					insert_data($dbc, 'gatepass_item', $order_items);

					if ($get_company['stock_manage'] == 1) {
						$product_id = $_REQUEST['product_ids'][$x];
						$user_id = $_SESSION['user_id'];

						// Decrease from_branch
						$inv_from = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT quantity_instock FROM inventory WHERE product_id='$product_id' AND branch_id='{$_REQUEST['from_branch']}' AND user_id='$user_id'"));
						$new_qty = max(0, $inv_from['quantity_instock'] - $product_quantites);
						mysqli_query($dbc, "UPDATE inventory SET quantity_instock='$new_qty' WHERE product_id='$product_id' AND branch_id='{$_REQUEST['from_branch']}' AND user_id='$user_id'");

						// Increase to_branch
						$inv_to = mysqli_query($dbc, "SELECT * FROM inventory WHERE product_id='$product_id' AND branch_id='{$_REQUEST['to_branch']}' AND user_id='$user_id'");
						if (mysqli_num_rows($inv_to) > 0) {
							$inv_data = mysqli_fetch_assoc($inv_to);
							$new_qty = $inv_data['quantity_instock'] + $product_quantites;
							mysqli_query($dbc, "UPDATE inventory SET quantity_instock='$new_qty' WHERE product_id='$product_id' AND branch_id='{$_REQUEST['to_branch']}' AND user_id='$user_id'");
						} else {
							$insert_inventory = [
								'product_id' => $product_id,
								'quantity_instock' => $product_quantites,
								'branch_id' => $_REQUEST['to_branch'],
								'user_id' => $user_id,
							];
							insert_data($dbc, 'inventory', $insert_inventory);
						}
					}
				}
				$total_ammount = isset($total_ammount) ? (float)$total_ammount : 0;

				$ordered_discount = isset($_REQUEST['ordered_discount']) ? (float)$_REQUEST['ordered_discount'] : 0;
				$paid_amount = isset($_REQUEST['paid_ammount']) ? (float)$_REQUEST['paid_ammount'] : 0;

				$total_grand = $total_ammount - $ordered_discount;
				$due_amount = $total_grand - $paid_amount;

				// Final update
				$update_total = [
					'total_amount' => $total_amount,
					'discount' => $_REQUEST['ordered_discount'],
					'grand_total' => $total_grand,
				];
				update_data($dbc, 'gatepass', $update_total, 'gatepass_id', $last_id);

				$msg = "Gatepass has been updated successfully.";
				$sts = "success";
			} else {
				$msg = mysqli_error($dbc);
				$sts = "danger";
			}
		}
	}
	echo json_encode(['msg' => $msg, 'sts' => $sts, 'order_id' => @$last_id, 'type' => "order_return", 'subtype' => $_REQUEST['payment_type']]);
}



