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
								<b class="text-center card-text">Product Sale Report</b>
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
										$productSql = "SELECT * FROM product WHERE status=1  ORDER BY product_name ASC";
										$productData = $connect->query($productSql);

										while ($row = $productData->fetch_array()) {
											$product_id = $row['product_id'];
											$fetchProduct = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM product WHERE status=1 AND product_id='$product_id'"));
											$fetchCategory = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM categories WHERE categories_id='{$fetchProduct['category_id']}'"));
											$brand = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM brands WHERE brand_id='{$fetchProduct['brand_id']}'"));
											$category_show = $fetchCategory['categories_name'] ?? '';
											$brand1 = $brand['brand_name'] ?? '';
											echo "<option value='{$row['product_id']}'>{$row['product_name']} ({$category_show}) [{$brand1}]</option>";
										}
										?>
									</select>
								</div>

								<div class="form-group col-md-4 mt-2">
									<button type="submit" name="show_deatils" class="btn btn-danger btn-block">Show
										Details</button>
								</div>

							</div>
						</form>
					</div>

				</div> <!-- .card -->

				<?php if (isset($_POST['show_deatils'])):

					?>

					<div class="card">
						<div class="card-body">
							<button onclick="window.print();"
								class="btn btn-admin btn-sm float-right print_btn print_hide ml-2">Print
								Report</button>
							<table class="table">

								<thead>
									<tr>
										<th>sr#</th>
										<th>Invoice#</th>
										<th>Date</th>
										<th>Customer</th>
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
									$totalQtyS = 0;
									$totalAmountS = 0;
									$totalQtySr = 0;
									$totalAmountSr = 0;
									$totalQtyG = 0;
									$totalAmountG = 0;
									$totalQtyDn = 0;
									$totalAmountDn = 0;

									// --- ORDERS ---
									$q = mysqli_query($dbc, "SELECT oi.*, o.order_date, o.client_name, 'Sale' as source FROM order_item oi
                                                                           JOIN orders o ON oi.order_id = o.order_id
                                                                           WHERE oi.branch_id = '$branch_id' $product_id_filter ORDER BY order_id DESC");

									while ($r = mysqli_fetch_assoc($q)) {
										$product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT product_name,brand_id FROM product WHERE product_id='{$r['product_id']}'"));
										$brand = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT brand_name FROM brands WHERE brand_id='{$product['brand_id']}'"));
										$totalQtyS += $r['quantity'];
										$totalAmountS += $r['total'];
										$c++;
										?>
										<tr>
											<td><?= $c ?></td>
											<td>Sale #<?= $r['order_id'] ?></td>
											<td><?= $r['order_date'] ?></td>
											<td><?= $r['client_name'] ?></td>
											<td><?= $product['product_name'] ?><?= ($brand['brand_name'] ? '(' . $brand['brand_name'] . ')' : '') ?>
											</td>
											<td><?= $r['quantity'] ?></td>
											<td><?= $r['rate'] ?></td>
											<td><?= $r['total'] ?></td>
										</tr>

									<?php }

									// --- ORDER RETURNS ---
									$q = mysqli_query($dbc, "SELECT ori.*, orr.order_date, orr.client_name, 'Return' as source FROM order_return_item ori
                                                                           JOIN orders_return orr ON ori.order_id = orr.order_id
                                                                           WHERE ori.branch_id = '$branch_id' $product_id_filter ORDER BY order_id DESC");

									while ($r = mysqli_fetch_assoc($q)) {
										$product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT product_name,brand_id FROM product WHERE product_id='{$r['product_id']}'"));
										$brand = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT brand_name FROM brands WHERE brand_id='{$product['brand_id']}'"));
										$totalQtySr += $r['quantity'];
										$totalAmountSr += $r['total'];
										$c++; ?>
										<tr>
											<td><?= $c ?></td>
											<td>Sale Return #<?= $r['order_id'] ?></td>
											<td><?= $r['order_date'] ?></td>
											<td><?= $r['client_name'] ?></td>
											<td><?= $product['product_name'] ?><?= ($brand['brand_name'] ? '(' . $brand['brand_name'] . ')' : '') ?>
											</td>
											<td><?= $r['quantity'] ?></td>
											<td><?= $r['rate'] ?></td>
											<td><?= $r['total'] ?></td>
										</tr>

									<?php }

									// --- GATEPASS ---
									$q = mysqli_query($dbc, "SELECT gi.*, g.gatepass_date,g.to_branch, 'Gatepass' as source FROM gatepass_item gi
																		   JOIN gatepass g ON gi.gatepass_id = g.gatepass_id
																		   WHERE gi.from_branch = '$branch_id' $product_id_filter ORDER BY gatepass_id DESC");

									while ($r = mysqli_fetch_assoc($q)) {
										$product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT product_name,brand_id FROM product WHERE product_id='{$r['product_id']}'"));
										$brand = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT brand_name FROM brands WHERE brand_id='{$product['brand_id']}'"));
										$branch = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT branch_name FROM branch WHERE branch_id='{$r['to_branch']}'"));
										$totalQtyG += $r['quantity'];
										$totalAmountG += $r['total'];
										$c++; ?>
										<tr>
											<td><?= $c ?></td>
											<td>Gatepass #<?= $r['gatepass_id'] ?></td>
											<td><?= $r['gatepass_date'] ?></td>
											<td><?= $branch['branch_name'] ?> Branch</td>
											<td><?= $product['product_name'] ?><?= ($brand['brand_name'] ? '(' . $brand['brand_name'] . ')' : '') ?>
											</td>
											<td><?= $r['quantity'] ?></td>
											<td><?= $r['rate'] ?></td>
											<td><?= $r['total'] ?></td>
										</tr>

									<?php }

									// --- QUOTATIONS with is_delivery_note = 1 ---
									$q = mysqli_query($dbc, "SELECT qi.*, q.quotation_date, q.client_name FROM quotation_item qi
																		   JOIN quotations q ON qi.quotation_id = q.quotation_id
																		   WHERE q.is_delivery_note = '1' AND q.branch_id = '$branch_id' $product_id_filter ORDER BY quotation_id DESC");

									while ($r = mysqli_fetch_assoc($q)) {
										$product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT product_name,brand_id FROM product WHERE product_id='{$r['product_id']}'"));
										$brand = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT brand_name FROM brands WHERE brand_id='{$product['brand_id']}'"));
										$totalQtyDn += $r['quantity'];
										$totalAmountDn += $r['total'];
										$c++; ?>
										<tr>
											<td><?= $c ?></td>
											<td>DeliveryNote #<?= $r['quotation_id'] ?></td>
											<td><?= $r['quotation_date'] ?></td>
											<td><?= $r['client_name'] ?></td>
											<td><?= $product['product_name'] ?><?= ($brand['brand_name'] ? '(' . $brand['brand_name'] . ')' : '') ?>
											</td>
											<td><?= $r['quantity'] ?></td>
											<td><?= $r['rate'] ?></td>
											<td><?= $r['total'] ?></td>
										</tr>

									<?php }
									?>

								</tbody>
								<tfoot>
									<tr style="font-weight: bold;">
										<td colspan="5" align="right">Total</td>
										<td><?= $totalQtyS - $totalQtySr + $totalQtyG + $totalQtyDn ?></td>
										<td></td>
										<td><?= number_format($totalAmountS - $totalAmountSr + $totalAmountG + $totalAmountDn) ?>
										</td>
									</tr>
									<tr> <?php ?></tr>
								</tfoot>
							</table>


						</div>
					</div>

				<?php endif ?>
			</div> <!-- .container-fluid -->

		</main> <!-- main -->
	</div> <!-- .wrapper -->

</body>

</html>
<?php include_once 'includes/foot.php'; ?>