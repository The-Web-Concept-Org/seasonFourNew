<!DOCTYPE html>
<html>
<?php include_once 'includes/head.php';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    @page {
        size: A4;
        margin: 10mm;
    }

    body {
        margin: 0;
        padding: 0;
        background: white;
        font-family: 'Roboto', 'Arial', sans-serif;
    }

    td {
        font-size: 16px;
    }

    .invoice-container {
        width: 100%;
        min-height: 297mm;
        background: white;
        padding: 10mm;
        box-shadow: none;
    }

    .company-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 10px;
        background-color: #fff;
        font-family: Arial, sans-serif;
    }

    .company-name {
        font-size: 24px;
        font-weight: bold;
        color: #006400;
        margin: 0;
    }

    .company-sub {
        font-size: 14px;
        color: #006400;
        margin: 5px 0;
    }

    .contact-info,
    .contact-info_arabic {
        font-size: 12px;
        margin: 2px 0;
        color: #000;
    }

    .contact-info i {
        color: #1a5f3a;
    }

    .logo {
        width: 180px;
        height: 160px;
        object-fit: contain;
    }

    .rtl {
        text-align: right;
    }

    /* Add perforated edge effect with pseudo-elements */
    .company-header {
        position: relative;
        overflow: hidden;
    }

    .company-header::before,
    .company-header::after {
        content: '';
        position: absolute;
        width: 10px;
        height: 100%;
        background: repeating-linear-gradient(90deg,
                #fff 0,
                #fff 4px,
                #000 4px,
                #000 6px);
    }

    .company-header::before {
        left: -10px;
    }

    .company-header::after {
        right: -10px;
    }

    @media print {
        body {
            position: relative;
            margin: 0;
            padding: 0;
            font-family: 'Roboto', 'Arial', sans-serif;
        }

        .invoice-container {
            width: 100%;
            margin: 40px auto;
            background: white;
            padding: 0 30px;
        }


        .bg-img img {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .pdf-only-header {
            display: none;
        }

        #saveAsPdfBtn,
        #printBtn {
            display: none !important;
        }

        .pdf_footer {
            position: absolute;
            margin-left: 50px;
        }
    }

    .invo {
        text-align: center;
        /* margin-bottom: 20% */
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

    .content {
        position: relative;
        width: 100%;
    }

    .bg-img {
        position: absolute;
        display: flex;
        justify-content: center;
        width: 80%;
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

    .heder {
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
        /* margin-top: 200px; */
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

    .pdf-only-header {
        display: none;
    }

    #saveAsPdfBtn,
    #printBtn {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
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
                $invoice_name = "Credit purchase invoice";
            } else {
                $invoice_name = "Cash purchase invoice";
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
            if ($order['payment_type'] == "credit") {
                $invoice_name = "Credit Sale Invoice";
            } else {
                $invoice_name = "Cash Sale Invoice";
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
            if ($order['payment_type'] == "credit") {
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
            $id_name = "Id";
            $order = fetchRecord($dbc, "manual_bill", "order_id", $_REQUEST['id']);
            $unique_id = 'SF25-Id-' . $order['order_id'];

            $invoice_name = $order['type'];

            $getDate = $order['timestamp'];
            $comment = $order['order_narration'];
            // $order_item = json_decode($order['product_details'], true);
    
        }


        $date = date(' d-M-Y h:i A', strtotime(@$order['timestamp'] . " +7 hours"));
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
            <div class="pdf-only-header">
                <div class="company-header">
                    <div>
                        <h2 class="company-name">Season Four</h2>
                        <p class="company-sub">A/C & Refrigeration Contracting Est</p>
                        <p class="contact-info"><i class="fa-solid fa-map-marker-alt"></i> Farwaniyah Branch-Block 4, St 45
                            -
                            55529978
                            <i class="fa-brands fa-whatsapp"></i> 66944871
                        </p>
                        <p class="contact-info"><i class="fa-solid fa-map-marker-alt"></i> Shuwaikh Branch-Block 3, St 53 -
                            66945212 <i class="fa-brands fa-whatsapp"></i> 99408640</p>
                        <p class="contact-info"><i class="fa-solid fa-fax"></i> Telefax: 24734306</p>
                        <p class="contact-info"><i class="fa-solid fa-envelope"></i> season4-kw@hotmail.com</p>
                        <p class="contact-info"><i class="fa-brands fa-instagram"></i> seasonfourkwt</p>
                    </div>
                    <div><img class="logo" src="img/logo/<?= $get_company['logo'] ?>" alt="Logo"></div>
                    <div class="rtl">
                        <h2 class="company-name">مؤسسة الفصول الأربعة</h2>
                        <p class="company-sub">للأجهزه التكييف واللتبريد ومقاولاتها</p>
                        <p class="contact-info_arabic"> المعرض الفروانية - قطعة ٤ - شارع ٤٥ -
                            ٦٦٩٤٤٨٧١<i class="fa-brands fa-whatsapp"></i> ٥٥٥٢٩٩٧٨</p>
                        <p class="contact-info_arabic"> المعرض الشويخ - قطعة ٣ - شارع ٥٣ -
                            ٩٩٤٢٨٦٤٠ <i class="fa-brands fa-whatsapp"></i> ٦٦٩٤٥٢١٢
                        </p>
                        <p class="contact-info_arabic"> ت: ٢٤٧٦٤٣٠٦ </p>
                    </div>
                </div>
                <div class="label">
                    <p>ALL TYPES OF A/C, REFRIGERATOR, WASHING MACHINE SPARE PARTS</p>
                    <p>قطع غيار ، غسالات - ثلاجات - مكيفا وجميع انواع تبرید و تکییف</p>
                </div>
            </div>
            <!-- <div style="margin-top: 226.772px;"></div> -->
            <div class="invo">
                <h2 class="text-uppercase"><?= $invoice_name ?></h2>
                <!-- Manual PDF Button -->
                <div style="margin-top: 20px; text-align: end;">
                    <button id="saveAsPdfBtn">Save as PDF</button>
                    <button onclick="window.print();" id="printBtn">Print</button>
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
                                    <th class="heder" style="width: 5%;">S.No</th>
                                    <th style="width: 25%;" class="text-left pl-3 heder">Description</th>
                                    <th class="heder" style="width: 5%;">Qty</th>
                                    <?php $shouldShow = true; // Default to showing content
                                    
                                        // Hide if it's a gatepass
                                        if (($_REQUEST['type'] ?? '') === 'gatepass') {
                                            $shouldShow = false;
                                        }

                                        // Hide if it's a delivery note (either is_delivery_note=1 or type=delivery_note)
                                        if (($order['is_delivery_note'] ?? 0) == 1 || ($order['type'] ?? '') === 'delivery_note') {
                                            $shouldShow = false;
                                        }

                                        // Special case: Show if it's manualbill AND NOT delivery_note
                                        if (($_REQUEST['type'] ?? '') === 'manualbill' && ($order['type'] ?? '') !== 'delivery_note') {
                                            $shouldShow = true;
                                        }

                                        if ($shouldShow) { ?>
                                        <th class="heder" style="width: 5%;">Unit Price</th>
                                        <th class="heder" style="width: 5%;">Amount</th>
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
                                            <td class="text-left border pl-3">
                                                <?= strtoupper(preg_replace('/\s*-\s*CHINA\s*$/i', '', $item['product_name'])) ?>
                                            </td>

                                            <td class="text-center border"><?= $item['quantity'] ?></td>
                                            <?php
                                            // Special case: Show if it's manualbill AND NOT delivery_note
                                            if (($_REQUEST['type'] ?? '') === 'manualbill' && ($order['type'] ?? '') !== 'delivery_note'):
                                                ?>
                                                <td class="text-center border"><?= formatAmountWithoutKD($item['final_rate']) ?></td>
                                                <td class="text-center border">
                                                    <?= formatAmountWithoutKD($item['final_rate'] * $item['quantity']) ?>
                                                </td>
                                            <?php endif; ?>
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
                                        <tr class="w-100">
                                            <td class="text-center border"><?= $c ?></td>
                                            <td class="text-left border pl-3">
                                                <?php if (!empty($cat['categories_name']) && strtolower($cat['categories_name']) !== 'no category'): ?>
                                                    <?= strtoupper($cat['categories_name']) ?> |
                                                <?php endif; ?>
                                                <?= strtoupper($r['product_name']) ?>
                                                <?php if (!empty($brand['brand_name']) && strtolower($brand['brand_name']) !== 'china'): ?>
                                                    | <?= strtoupper($brand['brand_name']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center border"><?= $r['quantity'] ?></td>
                                            <?php if (@$_REQUEST['type'] != 'gatepass' && (!isset($order['is_delivery_note']) || $order['is_delivery_note'] != 1)): ?>
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
                                    <td colspan="3" class="text-left"><strong>Note:</strong> <span><?= $comment ?></span>
                                    </td>

                                    <?php $shouldShow = true; // Default to showing content
                                    
                                        // Hide if it's a gatepass
                                        if (($_REQUEST['type'] ?? '') === 'gatepass') {
                                            $shouldShow = false;
                                        }

                                        // Hide if it's a delivery note (either is_delivery_note=1 or type=delivery_note)
                                        if (($order['is_delivery_note'] ?? 0) == 1 || ($order['type'] ?? '') === 'delivery_note') {
                                            $shouldShow = false;
                                        }

                                        // Special case: Show if it's manualbill AND NOT delivery_note
                                        if (($_REQUEST['type'] ?? '') === 'manualbill' && ($order['type'] ?? '') !== 'delivery_note') {
                                            $shouldShow = true;
                                        }

                                        if ($shouldShow): ?>
                                        <?php if (!empty($order['discount']) && $order['discount'] > 0): ?>
                                            <td class="border">Total Amount:</td>
                                            <td class="border"><?= formatAmountWithKD($order['total_amount']) ?></td>
                                        </tr>
                                        <tr class="tablefooter" style="font-size: 14px;">
                                            <td colspan="3"></td>
                                            <td class="border">Discount:</td>
                                            <td class="border"><?= formatAmountWithKD($order['discount']) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php $shouldShow = true; // Default to showing content
                                
                                    // Hide if it's a gatepass
                                    if (($_REQUEST['type'] ?? '') === 'gatepass') {
                                        $shouldShow = false;
                                    }

                                    // Hide if it's a delivery note (either is_delivery_note=1 or type=delivery_note)
                                    if (($order['is_delivery_note'] ?? 0) == 1 || ($order['type'] ?? '') === 'delivery_note') {
                                        $shouldShow = false;
                                    }

                                    // Special case: Show if it's manualbill AND NOT delivery_note
                                    if (($_REQUEST['type'] ?? '') === 'manualbill' && ($order['type'] ?? '') !== 'delivery_note') {
                                        $shouldShow = true;
                                    }

                                    if ($shouldShow): ?>
                                    <tr class="tablefooter" style="font-size: 14px; border: none !important;">
                                        <td colspan="3" class="text-left border-none">
                                            <?= amountToWordsKD($order['grand_total']) ?>
                                            ONLY
                                        </td>
                                        <td class="text-sm border">Net Amount:</td>
                                        <td class="border"><?= formatAmountWithKD($order['grand_total']); ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (
                                    $_REQUEST['type'] !== 'lpo' &&
                                    $_REQUEST['type'] !== 'quotation' &&
                                    $_REQUEST['type'] !== 'gatepass' &&
                                    ($_REQUEST['type'] !== 'manualbill' || $order['type'] === 'Sale_Invoice')
                                ): ?>
                                    <?php if ($order['grand_total'] !== ""): ?>
                                        <tr class="tablefooter" style="font-size: 14px;">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-sm border">Paid:</td>
                                            <?php if ($_REQUEST['type'] == "manualbill"): ?>
                                                <td class="border"><?= formatAmountWithoutKD(@$order['grand_total']) ?></td>
                                            <?php else: ?>
                                                <td class="border"><?= formatAmountWithoutKD(@$order['paid']) ?></td>
                                            <?php endif; ?>
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
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tfoot>


                        </table>
                    </div>
                </div>
                <?php
                if (($_REQUEST['type'] == "quotation" && $order['is_delivery_note'] != 1) || ($_REQUEST['type'] == "manualbill" && $order['type'] == 'quotation')) { ?>
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
                            <div class="col-9 w-100 ">
                                <div class="row w-100 text-right ml-auto mr-3">
                                    <div class="col-12 text-right d-flex justify-content-end">
                                        <p><strong>Prepared By:</strong> </p>
                                        <p class="text-capitalize pr-">
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
                <?php } else {
                    if (($_REQUEST['type'] ?? '') !== 'gatepass') { ?>
                        <div class="row mt-5 pt-5 m-0 pl-5">
                            <div class="col-12 d-flex justify-content-end align-items-center">
                                <p class="mb-0 mr-1"><strong>Prepared By:</strong></p>
                                <div style="white-space: nowrap;">
                                    <?php
                                    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
                                    $user = fetchRecord($dbc, "users", "user_id", $userId);
                                    $fullName = !empty($user['fullname']) ? strtoupper($user['fullname']) : "_________________";
                                    ?>
                                    <span><?= $fullName ?></span>
                                </div>
                            </div>
                        </div>

                    <?php }
                } ?>


            </div><!-- end of container -->

        </div>
        <div class="pdf_footer">
            <div class="pdf-only-header ">
                <div class="return">
                    <p>
                        Goods can be returned within 14 days original packaging & Invoice
                    </p>
                    <p>يمكن إرجاع البضائع فصول ١٤ يوما مع العبوة الأصلية والفاتورة</p>
                </div>
                <div class="footer">
                    <div>
                        <img class="qr" src="img/logo/frame.svg" alt="" />
                    </div>
                    <div class="centerdiv text-center">
                        <p>
                            "Please issue The cheque In the name of "Season Four Electronic &
                            Repairing"
                        </p>
                        <p>
                            "يرجى إصدار الشيك باسم "مؤسسة فصول الاربعة للأجهزة الكهربائية والالكترونية وتصليحها"</p>
                        <div class="center">
                            <div><span>Receiver's Sign </span><span>توقيع المستلم</span></div>
                            <div><span>Salesman's Sign </span><span>توقيع البائع</span></div>
                        </div>
                    </div>
                    <div><img class="qr" src="img/logo/frame-2.svg" alt="" /></div>
                </div>
            </div>
        </div>
    <?php endfor; ?>


</body>

</html>
<script type="text/javascript">
    // Check if the URL contains ?pdf=true
    const urlParams = new URLSearchParams(window.location.search);
    const isPdf = urlParams.get('pdf') === 'true';
    console.log('PDF mode:', isPdf); // Debug: Check if pdf=true is detected

    // Function to handle PDF generation
    function generatePdf() {
        const headers = document.querySelectorAll('.pdf-only-header');
        if (headers.length > 0) {
            headers.forEach(header => {
                header.style.display = 'block';
            });
            console.log('Headers made visible for PDF'); // Debug: Confirm header visibility
            // Delay to ensure DOM update is rendered
            setTimeout(() => {
                window.print();
                // Hide headers after printing to reset state
                setTimeout(() => {
                    headers.forEach(header => {
                        header.style.display = 'none';
                    });
                }, 100);
            }, 1000); // Increased delay to 1000ms for reliability
        } else {
            console.error('Header elements not found');
        }
    }

    // Automatic PDF generation if ?pdf=true
    if (isPdf) {
        generatePdf();
    }

    // Show manual button and handle click for fallback
    const saveAsPdfBtn = document.getElementById('saveAsPdfBtn');
    if (saveAsPdfBtn) {
        saveAsPdfBtn.addEventListener('click', () => {
            // Append ?pdf=true to URL and trigger PDF generation
            window.history.pushState({}, document.title, window.location.pathname + '?pdf=true');
            generatePdf();
        });
    }
</script>