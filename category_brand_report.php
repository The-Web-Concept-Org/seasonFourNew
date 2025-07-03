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

</style>

<body class="horizontal light  ">
    <div class="wrapper">
        <?php include_once 'includes/header.php'; ?>
        <main role="main" class="main-content">
            <div class="container-fluid">
                <div class="card form_sec">
                    <div class="card-header card-bg" align="center">

                        <div class="row d-print-none">
                            <div class="col-12 mx-auto h4">
                                <b class="text-center card-text">Category/brand Report</b>
                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <form action="" method="get" class=" d-print-none">
                            <div class="row">

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="">Branch</label>
                                        <select class="form-control searchableSelect text-capitalize" name="branch_id"
                                            id="branch_id" required>
                                            <option selected disabled>Select Branch</option>
                                            <?php $branch = mysqli_query($dbc, "SELECT * FROM branch WHERE branch_status = 1");
                                            while ($row = mysqli_fetch_array($branch)) { ?>
                                                ?>
                                                <option class="text-capitalize"
                                                    value="<?= $row['branch_id'] ?>"><?= $row['branch_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div><!-- group -->
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="">Category</label>
                                        <select class="form-control searchableSelect text-capitalize" name="category_id"
                                            id="category_id">
                                            <option selected disabled>Select Category</option>
                                            <?php $category = mysqli_query($dbc, "SELECT * FROM categories WHERE categories_status = 1");
                                            while ($row = mysqli_fetch_array($category)) { ?>
                                        
                                                <option class="text-capitalize"
                                                    
                                                     value="<?= $row['categories_id'] ?>">
                                                    <?= $row['categories_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="">Brand</label>
                                        <select class="form-control searchableSelect text-capitalize" name="brand_id"
                                            id="brand_id">
                                            <option selected disabled>Select Brand</option>
                                            <?php $brand = mysqli_query($dbc, "SELECT * FROM brands WHERE brand_status = 1");
                                            while ($row = mysqli_fetch_array($brand)) { ?>
                                            
                                                <option class="text-capitalize"
                                                    
                                                    value="<?= $row['brand_id'] ?>"><?= $row['brand_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="">Product</label>
                                        <select class="form-control searchableSelect text-capitalize" name="product_id"
                                            id="product_id">
                                            <option selected disabled>Select product</option>
                                            <?php $product = mysqli_query($dbc, "SELECT * FROM product WHERE status = 1");
                                            while ($row = mysqli_fetch_array($product)) { ?>
                                                ?>
                                                <option class="text-capitalize"
                                                    
                                                    value="<?= $row['product_id'] ?>"><?= $row['product_name'] ?></option>
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
                </div> <!-- .card -->
                <?php if (isset($_GET['search_sale'])) { ?>
                    <div class="card">
                        <div class="card-header card-bg" align="center">
                            <div class="row">
                                <div class="col-12 mx-auto h4">
                                    <b class="text-center card-text">Category/Brand Report</b>
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
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:5% ;">Pro_Id</th>
                                        <th>Category</th>
                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $branchId = $_GET['branch_id'] ?? '';
                                    $categoryId = $_GET['category_id'] ?? '';
                                    $brandId = $_GET['brand_id'] ?? '';
                                    $productId = $_GET['product_id'] ?? '';

                                    $where = "WHERE inventory.quantity_instock > 0";

                                    if (!empty($branchId)) {
                                        $where .= " AND inventory.branch_id = '$branchId'";
                                    }
                                    if (!empty($categoryId)) {
                                        $where .= " AND product.category_id = '$categoryId'";
                                    }
                                    if (!empty($brandId)) {
                                        $where .= " AND product.brand_id = '$brandId'";
                                    }
                                    if (!empty($productId)) {
                                        $where .= " AND product.product_id = '$productId'";
                                    }


                                    $query = mysqli_query($dbc, "SELECT 
                                                            product.product_name,
                                                            product.product_id,
                                                            inventory.quantity_instock,
                                                            brands.brand_name,
                                                            categories.categories_name
                                                        FROM inventory
                                                        JOIN product ON inventory.product_id = product.product_id
                                                        JOIN brands ON product.brand_id = brands.brand_id
                                                        JOIN categories ON product.category_id = categories.categories_id
                                                        $where
                                                        ORDER BY product.product_name DESC
                                                        ");


                                    $sr = 1;
                                    $totalStock = 0;
                                    while ($row = mysqli_fetch_assoc($query)) {
                                        ?>
                                        <tr>
                                            <td><?= $row['product_id'] ?></td>
                                            <td class="text-capitalize"><?= $row['categories_name'] ?></td>
                                            <td class="text-capitalize"><?= $row['product_name'] ?></td>
                                            <td class="text-capitalize"><?= $row['brand_name'] ?></td>
                                            <td><?= $row['quantity_instock'] ?></td>
                                        </tr>
                                        <?php
                                        $totalStock += $row['quantity_instock'];
                                        $sr++;
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <h3>Total</h3>
                                        </td>
                                        <td>
                                            <h3><?= $totalStock ?></h3>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>


            </div> <!-- .container-fluid -->

        </main> <!-- main -->
    </div> <!-- .wrapper -->

</body>

</html>
<?php include_once 'includes/foot.php'; ?>