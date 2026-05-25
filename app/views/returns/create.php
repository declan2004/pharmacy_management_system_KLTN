<?php
/** * @var string $title | @var string $fullName | @var array $batches | @var string $error */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --sidebar-bg: #2c3e50; --sidebar-hover: #34495e; --main-bg: #f4f7f6; --text-light: #ecf0f1; --navy-blue: #152b48; }
        body { background-color: var(--main-bg); font-family: 'Segoe UI', Tahoma, sans-serif; overflow-x: hidden; }
        
        .sidebar { width: 250px; height: 100vh; background-color: var(--sidebar-bg); position: fixed; top: 0; left: 0; color: var(--text-light); z-index: 1000; }
        .sidebar-header { padding: 20px; font-size: 1.5rem; font-weight: 700; background-color: #1a252f; display: flex; align-items: center; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; color: #7f8c8d; padding: 15px 20px 5px; font-weight: bold; letter-spacing: 1px; }
        .sidebar a { padding: 12px 20px; text-decoration: none; color: #bdc3c7; display: flex; align-items: center; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--sidebar-hover); color: #ffffff; border-left: 4px solid #3498db; }
        .sidebar a i { margin-right: 15px; font-size: 1.1rem; }
        
        /* FIX LAYOUT: Main Content wrapper */
        .main-content { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }
        .dashboard-container { padding: 30px; flex-grow: 1; }
        .form-card { border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><i class="bi bi-capsule me-2"></i> PMS</div>
        
        <div class="nav-category">Main</div>
        <a href="/"><i class="bi bi-speedometer2"></i> Dashboard</a>
        
        <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): ?>
            <div class="nav-category">Sales & Pos</div>
            <a href="/pos"><i class="bi bi-cart-plus"></i> Create Invoice</a>
            <a href="/invoices"><i class="bi bi-receipt"></i> Selling History</a>
        <?php endif; ?>
        
        <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3): ?>
            <div class="nav-category">Inventory</div>
            <a href="/medicines"><i class="bi bi-box-seam"></i> Medicine Catalog</a>
            <a href="/inventory"><i class="bi bi-stack"></i> Manage Inventory</a>
            <a href="/imports"><i class="bi bi-truck"></i> Import Receipt</a>
            <a href="/returns" class="active"><i class="bi bi-arrow-return-left"></i> Return Orders</a>
        <?php endif; ?>
        
        <?php if ($_SESSION['role_id'] == 1): ?>
            <div class="nav-category">System</div>
            <a href="/reports"><i class="bi bi-bar-chart-line"></i> Analytics</a>
            <a href="/users"><i class="bi bi-people"></i> Manage Users</a>
        <?php endif; ?>
    </div>

    <div class="main-content">
        <div class="dashboard-container">
            <div class="mb-4">
                <a href="/returns" class="text-decoration-none text-muted fw-bold"><i class="bi bi-arrow-left me-1"></i> Back to Return Orders</a>
                <h3 class="fw-bold text-navy mt-2">Create Return Order</h3>
                <p class="text-muted small">Return expired or damaged items to supplier. This action will deduct stock immediately.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mb-4 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?></div>
            <?php endif; ?>

            <form action="/returns/create" method="POST" id="returnForm">
                <div class="card form-card mb-4">
                    <div class="card-body px-4 pb-4">
                        <label class="form-label fw-bold text-secondary small mt-3">General Note / Reason for entire receipt</label>
                        <textarea class="form-control" name="note" rows="2" placeholder="E.g. Monthly return of near-expiry items to supplier..."></textarea>
                    </div>
                </div>

                <div class="card form-card mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-navy fw-bold"><i class="bi bi-box-arrow-up me-2"></i>Items to Return</h5>
                        <button type="button" class="btn btn-sm btn-outline-danger fw-bold" onclick="addRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add Row
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle" id="detailsTable">
                                <thead class="table-light text-uppercase" style="font-size: 0.85rem;">
                                    <tr>
                                        <th style="width: 40%;" class="ps-4">Select Batch (Medicine - Lot No) <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Current Stock</th>
                                        <th style="width: 15%;">Return Qty <span class="text-danger">*</span></th>
                                        <th style="width: 25%;">Specific Reason</th>
                                        <th style="width: 5%;" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <select class="form-select batch-select" name="batch_id[]" required onchange="updateMaxQty(this)">
                                                <option value="" data-max="0" data-med-id="0">-- Select Batch from Inventory --</option>
                                                <?php foreach($batches as $b): ?>
                                                    <option value="<?= $b['batch_id'] ?>" data-max="<?= $b['quantity'] ?>" data-med-id="<?= $b['medicine_id'] ?? '' ?>">
                                                        <?= htmlspecialchars($b['medicine_name']) ?> (LOT: <?= $b['batch_number'] ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control bg-light stock-display fw-bold text-center" readonly value="0"></td>
                                        <td><input type="number" class="form-control qty-input text-center" name="quantity[]" required min="1" value="1"></td>
                                        <td><input type="text" class="form-control" name="return_reason[]" placeholder="E.g. Expired, Damaged"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm text-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <a href="/returns" class="btn btn-light px-4 me-2">Cancel</a>
                    <button type="submit" class="btn btn-danger px-5 py-2 fw-bold shadow-sm"><i class="bi bi-check2-circle me-2"></i>Submit Return</button>
                </div>
            </form>
        </div>
    </div> <table style="display: none;"><tbody id="rowTemplate">
        <tr>
            <td class="ps-4 py-3">
                <select class="form-select batch-select" name="batch_id[]" required onchange="updateMaxQty(this)">
                    <option value="" data-max="0" data-med-id="0">-- Select Batch from Inventory --</option>
                    <?php foreach($batches as $b): ?>
                        <option value="<?= $b['batch_id'] ?>" data-max="<?= $b['quantity'] ?>" data-med-id="<?= $b['medicine_id'] ?? '' ?>">
                            <?= htmlspecialchars($b['medicine_name']) ?> (LOT: <?= $b['batch_number'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="text" class="form-control bg-light stock-display fw-bold text-center" readonly value="0"></td>
            <td><input type="number" class="form-control qty-input text-center" name="quantity[]" required min="1" value="1"></td>
            <td><input type="text" class="form-control" name="return_reason[]" placeholder="E.g. Expired, Damaged"></td>
            <td class="text-center"><button type="button" class="btn btn-sm text-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
        </tr>
    </tbody></table>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addRow() {
            const tbody = document.querySelector('#detailsTable tbody');
            tbody.insertAdjacentHTML('beforeend', document.querySelector('#rowTemplate').innerHTML);
        }
        
        function removeRow(btn) {
            const tbody = document.querySelector('#detailsTable tbody');
            if (tbody.children.length > 1) btn.closest('tr').remove();
        }
        
        function updateMaxQty(selectElement) {
            const row = selectElement.closest('tr');
            const maxQty = selectElement.options[selectElement.selectedIndex].getAttribute('data-max');
            row.querySelector('.stock-display').value = maxQty;
            const qtyInput = row.querySelector('.qty-input');
            qtyInput.max = maxQty; 
            if(parseInt(qtyInput.value) > parseInt(maxQty)) qtyInput.value = maxQty;
        }

        // BARCODE SCANNER 
        let barcodeString = "";
        let lastKeyTime = Date.now();

        window.addEventListener('keypress', function(e) {
            let currentTime = Date.now();
            if (currentTime - lastKeyTime > 150) barcodeString = "";
            
            if (e.key === "Enter" && barcodeString.length > 6) {
                e.preventDefault(); 
                processReturnBarcode(barcodeString);
                barcodeString = ""; 
            } else if (e.key !== "Enter") {
                barcodeString += e.key; 
            }
            lastKeyTime = currentTime;
        });

        function processReturnBarcode(barcode) {
            fetch('/pos/apiScanBarcode?barcode=' + barcode)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        autoSelectReturnBatch(data.medicine.medicine_id);
                    } else {
                        alert("Error: Scanned barcode not found in catalog!");
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function autoSelectReturnBatch(medId) {
            const tbody = document.querySelector('#detailsTable tbody');
            let selects = tbody.querySelectorAll('.batch-select');
            let targetSelect = null;

            // 1. Tìm dòng trống
            for(let i = 0; i < selects.length; i++) {
                if(selects[i].value === "") {
                    targetSelect = selects[i];
                    break;
                }
            }

            // 2. Thêm dòng mới nếu hết chỗ
            if(!targetSelect) {
                addRow();
                selects = tbody.querySelectorAll('.batch-select');
                targetSelect = selects[selects.length - 1];
            }

            // 3. Quét qua các thẻ <option> để tìm Lô thuốc khớp với ID thuốc vừa quét
            let optionFound = false;
            let options = targetSelect.options;
            
            for(let i = 0; i < options.length; i++) {
                // Sử dụng data-med-id để khớp dữ liệu
                if (options[i].getAttribute('data-med-id') == medId) {
                    targetSelect.selectedIndex = i;
                    optionFound = true;
                    break; 
                }
            }

            if (optionFound) {
                updateMaxQty(targetSelect); // Cập nhật số lượng tồn kho tự động
                
                let row = targetSelect.closest('tr');
                let originalBg = row.style.backgroundColor;
                row.style.backgroundColor = "#ffebee"; 
                setTimeout(() => row.style.backgroundColor = originalBg, 500);
                
                // Focus vào ô số lượng trả
                let qtyInput = row.querySelector('.qty-input');
                if (qtyInput) qtyInput.focus();
            } else {
                alert("Error: Found the medicine, but there are no matching batches in stock to return!");
                if (targetSelect.value === "") targetSelect.closest('tr').remove(); // Dọn dẹp dòng trống vừa sinh ra
            }
        }
        
        // Chặn phím Enter trong bảng
        document.querySelector('#detailsTable').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') e.preventDefault();
        });
    </script>
</body>
</html>