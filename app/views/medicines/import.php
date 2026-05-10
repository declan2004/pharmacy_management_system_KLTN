<?php
/** * @var string $title 
 * @var string $fullName 
 * @var string $error 
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
        .sidebar { width: 250px; height: 100vh; background-color: var(--sidebar-bg); position: fixed; top: 0; left: 0; color: var(--text-light); z-index: 1000; }
        .sidebar-header { padding: 20px; font-size: 1.5rem; font-weight: 700; background-color: #1a252f; display: flex; align-items: center; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; color: #7f8c8d; padding: 15px 20px 5px; font-weight: bold; letter-spacing: 1px; }
        .sidebar a { padding: 12px 20px; text-decoration: none; color: #bdc3c7; display: flex; align-items: center; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--sidebar-hover); color: #ffffff; border-left: 4px solid #3498db; }
        .sidebar a i { margin-right: 15px; font-size: 1.1rem; }
        .main-content { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { background-color: #3498db; height: 60px; display: flex; align-items: center; justify-content: flex-end; padding: 0 20px; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .dashboard-container { padding: 30px; flex-grow: 1; display: flex; justify-content: center; align-items: flex-start; }
        .form-card { border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 600px; margin-top: 20px; }
        .btn-navy { background-color: var(--navy-blue); color: white; }
        .btn-navy:hover { background-color: #0e1d30; color: white; }
        .upload-box { border: 2px dashed #bdc3c7; border-radius: 10px; padding: 40px 20px; text-align: center; background-color: #f8f9fa; transition: 0.3s; cursor: pointer; }
        .upload-box:hover { border-color: #3498db; background-color: #eaf4fc; }
        .upload-box i { font-size: 3rem; color: #3498db; margin-bottom: 15px; }
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

        <div class="dashboard-container flex-column align-items-center">
            <div class="w-100 mb-2" style="max-width: 600px;">
                <a href="/medicines" class="text-decoration-none text-muted fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Catalog
                </a>
            </div>

            <div class="card form-card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h4 class="text-navy fw-bold">Mass Import Medicines</h4>
                        <p class="text-muted">Upload a CSV file to add multiple medicines at once.</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?></div>
                    <?php endif; ?>

                    <form action="/medicines/import" method="POST" enctype="multipart/form-data">
                        <div class="upload-box mb-4" onclick="document.getElementById('csv_file').click()">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <h6 class="fw-bold text-dark">Click to select CSV file</h6>
                            <p class="text-muted small mb-0">Format supported: .csv (Comma delimited)</p>
                            <input type="file" id="csv_file" name="csv_file" accept=".csv" required style="display: none;" onchange="updateFileName(this)">
                        </div>
                        <div id="fileNameDisplay" class="text-center text-primary fw-bold mb-4" style="display: none;"></div>

                        <div class="alert alert-info bg-light border-info small">
                            <i class="bi bi-info-circle-fill text-info me-2"></i>
                            <strong>Tip:</strong> Download the current catalog via <a href="/medicines/export" class="alert-link">Export CSV</a> to get the exact file template.
                        </div>

                        <button type="submit" class="btn btn-navy w-100 py-2 fw-bold"><i class="bi bi-upload me-2"></i> Start Import Process</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateFileName(input) {
            const display = document.getElementById('fileNameDisplay');
            if (input.files && input.files[0]) {
                display.innerText = "Selected file: " + input.files[0].name;
                display.style.display = "block";
            } else {
                display.style.display = "none";
            }
        }
    </script>
</body>
</html>