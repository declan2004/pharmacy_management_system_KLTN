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
        :root { --sidebar-bg: #2c3e50; --sidebar-hover: #34495e; --main-bg: #f4f7f6; --text-light: #ecf0f1; }
        body { background-color: var(--main-bg); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; }
        
        /* Sidebar & Navbar */
        .sidebar { width: 250px; height: 100vh; background-color: var(--sidebar-bg); position: fixed; top: 0; left: 0; color: var(--text-light); z-index: 1000; }
        .sidebar-header { padding: 20px; font-size: 1.5rem; font-weight: 700; background-color: #1a252f; display: flex; align-items: center; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; color: #7f8c8d; padding: 15px 20px 5px; font-weight: bold; letter-spacing: 1px; }
        .sidebar a { padding: 12px 20px; text-decoration: none; color: #bdc3c7; display: flex; align-items: center; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--sidebar-hover); color: #ffffff; border-left: 4px solid #3498db; }
        .sidebar a i { margin-right: 15px; font-size: 1.1rem; }
        
        .main-content { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { background-color: #3498db; height: 60px; display: flex; align-items: center; justify-content: flex-end; padding: 0 20px; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .dashboard-container { padding: 25px; flex-grow: 1; }

        /* Summary Cards */
        .summary-card { border-radius: 4px; color: white; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .card-blue { background-color: #007bff; } .card-red { background-color: #dc3545; } .card-yellow { background-color: #ffc107; color: #333 !important; } .card-green { background-color: #28a745; }
        .card-title-sm { font-size: 0.9rem; margin-bottom: 5px; opacity: 0.9; }
        .card-value { font-size: 2rem; font-weight: 700; margin: 0; transition: all 0.3s ease; }

        /* Tables & Alerts */
        .custom-table th { background-color: #f8f9fa; color: #333; font-weight: 600; border-bottom: 2px solid #dee2e6; }
        .warning-box { background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px; padding: 20px; }
        .warning-title { color: #856404; font-weight: 600; font-size: 1.1rem; margin-bottom: 15px; }

        /* VERTICAL MARQUEE ANIMATION (OUTRO PHIM) */
        .marquee-container { height: 200px; overflow: hidden; position: relative; }
        .marquee-content { position: absolute; width: 100%; top: 100%; animation: scrollUp 15s linear infinite; }
        .marquee-content:hover { animation-play-state: paused; } /* Dừng cuộn khi rê chuột vào */
        .warning-item { padding: 10px 0; border-bottom: 1px dashed #ffe69c; color: #856404; font-size: 0.95rem; }
        
        @keyframes scrollUp {
            0% { top: 100%; }
            100% { top: -150%; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><i class="bi bi-capsule me-2"></i> PMS</div>
        
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
            <a href="/users"><i class="bi bi-people"></i> Manage Users</a>
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
                        <p class="card-value" id="valTotal">...</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card card-red">
                        <div class="card-title-sm">Out of Stock</div>
                        <p class="card-value" id="valOOS">...</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card card-yellow">
                        <div class="card-title-sm">Expired Batches</div>
                        <p class="card-value" id="valExpired">...</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card card-green">
                        <div class="card-title-sm">Today's Sales Value</div>
                        <p class="card-value" id="valSales">...</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 mb-4 h-100">
                        <div class="card-body p-0">
                            <h6 class="fw-bold p-3 mb-0 text-navy-blue border-bottom">Inventory Overview</h6>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Medicine Name</th>
                                            <th>Batch</th>
                                            <th>Quantity</th>
                                            <th>Expiry Date</th>
                                            <th>Price</th>
                                            <th class="pe-4">Stock Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="inventoryTable">
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div> Loading real-time data...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="warning-box shadow-sm h-100">
                        <div class="warning-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Stock & Expiry Warnings</div>
                        <div class="marquee-container">
                            <div class="marquee-content" id="warningMarquee">
                                <p class="text-muted text-center mt-4">Analyzing GPP compliance...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> 
    </div> 
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const formatVND = new Intl.NumberFormat('vi-VN');

        function fetchDashboardData() {
            fetch('/home/apiStats')
                .then(res => res.json())
                .then(data => {
                    // 1. CẬP NHẬT 4 WIDGETS
                    document.getElementById('valTotal').innerText = data.stats.total_medicines;
                    document.getElementById('valOOS').innerText = data.stats.out_of_stock;
                    document.getElementById('valExpired').innerText = data.stats.expired;
                    document.getElementById('valSales').innerText = formatVND.format(data.stats.today_sales) + ' ₫';

                    // 2. CẬP NHẬT BẢNG TỒN KHO 
                    let invHtml = '';
                    if(data.inventory.length === 0) {
                        invHtml = '<tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No inventory data available.</td></tr>';
                    } else {
                        data.inventory.forEach(item => {
                            let expDate = new Date(item.expiry_date).toLocaleDateString('en-GB');
                            let badgeType = item.medicine_type === 'ETC' ? 'bg-danger' : 'bg-success';
                            let stockStatus = item.quantity <= 10 ? '<span class="badge bg-warning text-dark"><i class="bi bi-arrow-down"></i> Low Stock</span>' : '<span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">In Stock</span>';

                            invHtml += `<tr>
                                <td class="ps-4 fw-bold text-dark">${item.medicine_name} <span class="badge ${badgeType} ms-1" style="font-size:0.6rem">${item.medicine_type}</span></td>
                                <td><span class="badge bg-light text-dark border">${item.batch_number}</span></td>
                                <td class="fw-bold ${item.quantity <= 10 ? 'text-danger' : 'text-primary'}">${item.quantity}</td>
                                <td>${expDate}</td>
                                <td>${formatVND.format(item.base_price)} ₫</td>
                                <td class="pe-4">${stockStatus}</td>
                            </tr>`;
                        });
                    }
                    document.getElementById('inventoryTable').innerHTML = invHtml;

                    // 3. CẬP NHẬT widget CẢNH BÁO
                    let warnHtml = '';
                    if(data.warnings.length === 0) {
                        warnHtml = '<div class="text-center text-success mt-5"><i class="bi bi-check-circle-fill me-2"></i>All stocks and expiries are perfectly healthy!</div>';
                    } else {
                        data.warnings.forEach(item => {
                            let expDate = new Date(item.expiry_date).toLocaleDateString('en-GB');
                            let issue = '';
                            let icon = '';
                            
                            if (item.quantity <= 10) {
                                issue = `LOW STOCK (${item.quantity} units left)`; icon = '<i class="bi bi-arrow-down-circle-fill text-warning me-2"></i>';
                            } else if (item.days_left < 0) {
                                issue = 'ALREADY EXPIRED!'; icon = '<i class="bi bi-x-octagon-fill text-danger me-2"></i>';
                            } else {
                                issue = `Expiring soon (${item.days_left} days)`; icon = '<i class="bi bi-clock-fill text-warning me-2"></i>';
                            }

                            warnHtml += `
                                <div class="warning-item">
                                    <div class="d-flex align-items-start">
                                        ${icon}
                                        <div>
                                            <strong class="text-dark">${item.medicine_name}</strong> (Lot: ${item.batch_number})
                                            <div class="text-danger mt-1"><strong>Action Required:</strong> ${issue}</div>
                                            <div class="text-muted" style="font-size: 0.8rem">Expires: ${expDate}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    document.getElementById('warningMarquee').innerHTML = warnHtml;
                })
                .catch(err => console.error("Data Fetch Error:", err));
        }

        // Gọi ngay khi load trang
        fetchDashboardData();
        
        // Polling (Refresh dữ liệu mỗi 5 giây mà không F5)
        setInterval(fetchDashboardData, 5000);
    </script>
</body>
</html>