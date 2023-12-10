<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function headerTemplate() {
    
    $base_url = BI_PLUGIN_URL . 'includes/invoice/template/images/logo.png';

    $header = <<<HTML
            <head>
                <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"> -->
                <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> -->
                <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
                <style type="text/css">

                    @media print {
                        /* Hide header and footer information */
                        @page {
                            size: A4;
                            margin: 1cm; /* Adjust margins as needed */
                        }

                        .page-break {
                            page-break-after: always; /* or page-break-after: always; */
                        }
                        /* Add more print-specific styles here */
                    }
                    
                    body {
                        display: flex;
                        flex-direction: column;
                        min-height: 95vh;
                        margin: 0;
                        padding: 15px;
                        font-family: Arial, sans-serif;
                    }

                    .invoice {
                        flex: 1;
                        width: 100%;
                    }

                    .footerText {
                        font-size: 12px;
                        text-align: center;
                        /* padding: 10px; */
                        /* background-color: #f1f1f1; */
                    }

                    .logo {
                        width: 120px;
                        height: auto;
                    }

                    p {
                        margin-bottom: 0;
                        font-size: 12px;
                    }

                    .header {
                        font-size: 12px;
                    }

                    .text-center {
                        text-align: center!important;
                    }

                    .invoice-table {
                        border-collapse: collapse;
                        width: 100%;
                        font-size: 12px;
                    }

                    .invoice-table th,
                    .invoice-table td {
                        /* border: 1px solid #dddddd; */
                        text-align: left;
                        padding: 8px;
                    }

                    .invoice-table th {
                        background-color: #f2f2f2;
                    }

                    .invoice-details {
                        border-collapse: collapse;
                        width: 100%;
                        font-size: 12px;
                    }

                    .invoice-details th,
                    .invoice-details td {
                        border: 1px solid #dddddd;
                        box-sizing: border-box;
                        text-align: left;
                        padding: 8px;
                    }

                    .invoice-details th {
                        background-color: #f2f2f2;
                    }

                    /* Adjust the width of specific columns */
                    .invoice-details th:nth-child(1),
                    .invoice-details td:nth-child(1) {
                        width: 20%; /* Set the width for the "Date" column */
                    }

                    .invoice-details th:nth-child(2),
                    .invoice-details td:nth-child(2) {
                        width: 65%; /* Set the width for the "Description" column */
                    }

                    .invoice-details th:nth-child(3),
                    .invoice-details td:nth-child(3) {
                        text-align: right; /* Align the text to the right */
                    }

                    .invoice-summary {
                        border-collapse: collapse;
                        width: 100%;
                        font-size: 12px;
                    }

                    .invoice-summary th,
                    .invoice-summary td {
                        box-sizing: border-box;
                        text-align: left;
                        padding-left: 8px;
                        padding-right: 8px;
                    }

                    /* Adjust the width of specific columns */
                    .invoice-summary th:nth-child(1),
                    .invoice-summary td:nth-child(1) {
                        width: 65%; /* Set the width for the "Date" column */
                    }

                    .invoice-summary th:nth-child(2),
                    .invoice-summary td:nth-child(2) {
                        width: 20%; /* Set the width for the "Description" column */
                    }
                    
                    .invoice-summary th:nth-child(3),
                    .invoice-summary td:nth-child(3) {
                        text-align: right; /* Align the text to the right */
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
            <p class="footerText">This is a computer generated invoice. No signature is required.</p>
            <p class="footerText">Thank you for staying with us. Hope to see you soon!</p>  
    HTML;

    return $footer;
}

function nextPageTemplate() {
    $output = <<<HTML
            <div class="page-break"> Page Break </div> 
        </body>
    HTML;

    return $output;
}

function template1($data) {
    $result = $data[0];
    $contact = json_decode($result["contact_info"]);
    $bookingNo = $result["booking_number"];
    $bookingInfo = json_decode($result["booking_info"]);

    $detailString = "<tr>";

    // Summary Build
    $totalOriginalSum = 0;
    $totalDiscountedSum = 0;
    $totalDiscount = 0;

    foreach ($data as $row) {

        $booking = json_decode($row["booking_info"]);
        $resources = $booking->bookings[0]->resource;
        $addons = $booking->bookings[0]->addon;
        $total = json_decode($row["total_info"]);
        $totalOriginalSum += $total->original;
        $totalDiscountedSum += $total->total_after_final_discounted;
        $discount = $total->original - $total->total_after_final_discounted;
        $totalDiscount += $discount;

        foreach ($resources as $resource) {
            $detailString .= "<td>". $resource->booking_date . "</td>";
            $detailString .= "<td>". $resource->resource->resource_name . " | Adults: " . $booking->adults . " | Children: " . $booking->children . "</td>";
            $detailString .= "<td>RM ". $resource->resource_price . "</td>";
            $detailString .= "</tr>";
        }

        foreach ($addons as $addon) {
            $detailString .= "<td colspan='1'></td>";
            $detailString .= "<td>". $addon->addon->addon_name . " | Adults: " . $booking->adults . " | Children: " . $booking->children . "</td>";
            $detailString .= "<td>RM ". $addon->addon_price . "</td>";
            $detailString .= "</tr>";
        }

        $detailString .= "<td colspan='1'></td>";
        $detailString .= "<td> Deposit | " . $resources[0]->resource->resource_name . " | Refundable</td>";
        $detailString .= "<td>RM ". $total->deposit . "</td>";
        $detailString .= "</tr>";

        $detailString .= "<td colspan='1'></td>";
        $detailString .= "<td> Discount </td>";
        $detailString .= "<td>- RM ". $discount . "</td>";
        $detailString .= "</tr>";

    }

    $output = <<<HTML
            <div class="invoice">
                <table class="invoice-table">
                    <tr>
                        <td colspan="3"></td>
                        <td>Invoice/Receipt</td>
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
                        <td>{$bookingInfo->from}</td>
                    </tr>
                    <tr>
                        <td>Guest Contact:</td>
                        <td>{$contact->phone}</td>
                        <td colspan="1"></td>
                        <td>Departure:</td>
                        <td>{$bookingInfo->to}</td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td>Nights:</td>
                        <td>{$bookingInfo->nights}</td>
                    </tr>
                </table>
                <br>
                
                <table class="invoice-details">
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Total</th>
                    </tr>
                    {$detailString}
                </table>
                <br>
                <table class="invoice-summary">
                    <tr>
                        <td colspan="1"></td>
                        <td>Total</td>
                        <td>RM {$totalOriginalSum}</td>
                    </tr>
                    <tr>
                        <td colspan="1"></td>
                        <td>Discount</td>
                        <td>- RM {$totalDiscount}</td>
                    </tr>
                    <tr>
                        <td colspan="1"></td>
                        <td>Net Amount Due</td>
                        <td>RM {$totalDiscountedSum}</td>
                    </tr>
                </table>

                <br>
                <br>

                

            </div>
        HTML;

    return $output;
}