<?php
/** * @var string $title 
 * @var string $fullName 
 * @var array $medicines 
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
        .dashboard-container { padding: 25px; flex-grow: 1; }
        
        /* Table Styles */
        .custom-table th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 15px; border-bottom: 2px solid #e9ecef; }
        .custom-table td { padding: 15px; vertical-align: middle; color: #333; }
        .action-btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s; }
        .action-btn:hover { transform: translateY(-2px); }
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
            <a href="/medicines" class="active"><i class="bi bi-box-seam"></i> Medicine Catalog</a>
            <a href="/inventory"><i class="bi bi-stack"></i> Manage Inventory</a>
            <a href="/imports"><i class="bi bi-truck"></i> Import Receipt</a>
            <a href="/returns"><i class="bi bi-arrow-return-left"></i> Return Orders</a>
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
                    if ($_SESSION['role_id'] == 1) $displayRole = 'Manager';
                    elseif ($_SESSION['role_id'] == 3) $displayRole = 'Inventory Staff';
                ?>
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-5 me-2"></i>
                    <strong><?= htmlspecialchars($fullName) ?> (<?= $displayRole ?>)</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item py-2" href="/profile"><i class="bi bi-person me-2"></i> Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</a></li>
                </ul>
            </div>
        </div>

        <div class="dashboard-container">
            
            <?php if (isset($_SESSION['import_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> 
                    Successfully imported <strong><?= $_SESSION['import_success'] ?></strong> medicines to catalog.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['import_success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['import_errors'])): ?>
                <div class="alert alert-warning alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Import Warnings:</h6>
                    <ul class="mb-0 mt-2" style="font-size: 0.85rem;">
                        <?php foreach ($_SESSION['import_errors'] as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['import_errors']); ?>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="text-navy fw-bold mb-0">Medicine Catalog</h3>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.9rem;">Manage master data of all medicines</p>
                </div>
                <div>
                    <div class="btn-group me-2">
                        <a href="/medicines/import" class="btn btn-outline-secondary shadow-sm"><i class="bi bi-upload me-1"></i> Import CSV</a>
                        <a href="/medicines/export" class="btn btn-outline-secondary shadow-sm"><i class="bi bi-download me-1"></i> Export CSV</a>
                    </div>
                    <a href="/medicines/create" class="btn btn-primary shadow-sm px-4">
                        <i class="bi bi-plus-lg me-2"></i> Add Medicine
                    </a>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom-table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Code</th>
                                    <th>Medicine Name</th>
                                    <th>Active Ingredient</th>
                                    <th>Unit</th>
                                    <th>Base Price</th>
                                    <th>Type</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($medicines)): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-5">No medicines found in catalog.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($medicines as $med): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary"><?= htmlspecialchars($med['medicine_code']) ?></td>
                                        <td class="fw-bold text-primary"><?= htmlspecialchars($med['medicine_name']) ?></td>
                                        <td><?= htmlspecialchars($med['active_ingredient']) ?: '<span class="text-muted fst-italic">N/A</span>' ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($med['unit']) ?></span></td>
                                        <td class="fw-semibold"><?= number_format($med['base_price'], 0, ',', '.') ?> ₫</td>
                                        <td>
                                            <?php if($med['medicine_type'] == 'ETC'): ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">ETC</span>
                                            <?php else: ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success">OTC</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="/medicines/edit?id=<?= $med['medicine_id'] ?>" class="btn btn-light text-primary action-btn me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                            
                                            <form action="/medicines/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this medicine? This will hide it from the catalog.');">
                                                <input type="hidden" name="medicine_id" value="<?= $med['medicine_id'] ?>">
                                                <button type="submit" class="btn btn-light text-danger action-btn" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>