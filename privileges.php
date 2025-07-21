<?php include_once 'includes/head.php'; ?>
<!DOCTYPE html>
<html lang="en">

<body class="horizontal light">
	<div class="wrapper">
		<?php
		include_once 'includes/header.php';
		$new_user_id = base64_decode($_REQUEST['new_user_id']);
		$fetchUSer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM users WHERE user_id = '$new_user_id'"));
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
									<p style="font-size: 18px">Allow This user to manage these tools</p>
									<input type="text" class="form-control" name="now_user_id" readonly
										value="<?= htmlspecialchars($fetchUSer['username']) ?>">
									<input type="hidden" name="new_user_id" value="<?= $_REQUEST['new_user_id'] ?>">
								</div>
								<div class="col-sm-4"></div>
							</div>

							<?= getMessage(@$msg, @$sts); ?>

							<input type="checkbox" id="checkAl" class="checkbox"> CheckAll<br />
							<hr />

							<?php
							$sql = mysqli_query($dbc, "SELECT * FROM menus");
							$index = 0;
							while ($row = mysqli_fetch_assoc($sql)):
								if ($row['page'] == '#')
									continue;

								$fetchchecked = mysqli_fetch_assoc(mysqli_query(
									$dbc,
									"SELECT * FROM privileges WHERE user_id = '$new_user_id' AND nav_id = '{$row['id']}'"
								));

								$checked = $fetchchecked ? "checked" : "";
								?>

								<div class="row">
									<div class="col-sm-3">
										<input type="checkbox" class="checkbox" name="name[<?= $index ?>]"
											value="<?= $row['id'] ?>" title="<?= htmlspecialchars($row['page']) ?>"
											<?= $checked ?>>
										<label><?= htmlspecialchars($row['title']) ?></label><br />
										<input type="hidden" name="url[<?= $index ?>]"
											value="<?= htmlspecialchars(rtrim($row['page'], '#')) ?>">
									</div>

									<div class="col-sm-6">
										<input type="hidden" name="nav_edit[<?= $index ?>]" value="0">
										<input type="hidden" name="nav_delete[<?= $index ?>]" value="0">
										<input type="hidden" name="nav_add[<?= $index ?>]" value="0">

										<?php if ($row['nav_edit'] == 1): ?>
											<input type="checkbox" name="nav_edit[<?= $index ?>]" value="1"
												<?= @$fetchchecked['nav_edit'] == 1 ? 'checked' : '' ?>>
											<label>Edit</label>&nbsp;
										<?php endif; ?>

										<?php if ($row['nav_delete'] == 1): ?>
											<input type="checkbox" name="nav_delete[<?= $index ?>]" value="1"
												<?= @$fetchchecked['nav_delete'] == 1 ? 'checked' : '' ?>>
											<label>Delete</label>&nbsp;
										<?php endif; ?>

										<?php if ($row['nav_add'] == 1): ?>
											<input type="checkbox" name="nav_add[<?= $index ?>]" value="1"
												<?= @$fetchchecked['nav_add'] == 1 ? 'checked' : '' ?>>
											<label>Add</label>&nbsp;
										<?php endif; ?>
									</div>
								</div>
								<?php $index++; endwhile; ?>

							<input type="submit" name="save" class="btn btn-info" />
						</form>
						<br><br>
					</div>
				</div>
			</div>
		</main>
	</div>
</body>

</html>

<?php
include_once 'includes/foot.php';

if (isset($_POST['save'])) {
	$new_user_id = base64_decode($_POST['new_user_id']);
	$name = $_POST['name'] ?? [];
	$url = $_POST['url'] ?? [];
	$nav_edit = $_POST['nav_edit'] ?? [];
	$nav_add = $_POST['nav_add'] ?? [];
	$nav_delete = $_POST['nav_delete'] ?? [];

	mysqli_query($dbc, "DELETE FROM privileges WHERE user_id = '$new_user_id'");

	foreach ($name as $i => $nav_id) {
		if (!empty($nav_id) && !empty($url[$i])) {
			$edit = $nav_edit[$i] ?? 0;
			$add = $nav_add[$i] ?? 0;
			$delete = $nav_delete[$i] ?? 0;
			$nav_url = $url[$i];
			$addby = "Added By: admin";

			mysqli_query($dbc, "INSERT INTO privileges (user_id, nav_id, addby, nav_delete, nav_add, nav_edit, nav_url) 
                                VALUES ('$new_user_id', '$nav_id', '$addby', '$delete', '$add', '$edit', '$nav_url')");
		}
	}

	$msg = "Role assigned successfully";
	$sts = "success";
	redirect("users.php", 1200);
}
?>


<style>
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