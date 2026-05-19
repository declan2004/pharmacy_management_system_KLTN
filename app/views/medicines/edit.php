<?php
/** * @var string $title 
 * @var string $fullName 
 * @var string $error 
 * @var array $medicine 
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
        
        /* Sidebar Styles */
        .sidebar { width: 250px; height: 100vh; background-color: var(--sidebar-bg); position: fixed; top: 0; left: 0; color: var(--text-light); z-index: 1000; }
        .sidebar-header { padding: 20px; font-size: 1.5rem; font-weight: 700; background-color: #1a252f; display: flex; align-items: center; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; color: #7f8c8d; padding: 15px 20px 5px; font-weight: bold; letter-spacing: 1px; }
        .sidebar a { padding: 12px 20px; text-decoration: none; color: #bdc3c7; display: flex; align-items: center; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--sidebar-hover); color: #ffffff; border-left: 4px solid #3498db; }
        .sidebar a i { margin-right: 15px; font-size: 1.1rem; }
        
        /* Main Content Styles */
        .main-content { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { background-color: #3498db; height: 60px; display: flex; align-items: center; justify-content: flex-end; padding: 0 20px; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .dashboard-container { padding: 30px; flex-grow: 1; }
        
        /* Form Card Styles */
        .form-card { border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: #34495e; font-size: 0.9rem; }
        .btn-navy { background-color: var(--navy-blue); color: white; padding: 10px 25px; }
        .btn-navy:hover { background-color: #0e1d30; color: white; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><i class="bi bi-capsule me-2"></i> PMS</div>
        
        <div class="nav-category">Main</div>
        <a href="/"><i class="bi bi-speedometer2"></i> Dashboard</a>
        
        <div class="nav-category">Inventory</div>
        <a href="/medicines" class="active"><i class="bi bi-box-seam"></i> Medicine Catalog</a>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5 me-2"></i><strong><?= htmlspecialchars($fullName) ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                </ul>
            </div>
        </div>

        <div class="dashboard-container">
            <div class="mb-4">
                <a href="/medicines" class="text-decoration-none text-muted fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Catalog
                </a>
            </div>

            <div class="card form-card mb-5">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-navy fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Medicine Information</h5>
                    <span class="badge bg-warning text-dark"><i class="bi bi-shield-lock me-1"></i> Code is locked</span>
                </div>
                <div class="card-body px-4 pb-4">
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger mb-4"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?></div>
                    <?php endif; ?>

                    <form action="/medicines/edit" method="POST">
                        <input type="hidden" name="medicine_id" value="<?= $medicine['medicine_id'] ?>">

                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="form-label">Medicine Code</label>
                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($medicine['medicine_code']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="medicine_name" required value="<?= htmlspecialchars($medicine['medicine_name']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Barcode <span class="text-muted fw-normal" style="font-size: 0.8rem;">(Scan or Type)</span></label>
                                <input type="text" class="form-control" name="barcode" value="<?= htmlspecialchars($medicine['barcode'] ?? '') ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Active Ingredient</label>
                                <input type="text" class="form-control" name="active_ingredient" value="<?= htmlspecialchars($medicine['active_ingredient'] ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Concentration</label>
                                <input type="text" class="form-control" name="concentration" value="<?= htmlspecialchars($medicine['concentration'] ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Unit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="unit" required value="<?= htmlspecialchars($medicine['unit']) ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Base Price (VND) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="base_price" required min="0" step="100" value="<?= round($medicine['base_price']) ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="medicine_type" required>
                                    <option value="OTC" <?= ($medicine['medicine_type'] == 'OTC') ? 'selected' : '' ?>>OTC (Non-Prescription)</option>
                                    <option value="ETC" <?= ($medicine['medicine_type'] == 'ETC') ? 'selected' : '' ?>>ETC (Prescription)</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Usage Instructions & Description</label>
                                <textarea class="form-control" name="description" rows="2"><?= htmlspecialchars($medicine['description'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <a href="/medicines" class="btn btn-light px-4 me-2">Cancel</a>
                            <button type="submit" class="btn btn-navy px-4"><i class="bi bi-save me-2"></i>Update Medicine</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    let barcodeString = "";
    let lastKeyTime = Date.now();

    window.addEventListener('keypress', function(e) {
        let currentTime = Date.now();
        
        if (currentTime - lastKeyTime > 30) {
            barcodeString = "";
        }
        
        if (e.key === "Enter" && barcodeString.length > 6) {
            e.preventDefault(); // Chặn submit form
            
            let barcodeInput = document.querySelector('input[name="barcode"]');
            if (barcodeInput) {
                barcodeInput.value = barcodeString; // Ghi đè mã vạch cũ
                
                let originalBg = barcodeInput.style.backgroundColor;
                barcodeInput.style.backgroundColor = "#e8f0fe";
                setTimeout(() => barcodeInput.style.backgroundColor = originalBg, 400);
            }
            
            barcodeString = ""; 
        } else if (e.key !== "Enter") {
            barcodeString += e.key; 
        }
        
        lastKeyTime = currentTime;
    });

    let barcodeInputNode = document.querySelector('input[name="barcode"]');
    if (barcodeInputNode) {
        barcodeInputNode.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); 
            }
        });
    }
</script>
</body>
</html>