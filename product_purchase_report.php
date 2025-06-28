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
		font-weight: bolder !important;
		color: #000 !important;
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

					<div class="card-body">
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

								<table class="table myTable" id="" class="table-responsive">

									<thead>
										<tr>
											<th>Sr#</th>
											<th>Purchase No#</th>
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
										if (!empty($_REQUEST['productName'])) {
											$product_id = $_POST['productName'];
											$q = mysqli_query($dbc, "SELECT * FROM purchase_item WHERE product_id = '$product_id' AND branch_id = '$branch_id' ORDER BY purchase_item_id DESC");
										} else {
											$q = mysqli_query($dbc, "SELECT * FROM purchase_item WHERE branch_id = '$branch_id' ORDER BY purchase_item_id DESC");
										}
										$c = 0;
										while ($r = mysqli_fetch_assoc($q)):
											$purchase__fetch_id = $r['purchase_id'];
											$c++;
											$q2 = mysqli_query($dbc, "SELECT * FROM purchase WHERE purchase_id = '$purchase__fetch_id'");
											while ($r2 = mysqli_fetch_assoc($q2)) {


												?>
												<tr>
													<?php
													$purchase_id = $r['purchase_id'];
													$fetchCustomer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM customers WHERE customer_id='$r2[customer_account]'"));
													$fetchProductName = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM product WHERE product_id='$r[product_id]'"));

													?>
													<td><?= $c ?></td>
													<td><?= $r2['purchase_id'] ?></td>
													<td><?= $r2['purchase_date'] ?></td>
													<td><?= @$r2['client_name']; ?></td>
													<td><?= $fetchProductName['product_name'] ?></td>
													<td><?= $r['quantity'] ?></td>
													<td><?= $r['rate'] ?></td>
													<td><?= $r['total'] ?></td>

												</tr>


												<?php
											}
										endwhile; ?>
									</tbody>
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