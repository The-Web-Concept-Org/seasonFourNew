<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php'; ?>
<style type="text/css">
	thead tr th {
		font-size: 19px !important;
		font-weight: bolder !important;
		color: #000 !important;
	}

	tr td {
		font-size: 18px !important;
		/* font-weight: bolder !important; */
		color: #000 !important;
	}

	@media print {

		.print_hide {
			display: none !important;
		}

		.form_sec {
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
								<b class="text-center card-text">Product Purchase Report</b>


							</div>
						</div>

					</div>

					<div class="card-body form_sec">
						<form method="post">
							<div class="form-row align-items-end">

								<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
									<div class="form-group col-md-4">
										<label for="branch_id">Branch</label>
										<select class="form-control searchableSelect" name="branch_id" id="branch_id"
											required>
											<option selected disabled value="">Select Branch</option>
											<?php
											$branch = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status = 1");
											while ($row = mysqli_fetch_array($branch)) { ?>
												<option <?= (@$voucher['branch_id'] == $row['branch_id']) ? 'selected' : '' ?>
													value="<?= $row['branch_id'] ?>">
													<?= $row['branch_name'] ?>
												</option>
											<?php } ?>
										</select>
									</div>
								<?php else: ?>
									<input type="hidden" name="branch_id" value="<?= $_SESSION['branch_id'] ?>">
								<?php endif; ?>
								<div class="form-group col-md-4">
									<label for="productName">Product</label>
									<select class="form-control searchableSelect" name="productName" id="productName">
										<option value="">~~SELECT~~</option>
										<?php
										$productSql = "SELECT * FROM product ORDER BY product_name ASC";
										$productData = $connect->query($productSql);

										while ($row = $productData->fetch_array()) {
											$product_id = $row['product_id'];
											$fetchProduct = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM product WHERE product_id='$product_id'"));
											$fetchCategory = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM categories WHERE categories_id='{$fetchProduct['category_id']}'"));
											$brand = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM brands WHERE brand_id='{$fetchProduct['brand_id']}'"));
											$category_show = $fetchCategory['categories_name'] ?? '';
											$brand1 = $brand['brand_name'] ?? '';
											echo "<option value='{$row['product_id']}'>{$row['product_name']} ({$category_show}) [{$brand1}]</option>";
										}
										?>
									</select>
								</div>

								<div class="form-group col-md-4 mt-4">
									<button type="submit" name="show_deatils" class="btn btn-danger btn-block">Show
										Details</button>
								</div>

							</div>
						</form>
					</div>

					<!-- .card -->

					<?php if (isset($_POST['show_deatils'])):
						$product_id = $_POST['productName'];
						?>
						<div class="card">
							<div class="card-body">
								<button onclick="window.print();"
									class="btn btn-admin btn-sm float-right print_btn print_hide ml-2">Print
									Report</button>
								<table class="table myTable" id="" class="table-responsive">

									<thead>
										<tr>
											<th>Sr#</th>
											<th>Invoice#</th>
											<th>Date</th>
											<th>Supplier</th>
											<th>Product Name</th>
											<th>Quantity</th>
											<th>Rate</th>
											<th>Total Amount</th>

										</tr>

									</thead>
									<tbody>
										<?php
										$branch_id = $_POST['branch_id'];
										$product_id_filter = !empty($_POST['productName']) ? "AND product_id = '{$_POST['productName']}'" : '';
										$c = 0;
										$totalQtyp = 0;
										$totalAmountp = 0;
										$totalQtypr = 0;
										$totalAmountpr = 0;
										// --- ORDERS ---
										$q = mysqli_query($dbc, "SELECT pi.*, p.purchase_date, p.client_name, 'Purchase' as source FROM purchase_item pi
																				JOIN purchase p ON pi.purchase_id = p.purchase_id
																				WHERE pi.branch_id = '$branch_id' $product_id_filter ORDER BY purchase_id DESC");

										while ($r = mysqli_fetch_assoc($q)) {
											$product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT product_name,brand_id FROM product WHERE product_id='{$r['product_id']}'"));
											$brand = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT brand_name FROM brands WHERE brand_id='{$product['brand_id']}'"));
											$totalQtyp += $r['quantity'];
											$totalAmountp += $r['total'];
											$c++; ?>
											<tr>
												<td><?= $c ?></td>
												<td>Purchase #<?= $r['purchase_id'] ?></td>
												<td><?= $r['purchase_date'] ?></td>
												<td><?= $r['client_name'] ?></td>
												<td><?= $product['product_name'] ?><?= ($brand['brand_name'] ? '(' . $brand['brand_name'] . ')' : '') ?>
												</td>
												<td><?= $r['quantity'] ?></td>
												<td><?= $r['rate'] ?></td>
												<td><?= $r['total'] ?></td>
											</tr>

										<?php }

										// --- ORDER RETURNS ---
										$q = mysqli_query($dbc, "SELECT pri.*, prr.purchase_date, prr.client_name, 'Return' as source FROM purchase_return_item pri
																			   JOIN purchase_return prr ON pri.purchase_id = prr.purchase_id
																			   WHERE pri.branch_id = '$branch_id' $product_id_filter ORDER BY purchase_id DESC");

										while ($r = mysqli_fetch_assoc($q)) {
											$product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT product_name,brand_id FROM product WHERE product_id='{$r['product_id']}'"));
											$brand = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT brand_name FROM brands WHERE brand_id='{$product['brand_id']}'"));
											$totalQtypr += $r['quantity'];
											$totalAmountpr += $r['total'];
											$c++; ?>
											<tr>
												<td><?= $c ?></td>
												<td>Pur Return #<?= $r['purchase_id'] ?></td>
												<td><?= $r['purchase_date'] ?></td>
												<td><?= $r['client_name'] ?></td>
												<td><?= $product['product_name'] ?><?= ($brand['brand_name'] ? '(' . $brand['brand_name'] . ')' : '') ?>
												</td>
												<td><?= $r['quantity'] ?></td>
												<td><?= $r['rate'] ?></td>
												<td><?= $r['total'] ?></td>
											</tr>
											<?php
										}

										// --- GATEPASS (from_branch as Supplier) ---
										$q = mysqli_query($dbc, "SELECT gi.*, g.gatepass_date, g.from_branch, 'Gatepass Out' as source 
																				FROM gatepass_item gi
																				JOIN gatepass g ON gi.gatepass_id = g.gatepass_id
																				WHERE gi.to_branch = '$branch_id' $product_id_filter 
																				ORDER BY gi.gatepass_id DESC");

										while ($r = mysqli_fetch_assoc($q)) {
											$product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT product_name, brand_id FROM product WHERE product_id='{$r['product_id']}'"));
											$brand = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT brand_name FROM brands WHERE brand_id='{$product['brand_id']}'"));
											$fromBranchName = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT branch_name FROM branch WHERE branch_id = '{$r['from_branch']}'"))['branch_name'];

											$totalQtyp += $r['quantity']; // You can keep separate total if needed
											$c++; ?>
											<tr>
												<td><?= $c ?></td>
												<td>Gatepass #<?= $r['gatepass_id'] ?></td>
												<td><?= $r['gatepass_date'] ?></td>
												<td><?= $fromBranchName ?> Branch</td>
												<td><?= $product['product_name'] ?><?= ($brand['brand_name'] ? '(' . $brand['brand_name'] . ')' : '') ?>
												</td>
												<td><?= $r['quantity'] ?></td>
												<td>-</td>
												<td>-</td>
											</tr>
										<?php } ?>

									</tbody>
									<tfoot>
										<tr style="font-weight: bold;">
											<td colspan="5" align="right">Total</td>
											<td><?= $totalQtyp - $totalQtypr ?></td>
											<td></td>
											<td><?= number_format($totalAmountp - $totalAmountpr) ?></td>
										</tr>
									</tfoot>

								</table>


							</div>
						</div>
						<!-- .card -->
					<?php endif ?>

				</div> <!-- .container-fluid -->

		</main> <!-- main -->
	</div> <!-- .wrapper -->

</body>

</html>
<?php include_once 'includes/foot.php'; ?>