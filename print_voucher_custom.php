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
            margin: 15mm;
        }

        body {
            margin: 20px 20px;
            padding: 0;
            background: #f4f6f9;
            font-family: 'Roboto', 'Arial', sans-serif;
            color: #333;
        }

        .invoice-container {
            width: 100%;
            margin: 40px auto;
            background: white;
            padding: 0 30px;
        }

        .company-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            /* border-bottom: 3px solid #1a5f3a; */
            background: linear-gradient(180deg, #f7fff7, #ffffff);
        }

        .company-header div {
            font-size: 15px;
            line-height: 0.5;
        }

        .logo {
            width: 180px;
            height: 160px;
            object-fit: contain;
        }



        .company-name {
            font-size: 28px;
            font-weight: 700;
            color: #1a5f3a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .company-sub {
            font-size: 16px;
            color: #4a4a4a;
            margin-bottom: 14px;
            font-style: italic;
        }

        .rtl {
            text-align: right;
            direction: rtl;
        }

        .contact-info i {
            margin-right: 8px;
            color: #1a5f3a;
            line-height: 0.4;
        }

        .contact-info_arabic {
            line-height: 0.4;
            font-weight: 700;
        }

        /* Label Bar */
        .label-bar {
            background: rgb(17, 179, 85);
            color: white;
            padding: 12px 20px 0px;
            border-radius: 50px;
            margin: 0 0 25px 0;
            display: flex;
            justify-content: space-between;
            font-size: 15px;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }


        .receipt {
            background: #ffffff;
            /* padding: 40px; */
        }


        .receipt .header {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            color: #1a5f3a;
            border-bottom: 3px solid #1a5f3a;
            padding-bottom: 15px;
            margin-bottom: 30px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .sub-header {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            margin-bottom: 30px;
            color: #444;
            background: #f7fff7;
            padding: 10px;
            border-radius: 8px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 18px 0;
            padding: 10px;
            border-radius: 6px;
            transition: background 0.2s ease;
        }

        .row .label {
            font-weight: 600;
            min-width: 180px;
            color: #1a5f3a;
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
            border: 2px solid #1a5f3a;
            background-color: #e6f3e6;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            min-width: 120px;
            text-align: center;
            font-size: 18px;
            color: #1a5f3a;
            transition: background 0.3s ease;
        }

        .amount-row {
            margin: 40px 0;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            color: #1a5f3a;
            background: #f7fff7;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
            text-align: center;
            margin: 30px 0;
            font-size: 16px;
            color: #444;
            font-weight: 500;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 70px;
            border-top: 2px solid #d4e4d4;
            padding-top: 25px;
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }

        .signature .line {
            flex: 1;
            border-bottom: 2px dashed #1a5f3a;
            margin: 0 15px;
            height: 2px;
        }

        @media print {
            body {
                background: white;
            }

            .invoice-container {
                box-shadow: none;
                margin: 20px auto;
                padding-left: 30px;
            }

            .receipt {
                box-shadow: none;
                padding: 0;
            }

            .boxed,
            .payment-box {
                background-color: white !important;
                border-color: #000 !important;
                color: #000 !important;
            }

            .sub-header,
            .amount-row {
                background: white !important;
            }

            .row:hover {
                background: white !important;
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




    // Assume voucher_id is numeric (e.g., 1, 25, 345)
    $prefix = "SF25";
    // You can dynamically generate this from the voucher_type if needed
    $numericId = str_pad($vouchers['voucher_id'], 7, '0', STR_PAD_LEFT); // Pads to 7 digits
    
    $formattedVoucherId = "$prefix-$typeCode-$numericId";

// $vouchert = $vouchers['voucher_group'] === "general_voucher";    
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


    // Helper functions
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
                <h2 class="company-name">Season Four</h2>
                <p class="company-sub">A/C & Refrigeration Contracting Est</p>
                <p class="contact-info"><i class="fa-solid fa-map-marker-alt"></i> Farwaniyah Block 4, St 45 - 55529978
                    <i class="fa-brands fa-whatsapp"></i> 66944871
                </p>
                <p class="contact-info"><i class="fa-solid fa-map-marker-alt"></i> Shuwaikh Block 3, St 53 - 66945212 <i
                        class="fa-brands fa-whatsapp"></i> 99408640</p>
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
                <!-- <p class="contact-info"><i class="fa-solid fa-envelope"></i> season4-kw@hotmail.com</p>
                <p class="contact-info"><i class="fa-brands fa-instagram"></i> seasonfourkwt</p> -->
            </div>
        </div>

        <!-- Label Strip -->
        <div class="label-bar">
            <p>ALL TYPES OF A/C, REFRIGERATOR, WASHING MACHINE SPARE PARTS</p>
            <p>جميع أنواع قطع غيار المكيفات والثلاجات والغسالات</p>
        </div>

        <!-- Receipt Block -->
        <div class="receipt">
            <div class="header"><?= strtoupper(
    $vouchers['voucher_group'] === "general_voucher" ? $vouchers['voucher_type'] :
    ($vouchers['voucher_group'] === "expense_voucher" ? 'expense' :
    ($vouchers['voucher_group'] === "single_voucher" ? 'single' :
    $vouchers['voucher_group']))
) ?>
 Voucher</div>

            <div class="sub-header">

                <div><strong>Voucher ID:</strong><span class="font-weight-bold"><?= $voucher['id'] ?> </span></div>
                <div><strong>Date:</strong> <span class="font-weight-bold"><?= $voucher['date'] ?> </span>
                </div>
            </div>

            <div class="row">
                <div class="label">Received From</div>
                <div class="value font-weight-bold"><?= $voucher['customer_name'] ?></div>
            </div>

            <div class="row">
                <div class="label">Amount</div>
                <div class="value" style="display:flex; gap:15px;">
                    <div class="boxed"><?= $kd ?> KD</div>
                    <div class="boxed"><?= $fils ?> Fils</div>
                </div>
            </div>

            <div class="amount-row">
                The Sum of KD <?= ucwords(strtolower(amountToWordsKD($voucher['amount']))) ?> Only
            </div>

            <div class="row">
                <div class="label">Payment Mode</div>
                <div class="value"><?= $voucher['payment_mode'] ?> </div>
            </div>

            <div class="reference-row">
                <strong>Invoice References:</strong> <?= $voucher['invoice_ref'] ?>
            </div>

            <div class="signature">
                <span>Received By: </span>
                <span class="line"></span>
                <!-- <span>Authorized Signature</span> -->
            </div>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>