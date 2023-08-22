<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function pricings_edit_page() {
    
    $pricingClass = new BookedInpricings();

    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['pricing_id'])) {
        $pricing_id = intval($_GET['pricing_id']);
        
        $pricing = $pricingClass->get_pricings($pricing_id);

    }

    bookedInNavigation('Pricing');
    ?>
    <!-- Form to add new pricings -->
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

        <h2>Add New pricing</h2>
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
            <input type="submit" name="add_pricing" value="Update pricing">

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
    bookInFooter();
}