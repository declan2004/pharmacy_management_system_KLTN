<?php
/** * @var string $title | @var string $fullName | @var array $batches | @var string $error */

$uniqueMeds = [];
if (!empty($batches)) {
    foreach($batches as $b) {
        if (!isset($uniqueMeds[$b['medicine_id']])) {
            $uniqueMeds[$b['medicine_id']] = $b['medicine_name'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Create Return Order' ?></title>
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
        
        /* LAYOUT & NAVBAR */
        .main-content { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { background-color: #3498db; height: 60px; min-height: 60px; display: flex; align-items: center; justify-content: flex-end; padding: 0 20px; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 10; }
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
        <div class="top-navbar">
            <div class="dropdown">
                <?php 
                    $displayRole = 'Unknown';
                    if (isset($_SESSION['role_id'])) {
                        if ($_SESSION['role_id'] == 1) $displayRole = 'Manager';
                        elseif ($_SESSION['role_id'] == 2) $displayRole = 'Pharmacist';
                        elseif ($_SESSION['role_id'] == 3) $displayRole = 'Inventory Staff';
                    }
                ?>
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-5 me-2"></i>
                    <strong><?= htmlspecialchars($fullName ?? 'Staff') ?> (<?= $displayRole ?>)</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item py-2" href="/profile"><i class="bi bi-person me-2 text-muted"></i> Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</a></li>
                </ul>
            </div>
        </div>

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
                                        <th style="width: 25%;" class="ps-4">Medicine <span class="text-danger">*</span></th>
                                        <th style="width: 20%;">Batch No. <span class="text-danger">*</span></th>
                                        <th style="width: 10%;">Stock</th>
                                        <th style="width: 15%;">Return Qty <span class="text-danger">*</span></th>
                                        <th style="width: 25%;">Specific Reason</th>
                                        <th style="width: 5%;" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <!-- CỘT 1: CHỌN THUỐC -->
                                        <td class="ps-4 py-3">
                                            <select class="form-select medicine-select" required onchange="loadBatches(this)">
                                                <option value="">-- Select Medicine --</option>
                                                <?php foreach($uniqueMeds as $id => $name): ?>
                                                    <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <!-- CỘT 2: CHỌN LÔ (Tự động load theo thuốc) -->
                                        <td>
                                            <select class="form-select batch-select" name="batch_id[]" required disabled onchange="updateMaxQty(this)">
                                                <option value="">-- Select Batch --</option>
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
    </div> 
    
    <table style="display: none;"><tbody id="rowTemplate">
        <tr>
            <td class="ps-4 py-3">
                <select class="form-select medicine-select" required onchange="loadBatches(this)">
                    <option value="">-- Select Medicine --</option>
                    <?php foreach($uniqueMeds as $id => $name): ?>
                        <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <select class="form-select batch-select" name="batch_id[]" required disabled onchange="updateMaxQty(this)">
                    <option value="">-- Select Batch --</option>
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
        // Data động từ Server truyền sang JS
        const allBatchesData = <?= json_encode($batches ?? []) ?>;

        function addRow() {
            const tbody = document.querySelector('#detailsTable tbody');
            tbody.insertAdjacentHTML('beforeend', document.querySelector('#rowTemplate').innerHTML);
        }
        
        function removeRow(btn) {
            const tbody = document.querySelector('#detailsTable tbody');
            if (tbody.children.length > 1) btn.closest('tr').remove();
        }

        // Load danh sách lô tương ứng khi chọn Thuốc
        function loadBatches(medicineSelect) {
            const row = medicineSelect.closest('tr');
            const batchSelect = row.querySelector('.batch-select');
            const medId = medicineSelect.value;
            
            // Reset các ô liên quan
            row.querySelector('.stock-display').value = '0';
            const qtyInput = row.querySelector('.qty-input');
            qtyInput.max = '';
            qtyInput.value = '1';
            
            batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';
            
            if(!medId) {
                batchSelect.disabled = true;
                return;
            }
            
            batchSelect.disabled = false;
            
            // Lọc ra các Lô thuộc về Thuốc đã chọn
            const filteredBatches = allBatchesData.filter(b => b.medicine_id == medId);
            
            filteredBatches.forEach(b => {
                batchSelect.insertAdjacentHTML('beforeend', `<option value="${b.batch_id}" data-max="${b.quantity}">LOT: ${b.batch_number} (Exp: ${b.expiry_date})</option>`);
            });
        }
        
        function updateMaxQty(selectElement) {
            const row = selectElement.closest('tr');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            
            if(selectElement.value === "") {
                row.querySelector('.stock-display').value = '0';
                return;
            }

            const maxQty = selectedOption.getAttribute('data-max');
            row.querySelector('.stock-display').value = maxQty;
            const qtyInput = row.querySelector('.qty-input');
            qtyInput.max = maxQty; 
            
            if(parseInt(qtyInput.value) > parseInt(maxQty)) {
                qtyInput.value = maxQty;
            }
        }

        // BARCODE SCANNER LOGIC 
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
                        autoSelectReturnMedicine(data.medicine.medicine_id);
                    } else {
                        alert("Error: Scanned barcode not found in catalog!");
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function autoSelectReturnMedicine(medId) {
            const tbody = document.querySelector('#detailsTable tbody');
            let selects = tbody.querySelectorAll('.medicine-select');
            let targetSelect = null;

            // Tìm dòng trống
            for(let i = 0; i < selects.length; i++) {
                if(selects[i].value === "") {
                    targetSelect = selects[i];
                    break;
                }
            }

            // Thêm dòng mới nếu không có dòng trống
            if(!targetSelect) {
                addRow();
                selects = tbody.querySelectorAll('.medicine-select');
                targetSelect = selects[selects.length - 1];
            }

            // Quét tìm Medicine ID
            let optionFound = false;
            for(let i = 0; i < targetSelect.options.length; i++) {
                if (targetSelect.options[i].value == medId) {
                    targetSelect.selectedIndex = i;
                    optionFound = true;
                    break; 
                }
            }

            if (optionFound) {
                // Gọi hàm load Lô thuốc
                loadBatches(targetSelect);
                
                let row = targetSelect.closest('tr');
                let originalBg = row.style.backgroundColor;
                row.style.backgroundColor = "#ffebee"; 
                setTimeout(() => row.style.backgroundColor = originalBg, 500);
                
                // Focus thẳng vào ô chọn Lô (Batch) để người dùng thao tác bước tiếp theo
                let batchSelect = row.querySelector('.batch-select');
                if (batchSelect) batchSelect.focus();
            } else {
                alert("Error: Found the medicine, but there are no matching batches in stock to return!");
                if (targetSelect.value === "") targetSelect.closest('tr').remove();
            }
        }
        
        // Chặn phím Enter submit form ngoài ý muốn
        document.querySelector('#detailsTable').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') e.preventDefault();
        });
    </script>
</body>
</html>