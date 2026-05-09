<?php
/**
 * @var string $title
 * @var string $fullName
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
        :root {
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
            --main-bg: #f4f7f6;
            --text-light: #ecf0f1;
        }
        body {
            background-color: var(--main-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            color: var(--text-light);
            z-index: 1000;
        }
        .sidebar-header {
            padding: 20px;
            font-size: 1.5rem;
            font-weight: 700;
            background-color: #1a252f;
            display: flex;
            align-items: center;
        }
        .nav-category {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #7f8c8d;
            padding: 15px 20px 5px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .sidebar a {
            padding: 12px 20px;
            text-decoration: none;
            color: #bdc3c7;
            display: flex;
            align-items: center;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: var(--sidebar-hover);
            color: #ffffff;
            border-left: 4px solid #3498db;
        }
        .sidebar a i {
            margin-right: 15px;
            font-size: 1.1rem;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Top Navbar Styles */
        .top-navbar {
            background-color: #3498db;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 20px;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Dashboard Container */
        .dashboard-container {
            padding: 25px;
            flex-grow: 1;
        }

        /* Summary Cards */
        .summary-card {
            border-radius: 4px;
            color: white;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .card-blue { background-color: #007bff; }
        .card-red { background-color: #dc3545; }
        .card-yellow { background-color: #ffc107; color: #333 !important; }
        .card-green { background-color: #28a745; }
        .card-title-sm {
            font-size: 0.9rem;
            margin-bottom: 5px;
            opacity: 0.9;
        }
        .card-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        /* Tables & Alerts */
        .custom-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            padding: 20px;
        }
        .warning-title {
            color: #856404;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
    <div class="sidebar-header">
        <i class="bi bi-capsule me-2"></i> PMS
    </div>
    
    <div class="nav-category">Main</div>
    <a href="/" class="<?= ($title == 'Dashboard - Pharmacy Management System') ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    
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
        <a href="/returns"><i class="bi bi-arrow-return-left"></i> Return Orders</a>
    <?php endif; ?>
    
    <?php if ($_SESSION['role_id'] == 1): ?>
        <div class="nav-category">System</div>
        <a href="/reports"><i class="bi bi-bar-chart-line"></i> Analytics</a>
        <a href="/users" class="<?= (strpos($title, 'User Management') !== false) ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Manage Users
        </a>
    <?php endif; ?>
</div>

    <div class="main-content">
        <div class="top-navbar">
            <div class="dropdown">
                <?php 
                    $displayRole = 'Unknown';
                    if ($_SESSION['role_id'] == 1) $displayRole = 'Manager';
                    elseif ($_SESSION['role_id'] == 2) $displayRole = 'Pharmacist';
                    elseif ($_SESSION['role_id'] == 3) $displayRole = 'Inventory Staff';
                ?>
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-5 me-2"></i>
                    <strong><?= htmlspecialchars($fullName) ?> (<?= $displayRole ?>)</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                </ul>
            </div>
        </div>

        <div class="dashboard-container">
            
            <div class="row">
                <div class="col-md-3">
                    <div class="summary-card card-blue">
                        <div class="card-title-sm">Total Medicines</div>
                        <p class="card-value">0</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card card-red">
                        <div class="card-title-sm">Out of Stock</div>
                        <p class="card-value">0</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card card-yellow">
                        <div class="card-title-sm">Expired</div>
                        <p class="card-value">0</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card card-green">
                        <div class="card-title-sm">Today's Sales Value</div>
                        <p class="card-value">0 ₫</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom-table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-4">Medicine Name</th>
                                    <th>Batch</th>
                                    <th>Quantity</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th class="pe-4">Stock Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        No data available. Data will be loaded from the database.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="warning-box shadow-sm">
                        <div class="warning-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Stock & Expiry Warnings</div>
                        <p class="text-muted mb-0" style="font-size: 0.95rem;">No warnings at this time.</p>
                    </div>
                </div>
            </div>

        </div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>