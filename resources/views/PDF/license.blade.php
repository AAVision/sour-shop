<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchase License</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: -15px;
        }

        body p {
            font-size: 16px !important;
        }


        .header {
            text-align: center;
            margin-bottom: -5px;
        }

        .logo {
            width: 220px;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 10px 0 5px;
            font-size: 25px;
            font-weight: bold;
            color: #24262d;
        }

        .header p {
            color: #555;
        }

        .header a {
            color: #007bff;
            font-size: 16px;
            text-decoration: none;
        }

        .details {
            margin-bottom: 30px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .row span:first-child {
            font-weight: bold;
            color: #333;
        }

        .row span:last-child {
            max-width: 70%;
            text-align: right;
            color: #666;
            word-break: break-word;
        }

        .purchase-code a {
            color: #007bff;
            font-weight: bold;
        }

        .footer-note {
            text-align: center;
            color: #555;
            margin-bottom: 30px;
            margin-top: 30px
        }

        .footer-note a {
            font-size: 17px;
            color: #007bff;
            text-decoration: none;
        }

        .invoice-note {
            text-align: center;
            font-size: 22px;
            position: relative;
            color: var(--Gray-500, #687387);
            font-style: normal;
            font-weight: 700;
            line-height: 28px;
            margin-top: 70px;
        }

        .author {
            position: absolute;
            right: 20px;
            bottom: -25px;
            background: #ecf0ff;
            padding: 5px 10px;
            border-radius: 10px;
            font-size: 14px;
            color: #7b61ff;
        }

        .footer {
            text-align: center;
            font-size: 16px;
            color: #555;
            position: fixed;
            bottom: -40px;
            left: 0;
            width: 100%;
        }

        .contact-info {
            display: flex;
            justify-content: space-between;
            gap: 80px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .contact-info img {
            width: 24px;
            height: 24px;
        }

        .contact-info span {
            font-size: 20px;
            margin-bottom: 4px;
            display: inline-block;
            /* width: 48%; */
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .license-box {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border: 2px dashed #000;
            padding: 15px;
            background: #f5f5f5;
            margin: 6px 0;
        }
    </style>

    </style>
</head>

<body>

    <div class="header">
        <img src="{{ $logo }}" alt="Logo" class="logo">
        <hr style="border:0.5px solid #f1f1f1">
        <h2>{{ __('Purchase License') }}</h2>
        <p>
            {{ __('Thank you for buying the') }} <a href="#"
                style="color: #007bff;font-weight: bold; text-decoration: none;">{{ __('Regular') }}
                {{ __('LICENSE') }}</a>.
        </p>
        <p style="margin-top:-7px;">
            {{ __('To see the full license details, head over to your Downloads page.') }}
        </p>

        <hr style="border:0.5px solid #f1f1f1">
    </div>
    <div style="margin-right:20px">
        <div style="width:900px;margin:0 auto">

            <div style="margin-top:20px;width: 100%; margin-bottom: 20px;">
                <div style="width: 20%; float: left">{{ __('Owner Username') }}</div>
                <div style="width: 4%; float: left">:</div>
                <div style="width: 76%">{{ $licenseData->user?->name ?? 'N/A' }}</div>
            </div>

            <div style="width: 100%; margin-bottom: 20px;">
                <div style="width: 20%; float: left">{{ __('Product name') }}</div>
                <div style="width: 4%; float: left">:</div>
                <div style="width: 76%">{{ $licenseData->product?->name ?? '' }}</div>
            </div>

            <div style="width: 100%; margin-bottom: 20px;">
                <div style="width: 20%; float: left">{{ __('License Capability') }}</div>
                <div style="width: 4%; float: left">:</div>
                <div style="width: 76%">{{ __('One License') }} <small style="font-size: 12px;color:#555">(<strong
                            style="color:red">{{ __('Note') }}</strong>:
                        {{ __('Each license is valid for use on a single domain only') }})</small></div>
            </div>
        </div>
    </div>
    <hr style="border:0.5px solid #f1f1f1">
    <div class="license-box">
        {{ $licenseData->product_license ?? 'N/A' }}
    </div>
    <hr style="border:0.5px solid #f1f1f1">
    <div class="footer-note">
        <p>{{ __('For any queries related to this document or license please contact us.') }}</p>
        <p style="margin-top:-7px;">{{ __('Support via') }} <a href="#">{{ config('app.url') }}</a></p>
    </div>

    <div class="footer">
        {{ __('Thank you for your purchase.') }}<br>
        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}.
    </div>

</body>

</html>
