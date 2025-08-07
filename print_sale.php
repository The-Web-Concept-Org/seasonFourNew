<!DOCTYPE html>
<html>
<?php include_once 'includes/head.php'; ?>
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
        /* font-family: 'Phoenix Sans', sans-serif; */
        color: black
    }

    td {
        font-size: 16px;
    }

    .invoice-container {
        width: 100%;
        min-height: 297mm;
        /* Adjusted for top and bottom margins */
        background: white;
        padding: 10mm;
        /* 20mm top/bottom padding for margins */
        box-shadow: none;
        position: relative;
        /* Relative positioning for absolute footer */
        page-break-after: always;
    }

    .invoice-container:last-child {
        page-break-after: auto;
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
        font-size: 50px;
        font-weight: bold;
        color: #006400;
        margin: 0;
        font-family: 'Phoenix Sans', sans-serif !important;
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
        margin-top: 20px;
        width: 180px;
        height: 160px;
        object-fit: contain;
    }

    .rtl {
        text-align: right;
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

    .pdf-only-header {
        display: none;
        /* Hidden by default for screen and simple print */
    }

    .pdf_footer {
        display: none;
        position: absolute;
        bottom: 5mm;
        left: 10mm;
        right: 10mm;
        width: calc(100% - 20mm);
        line-height: 0.5;
    }

    .pdf-only-header.pdf-visible {
        display: block;
    }

    .pdf_footer.pdf-visible {
        position: fixed;
        bottom: 40mm;
        left: 10mm;
        right: 10mm;
        display: block;
        height: 100px;
        z-index: 1000;

    }

    @media print {
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', 'Arial', sans-serif;
        }

        .company-name {
            font-size: 40px;
            font-weight: bold;
            color: #006400;
            margin: 0;
            font-family: 'Phoenix Sans', sans-serif !important;
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
            /* Hidden for simple print */
        }

        .pdf_footer {
            display: none;
            /* Hidden for simple print */
        }



        #saveAsPdfBtn,
        #printBtn {
            display: none !important;
        }

        .tablefooter.last-page-only {
            display: none;
        }

        .tablefooter.last-page {
            display: table-row;
        }
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

    .content {
        position: relative;
        width: 100%;
        padding-bottom: 15mm;
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
        color: black;
    }

    .cheque_instr {
        font-size: 16px;
        color: black;
        border-top: 3px solid black;
        padding-top: 10px;
    }


    p {
        font-size: 16px;
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

    .footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        width: 100%;
    }

    .footer-item {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
        min-height: 100%;
    }

    .text-center {
        text-align: center;
    }

    .qr {
        max-width: 100px;
        height: auto;
    }

    .footer-item span,
    .footer-item p {
        margin: 5px 0 0 0;
        font-size: 14px;
        color: black;
        font-weight: bold;

    }

    .qr_text {
        color: #1a5f3a !important;
    }
</style>

