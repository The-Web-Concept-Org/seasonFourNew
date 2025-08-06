<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/head.php'; ?>

<head>
    <meta charset="UTF-8">
    <title>Receipt Voucher</title>
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
            color: #333;
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

        .company-header div {
            font-size: 15px;
            line-height: 0.5;
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

        .logo {
            width: 180px;
            height: 160px;
            object-fit: contain;
        }

        .rtl {
            text-align: right;
        }


        .receipt {
            background: #ffffff;
            margin-top: 10px;
        }

        .receipt .header {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            color: black;
            padding-bottom: 15px;
            margin-bottom: 30px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .sub-header {
            display: flex;
            justify-content: flex-end;
            font-size: 16px;
            margin-bottom: 10px;
            color: #444;
            padding: 10px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            align-items: center;



        }

        .row .label {
            font-weight: 600;
            min-width: 140px;
            color: black;
            font-size: 16px;
        }

        .row .value {
            flex: 1;
            padding: 8px 12px;
            font-weight: 500;
            border-bottom: 2px dashed #d4e4d4;
            font-size: 16px;
            color: #333;
        }

        .boxed {
            border: 2px solid black;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            min-width: 120px;
            text-align: center;
            font-size: 18px;
            color: black;
        }

        .amount-row {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            color: black;
            padding: 15px;
            margin-top: 10px;
        }

        .payment-box {
            border: 2px solid #1a5f3a;
            display: inline-block;
            padding: 10px 40px;
            background-color: #e6f3e6;
            font-weight: 600;
            margin-top: 15px;
            border-radius: 8px;
            color: #1a5f3a;
        }

        .reference-row {
            display: flex;
            margin: 20px 0;
            font-size: 17px;
            color: black;
            font-weight: 500;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            border-top: 2px solid #d4e4d4;
            padding-top: 25px;
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }

        .footer div {
            display: flex;
            align-items: center;
            gap: 10px;
        }


        @media print {
            body {
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

        }
    </style>
</head>

<body>
    <?php include_once 'includes/head.php';
    $vouchers = fetchRecord($dbc, "vouchers", "voucher_id", base64_decode($_REQUEST['voucher_id']));
    $customer_id1 = fetchRecord($dbc, "customers", "customer_id", $vouchers['customer_id1']);
    $customer_id2 = fetchRecord($dbc, "customers", "customer_id", $vouchers['customer_id2']);

    ?>
    <?php

    // Example: "general_voucher" → "GV"
    $voucherType = $vouchers['voucher_group']; // e.g., "general_voucher"
    $parts = explode('_', $voucherType);
    $typeCode = strtoupper(substr($parts[0], 0, 1) . substr($parts[1] ?? '', 0, 1));


    $prefix = "SF25";
    $numericId = str_pad($vouchers['voucher_id'], 7, '0', STR_PAD_LEFT); // Pads to 7 digits
    
    $formattedVoucherId = "$prefix-$typeCode-$numericId";
    ?>
    <?php
    $voucher = [
        'id' => $formattedVoucherId,
        'date' => date('D d-M-Y h:i A', strtotime($vouchers['timestamp'] . " +7 hours")),
        'amount' => $vouchers['voucher_amount'],
        'receiver' => 'Rana Khalil',
        'customer_name' => ucfirst($customer_id1['customer_name']),
        'payment_mode' => ucfirst(@$customer_id2['customer_name']),
        'invoice_ref' => ucfirst($vouchers['voucher_hint'])
    ];

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
        if ($number < 100)
            return $words[10 * floor($number / 10)] . ($number % 10 ? ' ' . $words[$number % 10] : '');
        if ($number < 1000)
            return $words[floor($number / 100)] . ' HUNDRED' . ($number % 100 ? ' ' . numberToWords($number % 100) : '');
        if ($number < 1000000)
            return numberToWords(floor($number / 1000)) . ' THOUSAND' . ($number % 1000 ? ' ' . numberToWords($number % 1000) : '');
        return 'NUMBER TOO LARGE';
    }
    function amountToWordsKD($amount)
    {
        $parts = explode('.', number_format($amount, 3, '.', ''));
        $kd = (int) $parts[0];
        $fils = isset($parts[1]) ? (int) round($parts[1]) : 0;
        $kdPart = numberToWords($kd) . ' KD';
        $filsPart = $fils > 0 ? ' AND ' . numberToWords($fils) . ' FILS' : '';
        return $kdPart . $filsPart;
    }
    $kd = explode('.', number_format($voucher['amount'], 3))[0];
    $fils = explode('.', number_format($voucher['amount'], 3))[1];
    ?>

    <div class="invoice-container">
        <!-- Company Header -->
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
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" style="width: 15px; fill: #1a5f3a;">
                        <path
                            d="M128 252.6C128 148.4 214 64 320 64C426 64 512 148.4 512 252.6C512 371.9 391.8 514.9 341.6 569.4C329.8 582.2 310.1 582.2 298.3 569.4C248.1 514.9 127.9 371.9 127.9 252.6zM320 320C355.3 320 384 291.3 384 256C384 220.7 355.3 192 320 192C284.7 192 256 220.7 256 256C256 291.3 284.7 320 320 320z" />
                    </svg>
                    Shuwaikh Branch-Block 3,Beside City Star - Tel:66990815
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" style="width: 15px; fill: #1a5f3a;">
                        <path
                            d="M476.9 161.1C435 119.1 379.2 96 319.9 96C197.5 96 97.9 195.6 97.9 318C97.9 357.1 108.1 395.3 127.5 429L96 544L213.7 513.1C246.1 530.8 282.6 540.1 319.8 540.1L319.9 540.1C442.2 540.1 544 440.5 544 318.1C544 258.8 518.8 203.1 476.9 161.1zM319.9 502.7C286.7 502.7 254.2 493.8 225.9 477L219.2 473L149.4 491.3L168 423.2L163.6 416.2C145.1 386.8 135.4 352.9 135.4 318C135.4 216.3 218.2 133.5 320 133.5C369.3 133.5 415.6 152.7 450.4 187.6C485.2 222.5 506.6 268.8 506.5 318.1C506.5 419.9 421.6 502.7 319.9 502.7zM421.1 364.5C415.6 361.7 388.3 348.3 383.2 346.5C378.1 344.6 374.4 343.7 370.7 349.3C367 354.9 356.4 367.3 353.1 371.1C349.9 374.8 346.6 375.3 341.1 372.5C308.5 356.2 287.1 343.4 265.6 306.5C259.9 296.7 271.3 297.4 281.9 276.2C283.7 272.5 282.8 269.3 281.4 266.5C280 263.7 268.9 236.4 264.3 225.3C259.8 214.5 255.2 216 251.8 215.8C248.6 215.6 244.9 215.6 241.2 215.6C237.5 215.6 231.5 217 226.4 222.5C221.3 228.1 207 241.5 207 268.8C207 296.1 226.9 322.5 229.6 326.2C232.4 329.9 268.7 385.9 324.4 410C359.6 425.2 373.4 426.5 391 423.9C401.7 422.3 423.8 410.5 428.4 397.5C433 384.5 433 373.4 431.6 371.1C430.3 368.6 426.6 367.2 421.1 364.5z" />
                    </svg>
                    65692904
                </p>
                <p class="contact-info">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" style="width: 15px; fill: #1a5f3a;">
                        <path
                            d="M128 252.6C128 148.4 214 64 320 64C426 64 512 148.4 512 252.6C512 371.9 391.8 514.9 341.6 569.4C329.8 582.2 310.1 582.2 298.3 569.4C248.1 514.9 127.9 371.9 127.9 252.6zM320 320C355.3 320 384 291.3 384 256C384 220.7 355.3 192 320 192C284.7 192 256 220.7 256 256C256 291.3 284.7 320 320 320z" />
                    </svg>
                    Shuwaikh Branch-Block 2,Zeena St. 18 - Tel:99408640
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" style="width: 15px; fill: #1a5f3a;">
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
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" style="width: 15px; fill: #1a5f3a;">
                        <path
                            d="M476.9 161.1C435 119.1 379.2 96 319.9 96C197.5 96 97.9 195.6 97.9 318C97.9 357.1 108.1 395.3 127.5 429L96 544L213.7 513.1C246.1 530.8 282.6 540.1 319.8 540.1L319.9 540.1C442.2 540.1 544 440.5 544 318.1C544 258.8 518.8 203.1 476.9 161.1zM319.9 502.7C286.7 502.7 254.2 493.8 225.9 477L219.2 473L149.4 491.3L168 423.2L163.6 416.2C145.1 386.8 135.4 352.9 135.4 318C135.4 216.3 218.2 133.5 320 133.5C369.3 133.5 415.6 152.7 450.4 187.6C485.2 222.5 506.6 268.8 506.5 318.1C506.5 419.9 421.6 502.7 319.9 502.7zM421.1 364.5C415.6 361.7 388.3 348.3 383.2 346.5C378.1 344.6 374.4 343.7 370.7 349.3C367 354.9 356.4 367.3 353.1 371.1C349.9 374.8 346.6 375.3 341.1 372.5C308.5 356.2 287.1 343.4 265.6 306.5C259.9 296.7 271.3 297.4 281.9 276.2C283.7 272.5 282.8 269.3 281.4 266.5C280 263.7 268.9 236.4 264.3 225.3C259.8 214.5 255.2 216 251.8 215.8C248.6 215.6 244.9 215.6 241.2 215.6C237.5 215.6 231.5 217 226.4 222.5C221.3 228.1 207 241.5 207 268.8C207 296.1 226.9 322.5 229.6 326.2C232.4 329.9 268.7 385.9 324.4 410C359.6 425.2 373.4 426.5 391 423.9C401.7 422.3 423.8 410.5 428.4 397.5C433 384.5 433 373.4 431.6 371.1C430.3 368.6 426.6 367.2 421.1 364.5z" />
                    </svg>٦٦٩٤٤٨٧١
                </p>

                <p class="contact-info">المعرض الشويخ - قطعة ٣ - قرب سيتى ستار - تلفون: ٦٦٩٩٠٨١٥ <svg
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" style="width: 15px; fill: #1a5f3a;">
                        <path
                            d="M476.9 161.1C435 119.1 379.2 96 319.9 96C197.5 96 97.9 195.6 97.9 318C97.9 357.1 108.1 395.3 127.5 429L96 544L213.7 513.1C246.1 530.8 282.6 540.1 319.8 540.1L319.9 540.1C442.2 540.1 544 440.5 544 318.1C544 258.8 518.8 203.1 476.9 161.1zM319.9 502.7C286.7 502.7 254.2 493.8 225.9 477L219.2 473L149.4 491.3L168 423.2L163.6 416.2C145.1 386.8 135.4 352.9 135.4 318C135.4 216.3 218.2 133.5 320 133.5C369.3 133.5 415.6 152.7 450.4 187.6C485.2 222.5 506.6 268.8 506.5 318.1C506.5 419.9 421.6 502.7 319.9 502.7zM421.1 364.5C415.6 361.7 388.3 348.3 383.2 346.5C378.1 344.6 374.4 343.7 370.7 349.3C367 354.9 356.4 367.3 353.1 371.1C349.9 374.8 346.6 375.3 341.1 372.5C308.5 356.2 287.1 343.4 265.6 306.5C259.9 296.7 271.3 297.4 281.9 276.2C283.7 272.5 282.8 269.3 281.4 266.5C280 263.7 268.9 236.4 264.3 225.3C259.8 214.5 255.2 216 251.8 215.8C248.6 215.6 244.9 215.6 241.2 215.6C237.5 215.6 231.5 217 226.4 222.5C221.3 228.1 207 241.5 207 268.8C207 296.1 226.9 322.5 229.6 326.2C232.4 329.9 268.7 385.9 324.4 410C359.6 425.2 373.4 426.5 391 423.9C401.7 422.3 423.8 410.5 428.4 397.5C433 384.5 433 373.4 431.6 371.1C430.3 368.6 426.6 367.2 421.1 364.5z" />
                    </svg> ٦٥٦٩٢٩٠٤</p>
                <p class="contact-info">المعرض الشويخ - قطعة ٢ - شارع الزينة ١٨ - تلفون: ٩٩٤٢٨٦٤٠ <svg
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" style="width: 15px; fill: #1a5f3a;">
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

        <!-- Receipt Block -->
        <div class="receipt">
            <div
                style="position: relative;display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; flex-wrap: wrap;">
                <div class="font-weight-bold"
                    style="position:absolute ; top: -20px;left:30px; font-weight: 500;font-size: 16px;color: #333;">
                    Amount</div>
                <div class="value" style="display:flex; gap:10px; margin-top: 5px;">
                    <div class="boxed"><?= $kd ?> KD</div>
                    <div class="boxed"><?= $fils ?> Fils</div>
                </div>

                <div class="boxed" style="margin-top: 5px;">
                    <?= strtoupper(
                        $vouchers['voucher_group'] === "general_voucher" ? $vouchers['voucher_type'] :
                        ($vouchers['voucher_group'] === "expense_voucher" ? 'expense' :
                            ($vouchers['voucher_group'] === "single_voucher" ? 'single' :
                                $vouchers['voucher_group']))
                    ) ?> VOUCHER
                </div>

                <div class="boxed" style="margin-top: 5px;">
                    <strong>ID:</strong> <span><?= $voucher['id'] ?></span>
                </div>
            </div>

            <!-- Date Row Aligned Right -->
            <div class="sub-header" style="justify-content: flex-end; margin-top: 10px;">
                <div><strong>Date:</strong> <span class="font-weight-bold"><?= $voucher['date'] ?></span></div>
            </div>

            <!-- Received From -->
            <div class="row" style="margin-left: 1px;">
                <div class="label">Received From</div>
                <div class="value font-weight-bold"><?= $voucher['customer_name'] ?></div>
            </div>

            <!-- Amount in Words -->
            <div class="row" style="margin-left: 1px;">
                <div class="label">Amount In Words</div>
                <div class="value font-weight-bold"><?= ucwords(strtolower(amountToWordsKD($voucher['amount']))) ?> Only
                </div>
            </div>

            <!-- Bank Info and Check No in One Row -->
            <div class="" style="display: flex; gap: 40px; margin-left: 15px;">
                <div class="row" style="display: flex; flex: 1;">
                    <div class="label" style="min-width: 100px;">Bank</div>
                    <div class="value font-weight-bold"><?= $voucher['payment_mode'] ?></div>
                </div>
                <div class="row" style="display: flex; flex: 1;">
                    <div class="label" style="min-width: 140px;">Cash / Check No</div>
                    <div class="value font-weight-bold"><?= @$vouchers['td_check_no'] ?></div>
                </div>
            </div>


            <!-- Invoice Reference -->
            <div class="reference-row" style="display: flex; align-items: center;">
                <span style="font-weight: 600; min-width: 180px; color: black; font-size: 16px;">Invoice
                    References:</span>
                <span style="margin-left: 35px"><?= $voucher['invoice_ref'] ?></span>
            </div>

            <!-- Footer Signatures -->
            <div class="footer">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span>Signature:</span>
                    <span style="border-bottom: 2px dashed #000; width: 120px;"></span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span>Received By:</span>
                    <span style="border-bottom: 2px dashed #000; width: 120px;"></span>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">Software Developed By <br>
            <strong> The Web Concept (+965 6699 0815) </strong>
        </div>
    </div>
    <script>
        window.print();
    </script>
</body>

</html>