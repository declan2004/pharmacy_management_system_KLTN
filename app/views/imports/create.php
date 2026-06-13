<?php
/** * @var string $title 
 * @var string $fullName 
 * @var array $medicines 
 * @var string $error
 * @var string $nextBatchNo
 */
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
        
        /* Sidebar (Thu gọn) */
        .sidebar { width: 250px; height: 100vh; background-color: var(--sidebar-bg); position: fixed; top: 0; left: 0; color: var(--text-light); z-index: 1000; }
        .sidebar-header { padding: 20px; font-size: 1.5rem; font-weight: 700; background-color: #1a252f; display: flex; align-items: center; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; color: #7f8c8d; padding: 15px 20px 5px; font-weight: bold; letter-spacing: 1px; }
        .sidebar a { padding: 12px 20px; text-decoration: none; color: #bdc3c7; display: flex; align-items: center; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--sidebar-hover); color: #ffffff; border-left: 4px solid #3498db; }
        .sidebar a i { margin-right: 15px; font-size: 1.1rem; }
        
        /* Main */
        .main-content { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { background-color: #3498db; height: 60px; display: flex; align-items: center; justify-content: flex-end; padding: 0 20px; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .dashboard-container { padding: 30px; flex-grow: 1; }
        
        .form-card { border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .btn-navy { background-color: var(--navy-blue); color: white; }
        .btn-navy:hover { background-color: #0e1d30; color: white; }
        
        /* Table Input Styles */
        .table-inputs th { background-color: #f8f9fa; font-size: 0.85rem; color: #495057; text-transform: uppercase; }
        .table-inputs td { vertical-align: middle; }
        .table-inputs .form-control, .table-inputs .form-select { border-radius: 4px; border: 1px solid #ced4da; }
        .table-inputs .form-control:focus { box-shadow: none; border-color: #3498db; }
        .total-amount-box { background-color: #eaf4fc; border: 1px dashed #3498db; border-radius: 8px; padding: 15px 20px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><i class="bi bi-capsule me-2"></i> PMS</div>
        <div class="nav-category">Main</div>
        <a href="/"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <div class="nav-category">Inventory</div>
        <a href="/imports" class="active"><i class="bi bi-truck"></i> Import Receipt</a>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5 me-2"></i><strong><?= htmlspecialchars($fullName) ?></strong>
                </a>
            </div>
        </div>

        <div class="dashboard-container">
            <div class="mb-4">
                <a href="/imports" class="text-decoration-none text-muted fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Receipts
                </a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mb-4 shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="/imports/create" method="POST" id="importForm">
                
                <div class="card form-card mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h5 class="mb-0 text-navy fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Receipt Information</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="supplier_name" required placeholder="Enter supplier company name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">Note / Reference <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="note" required placeholder="E.g. Invoice #12345">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card form-card mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-navy fw-bold"><i class="bi bi-box-seam me-2"></i>Imported Items</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold" onclick="addRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add Row
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-inputs mb-0" id="detailsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">Medicine <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Batch No. <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Expiry Date <span class="text-danger">*</span></th>
                                        <th style="width: 12%;">Qty <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Import Price <span class="text-danger">*</span></th>
                                        <th style="width: 13%; text-align: right;">Subtotal</th>
                                        <th style="width: 5%; text-align: center;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-select select-medicine" name="medicine_id[]" required>
                                                <option value="">-- Select Medicine --</option>
                                                <?php foreach($medicines as $med): ?>
                                                    <option value="<?= $med['medicine_id'] ?>">
                                                        <?= htmlspecialchars($med['medicine_code'] . ' - ' . $med['medicine_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control bg-light fw-bold text-primary" name="batch_number[]" value="<?= isset($nextBatchNo) ? htmlspecialchars($nextBatchNo) : '' ?>" readonly>
                                        </td>
                                        <td><input type="date" class="form-control" name="expiry_date[]" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required></td>
                                        <td><input type="number" class="form-control input-qty" name="quantity[]" required min="1" value="1" oninput="calcTotals()"></td>
                                        <td><input type="number" class="form-control input-price" name="import_price[]" required min="0" step="100" placeholder="0" oninput="calcTotals()"></td>
                                        <td class="text-end fw-bold text-primary align-middle subtotal-text">0 ₫</td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-sm text-danger" onclick="removeRow(this)" title="Remove"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="total-amount-box">
                        <span class="text-muted fw-bold me-3">TOTAL AMOUNT:</span>
                        <span class="fs-4 fw-bold text-navy" id="grandTotalText">0 ₫</span>
                    </div>
                    <div>
                        <a href="/imports" class="btn btn-light px-4 me-2">Cancel</a>
                        <button type="submit" class="btn btn-navy px-5 py-2 fw-bold"><i class="bi bi-check2-circle me-2"></i>Submit Receipt</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <table style="display: none;">
        <tbody id="rowTemplate">
            <tr>
                <td>
                    <select class="form-select select-medicine" name="medicine_id[]" required>
                        <option value="">-- Select Medicine --</option>
                        <?php foreach($medicines as $med): ?>
                            <option value="<?= $med['medicine_id'] ?>"><?= htmlspecialchars($med['medicine_code'] . ' - ' . $med['medicine_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control bg-light fw-bold text-primary" name="batch_number[]" value="<?= isset($nextBatchNo) ? htmlspecialchars($nextBatchNo) : '' ?>" readonly>
                </td>
                <td><input type="date" class="form-control" name="expiry_date[]" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required></td>
                <td><input type="number" class="form-control input-qty" name="quantity[]" required min="1" value="1" oninput="calcTotals()"></td>
                <td><input type="number" class="form-control input-price" name="import_price[]" required min="0" step="100" placeholder="0" oninput="calcTotals()"></td>
                <td class="text-end fw-bold text-primary align-middle subtotal-text">0 ₫</td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm text-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hàm thêm dòng mới
        function addRow() {
            const tbody = document.querySelector('#detailsTable tbody');
            const template = document.querySelector('#rowTemplate').innerHTML;
            tbody.insertAdjacentHTML('beforeend', template);
        }

        // Hàm xóa dòng
        function removeRow(btn) {
            const tbody = document.querySelector('#detailsTable tbody');
            if (tbody.children.length > 1) {
                btn.closest('tr').remove();
                calcTotals(); // Tính lại tổng tiền sau khi xóa
            } else {
                alert("Receipt must contain at least one item.");
            }
        }

        // Hàm tính Subtotal và Total Amount tự động (Real-time)
        function calcTotals() {
            let grandTotal = 0;
            const rows = document.querySelectorAll('#detailsTable tbody tr');
            
            rows.forEach(row => {
                const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
                const price = parseFloat(row.querySelector('.input-price').value) || 0;
                const subtotal = qty * price;
                
                row.querySelector('.subtotal-text').innerText = subtotal.toLocaleString('vi-VN') + ' ₫';
                grandTotal += subtotal;
            });
            
            document.getElementById('grandTotalText').innerText = grandTotal.toLocaleString('vi-VN') + ' ₫';
        }

        // BARCODE SCANNER INTEGRATION 
        let barcodeString = "";
        let lastKeyTime = Date.now();

        // 1. Listen for scanner input
        window.addEventListener('keypress', function(e) {
            let currentTime = Date.now();
            let timeDiff = currentTime - lastKeyTime;
            
            // LOG RA XEM TỐC ĐỘ GÕ VÀ KÝ TỰ LÀ GÌ
            console.log(`Ký tự: ${e.key} | Độ trễ: ${timeDiff}ms`);
            
            if (timeDiff > 150) {
                barcodeString = "";
            }
            
            if (e.key === "Enter") {
                console.log("-> BẮT ĐƯỢC ENTER! Chuỗi hiện tại: ", barcodeString); // Xem chuỗi có bị rỗng không
                if (barcodeString.length > 6) {
                    e.preventDefault(); 
                    console.log("-> ĐANG GỌI API API..."); 
                    processImportBarcode(barcodeString);
                } else {
                    console.log("-> Chuỗi quá ngắn, bỏ qua!");
                }
                barcodeString = ""; 
            } else if (e.key !== "Enter") {
                barcodeString += e.key; 
            }
            
            lastKeyTime = currentTime;
        });

        // 2. Fetch API to get Medicine ID
        function processImportBarcode(barcode) {
            fetch('/pos/apiScanBarcode?barcode=' + barcode)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        let medId = data.medicine.medicine_id;
                        autoSelectMedicineInRow(medId);
                    } else {
                        alert("Error: Scanned barcode does not match any medicine in the catalog!");
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // 3. Auto-fill logic
        function autoSelectMedicineInRow(medicineId) {
            const tbody = document.querySelector('#detailsTable tbody');
            let selects = tbody.querySelectorAll('.select-medicine');
            let targetSelect = null;

            // Step 3.1: Find the first empty row (where no medicine is selected)
            for(let i = 0; i < selects.length; i++) {
                if(selects[i].value === "") {
                    targetSelect = selects[i];
                    break;
                }
            }

            // Step 3.2: If no empty row found, automatically add a new row
            if(!targetSelect) {
                addRow(); // Call your existing function
                selects = tbody.querySelectorAll('.select-medicine');
                targetSelect = selects[selects.length - 1]; // Get the newly added select
            }

            // Step 3.3: Change the select value to match the scanned medicine
            targetSelect.value = medicineId;

            // Step 3.4: UX Enhancements (Highlight row and move focus)
            let row = targetSelect.closest('tr');
            
            // Brief highlight effect
            let originalBg = row.style.backgroundColor;
            row.style.backgroundColor = "#e8f0fe";
            setTimeout(() => row.style.backgroundColor = originalBg, 500);
            
            // Automatically focus the Batch Number input for fast typing
            let batchInput = row.querySelector('input[name="batch_number[]"]');
            if (batchInput) {
                batchInput.focus();
            }
        }

        // 4. Block Enter key specifically on input fields 
        document.querySelector('#detailsTable').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>