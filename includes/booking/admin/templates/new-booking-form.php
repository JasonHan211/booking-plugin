<?php

function newBookingForm($resources) {
    ?>
        <div class="container">
            <form method="post" action="">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div id="booking_dates" data-date=""></div>
                            <input type="hidden" name="booking_date_from">
                            <input type="hidden" name="booking_date_to">
                        </div>
                        <div class="mb-3">
                            <label for="booking_resource" class="form-label">Resource:</label>
                            <select class="form-select" name="booking_resource">
                                <?php if (sizeof($resources) == 0): ?>
                                    <option value="">Please create a resource</option>
                                <?php else: ?>
                                    <?php foreach ($resources as $resource): ?>
                                        <option value="<?php echo $resource['id']; ?>"><?php echo $resource['resource_name']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="booking_description" class="form-label">Description:</label>
                            <textarea class="form-control" name="booking_description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="booking_paid" class="form-label">Paid:</label>
                            <select class="form-select" name="booking_paid">
                                <option value="NO">No</option>
                                <option value="YES">Yes</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="booking_adults" class="form-label">Adults:</label>
                            <input type="number" class="form-control" name="booking_adults" value="0" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="booking_children" class="form-label">Children:</label>
                            <input type="number" class="form-control" name="booking_children" value="0" min="0" required>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">

                        <h3>User Details</h3>

                        <div class="mb-3">
                            <label for="booking_user" class="form-label">Name:</label>
                            <input type="text" class="form-control" name="booking_user" required>
                        </div>
                        <div class="mb-3">
                            <label for="booking_email" class="form-label">Email:</label>
                            <input type="email" class="form-control" name="booking_email" required>
                        </div>
                        <div class="mb-3">
                            <label for="booking_phone" class="form-label">Phone:</label>
                            <input type="text" class="form-control" name="booking_phone" required>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="add_booking" class="btn btn-primary">Add Booking</button>
            </form>
        </div>
    <?php
}
