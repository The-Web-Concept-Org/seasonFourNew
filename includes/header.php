<?php
date_default_timezone_set("Asia/Karachi");
$user_id_current = $_SESSION['userId'];
$branch_id_current = $_SESSION['branch_id'];

$get_company_user = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM users WHERE branch_id = '$branch_id_current' AND user_id = '$user_id_current'"));
$get_company_br = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM branch WHERE branch_id = '$branch_id_current' "));
// $userPrivileges = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM privileges WHERE user_id = '$user_id_current'"));

$UserData = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM users WHERE user_id = '$user_id_current'"));
$_SESSION['user_role'] = $UserData['user_role'];
if (isset($_REQUEST['credit_type']) and $_REQUEST['credit_type'] == "15days") {
  $credit_sale_type = "15days";
  $credit_sale_type_text = "15 days";
  // code...
} elseif (isset($_REQUEST['credit_type']) and $_REQUEST['credit_type'] == "30days") {
  $credit_sale_type = "30days";
  $credit_sale_type_text = "30 days";
  // code...
} else {
  $credit_sale_type = "5days";
  $credit_sale_type_text = "5 days(special)";
}

$pro_dropdown_quntity = mysqli_query($dbc, "SELECT p.*, IFNULL(i.quantity_instock, 0) AS quantity_instock
        FROM product p
        LEFT JOIN inventory i ON p.product_id = i.product_id AND i.branch_id = $branch_id_current
        WHERE p.status = 1
        ORDER BY i.quantity_instock DESC")
  ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white flex-row border-bottom shadow">
  <div class="container-fluid">
    <div class="d-flex flex-row align-items-center">
      <a class="navbar-brand mx-lg-1 mr-0" href="dashboard.php">
        <img src="img/logo/<?= $get_company['logo'] ?>" class="img-fluid" alt="" style="width: 40px;height: 40px;">
      </a>
      <div class="pl-3 text-capitalize">
        <p class="m-0 p-0 text-danger"><?= @$get_company_br['branch_name'] ?></p>
        <p class="m-0 p-0"><?= @$get_company_user['username'] ?></p>
      </div>
    </div>
    <button class="navbar-toggler mt-2 mr-auto toggle-sidebar text-muted">
      <i class="fe fe-menu navbar-toggler-icon"></i>
    </button>
    <div class="navbar-slide bg-white ml-lg-4" id="navbarSupportedContent">
      <a href="#" class="btn toggle-sidebar d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
        <i class="fe fe-x"><span class="sr-only"></span></i>
      </a>
      <ul class="navbar-nav mr-auto">
        <li class="nav-item dropdown">
          <a href="dashboard.php" class="nav-link">
            <span class="ml-lg-2">Dashboard</span>
          </a>

        </li>



        <?php
        if ($UserData['user_role'] != 'admin') {
          # code...
        

          $getNav = mysqli_query($dbc, "SELECT * FROM menus where parent_id=0 AND page!='dashboard.php' ORDER BY sort_order ASC LIMIT 9 OFFSET 0 ");
          $r = 1;
          while ($fetch_nav = mysqli_fetch_assoc($getNav)) {
            $c = 0;
            $getChild = mysqli_query($dbc, "SELECT * FROM menus where parent_id='" . $fetch_nav['id'] . "' AND page!='dashboard.php' ");
            while ($child = mysqli_fetch_assoc($getChild)) {
              if (countWhens($dbc, "privileges", 'user_id', $user_id_current, 'nav_id', $child['id']) > 0) {
                $c++;
              }
            }
            if ($c > 0 && $r < 9) {
              ?>
              <li class="nav-item dropdown">
                <a href="<?= $fetch_nav['page'] ?>" id="ui-elementsDropdown" class="dropdown-toggle nav-link" role="button"
                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="ml-lg-2"><?= strtoupper($fetch_nav['title']) ?></span>
                </a>
                <?php
                if (countWhen($dbc, "menus", 'parent_id', $fetch_nav['id']) > 0) {
                  ?>
                  <div class="dropdown-menu" aria-labelledby="ui-elementsDropdown">
                    <?php
                    $getChild = mysqli_query($dbc, "SELECT * FROM menus where parent_id='" . $fetch_nav['id'] . "' AND page!='dashboard.php'  ORDER BY nav_option_sort ASC ");
                    while ($child = mysqli_fetch_assoc($getChild)) {
                      if (countWhens($dbc, "privileges", 'user_id', $user_id_current, 'nav_id', $child['id']) > 0) {
                        ?>
                        <a class="nav-link pl-lg-2" href="<?= $child['page'] ?>"><span
                            class="ml-1"><?= strtoupper($child['title']) ?></span></a>

                      <?php }
                    } //end while child 
                    ?>
                  </div>


                <?php } //check statement 
                ?>
              </li>


              <?php $r++;
            }
          }
          if ($r > 9) {

            ?>
            <li class="nav-item dropdown more">
              <a class="dropdown-toggle more-horizontal nav-link" href="#" id="moreDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="ml-2 sr-only">More</span>
              </a>
              <ul class="dropdown-menu" aria-labelledby="moreDropdown">
                <?php $getNav2 = mysqli_query($dbc, "SELECT * FROM menus where parent_id=0 AND page!='dashboard.php' ORDER BY sort_order ASC LIMIT 10 OFFSET 10 ");
                $r = 1;
                while ($fetch_nav2 = mysqli_fetch_assoc($getNav2)) {
                  $c = 0;
                  $getChild = mysqli_query($dbc, "SELECT * FROM menus where parent_id='" . $fetch_nav2['id'] . "' AND page!='dashboard.php' ");
                  while ($child = mysqli_fetch_assoc($getChild)) {
                    if (countWhens($dbc, "privileges", 'user_id', $user_id_current, 'nav_id', $child['id']) > 0) {
                      $c++;
                    }
                  }
                  if ($c > 0) {
                    # code...
            
                    ?>
                    <li class="nav-item dropdown">
                      <a class="dropdown-toggle nav-link pl-lg-2" href="#" data-toggle="collapse" id="pagesDropdown"
                        aria-expanded="false">
                        <span class="ml-1"><?= strtoupper($fetch_nav2['title']) ?></span>
                      </a>

                      <ul class="dropdown-menu" aria-labelledby="pagesDropdown">
                        <?php
                        $getChild = mysqli_query($dbc, "SELECT * FROM menus where parent_id='" . $fetch_nav2['id'] . "' AND page!='dashboard.php'   ORDER BY nav_option_sort ASC");
                        while ($child = mysqli_fetch_assoc($getChild)) {
                          if (countWhens($dbc, "privileges", 'user_id', $user_id_current, 'nav_id', $child['id']) > 0) {
                            ?>

                            <a class="nav-link pl-lg-2" href="<?= $child['page'] ?>">
                              <span class="ml-1"><?= strtoupper($child['title']) ?></span>
                            </a>
                          <?php }
                        } ?>

                      </ul>
                    </li>
                  <?php }
                } ?>
              </ul>
            </li>
          <?php }
          //end of fetch nav
        } else { /*user validation*/
          # code...
        

          $getNav = mysqli_query($dbc, "SELECT * FROM menus where parent_id=0 AND page!='dashboard.php' ORDER BY sort_order ASC LIMIT 9 OFFSET 0 ");
          $r = 1;
          while ($fetch_nav = mysqli_fetch_assoc($getNav)) {
            $c = 0;
            $getChild = mysqli_query($dbc, "SELECT * FROM menus where parent_id='" . $fetch_nav['id'] . "' AND page!='dashboard.php' ");
            while ($child = mysqli_fetch_assoc($getChild)) {
              $c++;
            }
            if ($c > 0 && $r < 10) {
              ?>
              <li class="nav-item dropdown">
                <a href="<?= $fetch_nav['page'] ?>" id="ui-elementsDropdown" class="dropdown-toggle nav-link" role="button"
                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="ml-lg-2"><?= strtoupper($fetch_nav['title']) ?></span>
                </a>
                <?php
                if (countWhen($dbc, "menus", 'parent_id', $fetch_nav['id']) > 0) {
                  ?>
                  <div class="dropdown-menu" aria-labelledby="ui-elementsDropdown">
                    <?php
                    $getChild = mysqli_query($dbc, "SELECT * FROM menus where parent_id='" . $fetch_nav['id'] . "' AND page!='dashboard.php'   ORDER BY nav_option_sort ASC");
                    while ($child = mysqli_fetch_assoc($getChild)) {

                      ?>
                      <a class="nav-link pl-lg-2" href="<?= $child['page'] ?>"><span
                          class="ml-1"><?= strtoupper($child['title']) ?></span></a>

                      <?php
                    } //end while child 
                    ?>
                  </div>


                <?php } //check statement 
                ?>
              </li>


              <?php $r++;
            }
          }
          if ($r > 9) {

            ?>
            <li class="nav-item dropdown more">
              <a class="dropdown-toggle more-horizontal nav-link" href="#" id="moreDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="ml-2 sr-only">More</span>
              </a>
              <ul class="dropdown-menu" aria-labelledby="moreDropdown">
                <?php $getNav2 = mysqli_query($dbc, "SELECT * FROM menus where parent_id=0 AND page!='dashboard.php' ORDER BY sort_order ASC LIMIT 10 OFFSET 10 ");
                $r = 1;
                while ($fetch_nav2 = mysqli_fetch_assoc($getNav2)) {
                  $c = 0; ?>
                  <li class="nav-item dropdown">
                    <a class="dropdown-toggle nav-link pl-lg-2" href="#" data-toggle="collapse" id="pagesDropdown"
                      aria-expanded="false">
                      <span class="ml-1"><?= strtoupper($fetch_nav2['title']) ?></span>
                    </a>

                    <ul class="dropdown-menu" aria-labelledby="pagesDropdown">
                      <?php
                      $getChild = mysqli_query($dbc, "SELECT * FROM menus where parent_id='" . $fetch_nav2['id'] . "' AND page!='dashboard.php'  ORDER BY nav_option_sort ASC ");
                      while ($child = mysqli_fetch_assoc($getChild)) {

                        ?>

                        <a class="nav-link pl-lg-2" href="<?= $child['page'] ?>">
                          <span class="ml-1"><?= strtoupper($child['title']) ?></span>
                        </a>
                      <?php } ?>

                    </ul>
                  </li>
                <?php } ?>
              </ul>
            </li>
          <?php }
        }

        ?>


      </ul>
    </div>
    <?php
    $session_branch_id = $_SESSION['branch_id'];
    $user_role = $_SESSION['user_role'];

    // Apply branch filter based on role
    if ($user_role != 'admin') {
      $branch_filter_fornotifi = "WHERE to_branch = '$session_branch_id' AND stock_status = 'pending'";
    } else {
      $branch_filter_fornotifi = "WHERE stock_status = 'pending'";
    }

    // Fetch pending gatepass records
    $q = mysqli_query($dbc, "SELECT * FROM gatepass $branch_filter_fornotifi ORDER BY gatepass_id DESC");
    $pending_gatepasses = mysqli_fetch_all($q, MYSQLI_ASSOC);
    $notification_count = count($pending_gatepasses);
    ?>
    <ul class="navbar-nav d-flex flex-row">
      <li class="nav-item">
        <a class="nav-link text-muted my-2" href="./#" id="modeSwitcher" data-mode="light">
          <i class="fe fe-sun fe-16"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-muted my-2" href="./#" data-toggle="modal" data-target=".modal-shortcut">
          <i class="fe fe-grid fe-16"></i>
        </a>
      </li>
      <li class="nav-item nav-notif ">
        <a class="nav-link text-muted my-2" href="./#" data-toggle="modal" data-target=".modal-notif">
          <i class="fe fe-bell fe-16" style="position: relative;">
            <?php if ($notification_count > 0): ?>
              <span class="badge badge-pill badge-success rounded-circle"
                style="position: absolute; top:-12px; right:-8px;"><?php echo $notification_count; ?></span>
            <?php endif; ?></i>
        </a>
      </li>
      <li class="nav-item dropdown ml-lg-0">
        <a class="nav-link dropdown-toggle text-muted" href="#" id="navbarDropdownMenuLink" role="button"
          data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="avatar avatar-sm mt-2">
            <img src="img/logo/user.png" alt="..." class="avatar-img rounded-circle">
          </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
          <li class="nav-item">
            <a class="nav-link pl-3" href="setting.php">Profile</a>
          </li>
          <li class="nav-item">
            <a class="nav-link pl-3" href="logout.php">Logut</a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
<!-- Short cuts modal -->
<div class="modal fade modal-shortcut modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="defaultModalLabel">Shortcuts</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body px-5">
        <div class="row align-items-center">
          <div class="col-6 text-center">
            <div class="squircle bg-success justify-content-center">
              <i class="fe fe-cpu fe-32 align-self-center text-white"></i>
            </div>
            <p>Control area</p>
          </div>
          <div class="col-6 text-center">
            <div class="squircle bg-primary justify-content-center">
              <i class="fe fe-activity fe-32 align-self-center text-white"></i>
            </div>
            <p>Activity</p>
          </div>
        </div>
        <div class="row align-items-center">
          <div class="col-6 text-center">
            <div class="squircle bg-primary justify-content-center">
              <i class="fe fe-droplet fe-32 align-self-center text-white"></i>
            </div>
            <p>Droplet</p>
          </div>
          <div class="col-6 text-center">
            <div class="squircle bg-primary justify-content-center">
              <i class="fe fe-upload-cloud fe-32 align-self-center text-white"></i>
            </div>
            <p>Upload</p>
          </div>
        </div>
        <div class="row align-items-center">
          <div class="col-6 text-center">
            <div class="squircle bg-primary justify-content-center">
              <i class="fe fe-users fe-32 align-self-center text-white"></i>
            </div>
            <p>Users</p>
          </div>
          <div class="col-6 text-center">
            <div class="squircle bg-primary justify-content-center">
              <i class="fe fe-settings fe-32 align-self-center text-white"></i>
            </div>
            <p>Settings</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Notification modal -->
<div class="modal fade modal-notif modal-slide" id="notifModal" tabindex="-1" role="dialog"
  aria-labelledby="notifModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="notifModalLabel">Pending Gatepass</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="overflow-y: auto;">
        <div class="list-group list-group-flush my-n3">
          <?php if (empty($pending_gatepasses)): ?>
            <div class="list-group-item bg-transparent">
              <div class="row align-items-center">
                <div class="col">
                  <small>No pending gatepasses found.</small>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php foreach ($pending_gatepasses as $r): ?>
              <?php
              $from_branch = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM branch WHERE branch_id = '{$r['from_branch']}'"));
              $to_branch = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM branch WHERE branch_id = '{$r['to_branch']}'"));
              ?>
              <a href="view_gatepass_in.php?gatepass_id=<?= htmlspecialchars($r['gatepass_id']) ?>" class=" "
                style="text-decoration: none; cursor: pointer;">
                <div class="list-group-item bg-transparent">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="fe fe-bell fe-24 text-warning"></span>
                    </div>
                    <div class="col">
                      <small><strong>Gatepass SF25-G-<?= $r['gatepass_id'] ?></strong></small>
                      <div class="my-0 small">From: <?= htmlspecialchars($from_branch['branch_name']) ?></div>
                      <div class="my-0 small">To: <?= htmlspecialchars($to_branch['branch_name']) ?></div>
                      <div class="my-0 small">Narration: <?= htmlspecialchars($r['gatepass_narration']) ?></div>
                      <div class="my-0 small">Date: <?= $r['gatepass_date'] ?></div>

                    </div>
                  </div>
                </div>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>