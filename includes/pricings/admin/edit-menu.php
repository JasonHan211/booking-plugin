<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function pricings_edit_page() {
    
    $pricingClass = new BookedInpricings();

    // Handle price update
    if (isset($_POST['update_pricing'])) {
        $pricing_id = intval($_GET['pricing_id']);
        $pricing_name = sanitize_text_field($_POST['pricing_name']);
        $pricing_description = sanitize_text_field($_POST['pricing_description']);
        $pricing_structure = sanitize_text_field($_POST['pricing_structure']);
        $pricing_active = sanitize_text_field($_POST['pricing_active']);

        $pricingClass->update_pricing($pricing_id, $pricing_name, $pricing_description, $pricing_structure, $pricing_active);
    
        wp_redirect(admin_url('admin.php?page=bookedin_pricings_submenu'));
        exit;
    }

    // Handle discount update
    if (isset($_POST['update_discount'])) {
        $discount_id = intval($_GET['discount_id']);
        $discount_name = sanitize_text_field($_POST['discount_name']);
        $discount_description = sanitize_text_field($_POST['discount_description']);
        $discount_code = sanitize_text_field($_POST['discount_code']);
        $discount_type = sanitize_text_field($_POST['discount_type']);
        $discount_amount = sanitize_text_field($_POST['discount_amount']);
        $discount_start_date = sanitize_text_field($_POST['discount_start_date']);
        $discount_end_date = sanitize_text_field($_POST['discount_end_date']);
        $discount_on_type = sanitize_text_field($_POST['discount_on_type']);
        if ($discount_on_type == 'ALL') {
            $discount_on_id = null;
        } else {
            $discount_on_id = sanitize_text_field($_POST['discount_on_id']);
        }
        $discount_condition = sanitize_text_field($_POST['discount_condition']);
        $discount_auto_apply = sanitize_text_field($_POST['discount_auto_apply']);
        $discount_active = sanitize_text_field($_POST['discount_active']);

        $pricingClass->update_discount($discount_id, $discount_name, $discount_description, $discount_code, $discount_type, $discount_amount, $discount_start_date, $discount_end_date, $discount_on_type, $discount_on_id, $discount_condition, $discount_auto_apply, $discount_active);
    
        wp_redirect(admin_url('admin.php?page=bookedin_pricings_submenu'));
        exit;
    }

    
    bookedInNavigation('Pricing');

    // Get pricing to edit
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['pricing_id'])) {
        $pricing_id = intval($_GET['pricing_id']);
        
        $pricing = $pricingClass->get_pricings($pricing_id);

        ?>

            <style>
                
                #matrixRowLabels {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                margin-right: 5px;
                }
                
                #matrixContainer {
                display: flex;
                flex-direction: column;
                }
                
                .matrix-row {
                display: flex;
                margin: 5px;
                }
                
                .matrix-input {
                width: 160px;
                text-align: center;
                margin: 2px;
                }

                .container {
                margin-top: 20px;
                }

                .rotate-label {
                transform: rotate(-90deg);
                transform-origin: right center;
                white-space: nowrap;
                margin-top: auto;
                margin-bottom: auto;
                }

                .button-container {
                display: flex;
                align-items: center;
                float: right;
                }

                .button-container button {
                width: 30px;
                height: 30px;
                margin: 0px 5px;
                font-size: 15px;
                font-weight: bold;
                text-align: center;
                background-color: #e2bd84;
                color: white;
                border: none;
                cursor: pointer;
                transition: background-color 0.3s;
                }

                .button-container button:hover {
                background-color: #cca264;
                }
            </style>

            <h2>Edit Pricing</h2>
            <form id="addPricing" method="post" action="">
                <label for="pricing_name">Name:</label>
                <input type="text" name="pricing_name" value="<?php echo esc_attr($pricing['pricing_name']); ?>" required>
                <label for="pricing_description">Description:</label>
                <textarea name="pricing_description"><?php echo esc_textarea($pricing['pricing_description']); ?></textarea>
                
                <label for="pricing_active">Active:</label>
                <select name="pricing_active">
                    <option value="Y" <?php if ($pricing['pricing_active'] === 'Y') echo 'selected'; ?>>Yes</option>
                    <option value="N" <?php if ($pricing['pricing_active'] === 'N') echo 'selected'; ?>>No</option>
                </select>
                <input type="submit" name="update_pricing" value="Update pricing">

                <br><br>
                
                <h4>Price Chart</h4>
                <div class="container">
                    <div class="row">
                        <div class="col-2"></div>
                        <div class="col-10 button-container">
                            <button id="reduceColumn">-</button>
                            <label>Children</label>
                            <button id="addColumn">+</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2" style="min-height: 130px;">
                            <div class="button-container rotate-label">
                                <button id="addRow">+</button>
                                <label>Adults</label>
                                <button id="reduceRow">-</button>
                            </div>
                            
                        </div>
                        <div class="col-10">
                            <div id="matrixContainer"></div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="pricing_structure" id="pricing_structure" value="">

                <br><br>

            </form>

            <script>
                const matrixContainer = document.getElementById("matrixContainer");
                let matrix = <?php echo json_encode(json_decode($pricing['pricing_structure'])); ?> ;
                
                function createInput(row, col, value) {
                    const input = document.createElement("input");
                    input.type = "text";
                    input.className = "matrix-input";
                    input.dataset.row = row;
                    input.dataset.col = col;
                    input.value = value;
                    input.onchange = saveMatrix;
                    input.placeholder = `${row} Adult, ${col} Children`;
                    return input;
                }
                
                function renderMatrix() {
                    matrixContainer.innerHTML = "";
                    
                    for (let i = 0; i < matrix.length; i++) {
                        const rowDiv = document.createElement("div");
                        rowDiv.className = "matrix-row";
                        
                        for (let j = 0; j < matrix[i].length; j++) {
                        const input = createInput(i, j, matrix[i][j]);
                        rowDiv.appendChild(input);
                        }
                        
                        matrixContainer.appendChild(rowDiv);
                    }
                }
                
                function addRow(e) {
                    e.preventDefault();
                    matrix.push(new Array(matrix[0].length).fill(""));
                    renderMatrix();
                }
                
                function addColumn(e) {
                    e.preventDefault();
                    matrix.forEach(row => row.push(""));
                    renderMatrix();
                }

                function reduceRow(e) {
                    e.preventDefault();
                    if (matrix.length > 1) {
                        matrix.pop();
                        renderMatrix();
                    }
                }

                function reduceColumn(e) {
                    e.preventDefault();
                    if (matrix[0].length > 1) {
                        matrix.forEach(row => row.pop());
                        renderMatrix();
                    }
                }
                
                function resizeMatrix(newRowCount, newColCount) {
                    while (matrix.length < newRowCount) {
                        matrix.push(new Array(newColCount).fill(""));
                    }
                    
                    while (matrix.length > newRowCount) {
                        matrix.pop();
                    }
                    
                    for (let i = 0; i < matrix.length; i++) {
                        while (matrix[i].length < newColCount) {
                        matrix[i].push("");
                        }
                        
                        while (matrix[i].length > newColCount) {
                        matrix[i].pop();
                        }
                    }
                    
                    renderMatrix();
                }
                
                function saveMatrix(e) {
                    const inputs = document.getElementsByClassName("matrix-input");
                    
                    for (let input of inputs) {
                        const row = parseInt(input.dataset.row);
                        const col = parseInt(input.dataset.col);
                        matrix[row][col] = input.value;
                    }

                    document.getElementById("pricing_structure").value = JSON.stringify(matrix);
                    console.log("Matrix saved:", matrix);
                }
                
                document.getElementById("addRow").addEventListener("click", addRow);
                document.getElementById("addColumn").addEventListener("click", addColumn);
                document.getElementById("reduceRow").addEventListener("click", reduceRow);
                document.getElementById("reduceColumn").addEventListener("click", reduceColumn);
                
                renderMatrix();

                // On form submit, post request 
                document.querySelector("#addPricing").addEventListener("submit", function(e) {
                    e.preventDefault();

                    // Get all form data
                    const formData = new FormData(this);

                    // Get pricing structure from matrix
                    formData.append("pricing_id", <?php echo $pricing_id; ?>)
                    formData.append("pricing_structure", JSON.stringify(matrix));
                    formData.append("action", "update_pricing");

                    // Post request
                    $.ajax({
                        url: '<?php echo get_rest_url(null, 'v1/pricing/update_pricing');?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {

                            // Go back to pricings page
                            window.location.href = '<?php echo admin_url('admin.php?page=bookedin_pricings_submenu'); ?>';

                        }
                    });


                });


            </script>


        <?php

    }

    // Get discount to edit
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['discount_id'])) {
        $discount_id = intval($_GET['discount_id']);
        
        $discount = $pricingClass->get_discounts($discount_id);

        ?>

            <h2>Edit Discount</h2>
            <form id="addDiscount" method="post" action="">
                <label for="discount_name">Name:</label>
                <input type="text" name="discount_name" value="<?php echo esc_attr($discount['discount_name']); ?>" required>
                <label for="discount_description">Description:</label>
                <textarea name="discount_description"><?php echo esc_textarea($discount['discount_description']); ?></textarea>
                <label for="discount_code">Code:</label>
                <input type="text" name="discount_code" value="<?php echo esc_attr($discount['discount_code']); ?>" required>
                <label for="discount_type">Type:</label>
                <select name="discount_type">
                    <option value="percentage" <?php if ($discount['discount_type'] === 'percentage') echo 'selected'; ?>>Percentage</option>
                    <option value="fixed" <?php if ($discount['discount_type'] === 'fixed') echo 'selected'; ?>>Fixed</option>
                </select>
                <label for="discount_amount">Amount:</label>
                <input type="number" name="discount_amount" value="<?php echo esc_attr($discount['discount_amount']); ?>" required>
                <label for="discount_start_date">Start Date:</label>
                <input type="date" name="discount_start_date" value="<?php echo esc_attr($discount['discount_start_date']); ?>">
                <label for="discount_end_date">End Date:</label>
                <input type="date" name="discount_end_date" value="<?php echo esc_attr($discount['discount_end_date']); ?>">
                <label for="discount_on_type">Type On:</label>
                <select name="discount_on_type">
                    <option value="All" <?php if ($discount['discount_on_type'] === 'all') echo 'selected'; ?>>All</option>
                    <option value="Resources" <?php if ($discount['discount_on_type'] === 'resources') echo 'selected'; ?>>Resources</option>
                    <option value="Addon" <?php if ($discount['discount_on_type'] === 'addon') echo 'selected'; ?>>Addon</option>
                </select>
                <label for="discount_on_id">ID On:</label>
                <select name="discount_on_id" disabled>
                    <option value="null">N/A</option>
                </select>
                <label for="discount_condition">Condition:</label>
                <select name="discount_condition">
                    <option value="greater_than" <?php if ($discount['discount_condition'] === 'greater_than') echo 'selected'; ?>>Greater than</option>
                    <option value="less_than" <?php if ($discount['discount_condition'] === 'less_than') echo 'selected'; ?>>Less than</option>
                </select>
                <label for="discount_auto_apply">Auto Apply:</label>
                <select name="discount_auto_apply">
                    <option value="Y" <?php if ($discount['discount_auto_apply'] === 'Y') echo 'selected'; ?>>Yes</option>
                    <option value="N" <?php if ($discount['discount_auto_apply'] === 'N') echo 'selected'; ?>>No</option>
                </select>
                <label for="discount_active">Active:</label>
                <select name="discount_active">
                    <option value="Y" <?php if ($discount['discount_active'] === 'Y') echo 'selected'; ?>>Yes</option>
                    <option value="N" <?php if ($discount['discount_active'] === 'N') echo 'selected'; ?>>No</option>
                </select>
                <input type="submit" name="update_discount" value="Update discount">
            </form>

            <script>
                jQuery(document).ready(function($) {
                    $('select[name="discount_on_type"]').change(function() {
                        var discount_on_type = $(this).val();
                        var discount_on_id = $('select[name="discount_on_id"]');
                        if (discount_on_type == 'Resources') {

                            $.ajax({
                                url: '<?php echo get_rest_url(null, 'v1/resources/get_resources');?>',
                                type: 'POST',
                                data: {
                                    action: 'get_resources',
                                },
                                success: function (data) {
                                    
                                    discount_on_id.prop('disabled', false);
                                    discount_on_id.empty();

                                    resources = data.resources;

                                    discount_on_id.append('<option value="All"> All </option>');
                                    resources.forEach(function(resource) {
                                        discount_on_id.append('<option value="' + resource.id + '">' + resource.resource_name + '</option>');
                                    });

                                }
                            });
                            
                        } else if (discount_on_type == 'Addon') {

                            $.ajax({
                                url: '<?php echo get_rest_url(null, 'v1/addons/get_addons');?>',
                                type: 'POST',
                                data: {
                                    action: 'get_addons',
                                },
                                success: function (data) {
                                    
                                    discount_on_id.prop('disabled', false);
                                    discount_on_id.empty();

                                    addons = data.addons;

                                    discount_on_id.append('<option value="All"> All </option>');
                                    addons.forEach(function(addon) {
                                        discount_on_id.append('<option value="' + addon.id + '">' + addon.addon_name + '</option>');
                                    });

                                }
                            });

                        } else {
                            discount_on_id.prop('disabled', true);
                            discount_on_id.empty();
                            discount_on_id.append('<option value="null">N/A</option>');
                        }
                    });
                });
            </script>

        <?php

    }

    
    bookInFooter();
}