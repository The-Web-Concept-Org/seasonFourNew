<!DOCTYPE html>
<html>
<?php include_once 'includes/head.php';


?>
<style>
    @page {
        size: A4;
        margin: 10mm;
        /* Adjust margins as needed */
    }

    body {
        margin: 0;
        padding: 0;
        background: white;
    }

    td {
        font-size: 16px;
    }

    .invoice-container {
        width: 100%;
        /* A4 width */
        min-height: 297mm;
        /* A4 height */
        background: white;
        padding: 10mm;
        box-shadow: none;
    }

    @media print {
        body {
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            width: 100%;
            min-height: 297mm;
            padding: 10mm;
            box-shadow: none;
        }

        .bg-img img {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    .seasonh {
        font-size: 22px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header p {
        font-size: 16px;
    }

    .image {
        width: 150px;
        height: 150px;
    }

    .fright {
        text-align: end;
    }

    .heding {
        color: darkgreen;
    }

    .heding h2 {
        font-size: 18px;
    }

    .heding p {
        font-size: 16px;
    }

    .invo {
        text-align: center;
    }

    .label {
        font-size: 16px;
        display: flex;
        justify-content: space-between;
        background-color: darkgreen;
        color: white;
        border-radius: 30px;
        margin: 0;
        padding: 10px;
        margin-bottom: 20px;
    }

    .label p {
        margin: 0;
    }

    .invoice-bg {}

    .content {
        position: relative;
        width: 100%;
    }

    .bg-img {
        position: absolute;
        display: flex;
        justify-content: center;
        width: 80%;
        ;
    }

    .bg-image {
        width: 400px;
        opacity: 0.05;
    }

    .invoice-details {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    table {
        width: 100%;
        background-color: transparent !important;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th {
        border: 1px dotted darkgreen;
        padding: 6px 8px;
        text-align: center;
        color: darkgreen !important;
        font-size: 18px;
    }

    table tbody tr:first-child td {
        border-top: none !important;
    }

    table td {
        text-align: center;
        padding: 5px 0px;
        /* border-bottom: 1px solid #e9ecef; */
    }

    .table-border {
        border: 1px solid #e9ecef;
    }



    table .descri {
        text-align: left;
    }

    .invoice-details,
    .invoice-details p {
        margin: 4px;
    }

    .tablefooter {
        /* border-top: 1px dotted darkgreen; */
        margin-top: 20px;
        font-size: 18px;
        font-weight: bold;
        text-align: center;

    }

    .netamount {
        text-align: end;
        padding-right: 30px;
    }

    .return {
        display: flex;
        justify-content: space-between;
        font-size: 16px;
        font-weight: bold;
        border-bottom: 3px solid black;
        margin-top: 200px;
    }

    .footer {
        margin-top: 5px;
        display: flex;
        justify-content: space-between;
    }

    .centerdiv {
        margin: 0 5px;
        font-weight: bold;
    }

    .center {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }

    .qr {
        margin-top: 15px;
        width: 100px;
    }



    p {
        font-size: 16px;
    }

    @media print {
        @page {
            margin: 1in;
            size: auto;
        }

        body {
            margin: 0;
        }
    }
</style>

<body>
    <?php for ($i = 0; $i < 1; $i++):
        $totalQTY = 0;
        if ($i > 0) {
            $margin = "margin-top:-270px !important";
            $copy = "Company Copy";
        } else {
            $margin = "";
            $copy = "Customer Copy";
        }

        if ($_REQUEST['type'] == "purchase") {
            $nameSHow = 'Supplier';
            $id_name = "Purchase Id";
            $order = fetchRecord($dbc, "purchase", "purchase_id", $_REQUEST['id']);
            if ($order['payment_type'] == "credit_purchase") {
                $unique_id = 'SF-CRP-' . $order['purchase_id'];
            } else {
                $unique_id = 'SF25-CP-' . $order['purchase_id'];
            }
            // $unique_id = 'SF25-CP-' . $order['purchase_id'];
            $comment = $order['purchase_narration'];
            $table_row = "390px";
            $getDate = $order['purchase_date'];
            if ($order['payment_type'] == "credit_purchase") {
                $invoice_name = " purchase invoice";
            } else {
                $invoice_name = " purchase invoice";
            }
            $order_item = mysqli_query($dbc, "SELECT purchase_item.*,product.* FROM purchase_item INNER JOIN product ON purchase_item.product_id=product.product_id WHERE purchase_item.purchase_id='" . $_REQUEST['id'] . "'");
        } elseif ($_REQUEST['type'] == "gatepass") {
            $nameSHow = 'Customer';
            $invoice_name = "Gatepass";
            $id_name = "Gatepass Id";
            $order = fetchRecord($dbc, "gatepass", "gatepass_id", $_REQUEST['id']);
            $unique_id = 'SF25-G-' . $order['gatepass_id'];
            $getDate = $order['gatepass_date'];
            $comment = $order['gatepass_narration'];
            $order_item = mysqli_query($dbc, "SELECT gatepass_item.*,product.* FROM gatepass_item INNER JOIN product ON gatepass_item.product_id=product.product_id WHERE gatepass_item.gatepass_id='" . $_REQUEST['id'] . "'");
            if ($order['payment_type'] == "gatepass") {
                $table_row = "300px";
                if ($order['payment_type'] == "none") {
                    $order_type = "Gatepass";
                } else {
                    $order_type = " (Gatepass)";
                }
            } else {
                $order_type = "Gatepass";
                $table_row = "350px";
            }
        } elseif ($_REQUEST['type'] == "order") {
            $nameSHow = 'Customer';
            $id_name = "Sale Id";
            $order = fetchRecord($dbc, "orders", "order_id", $_REQUEST['id']);
            $unique_id = 'SF25-S-' . $order['order_id'];
            if ($order['payment_type'] == "credit_sale") {
                $invoice_name = " Sale Invoice";
            } else {
                $invoice_name = " Sale Invoice";
            }
            $getDate = $order['order_date'];
            $comment = $order['order_narration'];
            $order_item = mysqli_query($dbc, "SELECT order_item.*,product.* FROM order_item INNER JOIN product ON order_item.product_id=product.product_id WHERE order_item.order_id='" . $_REQUEST['id'] . "'");
            if ($order['payment_type'] == "credit_sale") {
                $table_row = "300px";
                if ($order['payment_type'] == "none") {
                    $order_type = "credit sale";
                } else {
                    $order_type = $order['credit_sale_type'] . " (Credit)";
                }
            } else {
                $order_type = "cash sale";
                $table_row = "350px";
            }
        } elseif ($_REQUEST['type'] == "quotation") {
            $nameSHow = 'Customer';


            $order = fetchRecord($dbc, "quotations", "quotation_id", $_REQUEST['id']);
            if ($order['is_delivery_note'] == 1) {
                $invoice_name = "Delivery Note";
                $id_name = "Delivery Note Id";
                $unique_id = 'SF25-DN-' . $order['quotation_id'];
            } else {
                $invoice_name = "Quotation";
                $id_name = "Quotation Id";
                $unique_id = 'SF25-Q-' . $order['quotation_id'];
            }
            $getDate = $order['quotation_date'];
            $comment = $order['quotation_narration'];
            $order_item = mysqli_query($dbc, "SELECT quotation_item.*,product.* FROM quotation_item INNER JOIN product ON quotation_item.product_id=product.product_id WHERE quotation_item.quotation_id='" . $_REQUEST['id'] . "'");
            if ($order['payment_type'] == "quotation") {
                $table_row = "300px";
                if ($order['payment_type'] == "none") {
                    $order_type = "Quotation";
                } else {
                    $order_type = " (Quotation)";
                }
            } else {
                $order_type = "Quotation";
                $table_row = "350px";
            }
        } elseif ($_REQUEST['type'] == "lpo") {
            $nameSHow = 'Customer';
            $invoice_name = "LPO";
            $id_name = "LPO Id";
            $order = fetchRecord($dbc, "lpo", "lpo_id", $_REQUEST['id']);
            $unique_id = 'SF25-LPO-' . $order['lpo_id'];
            $getDate = $order['lpo_date'];
            $comment = $order['lpo_narration'];
            $order_item = mysqli_query($dbc, "SELECT lpo_item.*,product.* FROM lpo_item INNER JOIN product ON lpo_item.product_id=product.product_id WHERE lpo_item.lpo_id='" . $_REQUEST['id'] . "'");
            if ($order['payment_type'] == "lpo") {
                $table_row = "300px";
                if ($order['payment_type'] == "none") {
                    $order_type = "LPO";
                } else {
                    $order_type = " (LPO)";
                }
            } else {
                $order_type = "LPO";
                $table_row = "350px";
            }
        } elseif ($_REQUEST['type'] == "purchase_return") {
            $nameSHow = 'Supplier';
            $id_name = "Purchase Id";
            $order = fetchRecord($dbc, "purchase_return", "purchase_id", $_REQUEST['id']);
            $unique_id = 'SF25-PR-' . $order['purchase_id'];
            $comment = $order['purchase_narration'];
            $table_row = "390px";
            $getDate = $order['purchase_date'];
            if ($order['payment_type'] == "credit_purchase") {
                $invoice_name = "credit purchase return invoice";
            } else {
                $invoice_name = "cash purchase return invoice";
            }
            $order_item = mysqli_query($dbc, "SELECT purchase_return_item.*,product.* FROM purchase_return_item INNER JOIN product ON purchase_return_item.product_id=product.product_id WHERE purchase_return_item.purchase_id='" . $_REQUEST['id'] . "'");
        } elseif ($_REQUEST['type'] == 'order_return') {
            $nameSHow = 'Customer';
            $id_name = "Sale Id";
            $order = fetchRecord($dbc, "orders_return", "order_id", $_REQUEST['id']);
            $unique_id = 'SF25R-S-' . $order['order_id'];
            $unique_id = $order['order_id'];
            if ($order['payment_type'] == "credit_sale") {
                $invoice_name = "Credit Sale Return Invoice";
            } else {
                $invoice_name = "Cash Sale Return Invoice";
            }
            $getDate = $order['order_date'];
            $comment = $order['order_narration'];
            $order_item = mysqli_query($dbc, "SELECT order_return_item.*,product.* FROM order_return_item INNER JOIN product ON order_return_item.product_id=product.product_id WHERE order_return_item.order_id='" . $_REQUEST['id'] . "'");
            if ($order['payment_type'] == "credit_sale") {
                $table_row = "300px";
                if ($order['payment_type'] == "none") {
                    $order_type = "credit sale";
                } else {
                    $order_type = $order['credit_sale_type'] . " (Credit)";
                }
            } else {
                $order_type = "cash sale";
                $table_row = "350px";
            }
        } elseif ($_REQUEST['type'] == "manualbill") {
            $nameSHow = 'Customer_name';
            $id_name = "Cash Incoice Id";
            $order = fetchRecord($dbc, "manual_bill", "order_id", $_REQUEST['id']);
            $unique_id = 'SF25-CI-' . $order['order_id'];

            $invoice_name = "Sale Invoice";

            $getDate = $order['order_date'];
            $comment = $order['order_narration'];
            // $order_item = json_decode($order['product_details'], true);
    
        }


        $date = date(' d-M-Y h:i A', strtotime(@$order['timestamp'] . " +10 hours"));
        function numberToWords($number)
        {
            $words = [
                0 => 'ZERO',
                1 => 'ONE',
                2 => 'TWO',
                3 => 'THREE',
                4 => 'FOUR',
                5 => 'FIVE',
                6 => 'SIX',
                7 => 'SEVEN',
                8 => 'EIGHT',
                9 => 'NINE',
                10 => 'TEN',
                11 => 'ELEVEN',
                12 => 'TWELVE',
                13 => 'THIRTEEN',
                14 => 'FOURTEEN',
                15 => 'FIFTEEN',
                16 => 'SIXTEEN',
                17 => 'SEVENTEEN',
                18 => 'EIGHTEEN',
                19 => 'NINETEEN',
                20 => 'TWENTY',
                30 => 'THIRTY',
                40 => 'FORTY',
                50 => 'FIFTY',
                60 => 'SIXTY',
                70 => 'SEVENTY',
                80 => 'EIGHTY',
                90 => 'NINETY'
            ];

            if ($number < 21)
                return $words[$number];
            if ($number < 100) {
                return $words[10 * floor($number / 10)] . ($number % 10 ? ' ' . $words[$number % 10] : '');
            }
            if ($number < 1000) {
                return $words[floor($number / 100)] . ' HUNDRED' . ($number % 100 ? ' ' . numberToWords($number % 100) : '');
            }
            if ($number < 1000000) {
                return numberToWords(floor($number / 1000)) . ' THOUSAND' . ($number % 1000 ? ' ' . numberToWords($number % 1000) : '');
            }

            return 'NUMBER TOO LARGE';
        }

        function amountToWordsKD($amount)
        {
            $parts = explode('.', number_format($amount, 3, '.', ''));
            $kd = (int) $parts[0];
            $fils = isset($parts[1]) ? (int) round($parts[1]) : 0;

            $kdPart = numberToWords($kd) . ' KD';
            $filsPart = $fils > 0 ? ' AND ' . numberToWords($fils) . ' FILLS' : '';

            return $kdPart . $filsPart;
        }

        function formatKDandFils($amount)
        {
            $parts = explode('.', number_format($amount, 3, '.', ''));
            $kd = (int) $parts[0];
            $fils = (int) $parts[1];

            $output = $kd . ' KD';
            if ($fils > 0) {
                $output .= ' AND ' . $fils . ' FILLS';
            }

            return $output;
        }

        function formatAmountWithKD($amount)
        {
            // If amount is not numeric or empty, default to 0
            if (!is_numeric($amount) || $amount === '') {
                $amount = 0;
            }
            return number_format((float) $amount, 3) . ' KD';
        }

        function formatAmountWithoutKD($amount)
        {
            if (!is_numeric($amount) || $amount === '') {
                $amount = 0;
            }
            return number_format((float) $amount, 3);
        }


        ?>

        <div class="invoice-container">
            <!-- <div class="header">
                <div>
                    <div class="heding">
                        <h2><span class="seasonh">season</span> FOUR</h2>
                        <p>A/C & REFRIGERATION CONTRACTING EST</p>
                    </div>
                    <div>
                        <p>
                            Farwaniyah Branch Block 4,5t 45-55529970
                            <i class="fa-brands fa-whatsapp"></i> 66944871
                        </p>
                        <p>
                            Shuwaikh Branch Block 3,5t 53-66945212
                            <i class="fa-brands fa-whatsapp"></i> 99408640
                        </p>
                        <p>Telefex : 24734306</p>
                        <p><i class="fa-solid fa-envelope"></i> season4-kw@hotmail.com</p>
                        <p><i class="fa-brands fa-instagram"></i> seasonfourkwt</p>
                    </div>
                </div>
                <div class="image"><img class="image" src="img/logo/<?= $get_company['logo'] ?>" alt="" /></div>
                <div class="fright">
                    <div class="heding">
                        <h2>الموسم الرابع</h2>
                        <p>مؤسسة المقاولات لتكييف الهواء والتبريد</p>
                    </div>
                    <div>
                        <p>
                            فرع الفروانية قطعة 4.5 45-55529970
                            <i class="fa-brands fa-whatsapp"></i> 66944871
                        </p>
                        <p>
                            فرع الشويخ قطعة 3.5 53-66945212
                            <i class="fa-brands fa-whatsapp"></i> 99408640
                        </p>
                        <p>تليفليكس : 24734306</p>
                        <p><i class="fa-solid fa-envelope"></i> season4-kw@hotmail.com</p>
                        <p><i class="fa-brands fa-instagram"></i> seasonfourkwt</p>
                    </div>
                </div>
            </div> -->

            <!-- <div class="label">
                <p>ALL TYPES OF A/C, REFRIGERATOR, WASHING MACHINE SPARE PARTS</p>
                <p>جميع أنواع قطع غيار المكيفات والثلاجات والغسالات</p>
            </div> -->
            <!-- <div style="margin-top: 226.772px;"></div> -->
            <div class="invo">
                <h2 class="text-uppercase"><?= $invoice_name ?></h2>
            </div>

            <div class="invoice-bg">
                <!-- <div class="bg-img">
                    <img class="bg-image" src="img/logo/<?= $get_company['logo'] ?>" alt="" />
                </div> -->
                <div class="content">
                    <div class="invoice-details">
                        <div class="m-0 p-0">
                            <p class="text-uppercase"><strong><?= $id_name ?> :</strong> <?= $unique_id ?></p>
                        </div>


                        <div class="m-0 p-0">
                            <p><strong>DATE:</strong> <?= $date ?> </p>
                        </div>
                    </div>
                    <div class="invoice-details">
                        <div class="m-0 p-0">
                            <?php
                            if ($_REQUEST['type'] == 'gatepass') {
                                $from = fetchRecord($dbc, "branch", "branch_id", $order['from_branch']);
                                $to = fetchRecord($dbc, "branch", "branch_id", $order['to_branch']);
                                ?>
                                <p class="text-uppercase"><strong> From Branch:</strong> <?= @$from['branch_name'] ?></p>
                            <?php } else { ?>

                                <p class="text-uppercase"><strong>Customer Name :</strong>
                                    <?= @$order['client_name'] ?: @$order['customer_name'] ?>
                                </p>

                            <?php } ?>

                            <!-- <p><strong>DATE:</strong> <?= $date ?> </p> -->
                            <!-- <p><strong>TIME:</strong> <?= date($order['timestamp']) ?>
                            </p> -->
                        </div>
                        <div class="m-0 p-0">
                            <?php
                            if ($_REQUEST['type'] == 'gatepass') {
                                $from = fetchRecord($dbc, "branch", "branch_id", $order['from_branch']);
                                $to = fetchRecord($dbc, "branch", "branch_id", $order['to_branch']);
                                ?>
                                <p class="text-uppercase"><strong> To Branch:</strong> <?= $to['branch_name'] ?></p>
                            <?php } else { ?>
                                <div>
                                    <?php if ($_REQUEST['type'] == 'gatepass') {
                                        $from = fetchRecord($dbc, "branch", "branch_id", @$order['from_branch']);
                                        $to = fetchRecord($dbc, "branch", "branch_id", @$order['to_branch']);
                                        ?>
                                    <?php } else {
                                        $branch = fetchRecord($dbc, "branch", "branch_id", @$order['branch_id']);
                                        if (isset($branch['branch_name'])) {
                                            ?>
                                            <p class="text-uppercase"><strong>Branch:</strong> <?= @$branch['branch_name'] ?></p>
                                        <?php }
                                    } ?>
                                </div>
                            <?php } ?>
                        </div>


                    </div>
                    <div class="invoice-details">

                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">S.No</th>
                                <th style="width: 25%;" class="text-left pl-3">Description</th>
                                <th style="width: 5%;">Qty</th>
                                <?php if (@$order['is_delivery_note'] != 1) { ?>
                                    <th style="width: 5%;">Unit Price</th>
                                    <th style="width: 5%;">Amount</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <?php if (!empty($order['product_details'])): ?>
                            <!-- Show JSON-decoded product_details -->
                            <tbody>
                                <?php
                                $json_items = json_decode($order['product_details'], true);
                                $jc = 0;
                                foreach ($json_items as $item) {
                                    $jc++;
                                    ?>
                                    <tr class="border">
                                        <td class="text-center border"><?= $jc ?></td>
                                        <td class="text-left border pl-3"><?= strtoupper($item['product_name']) ?></td>
                                        <td class="text-center border"><?= $item['quantity'] ?></td>
                                        <td class="text-center border"><?= formatAmountWithoutKD($item['final_rate']) ?></td>
                                        <td class="text-center border">
                                            <?= formatAmountWithoutKD($item['final_rate'] * $item['quantity']) ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>

                        <?php elseif (!empty($order_item) && gettype($order_item) === 'object'): ?>
                            <!-- Show MySQL Fetched Products -->
                            <tbody>
                                <?php
                                $c = 0;
                                $totalAm = 0;
                                $totalQTY = 0;
                                while ($r = mysqli_fetch_assoc($order_item)) {
                                    $c++;
                                    $brand = fetchRecord($dbc, "brands", "brand_id", $r['brand_id']);
                                    $cat = fetchRecord($dbc, "categories", "categories_id", $r['category_id']);
                                    ?>
                                    <tr class="border">
                                        <td class="text-center border"><?= $c ?></td>
                                        <td class="text-left border pl-3">
                                            <?php if (!empty($cat['categories_name'])): ?>
                                                <?= strtoupper($cat['categories_name']) ?> |
                                            <?php endif; ?>
                                            <?= strtoupper($r['product_name']) ?>
                                            <?php if (!empty($brand['brand_name']) && strtolower($brand['brand_name']) !== 'china'): ?>
                                                | <?= strtoupper($brand['brand_name']) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center border"><?= $r['quantity'] ?></td>
                                        <?php if (@$order['is_delivery_note'] != 1): ?>
                                            <td class="text-center border"><?= formatAmountWithoutKD($r['rate']) ?></td>
                                            <td class="text-center border"><?= formatAmountWithoutKD($r['rate'] * $r['quantity']) ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php
                                    $totalQTY += $r['quantity'];
                                    $totalAm += $r['rate'] * $r['quantity'];
                                } ?>
                            </tbody>
                        <?php endif; ?>

                        <tfoot>
                            <tr class="tablefooter" style="font-size: 14px;">
                                <td colspan="3" class="text-left"><strong>Note:</strong> <span><?= $comment ?></span></td>


                                <?php if (@$order['is_delivery_note'] != 1) { ?>
                                    <?php if (!empty($order['discount']) && $order['discount'] > 0): ?>

                                        <td class="border">Discount:</td>
                                        <td class="border"><?= formatAmountWithKD($order['discount']) ?></td>
                                    <?php endif; ?>
                                </tr>

                            <?php } ?>

                            <?php if (@$order['is_delivery_note'] != 1) { ?>
                                <tr class="tablefooter" style="font-size: 14px; border: none !important;">
                                    <td colspan="3" class="text-left  border-none"><?= amountToWordsKD($order['grand_total']) ?>
                                        ONLY</td>
                                    <td class="text-sm border">Net Amount:</td>
                                    <td class="border"><?= formatAmountWithKD($order['grand_total']); ?></td>
                                </tr>
                            <?php } ?>
                            <?php if ($_REQUEST['type'] !== 'lpo' && $_REQUEST['type'] !== 'quotation' && $_REQUEST['type'] !== 'gatepass') { ?>
                                <tr class="tablefooter" style="font-size: 14px;">
                                        <?php if ($order['grand_total'] !== "") { ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-sm border">Paid:</td>
                                        <?php if ($_REQUEST['type'] == "manualbill"){ ?>
                                        <td class="border"><?= formatAmountWithoutKD(@$order['grand_total']) ?></td>
                                        <?php }else {
                                        
                                         ?>
                                        <td class="border"><?= formatAmountWithoutKD(@$order['paid']) ?></td>
<?php } ?>
                                    </tr>
                                    <tr class="tablefooter" style="font-size: 14px;">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-sm border">Remaining:</td>
                                        <td class="border">
                                            <?php if (!empty($order['product_details'])): ?>

                                                <?= formatAmountWithoutKD(0) ?>
                                            <?php else: ?>
                                                <?= formatAmountWithoutKD($order['due']) ?>
                                            <?php endif; ?>

                                        </td>
                                    </tr>
                                <?php }
                            } ?>

                    </table>
                    </tfoot>
                </div>
            </div>
            <?php
            if ($_REQUEST['type'] == "quotation" && $order['is_delivery_note'] != 1) { ?>
                <div class="pt-5 mt-3 mb-5">
                    <div class="row">
                        <div class="col-2">

                            <p><strong>Payment Mode:</strong> </p>
                        </div>
                        <div class="col-1">
                            <p>______________________ </p>
                        </div>
                        <div class="col-9"></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-2">

                            <p><strong>Price Validity:</strong> </p>
                        </div>
                        <div class="col-1">
                            <p>______________________ </p>
                        </div>
                        <div class="col-9 w-100">
                            <div class="row w-100 text-right ml-auto">
                                <div class="col-12 text-right d-flex justify-content-end">
                                    <p><strong>Prepared By:</strong> </p>
                                    <p class="text-capitalize pl-3 pr-2">
                                        <?php
                                        $user = fetchRecord($dbc, "users", "user_id", $_SESSION['user_id']);
                                        if (isset($user['fullname'])) {
                                            echo $user['fullname'];
                                        } else {
                                            echo "______________________";
                                        }
                                        ?>
                                    </p>

                                </div>

                            </div>
                        </div>
                    </div>


                </div>
            <?php } else { ?>

                <div class="row mt-5 pt-5 m-0 px-5 mr-4" style=" !important;">
                    <div class="col-8"></div>
                    <div class="col-3 text-right">
                        <p><strong>Prepared By:</strong> </p>
                    </div>
                    <div class="col-1 pr-5">
                        <!-- <p>______________________ </p> -->
                        <?php
                        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


                        $user = fetchRecord($dbc, "users", "user_id", $userId);
                        $fullName = !empty($user['fullname']) ? strtoupper($user['fullname']) : "UNKNOWN USER";

                        ?>

                        <span style="white-space: nowrap;">
                            <?= $fullName ?>
                        </span>


                    </div>
                </div>
            <?php } ?>
            <!-- <div>
                <div class="return">
                    <p>
                        Goods can be returned within 14 days original packaging & Invoice
                    </p>
                    <p>يمكن إرجاع البضائع خلال 14 يومًا مع العبوة الأصلية والفاتورة</p>
                </div>

                <!-- <div class="footer">
                    <div>
                        <img class="qr" src="img/logo/frame.svg" alt="" />
                    </div>
                    <div class="centerdiv text-center">
                        <p>
                            Please issue The cheque In the name of "Season Four Electronic &
                            Repairing"
                        </p>
                        <p>
                            "يرجى إصدار الشيك باسم "مؤسسة فصول الاربعة للاجهزة الكهربائية والالكترونية وتصليحها""</p>
                        <div class="center">
                            <div><span>Receiver's Sign </span><span> علامة المستقبل</span></div>
                            <div><span>Salesman's Sign </span><span> علامة البائع</span></div>
                        </div>
                    </div>
                    <div><img class="qr" src="img/logo/frame-2.svg" alt="" /></div>
                </div> -->
        </div> -->
        </div><!-- end of container -->
    <?php endfor; ?>


</body>

</html>
<script type="text/javascript">
    window.print();
    //   window.close();
</script>