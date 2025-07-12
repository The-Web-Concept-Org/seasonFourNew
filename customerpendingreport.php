<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php'; ?>
<style type="text/css">
	/* thead tr th {
		font-size: 19px !important;
		font-weight: bolder !important;
		color: #000 !important;
	}

	tr td {
		font-size: 18px !important;
		color: #000 !important;
	} */

	@media print {

		.print_hide {
			display: none !important;
		}

		.width_for_print {
			width: 100% !important;
		}

		table th {
			width: 33.3% !important;
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
								<b class="text-center card-text">Pending Balance</b>


								<a href="developer.php" class="btn btn-admin float-right btn-sm print_hide"
									onclick="window.print();"><span class="glyphicon glyphicon-print"></span> Print All
									Report </a>
							</div>
						</div>

					</div>
					<div class="card-body width_for_print">

						<table class="table table-hover" cellspacing="5" cellpadding="5" id="myTable">
							<tr>
								<th>Customer Name</th>
								<th>phone</th>
								<th>Blance</th>
								<th class="print_hide">Print</th>


							</tr>

							<?php
							// $sql = "SELECT * FROM customers WHERE customer_status = 1 AND customer_type='customer' ORDER BY customer_name ASC";
							$user_role = $_SESSION['user_role'];
							$branch_id = $_SESSION['branch_id'];

							if ($user_role === 'admin') {
								// Show all customers
								$sql = "SELECT * FROM customers WHERE customer_status = 1 AND customer_type='customer' ORDER BY customer_name ASC";
							} else {
								// Only show customers from the same branch
								$sql = "SELECT * FROM customers WHERE customer_status = 1 AND customer_type='customer' And branch_id = '$branch_id' ORDER BY customer_name ASC";
							}
							$result = $connect->query($sql);
							$ttotalDue = 0;
							while ($row = $result->fetch_array()) {
								$customer_id = $row['customer_id'];
								$customer_name = $row['customer_name'];
								$customer_blance = '';
								$customer_phone = $row['customer_phone'];
								$from_balance = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(credit-debit) AS from_balance FROM transactions WHERE customer_id='" . $customer_id . "'"));



								?>
								<tr>
									<td class="text-capitalize"><?php echo $customer_name; ?>(<?= $row['customer_type'] ?>)
									</td>
									<td><?php echo $customer_phone; ?></td>
									<td><?php echo number_format($from_balance['from_balance']); ?></td>
									<td class="print_hide"><a href="print_balance.php?customer=<?php echo $row[0]; ?>">
											<button class="btn btn-info"><span class="glyphicon glyphicon-print"></span>
												Print</button></td>
								</tr>
								<?php
								$ttotalDue += $from_balance['from_balance'];
							} // while customer
							
							?>


							<tr>
								<th colspan="3">Total Due</th>
								<th>
									<h3><?= $ttotalDue ?></h3>
								</th>
							</tr>
						</table>

					</div>
				</div> <!-- .card -->
			</div> <!-- .container-fluid -->

		</main> <!-- main -->
	</div> <!-- .wrapper -->

</body>

</html>
<?php include_once 'includes/foot.php'; ?>