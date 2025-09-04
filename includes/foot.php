<!-- Add Product Modal -->
<div class="modal fade" id="add_product_modal" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel"
  aria-hidden="true">
  <div class="modal-dialog " style="max-width: 90%;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="defaultModalLabel">Add Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="addProductModalBody">
        <form action="php_action/custom_action.php" id="add_product_fm" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="product_module">
          <input type="hidden" name="product_id" value="<?= @base64_encode($fetchproduct['product_id']) ?>">
          <input type="hidden" id="product_add_from" value="modal">


          <div class="form-group row mx-auto justify-content-center">
            <div class="col-md-2 mt-3">
              <label>Product ID#</label>
              <?php
              $result = mysqli_query($dbc, "SELECT product_code FROM product WHERE product_code LIKE 'SF%' ORDER BY CAST(SUBSTRING(product_code, 3) AS UNSIGNED) DESC LIMIT 1");
              $data = mysqli_fetch_assoc($result);

              if ($data && isset($data['product_code'])) {
                $latest_code = $data['product_code'];
                $number_only = (int) substr($latest_code, 2);
                $number_only = $number_only + 1;
              }
              ?>
              <input type="text" name="next_increment" id="next_increment"
                value="SF25-PROD-<?= @empty($_REQUEST['edit_product_id']) ? $number_only : preg_replace('/\D/', '', $fetchproduct['product_code']) ?>"
                readonly class="form-control">
            </div>

            <div class="col-sm-2 mt-3">
              <label for="">Product Category</label>
              <div id="categoryDropdownContainer">
                <select class="form-control searchableSelect categorydropdown" name="category_id" id="" size="1">
                  <option value="">Select Category</option>
                  <?php
                  $result = mysqli_query($dbc, "select * from categories");
                  while ($row = mysqli_fetch_array($result)) {
                    ?>
                    <option data-price="<?= $row["category_price"] ?>" <?= @($fetchproduct['category_id'] != $row["categories_id"]) ? "" : "selected" ?> value="<?= $row["categories_id"] ?>">
                      <?= $row["categories_name"] ?>-<?= $row["category_price"] ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-2 mt-3">
              <label for="">Product Brand</label>
              <div id="brandDropdownContainer">
                <select class="form-control searchableSelect tableData brandAccordingCategory" name="brand_id" id=""
                  size="1">
                  <option value="">Select Brand</option>
                </select>
              </div>
            </div>
            <div class="col-sm-2 mb-3 mt-3 mb-sm-0">
              <?php
              // Fetch latest product code starting with 'SF'
              $result = mysqli_query($dbc, "SELECT product_code FROM product WHERE product_code LIKE 'SF%' ORDER BY CAST(SUBSTRING(product_code, 3) AS UNSIGNED) DESC LIMIT 1");
              $data = mysqli_fetch_assoc($result);

              // Parse and increment
              if ($data && isset($data['product_code'])) {
                $latest_code = strtoupper($data['product_code']); // Ensure the prefix is uppercase
                $prefix = 'SF';
                $number_part = (int) substr($latest_code, strlen($prefix));
                $next_number = $number_part + 1;
                $next_product_code = $prefix . $next_number;
              }


              // Use existing code if editing
              $input_value = !empty($_REQUEST['edit_product_id']) ? htmlspecialchars($fetchproduct['product_code']) : $next_product_code;
              ?>

              <label for="product_code">Product Code</label>
              <input type="text" class="form-control text-uppercase" id="product_code" name="product_code"
                value="<?= $input_value ?>" readonly required>
            </div>

            <div class="col-sm-3 mb-3 mt-3 mb-sm-0">
              <label for="">Product Name</label>
              <input type="text" class="form-control" id="product_name" placeholder="Product Name" name="product_name"
                required value="<?= htmlspecialchars(@$fetchproduct['product_name'], ENT_QUOTES) ?>">

            </div>

            <div class="col-sm-2 mt-3 mb-sm-0">
              <label for="">Product Description</label>

              <textarea class="form-control" name="product_description"
                placeholder="Product Description"><?= @$fetchproduct['product_description'] ?></textarea>
            </div>
            <div class="col-sm-2 mt-3 mb-sm-0">
              <label for="">Purchase Rate</label>
              <input type="text" class="form-control" id="purchase_rate" placeholder=" Rate" name="purchase_rate"
                required value="<?= @$fetchproduct['purchase_rate'] ?>">
            </div>
            <div class="col-sm-2 mt-3 mb-sm-0">
              <label for="">Sale Rate</label>
              <input type="text" class="form-control" id="" placeholder=" Rate" name="current_rate" required
                value="<?= @$fetchproduct['current_rate'] ?>">
            </div>
            <div class="col-sm-2 mt-3 mb-sm-0">
              <label for="">Final Rate</label>
              <input type="text" class="form-control" id="final_rate" placeholder=" Rate" name="final_rate" required
                value="<?= @$fetchproduct['final_rate'] ?>">
            </div>
            <div class="col-sm-1 mt-3">
              <label for="">MOQ Alert</label>
              <input type="text" required class="form-control"
                value="<?= (empty($fetchproduct)) ? 5 : $fetchproduct['alert_at'] ?>" id="alert_at"
                placeholder="Product Stock Alert" name="alert_at">
            </div>

            <div class="col-sm-2 mt-3 mb-sm-0">

              <label for="">Status</label>
              <select class="form-control" required name="availability" id="availability">
                <option value="1">Available</option>
                <option value="0">Not Available</option>
              </select>

            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
            <button class="btn btn-admin float-right" type="submit" id="add_product_btn">ADD</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="add_category_modal" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="defaultModalLabel">Add Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="php_action/panel.php" method="POST" role="form" id="formData4">
          <div class="msg"></div>
          <div class="form-group row">
            <div class="col-sm-4">
              <label for="categories_name">Name</label>
              <input type="text" class="form-control" value="<?= @$categories['categories_name'] ?>"
                id="categories_name" name="add_category_name" required>
              <input type="hidden" class="form-control" value="<?= @$categories['categories_id'] ?>" id="categories_id"
                name="categories_id">
            </div>
            <div class="col-sm-4">
              <label for="categories_country">Country</label>
              <select class="form-control searchableSelect" id="categories_country" name="categories_country">
                <option value="<?= @$categories['categories_country'] ?>">Select Country</option>
                <?php
                $countries = [
                  "Afghanistan",
                  "Albania",
                  "Algeria",
                  "Andorra",
                  "Angola",
                  "Antigua and Barbuda",
                  "Argentina",
                  "Armenia",
                  "Australia",
                  "Austria",
                  "Azerbaijan",
                  "Bahamas",
                  "Bahrain",
                  "Bangladesh",
                  "Barbados",
                  "Belarus",
                  "Belgium",
                  "Belize",
                  "Benin",
                  "Bhutan",
                  "Bolivia",
                  "Bosnia and Herzegovina",
                  "Botswana",
                  "Brazil",
                  "Brunei",
                  "Bulgaria",
                  "Burkina Faso",
                  "Burundi",
                  "Cabo Verde",
                  "Cambodia",
                  "Cameroon",
                  "Canada",
                  "Central African Republic",
                  "Chad",
                  "Chile",
                  "China",
                  "Colombia",
                  "Comoros",
                  "Congo, Democratic Republic of the",
                  "Congo, Republic of the",
                  "Costa Rica",
                  "Cote d'Ivoire",
                  "Croatia",
                  "Cuba",
                  "Cyprus",
                  "Czech Republic",
                  "Denmark",
                  "Djibouti",
                  "Dominica",
                  "Dominican Republic",
                  "East Timor",
                  "Ecuador",
                  "Egypt",
                  "El Salvador",
                  "Equatorial Guinea",
                  "Eritrea",
                  "Estonia",
                  "Eswatini",
                  "Ethiopia",
                  "Fiji",
                  "Finland",
                  "France",
                  "Gabon",
                  "Gambia",
                  "Georgia",
                  "Germany",
                  "Ghana",
                  "Greece",
                  "Grenada",
                  "Guatemala",
                  "Guinea",
                  "Guinea-Bissau",
                  "Guyana",
                  "Haiti",
                  "Honduras",
                  "Hungary",
                  "Iceland",
                  "India",
                  "Indonesia",
                  "Iran",
                  "Iraq",
                  "Ireland",
                  "Israel",
                  "Italy",
                  "Jamaica",
                  "Japan",
                  "Jordan",
                  "Kazakhstan",
                  "Kenya",
                  "Kiribati",
                  "Korea, North",
                  "Korea, South",
                  "Kosovo",
                  "Kuwait",
                  "Kyrgyzstan",
                  "Laos",
                  "Latvia",
                  "Lebanon",
                  "Lesotho",
                  "Liberia",
                  "Libya",
                  "Liechtenstein",
                  "Lithuania",
                  "Luxembourg",
                  "Madagascar",
                  "Malawi",
                  "Malaysia",
                  "Maldives",
                  "Mali",
                  "Malta",
                  "Marshall Islands",
                  "Mauritania",
                  "Mauritius",
                  "Mexico",
                  "Micronesia",
                  "Moldova",
                  "Monaco",
                  "Mongolia",
                  "Montenegro",
                  "Morocco",
                  "Mozambique",
                  "Myanmar",
                  "Namibia",
                  "Nauru",
                  "Nepal",
                  "Netherlands",
                  "New Zealand",
                  "Nicaragua",
                  "Niger",
                  "Nigeria",
                  "North Macedonia",
                  "Norway",
                  "Oman",
                  "Pakistan",
                  "Palau",
                  "Panama",
                  "Papua New Guinea",
                  "Paraguay",
                  "Peru",
                  "Philippines",
                  "Poland",
                  "Portugal",
                  "Qatar",
                  "Romania",
                  "Russia",
                  "Rwanda",
                  "Saint Kitts and Nevis",
                  "Saint Lucia",
                  "Saint Vincent and the Grenadines",
                  "Samoa",
                  "San Marino",
                  "Sao Tome and Principe",
                  "Saudi Arabia",
                  "Senegal",
                  "Serbia",
                  "Seychelles",
                  "Sierra Leone",
                  "Singapore",
                  "Slovakia",
                  "Slovenia",
                  "Solomon Islands",
                  "Somalia",
                  "South Africa",
                  "South Sudan",
                  "Spain",
                  "Sri Lanka",
                  "Sudan",
                  "Suriname",
                  "Sweden",
                  "Switzerland",
                  "Syria",
                  "Taiwan",
                  "Tajikistan",
                  "Tanzania",
                  "Thailand",
                  "Togo",
                  "Tonga",
                  "Trinidad and Tobago",
                  "Tunisia",
                  "Turkey",
                  "Turkmenistan",
                  "Tuvalu",
                  "Uganda",
                  "Ukraine",
                  "United Arab Emirates",
                  "United Kingdom",
                  "United States",
                  "Uruguay",
                  "Uzbekistan",
                  "Vanuatu",
                  "Vatican City",
                  "Venezuela",
                  "Vietnam",
                  "Yemen",
                  "Zambia",
                  "Zimbabwe"
                ];

                foreach ($countries as $country) {
                  // Trim whitespace and compare case-insensitively
                  $selected = (trim(strtolower(@$categories['categories_country'])) == trim(strtolower($country))) ? 'selected' : '';
                  ?>
                  <option <?= $selected ?> value="<?= htmlspecialchars($country) ?>">
                    <?= htmlspecialchars($country) ?>
                  </option>
                  <?php
                }
                ?>
              </select>
            </div>
            <div class="col-sm-4">
              <label for="categories_status">Status</label>
              <select class="form-control" id="categories_status" name="categories_status">

                <option <?= !isset($categories['categories_status']) || $categories['categories_status'] == 1 ? "selected" : "" ?> value="1">Active</option>
                <option <?= isset($categories['categories_status']) && $categories['categories_status'] == 0 ? "selected" : "" ?> value="0">Inactive</option>
              </select>
            </div>
          </div>
          <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin" and isset($_REQUEST['edit_brand_id'])): ?>
            <button type="submit" class="btn btn-admin2 float-right mt-3" id="formData_btn">Update</button>
          <?php endif ?>
          <?php if (@$userPrivileges['nav_add'] == 1 || $fetchedUserRole == "admin" and !isset($_REQUEST['edit_brand_id'])): ?>
            <button type="submit" class="btn btn-admin float-right mt-3" id="formData_btn">Add</button>
          <?php endif ?>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-dark float-right"
              id="category_cancel_btn">Cancel</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<!-- Add brand Modal   -->
<div class="modal fade" id="add_brand_modal" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="defaultModalLabel">Add Brand</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">


        <form action="php_action/panel.php" method="POST" role="form" id="formData3">
          <div class="msg"></div>
          <div class="form-group row">
            <div class="col-sm-6 ">
              <label for="">Brand Category</label>
              <div id="categoryDropdownContainer">
                <select class="form-control searchableSelect" name="category_id" id="tableData2" size="1">
                  <option value="">Select Category</option>
                  <?php
                  $result = mysqli_query($dbc, "select * from categories");
                  while ($row = mysqli_fetch_array($result)) {
                    ?>
                    <option data-price="<?= $row["category_price"] ?>" <?= @($brands['category_id'] != $row["categories_id"]) ? "" : "selected" ?> value="<?= $row["categories_id"] ?>">
                      <?= $row["categories_name"] ?>-<?= $row["category_price"] ?>
                    </option>
                  <?php } ?>
                </select>
              </div>

            </div>
            <div class="col-sm-6">
              <label for="">Brand</label>
              <input type="text" class="form-control" value="<?= @$brands['brand_name'] ?>" id="add_brand_name"
                name="add_brand_name">
              <input type="hidden" class="form-control " value="<?= @$brands['brand_id'] ?>" id="brand_id"
                name="brand_id">

            </div>
          </div>
          <div class="form-group row">
            <div class="col-sm-6">
              <label for="brand_country">Country</label>
              <select class="form-control searchableSelect" id="brand_country" name="brand_country">
                <option value="">Select Country</option>
                <?php
                foreach ($countries as $country) {
                  // Trim whitespace and compare case-insensitively
                  $selected = (trim(strtolower(@$brands['brand_country'])) == trim(strtolower($country))) ? 'selected' : '';
                  ?>
                  <option <?= $selected ?> value="<?= htmlspecialchars($country) ?>">
                    <?= htmlspecialchars($country) ?>
                  </option>
                  <?php
                }
                ?>
              </select>

            </div>
            <div class="col-sm-6">
              <label for="">Brand Status</label>
              <select class="form-control" id="brand_status" name="brand_status">

                <option <?= isset($brands['brand_status']) && $brands['brand_status'] == 1 ? "selected" : "" ?> value="1">
                  Active</option>
                <option <?= isset($brands['brand_status']) && $brands['brand_status'] == 0 ? "selected" : "" ?> value="0">
                  Inactive</option>

              </select>
            </div>
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-dark float-right"
          id="formData_btn">Cancel</button>

        <?php if (@$userPrivileges['nav_edit'] == 1 || $fetchedUserRole == "admin" and isset($_REQUEST['edit_brand_id'])): ?>
          <button type="submit" class="btn btn-admin2 float-right" id="formData_btn">Update</button>
        <?php endif ?>
        <?php if (@$userPrivileges['nav_add'] == 1 || $fetchedUserRole == "admin" and !isset($_REQUEST['edit_brand_id'])): ?>
          <button type="submit" class="btn btn-admin float-right" id="formData_btn">Add</button>
        <?php endif ?>
        </form>
      </div>

    </div>
  </div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/moment.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/simplebar.min.js"></script>
<script src='js/daterangepicker.js'></script>
<script src='js/jquery.stickOnScroll.js'></script>
<script src="js/tinycolor-min.js"></script>
<script src="js/config.js"></script>
<script src="js/d3.min.js"></script>
<script src="js/topojson.min.js"></script>
<script src="js/datamaps.all.min.js"></script>
<script src="js/datamaps-zoomto.js"></script>
<script src="js/datamaps.custom.js"></script>
<script src="js/Chart.min.js"></script>
<script src="js/jquery-ui.min.js"></script>

<script>
  /* defind global options */
  Chart.defaults.global.defaultFontFamily = base.defaultFontFamily;
  Chart.defaults.global.defaultFontColor = colors.mutedColor;
</script>
<script src="js/gauge.min.js"></script>
<script src="js/jquery.sparkline.min.js"></script>
<script src="js/apexcharts.min.js"></script>
<script src="js/apexcharts.custom.js"></script>
<script src='js/jquery.mask.min.js'></script>
<script src='js/select2.min.js'></script>
<script src='js/jquery.steps.min.js'></script>
<script src='js/jquery.validate.min.js'></script>
<script src='js/jquery.timepicker.js'></script>
<script src='js/dropzone.min.js'></script>
<script src='js/uppy.min.js'></script>
<script src='js/quill.min.js'></script>
<script src='js/jquery.dataTables.min.js'></script>
<script src='js/dataTables.bootstrap4.min.js'></script>
<script src="js/apps.js"></script>
<script src="js/custom.js"></script>
<script src="js/panel.js"></script>