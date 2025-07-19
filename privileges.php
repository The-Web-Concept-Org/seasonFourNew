<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php';



?>

<body class="horizontal light  ">
	<div class="wrapper">
		<?php include_once 'includes/header.php';
		$new_user_id = base64_decode($_REQUEST['new_user_id']);



		$fetchUSer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM users WHERE user_id =  '$new_user_id ' "))


			?>
		<main role="main" class="main-content">
			<div class="container-fluid">

				<div class="panel">
					<div class="panel-heading panel-heading-red" align="center">
						<h4>User Privileges</h4>
					</div>
					<div class="panel-body">
						<form class="form-horizontal" method="POST" action="">
							<div class="form-group row">
								<div class="col-sm-4"></div>
								<div class="col-sm-4 text-center">
									<p style="font-size: 18px">Allow this user to manage these tools</p>
									<input type="text" class="form-control" name="now_user_id" readonly
										value="<?= htmlspecialchars($fetchUSer['username']) ?>">
									<input type="hidden" name="new_user_id" value="<?= $_REQUEST['new_user_id'] ?>">
								</div>
								<div class="col-sm-4"></div>
							</div>
							<?= getMessage(@$msg, @$sts); ?>
							<input type="checkbox" id="checkAl" class="checkbox"> Check All<br />
							<hr />
							<?php
							$sql = mysqli_query($dbc, "SELECT * FROM menus");
							$index = 0; // Explicit index to ensure alignment
							while ($row = mysqli_fetch_assoc($sql)):
								if ($row['page'] == '#') {
									continue;
								}
								$fetchchecked = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM privileges WHERE user_id = '$new_user_id' AND nav_id = '{$row['id']}'"));
								$checked = $fetchchecked ? "checked" : "";
								?>
								<div class="row">
									<div class="col-sm-3">
										<input type="checkbox" class="checkbox menu-checkbox" name="name[<?= $index ?>]"
											value="<?= $row['id'] ?>" title="<?= htmlspecialchars($row['page']) ?>"
											<?= $checked ?>>
										<label><?= htmlspecialchars($row['title']) ?></label>
										<input type="hidden" name="url[<?= $index ?>]"
											value="<?= htmlspecialchars($row['page']) ?>">
									</div>
									<div class="col-sm-6">
										<?php if ($row['nav_edit'] == 1): ?>
											<input type="checkbox" name="nav_edit[<?= $index ?>]" value="1"
												title="<?= htmlspecialchars($row['page']) ?>" <?= $fetchchecked && $fetchchecked['nav_edit'] == 1 ? "checked" : "" ?>>
											<label class="checkbox-inline">Edit</label>
										<?php else: ?>
											<input type="hidden" name="nav_edit[<?= $index ?>]" value="0">
										<?php endif; ?>
										<?php if ($row['nav_delete'] == 1): ?>
											<input type="checkbox" name="nav_delete[<?= $index ?>]" value="1"
												title="<?= htmlspecialchars($row['page']) ?>" <?= $fetchchecked && $fetchchecked['nav_delete'] == 1 ? "checked" : "" ?>>
											<label class="checkbox-inline">Delete</label>
										<?php else: ?>
											<input type="hidden" name="nav_delete[<?= $index ?>]" value="0">
										<?php endif; ?>
										<?php if ($row['nav_add'] == 1): ?>
											<input type="checkbox" name="nav_add[<?= $index ?>]" value="1"
												title="<?= htmlspecialchars($row['page']) ?>" <?= $fetchchecked && $fetchchecked['nav_add'] == 1 ? "checked" : "" ?>>
											<label class="checkbox-inline">Add</label>
										<?php else: ?>
											<input type="hidden" name="nav_add[<?= $index ?>]" value="0">
										<?php endif; ?>
									</div>
								</div>
								<?php
								$index++;
							endwhile;
							?>
							<input type="submit" name="save" class="btn btn-info" />
						</form>
						<br><br>
					</div>
				</div>

			</div> <!-- .container-fluid -->

		</main> <!-- main -->
	</div> <!-- .wrapper -->

</body>

</html>
<?php include_once 'includes/foot.php';
if (isset($_REQUEST['save'])) {





	$name = $_REQUEST['name'];



	$now_user_id = base64_decode($_REQUEST['new_user_id']);

	// echo json_encode($_REQUEST['name']);

	// echo json_encode($_REQUEST['url']);



	$delte = mysqli_query($dbc, "SELECT * FROM privileges WHERE user_id = '$new_user_id'");

	while ($row = mysqli_fetch_assoc($delte)) {





		$q = mysqli_query($dbc, "DELETE FROM privileges WHERE user_id = '" . $row['user_id'] . "'");







	}







	for ($i = 0; $i <= count($name); $i++) {

		$nav_edit = @$_REQUEST['nav_edit'][$i];
		$nav_add = @$_REQUEST['nav_add'][$i];
		$nav_delete = @$_REQUEST['nav_delete'][$i];
		$url = @$_REQUEST['url'][$i];


		//$FetchURL = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM menus WHERE id ='".$name[$i]."' "));

		if ($name != '') {


			$test = mysqli_query($dbc, "INSERT INTO privileges(user_id,nav_id,addby,nav_delete,nav_add,nav_edit,nav_url) VALUES('" . @$new_user_id . "','" . @$name[$i] . "','Added By: admin','$nav_delete','$nav_add','$nav_edit','$url')");

			if ($test) {


				$msg = "Role Assigned successfully ";

				$sts = "success";

				redirect("users.php", 1200);

			}





		} else {



		}

	}



}



?>





<style type="text/css">
	.checkbox {

		width: 20px;

		height: 20px;

	}

	label {

		font-size: 20px;

	}
</style>



<script>

	$("#checkAl").click(function () {

		$('input:checkbox').not(this).prop('checked', this.checked);

	});

</script>