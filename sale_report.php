<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php'; ?>
<style type="text/css">
	thead tr th {
		font-size: 19px !important;
		font-weight: bolder !important;
		color: #000 !important;
	}

	tbody tr th,
	tbody tr th p {
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

						<div class="row d-print-none">
							<div class="col-12 mx-auto h4">
								<b class="text-center card-text">Sale Report</b>


							</div>
						</div>

					</div>
					<div class="card-body">
						<form action="" method="get" class=" d-print-none">
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label for="">Customer Account</label>
										<select class="form-control text-capitalize" id="clientName" name="customer_id" autofocus="true">
											<option value="">~~SELECT~~</option>
											<?php
											$sql = "SELECT * FROM customers WHERE customer_status = 1 AND customer_type='customer'";
											$result = $connect->query($sql);

											while ($row = $result->fetch_array()) {
												echo "<option class='" . 'text-capitalize' . "' value='" . $row['customer_id'] . "'>" . $row['customer_name'] . "</option>";
											} // while

											?>
										</select>
									</div><!-- group -->

								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="">Branch</label>
										<select class="form-control searchableSelect text-capitalize" name="branch_id" id="branch_id" required>
											<option selected disabled>Select Branch</option>
											<?php $branch = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status = 1");
											while ($row = mysqli_fetch_array($branch)) { ?>
												?>
												<option class="text-capitalize" <?= @($fetchusers['branch_id'] == $row['branch_id']) ? "seleted" : "" ?> value="<?= $row['branch_id'] ?>"><?= $row['branch_name'] ?></option>
											<?php } ?>
										</select>
									</div><!-- group -->
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="">From</label>
										<input type="text" class="form-control" autocomplete="off" name="from_date" id="from" placeholder="From Date">
									</div><!-- group -->
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="">To</label>
										<input type="text" class="form-control" autocomplete="off" name="to_date" id="to" placeholder="To Date">
									</div><!-- group -->
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Type</label>
										<select class="form-control" name="sale_type">
											<option value="all">Select Type</option>
											<option value="cash_in_hand">Cash Sale</option>
											<option value="credit_sale">Credit Sale</option>
										</select>
									</div>
								</div>
								<div class="col-sm-1">
									<label style="visibility: hidden;">a</label><br>
									<button class="btn btn-admin2" name="search_sale" type="submit">Search</button>

								</div>
							</div>


						</form>
					</div>
				</div> <!-- .card -->
				<?php if (isset($_REQUEST['search_sale'])):
					$qty = 0;
					$f_date = $_REQUEST['from_date'];
					$t_date = $_REQUEST['to_date'];
					$customer_id = $_REQUEST['customer_id'];
				?>
					<div class="card">
						<div class="card-header card-bg" align="center">

							<div class="row">
								<div class="col-12 mx-auto h4">
									<b class="text-center card-text">Sale Report</b>
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
										<th style="width: 100px">Bill#</th>
										<th>Item</th>
										<th>Sold Qty</th>
										<th>Rate</th>
										<th>Total</th>
										<th>Payment Detail</th>
										<th>Party Detail</th>
										<th>Sale Type</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i = 1;

									$conditions = [];

									if (!empty($_REQUEST['customer_id'])) {
										$conditions[] = "customer_account='" . $_REQUEST['customer_id'] . "'";
									}

									if (!empty($_REQUEST['sale_type']) && $_REQUEST['sale_type'] != 'all') {
										$conditions[] = "payment_type='" . $_REQUEST['sale_type'] . "'";
									}

									if (!empty($_REQUEST['branch_id'])) {
										$conditions[] = "branch_id='" . $_REQUEST['branch_id'] . "'";
									}

									if (!empty($f_date) && !empty($t_date)) {
										$conditions[] = "(order_date BETWEEN '$f_date' AND '$t_date')";
									}

									$q = "SELECT * FROM orders";
									if (!empty($conditions)) {
										$q .= " WHERE " . implode(' AND ', $conditions);
									}
									?>

									<?php
									//echo $q;
									$query = mysqli_query($dbc, $q);

									$Grandgrandtotal = 0;
									$creditGrand = 0;
									while ($r = mysqli_fetch_assoc($query)):

										$fetchCustomer = fetchRecord($dbc, "customers", "customer_id", $r['customer_account']);
										$getItem = mysqli_query($dbc, "SELECT * FROM order_item WHERE order_id='$r[order_id]'");

									?>

										<tr>
											<th><?= $i ?></th>
											<th><?= date('D, d-M-Y', strtotime($r['order_date'])) ?></th>
											<th
												<?php
												if ($r['payment_type'] == 'credit_sale') {
												?>
												style="background-color: black;color: white!important"
												<?php
													# code...
												}
												?>>
												<?php
												if ($r['payment_type'] == 'cash_in_hand') {
													echo "A.T. ";
												}
												?>
												<?= $r['order_id'] ?></th>
											<th>
												<?php

												while ($fetchItem = mysqli_fetch_assoc($getItem)):
													$fetchProduct = fetchRecord($dbc, "product", 'product_id', $fetchItem['product_id']);
													$fetchCategory = fetchRecord($dbc, "categories", "categories_id", $fetchProduct['category_id']); ?>
													<p><?= $fetchProduct['product_name'] ?> <small>(<?= strtoupper($fetchCategory['categories_name']) ?>)</small></p>
												<?php endwhile; ?>
											</th>
											<th>
												<?php
												$getItem = mysqli_query($dbc, "SELECT * FROM order_item WHERE order_id='$r[order_id]'");
												while ($fetchItem = mysqli_fetch_assoc($getItem)):
													$fetchProduct = fetchRecord($dbc, "product", 'product_id', $fetchItem['product_id']);
													$fetchCategory = fetchRecord($dbc, "categories", "categories_id", $fetchProduct['category_id']); ?>
													<p><?= $fetchItem['quantity'] ?> <span class="text-right">x</span></p>
												<?php endwhile; ?>
											</th>
											<th>
												<?php
												$getItem = mysqli_query($dbc, "SELECT * FROM order_item WHERE order_id='$r[order_id]'");
												while ($fetchItem = mysqli_fetch_assoc($getItem)):
													$fetchProduct = fetchRecord($dbc, "product", 'product_id', $fetchItem['product_id']);
													$fetchCategory = fetchRecord($dbc, "categories", "categories_id", $fetchProduct['category_id']); ?>
													<p><?= $fetchItem['rate'] ?></p>
												<?php endwhile; ?>
											</th>
											<th>
												<?php
												$getItem = mysqli_query($dbc, "SELECT * FROM order_item WHERE order_id='$r[order_id]'");
												while ($fetchItem = mysqli_fetch_assoc($getItem)):
													$fetchProduct = fetchRecord($dbc, "product", 'product_id', $fetchItem['product_id']);
													$fetchCategory = fetchRecord($dbc, "categories", "categories_id", $fetchProduct['category_id']); ?>
													<p><?= $fetchItem['total'] ?></p>
												<?php endwhile; ?>
											</th>
											<th>
												Grand Total:<?= @$r['grand_total'] ?><br>
												Paid:<?= @$r['paid'] ?><br>

												Due: <?= $r['due'] ?>

												<?php
												if ($r['payment_type'] == 'cash_in_hand') {
													# code...

													$Grandgrandtotal += $r['grand_total'];
												} else {
													$creditGrand += $r['grand_total'];
												}
												?>

											</th>
											<th>
												<?= $r['client_name'] ?> <br><?= $r['client_contact'] ?>

											</th>
											<th><?= $r['payment_type'] ?></th>
										</tr>
									<?php $i++;
									endwhile; ?>
								</tbody>
								<tr>
									<td colspan="7">
										<center>
											<h3>Cash Sale</h3>
										</center>
									</td>
									<td>
										<h3><?= $Grandgrandtotal ?></h3>
									</td>
									<td>
										<h3>Credit Sale </h3>
									</td>
									<td>
										<h3><?= $creditGrand ?></h3>
									</td>
								</tr>
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