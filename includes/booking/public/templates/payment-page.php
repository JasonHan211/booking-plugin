<?php



function bankTransferWhatsapp() {

    ?>

        <br>
        <br>

        <div>
            <h3>Step 1</h3>
            <p>
                Bank transfer to the following account. <br> 
                With the booking number as reference.
            </p>
            <p>
                Booking Number: <?php echo $_GET['booking_number']; ?> <br>
                <br>
                Maybank: <br>
                Account Name: Jason Han Zhi Kwang <br>
                Account Number: 106164277889 <br>
            </p>
        </div>
        <div>
            <h3>Step 2</h3>
            <p> 
                WhatsApp to the following number and send the transfer receipt.
            </p>
            <p>
                Phone Number: +6012-7037366
            </p>
        </div>
        <div>
            <h3>Step 3</h3>
            <p>
                Once completed, please wait for the confirmation on WhatsApp. Thank you!
            </p>
        </div>


    <?php

}