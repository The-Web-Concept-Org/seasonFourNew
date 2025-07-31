<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php'; ?>
<style>
  /* Main menu item styling */
  .main-menu {
    background-color: #f8f9fa;
    border-left: 5px solid #007bff;
    border-radius: 6px;
    padding: 10px 15px;
    margin-bottom: 10px;
    cursor: default;
    transition: background-color 0.3s;
  }

  .main-menu:hover {
    background-color: #e2e6ea;
  }

  .main-menu strong {
    font-size: 18px;
    color: #343a40;
  }

  /* Submenu container */
  .sub-menu {
    margin-top: 10px;
    padding-left: 15px;
  }

  /* Submenu items */
  .submenu-item {
    background-color: #e9ecef;
    border-left: 4px solid #6c757d;
    border-radius: 3px;
    padding: 6px 12px;
    margin-bottom: 6px;
    font-size: 15px;
    color: #212529;
    cursor: grab;
    transition: background-color 0.2s ease;
    list-style: none;
  }

  .submenu-item:hover {
    background-color: #d6d8db;
  }

  /* Drag placeholder */
  .ui-state-highlight {
    height: 40px;
    background-color: #ffeeba;
    border: 2px dashed #ffc107;
    margin-bottom: 10px;
  }

  /* Toggle icon animation */
  .toggle-icon {
    transition: transform 0.3s ease;
  }

  .rotate-up {
    transform: rotate(180deg);
  }
</style>

<body class="horizontal light">
  <div class="wrapper">
    <?php include_once 'includes/header.php'; ?>
    <main role="main" class="main-content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header card-bg text-center">
            <div class="row">
              <div class="col-12 h4">
                <b class="card-text">Modify Navbar</b>
                <a href="developer.php" class="btn btn-admin float-right btn-sm">Add New</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row justify-content-center">
              <div class="col-md-6">
                <ul class="list-unstyled" id="main_menu_list">
                  <?php
                  $menus = mysqli_query($dbc, "SELECT * FROM menus WHERE parent_id = 0 ORDER BY sort_order ASC");
                  while ($parent = mysqli_fetch_assoc($menus)) {
                    $parentId = $parent['id'];
                  ?>
                    <li class="main-menu" data-id="<?= $parentId ?>">
                      <div class="d-flex justify-content-between align-items-center toggle-parent" style="cursor: pointer;">
                        <strong><?= ucfirst($parent['title']) ?></strong>
                        <i class="fa fa-chevron-down toggle-icon text-secondary"></i>
                      </div>
                      <ul class="sub-menu" data-parent-id="<?= $parentId ?>" style="display: none;">
                        <?php
                        $submenus = mysqli_query($dbc, "SELECT * FROM menus WHERE parent_id = $parentId ORDER BY nav_option_sort ASC");
                        while ($child = mysqli_fetch_assoc($submenus)) {
                        ?>
                          <li class="submenu-item" data-id="<?= $child['id'] ?>">
                            <?= ucfirst($child['title']) ?>
                          </li>
                        <?php } ?>
                      </ul>
                    </li>
                  <?php } ?>
                </ul>
              </div>
            </div>
          </div> <!-- .card-body -->
        </div> <!-- .card -->
      </div> <!-- .container-fluid -->
    </main>
  </div>
</body>
<?php include_once 'includes/foot.php'; ?>

<!-- Dropdown toggle script -->
<script>
  $(document).ready(function () {
    $('.toggle-parent').on('click', function () {
      const submenu = $(this).next('.sub-menu');
      const icon = $(this).find('.toggle-icon');

      submenu.slideToggle(200);
      icon.toggleClass('rotate-up');
    });
  });
</script>
</html>
