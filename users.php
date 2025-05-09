	<!DOCTYPE html>
	<html lang="en">
	<?php include_once 'includes/head.php';
	//include_once 'includes/code.php';
	?>

	<body class="horizontal light  ">
		<div class="wrapper">
			<?php include_once 'includes/header.php'; ?>
			<main role="main" class="main-content">
				<div class="container">
					<div class="row ">
						<div class="col-12">

							<div class="card ">
								<div class="card-header card-bg " align="center">
									<h4 class="card-text"> Create Users</h4>
								</div>
								<div class="card-body">
									<?= getMessage(@$msg, @$sts); ?>
									<form class="form-horizontal" method="POST" action="includes/code.php" id="formData2">
										<input type="hidden" name="action" value="add_new_user">
										<input type="hidden" name="new_user_id" value="<?= @$_REQUEST['user_edit_id'] ?>">

										<div class="form-group row">
											<label for="clientContact" class="col-sm-2 control-label">Full Name</label>
											<div class="col-sm-4">
												<input type="text" class="form-control" id="username" name="username" placeholder="Username" autocomplete="off" required value="<?= @$fetchusers['username'] ?>" />
											</div>
											<label for="clientContact" class="col-sm-2 control-label">Username</label>
											<div class="col-sm-4">
												<input type="text" class="form-control" pattern="^[a-zA-Z0-9]*$" id="check_userName" name="username" placeholder="Username" autocomplete="off" required value="<?= @$fetchusers['username'] ?>" />
											</div>

										</div> <!--/form-group-->
										<div class="form-group row">
											<label for="clientContact" class="col-sm-2 control-label">Phone Number</label>
											<div class="col-sm-4">
												<input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" autocomplete="off" required value="<?= @$fetchusers['phone'] ?>" />
											</div>
											<label for="clientContact" class="col-sm-2 control-label">Email</label>
											<div class="col-sm-4">
												<input type="email" class="form-control" id="email" name="email" placeholder="Email" autocomplete="off" required value="<?= @$fetchusers['email'] ?>" />
											</div>

										</div> <!--/form-group-->

										<div class="form-group row">

											<label for="clientContact" class="col-sm-2 control-label">Password </label>

											<div class="col-sm-4">
												<input type="text" class="form-control" id="password" name="password" placeholder="Password" autocomplete="off" /><span style="color: red" class="text-center">Password Automatically encrypted due to security issues</span>
												<input type="hidden" name="old_password" value="<?= isset($_REQUEST['user_edit_id']) ? $fetchusers['password'] : '123456' ?>" />
											</div>

												<label for="branch_id" class="col-sm-2 control-label">Branch </label>

												<div class="col-sm-4">
													<select class="form-control searchableSelect" name="branch_id" id="branch_id" required>
														<option selected disabled>Select Branch</option>
														<?php $branch = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status = 1");
														while ($row = mysqli_fetch_array($branch)) { ?>
															?>
															<option <?= @($fetchusers['branch_id'] == $row['branch_id']) ? "seleted" : "" ?> value="<?= $row['branch_id'] ?>"><?= $row['branch_name'] ?></option>
														<?php } ?>
													</select>
												</div>

											<label for="clientContact" class="col-sm-2 control-label">UserRole</label>
											<div class="col-sm-4">
												<select class="form-control" name="user_role">
													<option value="subadmin">Sub Admin</option>
													<option value="manager">Manager</option>
													<option value="cashier">Cashier</option>
													<option value="localusers">Local USer</option>

												</select>
											</div>
											<label for="clientContact" class="col-sm-2 control-label">Status </label>

											<div class="col-sm-4">
												<select class="form-control" name="status">
													<option <?= @($fetchusers['status'] == "1") ? "seleted" : "" ?> value="1">Active</option>
													<option <?= @($fetchusers['status'] == "0") ? "seleted" : "" ?> value="0">Not Active</option>
												</select>
											</div>
										</div> <!--/form-group-->

										<div class="form-group row">


											<label for="clientContact" class="col-sm-2 control-label">Address</label>
											<div class="col-sm-4">
												<input type="text" class="form-control" id="address" name="address" placeholder="Enter Address" autocomplete="off" required value="<?= @$fetchusers['address'] ?>" />

											</div>
											<div class="col-sm-2 offset-10 ">
												<?= $users_button; ?>
											</div>

										</div> <!--/form-group-->

									</form>
									<br><br>
								</div>
							</div>

						</div>
						<div class="col-sm-12">
							<div class="card mt-2">
								<div class="card-header cyan-bgcolor" align="center">
									<h4>Users List</h4>
								</div>
								<div class="card-body">
									<?php getMessage(@$msg, @$sts); ?>
									<table class="table example1" id="myTable">
										<thead>
											<tr>
												<th class="text-dark">Sr</th>
												<th class="text-dark">Username</th>
												<th class="text-dark">Email</th>
												<th class="text-dark">Phone</th>
												<th class="text-dark">Address</th>
												<th class="text-dark">User Role</th>
												<th class="text-dark">Status</th>
												<th class="text-dark">Action</th>
												<th class="text-dark">Set Privileges </th>
											</tr>
										</thead>
										<tbody>
											<?php



											$sql = "SELECT * FROM users  Where status = 1";

											$result = mysqli_query($dbc, $sql);
											$i = 0;
											if (mysqli_num_rows($result) > 0) {
												while ($row = mysqli_fetch_array($result)) {
													$i++;
											?>
													<tr>
														<td><?= $i ?></td>
														<td class="text-capitalize"><?= $row['username']; ?></td>
														<td class="text-capitalize"><?= $row['email']; ?></td>
														<!-- <td>Encrypted </td> -->
														<td><?= $row['phone'] ?></td>
														<td class="text-capitalize"><?= $row['address']; ?></td>
														<td class="text-capitalize"><?= $row['user_role']; ?></td>

														<td>
															<?php
															if ($row['status'] == '1') {
															?>
																<span class="label label-lg label-info" style="font-size: ">Available</span>
															<?php
																# code...
															} else {
															?>
																<span class="label label-lg label-danger" style="font-size: "> Not Available</span>
															<?php
															}
															?>
														</td>
														<!-- <td><?= date('D, d-M-Y', strtotime($row['adddatetime'])); ?> -->


														</td>
														<td>
															<?php if (@$userPrivileges['nav_delete'] == 1 || $fetchedUserRole == "admin"): ?>
																<form action="users.php" method="POST">
																	<input type="hidden" name="user_del_id" value="<?= $row['user_id'] ?>">
																	<button type="submit" class="btn btn-admin2 btn-sm m-1">Delete</button>
																</form>
															<?php endif ?>

															<?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin"): ?>
																<form action="users.php" method="POST">
																	<input type="hidden" name="user_edit_id" value="<?= $row['user_id'] ?>">
																	<button type="submit" class="btn btn-admin btn-sm m-1">Edit</button>
																</form>
															<?php endif ?>
														</td>
														<td><a href="privileges.php?new_user_id=<?= base64_encode($row['user_id']) ?>" target="_blank" class="text-danger btn btn-admin2 "><i class=" text-white fa fa-user"></i></a>
														</td>

													</tr>
										</tbody>
								<?php
												}
											}
								?>


									</table>

								</div>
							</div>
						</div>
					</div> <!-- .row -->
				</div> <!-- .container-fluid -->

			</main> <!-- main -->
		</div> <!-- .wrapper -->

	</body>

	</html>
	<?php include_once 'includes/foot.php'; ?>