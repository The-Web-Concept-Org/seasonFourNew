<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php'; ?>

<body class="horizontal light  ">
	<div class="wrapper">
		<?php include_once 'includes/header.php'; ?>
		<main role="main" class="main-content">
			<div class="container-fluid">
				<div class="card">
					<div class="card-header card-bg" align="center">

						<div class="row">
							<div class="col-12 mx-auto h4">
								<b class="text-center card-text">Purchase Report</b>


							</div>
						</div>

					</div>
					<div class="card-body">
						<form action="" method="post" class=" print_hide">
							<div class="row">

								<div class="col-sm-2">
									<div class="form-group">
										<label for="">Customer Account</label>
										<select class="form-control" name="customer_id" autofocus="true">
											<option value="">~~SELECT~~</option>
											<?php
											$sql = "SELECT * FROM customers WHERE customer_status = 1 AND customer_type='supplier'";
											$result = $connect->query($sql);

											while ($row = $result->fetch_array()) {
												echo "<option value='" . $row[0] . "'>" . $row[1] . "</option>";
											} // while

											?>
										</select>
									</div><!-- group -->
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="">Branch</label>
										<select class="form-control searchableSelect" name="branch_id" id="branch_id" required>
											<option selected disabled>Select Branch</option>
											<?php $branch = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status = 1");
											while ($row = mysqli_fetch_array($branch)) { ?>
												?>
												<option <?= @($fetchusers['branch_id'] == $row['branch_id']) ? "seleted" : "" ?> value="<?= $row['branch_id'] ?>"><?= $row['branch_name'] ?></option>
											<?php } ?>
										</select>
									</div><!-- group -->
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="">From</label>
										<input type="text" class="form-control" autocomplete="off" name="from_date" id="from" placeholder="From Date">
									</div><!-- group -->
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="">To</label>
										<input type="text" class="form-control" autocomplete="off" name="to_date" id="to" placeholder="To Date">
									</div><!-- group -->
								</div>
								<div class="col-sm-2">
									<label style="visibility: hidden;">a</label><br>
									<button class="btn btn-admin2" name="search_sale" type="submit">Search</button>

								</div>
							</div>



						</form>
					</div>
				</div> <!-- .card -->
				<?php if (isset($_REQUEST['search_sale'])):



				?>
					<div class="card">
						<div class="card-header card-bg" align="center">

							<div class="row">
								<div class="col-12 mx-auto h4">
									<b class="text-center card-text">Purchase Report</b>
									<button onclick="window.print();" class="btn btn-admin btn-sm float-right print_btn print_hide">Print Report</button>


								</div>
							</div>

						</div>
						<div class="card-body">

							<table class="table table-bordered">

								<thead>
									<tr>
										<th>Sr.No</th>
										<th>Dated</th>
										<th>Bill#</th>
										<th>Item</th>
										<th>purchased Qty</th>
										<th>Rate</th>
										<th>Grand Total</th>
										<th>Party Detail</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i = 1;

									// Get filter values from POST
									$f_date      = $_POST['from_date'] ?? '';
									$t_date      = $_POST['to_date'] ?? '';
									$customer_id = $_POST['customer_id'] ?? '';
									$branch_id   = $_POST['branch_id'] ?? '';
									// Build the query
									$query = "SELECT * FROM purchase WHERE 1=1";

									if (!empty($f_date) && !empty($t_date)) {
										$query .= " AND purchase_date BETWEEN '$f_date' AND '$t_date'";
									}

									if (!empty($customer_id)) {
										$query .= " AND customer_account = '$customer_id'";
									}

									if (!empty($branch_id)) {
										$query .= " AND branch_id = '$branch_id'";
									}

									// Run the query
									$q = mysqli_query($dbc, $query); ?>
									<?php while ($r = mysqli_fetch_assoc($q)):

										$fetchCustomer = fetchRecord($dbc, "customers", "customer_id", $r['customer_account']);
										$getItem = mysqli_query($dbc, "SELECT * FROM purchase_item WHERE purchase_id='$r[purchase_id]'");

									?>

										<tr>
											<th><?= $i ?></th>
											<th><?= date('D, d-M-Y', strtotime($r['purchase_date'])) ?></th>
											<th><?= $r['purchase_id'] ?></th>
											<th>
												<?php

												while ($fetchItem = mysqli_fetch_assoc($getItem)):
													$fetchProduct = fetchRecord($dbc, "product", 'product_id', $fetchItem['product_id']);
													$fetchCategory = fetchRecord($dbc, "categories", "categories_id", $fetchProduct['category_id']); ?>
													<p><?= $fetchProduct['product_name'] ?> <small><?= $fetchCategory['categories_name'] ?></small></p>
												<?php endwhile; ?>
											</th>
											<th>
												<?php
												$getItem = mysqli_query($dbc, "SELECT * FROM purchase_item WHERE purchase_id='$r[purchase_id]'");
												while ($fetchItem = mysqli_fetch_assoc($getItem)):
													$fetchProduct = fetchRecord($dbc, "product", 'product_id', $fetchItem['product_id']);
													$fetchCategory = fetchRecord($dbc, "categories", "categories_id", $fetchProduct['category_id']); ?>
													<p><?= $fetchItem['quantity'] ?> <span class="text-right">x</span></p>
												<?php endwhile; ?>
											</th>
											<th>
												<?php
												$getItem = mysqli_query($dbc, "SELECT * FROM purchase_item WHERE purchase_id='$r[purchase_id]'");
												while ($fetchItem = mysqli_fetch_assoc($getItem)):
													$fetchProduct = fetchRecord($dbc, "product", 'product_id', $fetchItem['product_id']);
													$fetchCategory = fetchRecord($dbc, "categories", "categories_id", $fetchProduct['category_id']); ?>
													<p><?= $fetchItem['rate'] ?></p>
												<?php endwhile; ?>
											</th>
											<th>
												<?php
												$getItem = mysqli_query($dbc, "SELECT * FROM purchase_item WHERE purchase_id='$r[purchase_id]'");
												while ($fetchItem = mysqli_fetch_assoc($getItem)):
													$fetchProduct = fetchRecord($dbc, "product", 'product_id', $fetchItem['product_id']);
													$fetchCategory = fetchRecord($dbc, "categories", "categories_id", $fetchProduct['category_id']); ?>
													<p><?= $fetchItem['total'] ?></p>
												<?php endwhile; ?>
											</th>
											<th><?= $fetchCustomer['customer_name'] ?> <br><?= $r['client_contact'] ?></th>
										</tr>
									<?php $i++;
									endwhile; ?>
								</tbody>

							</table>
						</div>
					</div>
				<?php endif; ?>

			</div> <!-- .container-fluid -->

		</main> <!-- main -->
	</div> <!-- .wrapper -->

</body>

</html>
<?php include_once 'includes/foot.php'; ?>
<script>
	$(function() {
		var dateFormat = "yy-mm-dd";
		from = $("#from")
			.datepicker({
				changeMonth: true,
				numberOfMonths: 1,
				dateFormat: "yy-mm-dd",
			})
			.on("change", function() {
				to.datepicker("option", "minDate", getDate(this));
			}),
			to = $("#to").datepicker({
				changeMonth: true,
				numberOfMonths: 1,
				dateFormat: "yy-mm-dd",
			})
			.on("change", function() {
				from.datepicker("option", "maxDate", getDate(this));
			});

		function getDate(element) {
			var date;
			try {
				date = $.datepicker.parseDate(dateFormat, element.value);
			} catch (error) {
				date = null;
			}

			return date;
		}
	});
</script>