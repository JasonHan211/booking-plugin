<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function headerTemplate() {
    
    $base_url = BI_PLUGIN_URL . 'includes/invoice/template/images/logo.png';

    $header = <<<HTML
            <head>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <style type="text/css">

                    @media print {
                        /* Hide header and footer information */
                        @page {
                            size: auto;
                            margin: 200mm;
                        }

                        .page-break {
                            page-break-before: always; /* or page-break-after: always; */
                        }
                        /* Add more print-specific styles here */
                    }
                    
                    body {
                        display: flex;
                        flex-direction: column;
                        min-height: 100vh;
                        margin: 0;
                        font-family: Arial, sans-serif;
                    }

                    .invoice {
                        flex: 1;
                        width: 100%;
                        padding: 10px;
                    }

                    .footerText {
                        font-size: 12px;
                        text-align: center;
                        padding: 10px;
                        /* background-color: #f1f1f1; */
                    }

                    .logo {
                        width: 120px;
                        height: auto;
                    }

                    p {
                        margin-bottom: 0;
                    }

                    .header {
                        font-size: 12px;
                    }

                    table {
                        border-collapse: collapse;
                        width: 100%;
                    }

                    th, td {
                        border: 1px solid #dddddd;
                        text-align: left;
                        padding: 8px;
                    }

                    th {
                        background-color: #f2f2f2;
                    }

                </style>
            </head>
            <body>
                <div class="container text-center">
                    <img src="$base_url" class="logo" alt="Logo">
                    <p class="header">Beach & Breeze Glamping</p>
                    <p class="header">Owned and Operated by: Beach N Breeze Glamping Retreat Sdn. Bhd.</p>
                    <p class="header">Batu 27 Kampung Pantai, 78200 Kuala Sungai Baru, Melaka</p>
                    <p class="header">Tel No: +6012-9869961</p>
                    <p class="header">Reg No: 202301042359 (1536276-P)</p>
                </div>
        HTML;

        return $header;

}

function footerTemplate($data) {
    $footer = <<<HTML
            <p class="footerText">Thank you for staying with us. Hope to see you soon!</p>
        </body>
        
    HTML;

    return $footer;
}

function nextPageTemplate() {
    $output = <<<HTML
        <div class="page-break"></div>
    HTML;

    return $output;
}

function template1($data) {

    $result = $data[0];
    $contact = json_decode($result["contact_info"]);
    $bookingNo = $result["booking_number"];
    

    $output = <<<HTML
            <div class="invoice">
                <table>
                    <tr>
                        <td colspan="3"></td>
                        <td>Invoice</td>
                        <td colspan="1"></td>
                    </tr>
                    <tr>
                        <td>Guest Name:</td>
                        <td>{$contact->name}</td>
                        <td colspan="1"></td>
                        <td>Booking Number:</td>
                        <td>{$bookingNo}</td>
                    </tr>
                    <tr>
                        <td>Guest Email:</td>
                        <td>{$contact->email}</td>
                        <td colspan="1"></td>
                        <td>Arrival:</td>
                        <td>{$bookingNo}</td>
                    </tr>
                </table>
            </div>
        HTML;

    return $output;
}