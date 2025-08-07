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

    @media print {
        .print_hide {
            display: none !important;
        }

        .form_sec {
            display: none !important;
        }
    }

    /* Adjust Select2 width */
    .select2-container {
        width: 100% !important;
    }
</style>

<body class="horizontal light">
    <div class="wrapper">
        <?php include_once 'includes/header.php'; ?>
        <main role="main" class="main-content">
            <div class="container-fluid">
                <div class="card form_sec">
                    <div class="card-header card-bg" align="center">
                        <div class="row d-print-none">
                            <div class="col-12 mx-auto h4">
                                <b class="text-center card-text">Category Report</b>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="get" class="d-print-none">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="">Category</label>
                                        <select class="form-control searchableSelect text-capitalize"
                                            name="category_id[]" multiple>
                                            <option disabled>Select Category</option>
                                            <?php
                                            $category = mysqli_query($dbc, "SELECT * FROM categories WHERE categories_status = 1");
                                            while ($row = mysqli_fetch_array($category)) { ?>
                                                <option class="text-capitalize" value="<?= $row['categories_id'] ?>">
                                                    <?= $row['categories_name'] ?>
                                                </option>
                                            <?php } ?>
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
                </div>
                <?php if (isset($_GET['search_sale'])) { ?>
                    <div class="card">
                        <div class="card-header card-bg" align="center">
                            <div class="row">
                                <div class="col-12 mx-auto h4">
                                    <b class="text-center card-text">Category Wise Products</b>
                                    <span class="float-left mr-3 text-white">
                                        <strong class="text-white">Date:</strong> <?= date('Y-m-d') ?>
                                    </span>
                                    <button onclick="window.print();"
                                        class="btn btn-admin btn-sm float-right print_btn print_hide ml-2">Print
                                        Report</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered" id="noPaginationTable">
                                <thead>
                                    <tr>
                                        <!-- <th style="width:8% ;">Sr No.</th> -->
                                        <th>Category</th>
                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $categoryIds = isset($_GET['category_id']) && is_array($_GET['category_id']) ? $_GET['category_id'] : [];

                                    $where = "WHERE 1=1 AND status = 1";

                                    if (!empty($categoryIds)) {
                                        $categoryIds = array_map(function ($id) use ($dbc) {
                                            return mysqli_real_escape_string($dbc, $id);
                                        }, $categoryIds);
                                        $where .= " AND product.category_id IN ('" . implode("','", $categoryIds) . "')";
                                    }

                                    $query = mysqli_query($dbc, "
                                                                                SELECT 
                                                                                    product.product_name,
                                                                                    product.product_id,
                                                                                    brands.brand_name,
                                                                                    categories.categories_name
                                                                                FROM product
                                                                                JOIN categories ON product.category_id = categories.categories_id
                                                                                LEFT JOIN brands ON product.brand_id = brands.brand_id
                                                                                $where
                                                                                ORDER BY categories.categories_name ASC
                                                                            ");

                                    $sr = 1;
                                    while ($row = mysqli_fetch_assoc($query)) {
                                        ?>
                                        <tr>
                                            <!-- <td><?= $sr ?></td> -->
                                            <td class="text-capitalize"><?= $row['categories_name'] ?></td>
                                            <td class="text-capitalize"><?= $row['product_name'] ?></td>
                                            <td class="text-capitalize"><?= $row['brand_name'] ?></td>
                                            <td>0</td>
                                        </tr>
                                        <?php
                                        $sr++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </main>
    </div>
</body>

<?php include_once 'includes/foot.php'; ?>

<script type="text/javascript">
    $(document).ready(function () {
        $('#noPaginationTable').DataTable({
            paging: false,    // Disable pagination
            searching: false, // Optional: disable search
            info: false       // Optional: disable "Showing X of Y"
        });
    });

</script>

</html>