<body>
    <?php
    // Initialize variables
    $totalQTY = 0;
    $totalAm = 0;

    // Determine invoice details based on type
    if ($_REQUEST['type'] == "purchase") {
        $nameSHow = 'Supplier Name';
        $id_name = "Purchase Id";
        $order = fetchRecord($dbc, "purchase", "purchase_id", $_REQUEST['id']);
        $unique_id = $order['payment_type'] == "credit_purchase" ? 'SF-CRP-' . $order['purchase_id'] : 'SF25-CP-' . $order['purchase_id'];
        $comment = $order['purchase_narration'];
        $table_row = "390px";
        $getDate = $order['purchase_date'];
        $invoice_name = $order['payment_type'] == "credit_purchase" ? "credit invoice" : "purchase invoice";
        $order_item = mysqli_query($dbc, "SELECT purchase_item.*,product.* FROM purchase_item INNER JOIN product ON purchase_item.product_id=product.product_id WHERE purchase_item.purchase_id='" . $_REQUEST['id'] . "'");
    } elseif ($_REQUEST['type'] == "gatepass") {
        $nameSHow = 'Customer Name';
        $invoice_name = "Gatepass";
        $id_name = "Gatepass Id";
        $order = fetchRecord($dbc, "gatepass", "gatepass_id", $_REQUEST['id']);
        $unique_id = 'SF25-G-' . $order['gatepass_id'];
        $getDate = $order['gatepass_date'];
        $comment = $order['gatepass_narration'];
        $order_item = mysqli_query($dbc, "SELECT gatepass_item.*,product.* FROM gatepass_item INNER JOIN product ON gatepass_item.product_id=product.product_id WHERE gatepass_item.gatepass_id='" . $_REQUEST['id'] . "'");
        $table_row = $order['payment_type'] == "gatepass" ? "300px" : "350px";
        $order_type = $order['payment_type'] == "none" ? "Gatepass" : " (Gatepass)";
    } elseif ($_REQUEST['type'] == "order") {
        $nameSHow = 'Customer Name';
        $id_name = "Sale Id";
        $order = fetchRecord($dbc, "orders", "order_id", $_REQUEST['id']);
        $unique_id = 'SF25-S-' . $order['order_id'];
        $invoice_name = $order['payment_type'] == "credit" ? "Credit Invoice" : "Sale Invoice";
        $getDate = $order['order_date'];
        $comment = $order['order_narration'];
        $order_item = mysqli_query($dbc, "SELECT order_item.*,product.* FROM order_item INNER JOIN product ON order_item.product_id=product.product_id WHERE order_item.order_id='" . $_REQUEST['id'] . "'");
        $table_row = $order['payment_type'] == "credit" ? "300px" : "350px";
        $order_type = $order['payment_type'] == "none" ? "credit sale" : ($order['payment_type'] == "credit" ? $order['credit_sale_type'] . " (Credit)" : "cash sale");
    } elseif ($_REQUEST['type'] == "quotation") {
        $nameSHow = 'Customer Name';
        $order = fetchRecord($dbc, "quotations", "quotation_id", $_REQUEST['id']);
        if ($order['is_delivery_note'] == 1) {

            $invoice_name = $order['payment_status'] == 1 ? "Sale Invoice" : "Delivery Note";
            $id_name = $order['payment_status'] == 1 ? "Sale Invoice Id" : "Delivery Note Id";
            $unique_id = 'SF25-DN-' . $order['quotation_id'];
        } else {
            $invoice_name = "Quotation";
            $id_name = "Quotation Id";
            $unique_id = 'SF25-Q-' . $order['quotation_id'];
        }
        $getDate = $order['quotation_date'];
        $comment = $order['quotation_narration'];
        $order_item = mysqli_query($dbc, "SELECT quotation_item.*,product.* FROM quotation_item INNER JOIN product ON quotation_item.product_id=product.product_id WHERE quotation_item.quotation_id='" . $_REQUEST['id'] . "'");
        $table_row = $order['payment_type'] == "quotation" ? "300px" : "350px";
        $order_type = $order['payment_type'] == "none" ? "Quotation" : " (Quotation)";
    } elseif ($_REQUEST['type'] == "lpo") {
        $nameSHow = 'Supplier Name';
        $invoice_name = "LPO";
        $id_name = "LPO Id";
        $order = fetchRecord($dbc, "lpo", "lpo_id", $_REQUEST['id']);
        $unique_id = 'SF25-LPO-' . $order['lpo_id'];
        $getDate = $order['lpo_date'];
        $comment = $order['lpo_narration'];
        $order_item = mysqli_query($dbc, "SELECT lpo_item.*,product.* FROM lpo_item INNER JOIN product ON lpo_item.product_id=product.product_id WHERE lpo_item.lpo_id='" . $_REQUEST['id'] . "'");
        $table_row = $order['payment_type'] == "lpo" ? "300px" : "350px";
        $order_type = $order['payment_type'] == "none" ? "LPO" : " (LPO)";
    } elseif ($_REQUEST['type'] == "purchase_return") {
        $nameSHow = 'Supplier Name';
        $id_name = "Purchase Id";
        $order = fetchRecord($dbc, "purchase_return", "purchase_id", $_REQUEST['id']);
        $unique_id = 'SF25-PR-' . $order['purchase_id'];
        $comment = $order['purchase_narration'];
        $table_row = "390px";
        $getDate = $order['purchase_date'];
        $invoice_name = $order['payment_type'] == "credit_purchase" ? "credit return invoice" : "purchase return invoice";
        $order_item = mysqli_query($dbc, "SELECT purchase_return_item.*,product.* FROM purchase_return_item INNER JOIN product ON purchase_return_item.product_id=product.product_id WHERE purchase_return_item.purchase_id='" . $_REQUEST['id'] . "'");
    } elseif ($_REQUEST['type'] == "order_return") {
        $nameSHow = 'Customer Name';
        $id_name = "Sale Id";
        $order = fetchRecord($dbc, "orders_return", "order_id", $_REQUEST['id']);
        $unique_id = 'SF25R-S-' . $order['order_id'];
        $invoice_name = $order['payment_type'] == "credit" ? "Credit Return Invoice" : "Sale Return Invoice";
        $getDate = $order['order_date'];
        $comment = $order['order_narration'];
        $order_item = mysqli_query($dbc, "SELECT order_return_item.*,product.* FROM order_return_item INNER JOIN product ON order_return_item.product_id=product.product_id WHERE order_return_item.order_id='" . $_REQUEST['id'] . "'");
        $table_row = $order['payment_type'] == "credit" ? "300px" : "350px";
        $order_type = $order['payment_type'] == "none" ? "credit sale" : ($order['payment_type'] == "credit" ? $order['credit_sale_type'] . " (Credit)" : "cash sale");
    } elseif ($_REQUEST['type'] == "manualbill") {
        $id_name = "Id";
        $order = fetchRecord($dbc, "manual_bill", "order_id", $_REQUEST['id']);
        $unique_id = 'SF25-Id-' . $order['order_id'];
        $invoice_name = str_replace('_', ' ', $order['type']);
        $getDate = $order['timestamp'];
        $comment = $order['order_narration'];
        $nameSHow = $order['type'] == "lpo" ? "Supplier Name" : "Customer Name";
    }

    $date = date('d-M-Y h:i A', strtotime(@$order['timestamp'] . " +7 hours"));

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


    // Collect items for pagination
    $items = [];
    if (!empty($order['product_details'])) {
        $json_items = json_decode($order['product_details'], true);
        $items = $json_items;
    } elseif (!empty($order_item) && gettype($order_item) === 'object') {
        while ($r = mysqli_fetch_assoc($order_item)) {
            $items[] = $r;
        }
    }

    // Paginate items
    $totalItems = count($items);

    // Determine initial pagination logic
    $recordsPerPage = ($totalItems > 15) ? 15 : 10; // 15 items per page if > 15, otherwise 10
    $totalPages = ceil($totalItems / $recordsPerPage); // Calculate total pages
    
    // Create chunks
    $itemChunks = [];
    $start = 0;
    for ($i = 0; $i < $totalPages; $i++) {
        $end = min($start + $recordsPerPage, $totalItems);
        $chunkSize = $end - $start;

        // Check if this is the last page and it has exactly 15 records
        if ($i == $totalPages - 1 && $chunkSize == 15 && $totalItems > 15) {
            // Split the last 15 records into two pages (up to 10 each)
            $itemChunks[] = array_slice($items, $start, 10); // First part of the split (10 items)
            $itemChunks[] = array_slice($items, $start + 10, 5); // Second part (remaining 5 items)
            $start += 15; // Move past the 15 items
            $totalPages++; // Increment total pages due to the split
        } else {
            // Regular chunking with 15 or 10 items, last page limited to remaining or 10
            $itemChunks[] = array_slice($items, $start, min($recordsPerPage, $totalItems - $start));
            $start = $end;
        }
    }
    $totalPages = count($itemChunks); // Recalculate total pages based on final chunks
    ?>

    <?php for ($i = 0; $i < 1; $i++):
        if ($i > 0) {
            $margin = "margin-top:-270px !important";
            $copy = "Company Copy";
        } else {
            $margin = "";
            $copy = "Customer Copy";
        }
        ?>
        <?php foreach ($itemChunks as $pageIndex => $pageItems): ?>
            <div class="invoice-container" style="<?= $margin ?>">
                <div class="pdf-only-header">
                    <div class="company-header">
                        <div>
                            <h2 class="company-name">SEASON FOUR</h2>
                            <p class="company-sub" style="text-transform: uppercase;">A/C & Refrigeration Contracting Est</p>
                            <p class="contact-info"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M128 252.6C128 148.4 214 64 320 64C426 64 512 148.4 512 252.6C512 371.9 391.8 514.9 341.6 569.4C329.8 582.2 310.1 582.2 298.3 569.4C248.1 514.9 127.9 371.9 127.9 252.6zM320 320C355.3 320 384 291.3 384 256C384 220.7 355.3 192 320 192C284.7 192 256 220.7 256 256C256 291.3 284.7 320 320 320z" />
                                </svg>
                                Farwaniyah Branch-Block 4, St 45 - Tel: 24734306 <svg xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 640 640" style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M476.9 161.1C435 119.1 379.2 96 319.9 96C197.5 96 97.9 195.6 97.9 318C97.9 357.1 108.1 395.3 127.5 429L96 544L213.7 513.1C246.1 530.8 282.6 540.1 319.8 540.1L319.9 540.1C442.2 540.1 544 440.5 544 318.1C544 258.8 518.8 203.1 476.9 161.1zM319.9 502.7C286.7 502.7 254.2 493.8 225.9 477L219.2 473L149.4 491.3L168 423.2L163.6 416.2C145.1 386.8 135.4 352.9 135.4 318C135.4 216.3 218.2 133.5 320 133.5C369.3 133.5 415.6 152.7 450.4 187.6C485.2 222.5 506.6 268.8 506.5 318.1C506.5 419.9 421.6 502.7 319.9 502.7zM421.1 364.5C415.6 361.7 388.3 348.3 383.2 346.5C378.1 344.6 374.4 343.7 370.7 349.3C367 354.9 356.4 367.3 353.1 371.1C349.9 374.8 346.6 375.3 341.1 372.5C308.5 356.2 287.1 343.4 265.6 306.5C259.9 296.7 271.3 297.4 281.9 276.2C283.7 272.5 282.8 269.3 281.4 266.5C280 263.7 268.9 236.4 264.3 225.3C259.8 214.5 255.2 216 251.8 215.8C248.6 215.6 244.9 215.6 241.2 215.6C237.5 215.6 231.5 217 226.4 222.5C221.3 228.1 207 241.5 207 268.8C207 296.1 226.9 322.5 229.6 326.2C232.4 329.9 268.7 385.9 324.4 410C359.6 425.2 373.4 426.5 391 423.9C401.7 422.3 423.8 410.5 428.4 397.5C433 384.5 433 373.4 431.6 371.1C430.3 368.6 426.6 367.2 421.1 364.5z" />
                                </svg>
                                66944871</p>
                            <p class="contact-info">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M128 252.6C128 148.4 214 64 320 64C426 64 512 148.4 512 252.6C512 371.9 391.8 514.9 341.6 569.4C329.8 582.2 310.1 582.2 298.3 569.4C248.1 514.9 127.9 371.9 127.9 252.6zM320 320C355.3 320 384 291.3 384 256C384 220.7 355.3 192 320 192C284.7 192 256 220.7 256 256C256 291.3 284.7 320 320 320z" />
                                </svg>
                                Shuwaikh Branch-Block 3,Beside City Star - Tel:66990815
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M476.9 161.1C435 119.1 379.2 96 319.9 96C197.5 96 97.9 195.6 97.9 318C97.9 357.1 108.1 395.3 127.5 429L96 544L213.7 513.1C246.1 530.8 282.6 540.1 319.8 540.1L319.9 540.1C442.2 540.1 544 440.5 544 318.1C544 258.8 518.8 203.1 476.9 161.1zM319.9 502.7C286.7 502.7 254.2 493.8 225.9 477L219.2 473L149.4 491.3L168 423.2L163.6 416.2C145.1 386.8 135.4 352.9 135.4 318C135.4 216.3 218.2 133.5 320 133.5C369.3 133.5 415.6 152.7 450.4 187.6C485.2 222.5 506.6 268.8 506.5 318.1C506.5 419.9 421.6 502.7 319.9 502.7zM421.1 364.5C415.6 361.7 388.3 348.3 383.2 346.5C378.1 344.6 374.4 343.7 370.7 349.3C367 354.9 356.4 367.3 353.1 371.1C349.9 374.8 346.6 375.3 341.1 372.5C308.5 356.2 287.1 343.4 265.6 306.5C259.9 296.7 271.3 297.4 281.9 276.2C283.7 272.5 282.8 269.3 281.4 266.5C280 263.7 268.9 236.4 264.3 225.3C259.8 214.5 255.2 216 251.8 215.8C248.6 215.6 244.9 215.6 241.2 215.6C237.5 215.6 231.5 217 226.4 222.5C221.3 228.1 207 241.5 207 268.8C207 296.1 226.9 322.5 229.6 326.2C232.4 329.9 268.7 385.9 324.4 410C359.6 425.2 373.4 426.5 391 423.9C401.7 422.3 423.8 410.5 428.4 397.5C433 384.5 433 373.4 431.6 371.1C430.3 368.6 426.6 367.2 421.1 364.5z" />
                                </svg>
                                65692904
                            </p>
                            <p class="contact-info">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M128 252.6C128 148.4 214 64 320 64C426 64 512 148.4 512 252.6C512 371.9 391.8 514.9 341.6 569.4C329.8 582.2 310.1 582.2 298.3 569.4C248.1 514.9 127.9 371.9 127.9 252.6zM320 320C355.3 320 384 291.3 384 256C384 220.7 355.3 192 320 192C284.7 192 256 220.7 256 256C256 291.3 284.7 320 320 320z" />
                                </svg>
                                Shuwaikh Branch-Block 2,Zeena St. 18 - Tel:99408640
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M476.9 161.1C435 119.1 379.2 96 319.9 96C197.5 96 97.9 195.6 97.9 318C97.9 357.1 108.1 395.3 127.5 429L96 544L213.7 513.1C246.1 530.8 282.6 540.1 319.8 540.1L319.9 540.1C442.2 540.1 544 440.5 544 318.1C544 258.8 518.8 203.1 476.9 161.1zM319.9 502.7C286.7 502.7 254.2 493.8 225.9 477L219.2 473L149.4 491.3L168 423.2L163.6 416.2C145.1 386.8 135.4 352.9 135.4 318C135.4 216.3 218.2 133.5 320 133.5C369.3 133.5 415.6 152.7 450.4 187.6C485.2 222.5 506.6 268.8 506.5 318.1C506.5 419.9 421.6 502.7 319.9 502.7zM421.1 364.5C415.6 361.7 388.3 348.3 383.2 346.5C378.1 344.6 374.4 343.7 370.7 349.3C367 354.9 356.4 367.3 353.1 371.1C349.9 374.8 346.6 375.3 341.1 372.5C308.5 356.2 287.1 343.4 265.6 306.5C259.9 296.7 271.3 297.4 281.9 276.2C283.7 272.5 282.8 269.3 281.4 266.5C280 263.7 268.9 236.4 264.3 225.3C259.8 214.5 255.2 216 251.8 215.8C248.6 215.6 244.9 215.6 241.2 215.6C237.5 215.6 231.5 217 226.4 222.5C221.3 228.1 207 241.5 207 268.8C207 296.1 226.9 322.5 229.6 326.2C232.4 329.9 268.7 385.9 324.4 410C359.6 425.2 373.4 426.5 391 423.9C401.7 422.3 423.8 410.5 428.4 397.5C433 384.5 433 373.4 431.6 371.1C430.3 368.6 426.6 367.2 421.1 364.5z" />
                                </svg>
                                66945212
                            </p>
                            <p class="contact-info"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M415.9 344L225 344C227.9 408.5 242.2 467.9 262.5 511.4C273.9 535.9 286.2 553.2 297.6 563.8C308.8 574.3 316.5 576 320.5 576C324.5 576 332.2 574.3 343.4 563.8C354.8 553.2 367.1 535.8 378.5 511.4C398.8 467.9 413.1 408.5 416 344zM224.9 296L415.8 296C413 231.5 398.7 172.1 378.4 128.6C367 104.2 354.7 86.8 343.3 76.2C332.1 65.7 324.4 64 320.4 64C316.4 64 308.7 65.7 297.5 76.2C286.1 86.8 273.8 104.2 262.4 128.6C242.1 172.1 227.8 231.5 224.9 296zM176.9 296C180.4 210.4 202.5 130.9 234.8 78.7C142.7 111.3 74.9 195.2 65.5 296L176.9 296zM65.5 344C74.9 444.8 142.7 528.7 234.8 561.3C202.5 509.1 180.4 429.6 176.9 344L65.5 344zM463.9 344C460.4 429.6 438.3 509.1 406 561.3C498.1 528.6 565.9 444.8 575.3 344L463.9 344zM575.3 296C565.9 195.2 498.1 111.3 406 78.7C438.3 130.9 460.4 210.4 463.9 296L575.3 296z" />
                                </svg>
                                WWW.seasonfour.co</p>
                            <p class="contact-info"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M112 128C85.5 128 64 149.5 64 176C64 191.1 71.1 205.3 83.2 214.4L291.2 370.4C308.3 383.2 331.7 383.2 348.8 370.4L556.8 214.4C568.9 205.3 576 191.1 576 176C576 149.5 554.5 128 528 128L112 128zM64 260L64 448C64 483.3 92.7 512 128 512L512 512C547.3 512 576 483.3 576 448L576 260L377.6 408.8C343.5 434.4 296.5 434.4 262.4 408.8L64 260z" />
                                </svg>
                                season4-kw@hotmail.com</p>
                            <p class="contact-info"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M320.3 205C256.8 204.8 205.2 256.2 205 319.7C204.8 383.2 256.2 434.8 319.7 435C383.2 435.2 434.8 383.8 435 320.3C435.2 256.8 383.8 205.2 320.3 205zM319.7 245.4C360.9 245.2 394.4 278.5 394.6 319.7C394.8 360.9 361.5 394.4 320.3 394.6C279.1 394.8 245.6 361.5 245.4 320.3C245.2 279.1 278.5 245.6 319.7 245.4zM413.1 200.3C413.1 185.5 425.1 173.5 439.9 173.5C454.7 173.5 466.7 185.5 466.7 200.3C466.7 215.1 454.7 227.1 439.9 227.1C425.1 227.1 413.1 215.1 413.1 200.3zM542.8 227.5C541.1 191.6 532.9 159.8 506.6 133.6C480.4 107.4 448.6 99.2 412.7 97.4C375.7 95.3 264.8 95.3 227.8 97.4C192 99.1 160.2 107.3 133.9 133.5C107.6 159.7 99.5 191.5 97.7 227.4C95.6 264.4 95.6 375.3 97.7 412.3C99.4 448.2 107.6 480 133.9 506.2C160.2 532.4 191.9 540.6 227.8 542.4C264.8 544.5 375.7 544.5 412.7 542.4C448.6 540.7 480.4 532.5 506.6 506.2C532.8 480 541 448.2 542.8 412.3C544.9 375.3 544.9 264.5 542.8 227.5zM495 452C487.2 471.6 472.1 486.7 452.4 494.6C422.9 506.3 352.9 503.6 320.3 503.6C287.7 503.6 217.6 506.2 188.2 494.6C168.6 486.8 153.5 471.7 145.6 452C133.9 422.5 136.6 352.5 136.6 319.9C136.6 287.3 134 217.2 145.6 187.8C153.4 168.2 168.5 153.1 188.2 145.2C217.7 133.5 287.7 136.2 320.3 136.2C352.9 136.2 423 133.6 452.4 145.2C472 153 487.1 168.1 495 187.8C506.7 217.3 504 287.3 504 319.9C504 352.5 506.7 422.6 495 452z" />
                                </svg>
                                @seasonfourkwt</p>
                        </div>
                        <div><img class="logo" src="img/logo/<?= $get_company['logo'] ?>" alt="Logo"></div>
                        <div class="rtl">
                            <h2 class="company-name">مؤسسة الفصول الأربعة</h2>
                            <p class="company-sub">للأجهزه التكييف واللتبريد ومقاولاتها</p>
                            <p class="contact-info">المعرض الفروانية - قطعة ٤ - شارع ٤٥ - تلفون: ٢٤٧٣٤٣٠٦
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M476.9 161.1C435 119.1 379.2 96 319.9 96C197.5 96 97.9 195.6 97.9 318C97.9 357.1 108.1 395.3 127.5 429L96 544L213.7 513.1C246.1 530.8 282.6 540.1 319.8 540.1L319.9 540.1C442.2 540.1 544 440.5 544 318.1C544 258.8 518.8 203.1 476.9 161.1zM319.9 502.7C286.7 502.7 254.2 493.8 225.9 477L219.2 473L149.4 491.3L168 423.2L163.6 416.2C145.1 386.8 135.4 352.9 135.4 318C135.4 216.3 218.2 133.5 320 133.5C369.3 133.5 415.6 152.7 450.4 187.6C485.2 222.5 506.6 268.8 506.5 318.1C506.5 419.9 421.6 502.7 319.9 502.7zM421.1 364.5C415.6 361.7 388.3 348.3 383.2 346.5C378.1 344.6 374.4 343.7 370.7 349.3C367 354.9 356.4 367.3 353.1 371.1C349.9 374.8 346.6 375.3 341.1 372.5C308.5 356.2 287.1 343.4 265.6 306.5C259.9 296.7 271.3 297.4 281.9 276.2C283.7 272.5 282.8 269.3 281.4 266.5C280 263.7 268.9 236.4 264.3 225.3C259.8 214.5 255.2 216 251.8 215.8C248.6 215.6 244.9 215.6 241.2 215.6C237.5 215.6 231.5 217 226.4 222.5C221.3 228.1 207 241.5 207 268.8C207 296.1 226.9 322.5 229.6 326.2C232.4 329.9 268.7 385.9 324.4 410C359.6 425.2 373.4 426.5 391 423.9C401.7 422.3 423.8 410.5 428.4 397.5C433 384.5 433 373.4 431.6 371.1C430.3 368.6 426.6 367.2 421.1 364.5z" />
                                </svg>٦٦٩٤٤٨٧١
                            </p>

                            <p class="contact-info">المعرض الشويخ - قطعة ٣ - قرب سيتى ستار - تلفون: ٦٦٩٩٠٨١٥ <svg
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M476.9 161.1C435 119.1 379.2 96 319.9 96C197.5 96 97.9 195.6 97.9 318C97.9 357.1 108.1 395.3 127.5 429L96 544L213.7 513.1C246.1 530.8 282.6 540.1 319.8 540.1L319.9 540.1C442.2 540.1 544 440.5 544 318.1C544 258.8 518.8 203.1 476.9 161.1zM319.9 502.7C286.7 502.7 254.2 493.8 225.9 477L219.2 473L149.4 491.3L168 423.2L163.6 416.2C145.1 386.8 135.4 352.9 135.4 318C135.4 216.3 218.2 133.5 320 133.5C369.3 133.5 415.6 152.7 450.4 187.6C485.2 222.5 506.6 268.8 506.5 318.1C506.5 419.9 421.6 502.7 319.9 502.7zM421.1 364.5C415.6 361.7 388.3 348.3 383.2 346.5C378.1 344.6 374.4 343.7 370.7 349.3C367 354.9 356.4 367.3 353.1 371.1C349.9 374.8 346.6 375.3 341.1 372.5C308.5 356.2 287.1 343.4 265.6 306.5C259.9 296.7 271.3 297.4 281.9 276.2C283.7 272.5 282.8 269.3 281.4 266.5C280 263.7 268.9 236.4 264.3 225.3C259.8 214.5 255.2 216 251.8 215.8C248.6 215.6 244.9 215.6 241.2 215.6C237.5 215.6 231.5 217 226.4 222.5C221.3 228.1 207 241.5 207 268.8C207 296.1 226.9 322.5 229.6 326.2C232.4 329.9 268.7 385.9 324.4 410C359.6 425.2 373.4 426.5 391 423.9C401.7 422.3 423.8 410.5 428.4 397.5C433 384.5 433 373.4 431.6 371.1C430.3 368.6 426.6 367.2 421.1 364.5z" />
                                </svg> ٦٥٦٩٢٩٠٤</p>
                            <p class="contact-info">المعرض الشويخ - قطعة ٢ - شارع الزينة ١٨ - تلفون: ٩٩٤٢٨٦٤٠ <svg
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M476.9 161.1C435 119.1 379.2 96 319.9 96C197.5 96 97.9 195.6 97.9 318C97.9 357.1 108.1 395.3 127.5 429L96 544L213.7 513.1C246.1 530.8 282.6 540.1 319.8 540.1L319.9 540.1C442.2 540.1 544 440.5 544 318.1C544 258.8 518.8 203.1 476.9 161.1zM319.9 502.7C286.7 502.7 254.2 493.8 225.9 477L219.2 473L149.4 491.3L168 423.2L163.6 416.2C145.1 386.8 135.4 352.9 135.4 318C135.4 216.3 218.2 133.5 320 133.5C369.3 133.5 415.6 152.7 450.4 187.6C485.2 222.5 506.6 268.8 506.5 318.1C506.5 419.9 421.6 502.7 319.9 502.7zM421.1 364.5C415.6 361.7 388.3 348.3 383.2 346.5C378.1 344.6 374.4 343.7 370.7 349.3C367 354.9 356.4 367.3 353.1 371.1C349.9 374.8 346.6 375.3 341.1 372.5C308.5 356.2 287.1 343.4 265.6 306.5C259.9 296.7 271.3 297.4 281.9 276.2C283.7 272.5 282.8 269.3 281.4 266.5C280 263.7 268.9 236.4 264.3 225.3C259.8 214.5 255.2 216 251.8 215.8C248.6 215.6 244.9 215.6 241.2 215.6C237.5 215.6 231.5 217 226.4 222.5C221.3 228.1 207 241.5 207 268.8C207 296.1 226.9 322.5 229.6 326.2C232.4 329.9 268.7 385.9 324.4 410C359.6 425.2 373.4 426.5 391 423.9C401.7 422.3 423.8 410.5 428.4 397.5C433 384.5 433 373.4 431.6 371.1C430.3 368.6 426.6 367.2 421.1 364.5z" />
                                </svg> ٦٦٩٤٥٢١٢</p>
                            <p class="contact-info">
                                WWW.seasonfour.co <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M415.9 344L225 344C227.9 408.5 242.2 467.9 262.5 511.4C273.9 535.9 286.2 553.2 297.6 563.8C308.8 574.3 316.5 576 320.5 576C324.5 576 332.2 574.3 343.4 563.8C354.8 553.2 367.1 535.8 378.5 511.4C398.8 467.9 413.1 408.5 416 344zM224.9 296L415.8 296C413 231.5 398.7 172.1 378.4 128.6C367 104.2 354.7 86.8 343.3 76.2C332.1 65.7 324.4 64 320.4 64C316.4 64 308.7 65.7 297.5 76.2C286.1 86.8 273.8 104.2 262.4 128.6C242.1 172.1 227.8 231.5 224.9 296zM176.9 296C180.4 210.4 202.5 130.9 234.8 78.7C142.7 111.3 74.9 195.2 65.5 296L176.9 296zM65.5 344C74.9 444.8 142.7 528.7 234.8 561.3C202.5 509.1 180.4 429.6 176.9 344L65.5 344zM463.9 344C460.4 429.6 438.3 509.1 406 561.3C498.1 528.6 565.9 444.8 575.3 344L463.9 344zM575.3 296C565.9 195.2 498.1 111.3 406 78.7C438.3 130.9 460.4 210.4 463.9 296L575.3 296z" />
                                </svg></p>
                            <p class="contact-info">
                                season4-kw@hotmail.com <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M112 128C85.5 128 64 149.5 64 176C64 191.1 71.1 205.3 83.2 214.4L291.2 370.4C308.3 383.2 331.7 383.2 348.8 370.4L556.8 214.4C568.9 205.3 576 191.1 576 176C576 149.5 554.5 128 528 128L112 128zM64 260L64 448C64 483.3 92.7 512 128 512L512 512C547.3 512 576 483.3 576 448L576 260L377.6 408.8C343.5 434.4 296.5 434.4 262.4 408.8L64 260z" />
                                </svg></p>
                            <p class="contact-info">
                                @seasonfourkwt <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                    style="width: 15px; fill: #1a5f3a;">
                                    <path
                                        d="M320.3 205C256.8 204.8 205.2 256.2 205 319.7C204.8 383.2 256.2 434.8 319.7 435C383.2 435.2 434.8 383.8 435 320.3C435.2 256.8 383.8 205.2 320.3 205zM319.7 245.4C360.9 245.2 394.4 278.5 394.6 319.7C394.8 360.9 361.5 394.4 320.3 394.6C279.1 394.8 245.6 361.5 245.4 320.3C245.2 279.1 278.5 245.6 319.7 245.4zM413.1 200.3C413.1 185.5 425.1 173.5 439.9 173.5C454.7 173.5 466.7 185.5 466.7 200.3C466.7 215.1 454.7 227.1 439.9 227.1C425.1 227.1 413.1 215.1 413.1 200.3zM542.8 227.5C541.1 191.6 532.9 159.8 506.6 133.6C480.4 107.4 448.6 99.2 412.7 97.4C375.7 95.3 264.8 95.3 227.8 97.4C192 99.1 160.2 107.3 133.9 133.5C107.6 159.7 99.5 191.5 97.7 227.4C95.6 264.4 95.6 375.3 97.7 412.3C99.4 448.2 107.6 480 133.9 506.2C160.2 532.4 191.9 540.6 227.8 542.4C264.8 544.5 375.7 544.5 412.7 542.4C448.6 540.7 480.4 532.5 506.6 506.2C532.8 480 541 448.2 542.8 412.3C544.9 375.3 544.9 264.5 542.8 227.5zM495 452C487.2 471.6 472.1 486.7 452.4 494.6C422.9 506.3 352.9 503.6 320.3 503.6C287.7 503.6 217.6 506.2 188.2 494.6C168.6 486.8 153.5 471.7 145.6 452C133.9 422.5 136.6 352.5 136.6 319.9C136.6 287.3 134 217.2 145.6 187.8C153.4 168.2 168.5 153.1 188.2 145.2C217.7 133.5 287.7 136.2 320.3 136.2C352.9 136.2 423 133.6 452.4 145.2C472 153 487.1 168.1 495 187.8C506.7 217.3 504 287.3 504 319.9C504 352.5 506.7 422.6 495 452z" />
                                </svg></p>
                        </div>
                    </div>
                    <div class="label">
                        <p>ALL TYPES OF A/C, REFRIGERATOR, WASHING MACHINE SPARE PARTS</p>
                        <p>قطع غيار ، غسالات - ثلاجات - مكيفا وجميع انواع تبرید و تکییف</p>
                    </div>
                </div>
                <div class="invo">
                    <h2 class="text-uppercase"><?= $invoice_name ?>
                        <!-- (Page <?= $pageIndex + 1 ?> of <?= $totalPages ?>) -->
                    </h2>
                    <?php if ($pageIndex === 0): ?>
                        <div style="margin-top: 20px; text-align: end;">
                            <button id="saveAsPdfBtn">Save as PDF</button>
                            <button onclick="window.print();" id="printBtn">Print</button>
                        </div>
                    <?php endif; ?>
                    <div class="invoice-bg">
                        <div class="content">
                            <div class="invoice-details">
                                <div class="m-0 p-0">
                                    <p class="text-uppercase"><strong><?= $id_name ?> :</strong> <?= $unique_id ?></p>
                                </div>
                                <div class="m-0 p-0">
                                    <p><strong>DATE:</strong> <?= $date ?></p>
                                </div>
                            </div>
                            <div class="invoice-details">
                                <div class="m-0 p-0">
                                    <?php if ($_REQUEST['type'] == 'gatepass'): ?>
                                        <?php $from = fetchRecord($dbc, "branch", "branch_id", $order['from_branch']); ?>
                                        <p class="text-uppercase"><strong>From Branch:</strong> <?= @$from['branch_name'] ?></p>
                                    <?php else: ?>
                                        <p class="text-uppercase"><strong><?= $nameSHow ?>:</strong>
                                            <?= @$order['client_name'] ?: @$order['customer_name'] ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="m-0 p-0">
                                    <?php if ($_REQUEST['type'] == 'gatepass'): ?>
                                        <?php $to = fetchRecord($dbc, "branch", "branch_id", $order['to_branch']); ?>
                                        <p class="text-uppercase"><strong>To Branch:</strong> <?= @$to['branch_name'] ?></p>
                                    <?php else: ?>
                                        <?php $branch = fetchRecord($dbc, "branch", "branch_id", @$order['branch_id']); ?>
                                        <?php if (isset($branch['branch_name'])): ?>
                                            <p class="text-uppercase"><strong>Branch:</strong> <?= @$branch['branch_name'] ?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="heder" style="width: 5%;">S.No</th>
                                        <th style="width: 25%;" class="text-left pl-3 heder">Description</th>
                                        <th class="heder" style="width: 5%;">Qty</th>
                                        <?php
                                        $shouldShow = true;
                                        if (
                                            ($_REQUEST['type'] ?? '') === 'gatepass' ||
                                            (
                                                ($order['type'] ?? '') === 'delivery_note' ||
                                                ($order['is_delivery_note'] ?? 0) == 1
                                            ) &&
                                            ($order['payment_status'] ?? 0) == 0
                                        ) {
                                            $shouldShow = false;
                                        }
                                        if (($_REQUEST['type'] ?? '') === 'manualbill' && ($order['type'] ?? '') !== 'delivery_note') {
                                            $shouldShow = true;
                                        }
                                        if ($shouldShow): ?>
                                            <th class="heder" style="width: 5%;">Unit Price</th>
                                            <th class="heder" style="width: 5%;">Amount</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $c = $pageIndex * $recordsPerPage;
                                    $totalQTY = 0;
                                    $totalAm = 0;
                                    foreach ($pageItems as $item):
                                        $c++;
                                        if (!empty($order['product_details'])) {
                                            $product_name = strtoupper($item['product_name']);
                                            $quantity = $item['quantity'];
                                            $rate = $item['final_rate'] ?? 0;
                                        } else {
                                            $brand = fetchRecord($dbc, "brands", "brand_id", $item['brand_id']);
                                            $cat = fetchRecord($dbc, "categories", "categories_id", $item['category_id']);
                                            $product_name = strtoupper($item['product_name']);
                                            $quantity = $item['quantity'];
                                            $rate = $item['rate'];
                                        }
                                        $totalQTY += $quantity;
                                        $totalAm += $rate * $quantity;
                                        ?>
                                        <tr class="border">
                                            <td class="text-center border"><?= $c ?></td>
                                            <td class="text-left border pl-3">
                                                <?php if (!empty($order['product_details'])): ?>
                                                    <?= rtrim(preg_replace('/^no category\s*-\s*/i', '', str_ireplace('china', '', $product_name)), ' -') ?>
                                                <?php else: ?>
                                                    <?php if (!empty($cat['categories_name']) && strtolower($cat['categories_name']) !== 'no category'): ?>
                                                        <?= strtoupper($cat['categories_name']) ?> |
                                                    <?php endif; ?>
                                                    <?= $product_name ?>
                                                    <?php if (!empty($brand['brand_name']) && strtolower($brand['brand_name']) !== 'china'): ?>
                                                        | <?= strtoupper($brand['brand_name']) ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <!-- <td class="text-center border"><?= $quantity ?></td> -->
                                            <td class="text-center border">
                                                <?= ($quantity < 1) ? round($quantity * 15) . ' M' : $quantity ?>
                                            </td>
                                            <?php if ($shouldShow): ?>
                                                <td class="text-center border"><?= formatAmountWithoutKD($rate) ?></td>
                                                <td class="text-center border"><?= formatAmountWithoutKD($rate * $quantity) ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <?php if ($pageIndex === $totalPages - 1): ?>
                                    <tfoot>
                                        <tr class="tablefooter last-page" style="font-size: 14px;">
                                            <td colspan="3" class="text-left"><strong>Note:</strong> <span><?= $comment ?></span>
                                            </td>
                                            <?php if ($shouldShow): ?>
                                                <?php if (!empty($order['discount']) && $order['discount'] > 0): ?>
                                                    <td class="border">Total Amount:</td>
                                                    <td class="border"><?= formatAmountWithKD($order['total_amount']) ?></td>
                                                </tr>
                                                <tr class="tablefooter last-page" style="font-size: 14px;">
                                                    <td colspan="3"></td>
                                                    <td class="border">Discount:</td>
                                                    <td class="border"><?= formatAmountWithKD($order['discount']) ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr class="tablefooter last-page" style="font-size: 14px; border: none !important;">
                                                <td colspan="3" class="text-left border-none">
                                                    <?php
                                                    $amountWords = amountToWordsKD($order['grand_total']);
                                                    if (strpos($amountWords, 'KD') !== false) {
                                                        $parts = explode('KD', $amountWords, 2);
                                                        $beforeKD = trim($parts[0]) . ' KD';
                                                        $afterKD = trim($parts[1]);

                                                        if (!empty($afterKD)) {
                                                            echo $beforeKD . '<br>' . $afterKD . ' ONLY';
                                                        } else {
                                                            echo $beforeKD . ' ONLY';
                                                        }
                                                    } else {
                                                        echo $amountWords . ' ONLY';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-sm border">Net Amount:</td>
                                                <td class="border"><?= formatAmountWithKD($order['grand_total']) ?></td>
                                            </tr>
                                            <?php if (
                                                $_REQUEST['type'] !== 'lpo' &&
                                                ($_REQUEST['type'] !== 'quotation' || ($order['payment_status'] ?? 0) == 1) &&
                                                $_REQUEST['type'] !== 'gatepass' &&
                                                ($_REQUEST['type'] !== 'manualbill' || $order['type'] === 'Sale_Invoice')
                                            ): ?>
                                                <?php if ($order['grand_total'] !== ""): ?>
                                                    <tr class="tablefooter last-page" style="font-size: 14px;">
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-sm border">Paid:</td>
                                                        <td class="border">
                                                            <?= formatAmountWithoutKD($_REQUEST['type'] == "manualbill" ? @$order['grand_total'] : @$order['paid']) ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="tablefooter last-page" style="font-size: 14px;">
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-sm border">Remaining:</td>
                                                        <td class="border">
                                                            <?= formatAmountWithoutKD(!empty($order['product_details']) ? 0 : @$order['due']) ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </tfoot>
                                <?php else: ?>
                                    <tfoot>
                                        <tr class="tablefooter last-page-only" style="font-size: 14px;">
                                            <td colspan="3" class="text-left"><strong>Note:</strong> <span><?= $comment ?></span>
                                            </td>
                                            <?php if ($shouldShow): ?>
                                                <td colspan="2"></td>
                                            <?php endif; ?>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                    <?php if ($pageIndex === $totalPages - 1): ?>
                        <?php if (($_REQUEST['type'] == "quotation" && $order['is_delivery_note'] != 1) || ($_REQUEST['type'] == "manualbill" && $order['type'] == 'quotation')): ?>
                            <div class="mb-2">
                                <div class="row">
                                    <div class="col-2 d-flex align-items-start">
                                        <p><strong>Payment Mode:</strong></p>
                                    </div>
                                    <div class="col-1">

                                        <p><span class="pdf-only-header"> CASH </span></p>
                                    </div>
                                    <div class="col-9"></div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-2 d-flex align-items-start">
                                        <p><strong>Price Validity:</strong></p>
                                    </div>
                                    <div class="col-1">

                                        <p><span class="pdf-only-header"> 7 Days </span></p>
                                    </div>
                                    <div class="col-9 w-100">
                                        <div class="row w-100 text-right ml-auto mr-3">
                                            <div class="col-12 text-right d-flex justify-content-end">
                                                <p><strong>Prepared By:</strong></p>
                                                <p class="text-capitalize pr-3">
                                                    <?php
                                                    $user = fetchRecord($dbc, "users", "user_id", $_SESSION['user_id']);
                                                    echo isset($user['fullname']) ? $user['fullname'] : "______________________";
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else:
                            if (($_REQUEST['type'] ?? '') !== 'gatepass') { ?>
                                <div class="row  m-0 pl-5">
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
                            <?php }endif; ?>
                    <?php endif; ?>
                </div>
                <div class="pdf_footer">
                    <div class="return">
                        <p>Goods can be returned within 14 days only in original packaging & Invoice</p>
                        <p>يمكن إرجاع البضائع خلال ١٤ يوماً مع العبوة والفاتورة</p>
                    </div>
                    <div class="text-center cheque_instr">
                        <p>Please issue The cheque in the name of "Four Seasons Est For Electrical & Electronic Appliances &
                            Repairs"</p>
                        <p>"يرجى إصدار الشيك باسم : مؤسسة فصول الأربعة للأجهزة الكهربائية والالكترونيةوتصليحها"</p>
                    </div>
                    <div class="footer">
                        <div class="footer-item"><span>Receiver's Sign</span></div>
                        <div class="footer-item text-center">
                            <img class="qr" src="img/logo/shwaikh.png" alt="Shwaikh Logo" />
                            <p class="qr_text">الشويخ</p>
                        </div>
                        <div class="footer-item text-center">
                            <img class="qr" src="img/logo/farwaniya.png" alt="Farwaniya Logo" />
                            <p class="qr_text">الفروانية</p>
                        </div>
                        <div class="footer-item text-center">
                            <img class="qr" src="img/logo/zena.png" alt="Zena Logo" />
                            <p class="qr_text">الشويخ الزينة</p>
                        </div>
                        <div class="footer-item"><span>Salesman's Sign</span></div>
                    </div>


                </div>
            </div>
        <?php endforeach; ?>
    <?php endfor; ?>
</body>

</html>
<script type="text/javascript">
    const urlParams = new URLSearchParams(window.location.search);
    const isPdf = urlParams.get('pdf') === 'true';

    function generatePdf() {
        const companyNames = document.querySelectorAll('.company-name');
            companyNames.forEach(companyName => {
                companyName.style.fontFamily = "'Phoenix Sans', sans-serif";
            });
        const headers = document.querySelectorAll('.pdf-only-header');
        const footers = document.querySelectorAll('.pdf_footer');
        headers.forEach(header => header.classList.add('pdf-visible'));
        footers.forEach(footer => footer.classList.add('pdf-visible'));

        setTimeout(() => {
            window.print();
            setTimeout(() => {
                headers.forEach(header => header.classList.remove('pdf-visible'));
                footers.forEach(footer => footer.classList.remove('pdf-visible'));
            }, 100);
        }, 1000);
    }

    if (isPdf) {
        generatePdf();
    }

    const saveAsPdfBtn = document.getElementById('saveAsPdfBtn');
    if (saveAsPdfBtn) {
        saveAsPdfBtn.addEventListener('click', () => {
            window.history.pushState({}, document.title, window.location.pathname + '?pdf=true');
            generatePdf();
        });
    }
</script>