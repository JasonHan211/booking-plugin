<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function headerTemplate() {
    
    $base_url = BI_PLUGIN_URL . 'includes/invoice/template/images/logo.png';

    $header = <<<HTML
            <head>
            <title>Invoice</title>
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
                body {
                    font-family: Arial, sans-serif;
                }
                .invoice {
                    width: 100%;
                    padding: 10px;
                }
                .page-break {
                    page-break-before: always; /* or page-break-after: always; */
                }
                /* Add more print-specific styles here */
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

            .footer {
                font-size: 10px;
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
            <p class="footer">Thank you for staying with us. Hope to see you soon!</p>
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
    $output = <<<HTML
            <div class="invoice">
                <div class="invoice-header">
                    Test
                </div>
            </div>
        HTML;

    return $output;
}