<?php
/** * @var string $title 
 * @var string $fullName 
 * @var array $users 
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
            --navy-blue: #152b48;
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
        .sidebar a i { margin-right: 15px; font-size: 1.1rem; }

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
        
        .dashboard-container { padding: 25px; flex-grow: 1; }

        /* Custom Table & Card Styles */
        .user-card {
            border-radius: 10px;
            border: none;
        }
        .custom-table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        .custom-table td {
            padding: 15px;
            vertical-align: middle;
            color: #333;
        }
        .avatar-circle {
            width: 40px;
            height: 40px;
            background-color: #e9ecef;
            color: var(--navy-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-right: 12px;
        }
        .action-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .action-btn:hover { transform: translateY(-2px); }
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
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="text-navy fw-bold mb-0">User Management</h3>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.9rem;">Manage system accounts and access roles</p>
                </div>
                <a href="/users/create" class="btn btn-primary shadow-sm px-4">
                    <i class="bi bi-person-plus-fill me-2"></i> Add New Staff
                </a>
            </div>

            <div class="card user-card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom-table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">User Details</th>
                                    <th>Username</th>
                                    <th>System Role</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($user['full_name']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">@<?= htmlspecialchars($user['username']) ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                            $badgeClass = 'bg-secondary';
                                            if ($user['role_name'] === 'Manager') $badgeClass = 'bg-primary';
                                            if ($user['role_name'] === 'Pharmacist') $badgeClass = 'bg-info text-dark';
                                            if ($user['role_name'] === 'Inventory Staff') $badgeClass = 'bg-warning text-dark';
                                        ?>
                                        <span class="badge <?= $badgeClass ?> px-3 py-2 rounded-pill">
                                            <?= htmlspecialchars($user['role_name']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['status'] === 'Active'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Active
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="/users/edit?id=<?= $user['user_id'] ?>" class="btn btn-light text-primary action-btn me-1" title="Edit User">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        
                                        <form action="/users/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <button type="submit" class="btn btn-light text-danger action-btn" title="Delete User">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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