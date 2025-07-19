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
<?php
include_once 'includes/foot.php';

if (isset($_POST['save'])) {
    // Decode user ID
    $new_user_id = base64_decode($_POST['new_user_id']);
    
    // Validate user ID
    if (empty($new_user_id)) {
        $msg = "Invalid user ID";
        $sts = "danger";
    } else {
        // Log submitted data for debugging
        error_log("Submitted data: " . print_r($_POST, true));

        // Initialize arrays with defaults
        $name = isset($_POST['name']) && is_array($_POST['name']) ? $_POST['name'] : [];
        $url = isset($_POST['url']) && is_array($_POST['url']) ? $_POST['url'] : [];
        $nav_edit = isset($_POST['nav_edit']) && is_array($_POST['nav_edit']) ? $_POST['nav_edit'] : [];
        $nav_add = isset($_POST['nav_add']) && is_array($_POST['nav_add']) ? $_POST['nav_add'] : [];
        $nav_delete = isset($_POST['nav_delete']) && is_array($_POST['nav_delete']) ? $_POST['nav_delete'] : [];

        // Delete existing privileges in a single query
        $delete_query = "DELETE FROM privileges WHERE user_id = ?";
        $stmt = $dbc->prepare($delete_query);
        $stmt->bind_param("s", $new_user_id);
        $stmt->execute();
        $stmt->close();

        // Prepare insertion query
        $insert_query = "INSERT INTO privileges (user_id, nav_id, addby, nav_delete, nav_add, nav_edit, nav_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $dbc->prepare($insert_query);

        // Iterate through submitted menu IDs
        $success = true;
        foreach ($name as $i => $nav_id) {
            // Validate nav_id and url
            if (!empty($nav_id) && isset($url[$i]) && !empty($url[$i])) {
                $edit = isset($nav_edit[$i]) && $nav_edit[$i] == 1 ? 1 : 0;
                $add = isset($nav_add[$i]) && $nav_add[$i] == 1 ? 1 : 0;
                $delete = isset($nav_delete[$i]) && $nav_delete[$i] == 1 ? 1 : 0;
                $nav_url = $url[$i];
                $addby = "Added By: admin";

                // Log the data being inserted
                error_log("Inserting: user_id=$new_user_id, nav_id=$nav_id, edit=$edit, add=$add, delete=$delete, url=$nav_url");

                // Bind and execute
                $stmt->bind_param("sssssss", $new_user_id, $nav_id, $addby, $delete, $add, $edit, $nav_url);
                if (!$stmt->execute()) {
                    $msg = "Error assigning role: " . $stmt->error;
                    $sts = "danger";
                    $success = false;
                    break;
                }
            } else {
                error_log("Skipping invalid entry: nav_id=" . ($nav_id ?? 'null') . ", url=" . ($url[$i] ?? 'null'));
            }
        }
        $stmt->close();

        if ($success) {
            $msg = "Role assigned successfully";
            $sts = "success";
            redirect("users.php", 1200);
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