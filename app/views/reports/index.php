<?php 
/** * @var string $title 
 * @var string $fullName 
 */ 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Analytics - PMS' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --sidebar-bg: #2c3e50; --sidebar-hover: #34495e; --main-bg: #f5f6fa; --text-light: #ecf0f1; --navy-blue: #152b48; }
        body { background-color: var(--main-bg); font-family: 'Segoe UI', Tahoma, sans-serif; overflow-x: hidden;}
        
        /* SIDEBAR COMPONENT */
        .sidebar { width: 250px; height: 100vh; background-color: var(--sidebar-bg); position: fixed; top: 0; left: 0; color: var(--text-light); z-index: 1000; }
        .sidebar-header { padding: 20px; font-size: 1.5rem; font-weight: 700; background-color: #1a252f; display: flex; align-items: center; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; color: #7f8c8d; padding: 15px 20px 5px; font-weight: bold; letter-spacing: 1px; }
        .sidebar a { padding: 12px 20px; text-decoration: none; color: #bdc3c7; display: flex; align-items: center; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--sidebar-hover); color: #ffffff; border-left: 4px solid #3498db; }
        .sidebar a i { margin-right: 15px; font-size: 1.1rem; }
        
        /* LAYOUT STYLES */
        .main-content { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { background-color: #3498db; height: 60px; display: flex; align-items: center; justify-content: flex-end; padding: 0 20px; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .content-wrapper { padding: 20px 25px; flex: 1; }

        /* WIDGET CARDS STYLES */
        .widget-card { border-radius: 6px; padding: 15px 20px; color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; align-items: flex-start; justify-content: space-between; height: 100%;}
        .widget-blue { background-color: #1e75ff; }
        .widget-green { background-color: #10b981; }
        .widget-cyan { background-color: #06b6d4; color: #fff;}
        .widget-yellow { background-color: #fbc02d; color: #000; }
        
        .widget-title { font-size: 0.85rem; font-weight: 600; opacity: 0.9; margin-bottom: 5px; }
        .widget-value { font-size: 1.6rem; font-weight: 700; margin: 0; display: flex; align-items: center;}
        .widget-subtext { font-size: 0.75rem; opacity: 0.8; margin-top: 15px; }
        .widget-icon { font-size: 1.8rem; opacity: 0.8; margin-top: 5px;}

        /* ANALYTICS SECTION CARDS */
        .analytics-card { background: #fff; border-radius: 6px; border: 1px solid #e0e6ed; box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-bottom: 20px; }
        .card-header-custom { padding: 12px 20px; border-bottom: 1px solid #e0e6ed; background-color: #fafbfc; border-radius: 6px 6px 0 0; font-weight: 600; color: #34495e; font-size: 0.9rem;}
        
        /* SALES & PROFIT OVERVIEW LIST */
        .overview-item { padding: 15px 20px; border-bottom: 1px solid #f1f2f6; display: flex; justify-content: space-between; align-items: center; }
        .overview-item:last-child { border-bottom: none; }
        .overview-label { font-size: 0.9rem; font-weight: 600; color: #2c3e50; }
        .overview-date { font-size: 0.75rem; color: #7f8c8d; }
        .overview-sales { font-size: 0.9rem; font-weight: 700; color: #2c3e50; text-align: right;}
        .overview-profit { font-size: 0.85rem; font-weight: 700; color: #dc3545; text-align: right;}

        /* TABLE CUSTOM STYLES */
        .table-custom { margin: 0; font-size: 0.9rem; }
        .table-custom th { color: #2c3e50; font-weight: 600; padding: 12px 20px; border-bottom: 2px solid #e0e6ed; background: #fff;}
        .table-custom td { padding: 12px 20px; vertical-align: middle; color: #576574; border-bottom: 1px solid #f1f2f6;}
        
        /* INVENTORY PROGRESS BARS */
        .inv-bar-container { padding: 20px; }
        .inv-bar { display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; color: white; font-weight: 600; font-size: 0.85rem; border-radius: 4px; margin-bottom: 12px;}
        .inv-in-stock { background-color: #10b981; }
        .inv-stock-out { background-color: #ef4444; }
        .inv-badge { background: rgba(255,255,255,0.25); padding: 2px 10px; border-radius: 12px; font-weight: 700; }

        .btn-group-custom .btn { font-size: 0.75rem; padding: 5px 12px; font-weight: 600; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><i class="bi bi-capsule me-2"></i> PMS</div>
        
        <div class="nav-category">Main</div>
        <a href="/"><i class="bi bi-speedometer2"></i> Dashboard</a>
        
        <div class="nav-category">Sales & Pos</div>
        <a href="/pos"><i class="bi bi-cart-plus"></i> Create Invoice</a>
        <a href="/invoices"><i class="bi bi-receipt"></i> Selling History</a>
        
        <div class="nav-category">Inventory</div>
        <a href="/medicines"><i class="bi bi-box-seam"></i> Medicine Catalog</a>
        <a href="/inventory"><i class="bi bi-stack"></i> Manage Inventory</a>
        <a href="/imports"><i class="bi bi-truck"></i> Import Receipt</a>
        <a href="/returns"><i class="bi bi-arrow-return-left"></i> Return Orders</a>
        
        <div class="nav-category">System</div>
        <a href="/reports" class="active"><i class="bi bi-bar-chart-line"></i> Analytics</a>
        <a href="/users"><i class="bi bi-people"></i> Manage Users</a>
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
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-5 me-2"></i>
                    <strong><?= htmlspecialchars($fullName) ?> (<?= $displayRole ?>)</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item py-2" href="/profile"><i class="bi bi-person me-2 text-muted"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                </ul>
            </div>
        </div>

        <div class="content-wrapper">
            
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="widget-card widget-blue">
                        <div>
                            <div class="widget-title">Today's Sales</div>
                            <div class="widget-value" id="w-sales">...</div>
                        </div>
                        <i class="bi bi-cash-stack widget-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget-card widget-green">
                        <div>
                            <div class="widget-title">Today's Profit</div>
                            <div class="widget-value" id="w-profit">...</div>
                        </div>
                        <i class="bi bi-pie-chart-fill widget-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget-card widget-cyan">
                        <div>
                            <div class="widget-title">Inventory Value</div>
                            <div class="widget-value" id="w-inv-val">...</div>
                            <div class="widget-subtext">Current stock value</div>
                        </div>
                        <i class="bi bi-boxes widget-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget-card widget-yellow">
                        <div>
                            <div class="widget-title">Low Stock Items</div>
                            <div class="widget-value" id="w-low-stock">...</div>
                            <div class="widget-subtext">Items below threshold</div>
                        </div>
                        <i class="bi bi-exclamation-triangle-fill widget-icon"></i>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <div class="analytics-card h-100">
                        <div class="card-header-custom"><i class="bi bi-graph-up me-2"></i> Sales Trend - Last 30 Days</div>
                        <div class="p-3">
                            <canvas id="trendChart" height="110"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="analytics-card h-100">
                        <div class="card-header-custom"><i class="bi bi-hand-index-thumb-fill me-2"></i> Sales & Profit Overview</div>
                        <div id="overview-container">
                            <div class="text-center p-5 mt-4"><div class="spinner-border text-primary spinner-border-sm"></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="analytics-card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <div><i class="bi bi-trophy-fill me-2"></i> Top Selling Medicines</div>
                            <div class="btn-group btn-group-custom" id="topSellingBtns">
                                <button type="button" class="btn btn-primary" onclick="switchTopSelling('today', this)">Today</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchTopSelling('last_7', this)">Last 7 Days</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchTopSelling('this_month', this)">This Month</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchTopSelling('last_28', this)">Last 28 Days</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Medicine Name</th>
                                        <th class="text-center">Quantity Sold</th>
                                        <th class="text-end">Total Sales</th>
                                    </tr>
                                </thead>
                                <tbody id="top-selling-body">
                                    <tr><td colspan="3" class="text-center py-4 text-muted">Loading chart data...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="analytics-card h-100">
                        <div class="card-header-custom"><i class="bi bi-capsule me-2"></i> Inventory Status</div>
                        <div class="inv-bar-container">
                            <div class="inv-bar inv-in-stock">
                                <span>In Stock</span>
                                <span class="inv-badge" id="bar-in-stock">0</span>
                            </div>
                            <div class="inv-bar inv-stock-out">
                                <span>Stock Out</span>
                                <span class="inv-badge" id="bar-stock-out">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="analytics-card h-100">
                        <div class="card-header-custom"><i class="bi bi-calendar-x me-2"></i> Expiring Soon</div>
                        <div class="table-responsive">
                            <table class="table table-custom table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Medicine Name</th>
                                        <th class="text-center">Quantity</th>
                                        <th>Expires On</th>
                                        <th>Days Left</th>
                                    </tr>
                                </thead>
                                <tbody id="expiring-body">
                                    <tr><td colspan="4" class="text-center py-4 text-muted">Scanning batches...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const formatVND = new Intl.NumberFormat('vi-VN');
        const formatCurrency = (amount) => formatVND.format(amount) + ' ₫';

        // Biến toàn cục dùng để lưu trữ dữ liệu phân khoảng của danh mục bán chạy nhất
        window.topSellingData = {};

        // Hàm render giao diện bảng thuốc bán chạy
        function renderTopSellingTable(dataArray) {
            let topHtml = '';
            if(!dataArray || dataArray.length === 0) {
                topHtml = '<tr><td colspan="3" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-4 d-block mb-2"></i>No sales data recorded for this period.</td></tr>';
            } else {
                dataArray.forEach(item => {
                    topHtml += `<tr>
                        <td class="fw-bold text-dark">${item.medicine_name}</td>
                        <td class="text-center fw-semibold text-secondary">${item.total_qty}</td>
                        <td class="text-end fw-bold text-primary">${formatCurrency(item.total_sales)}</td>
                    </tr>`;
                });
            }
            document.getElementById('top-selling-body').innerHTML = topHtml;
        }

        // Hàm click điều phối sự kiện đổi Tab
        window.switchTopSelling = function(timeframe, btnElement) {
            document.querySelectorAll('#topSellingBtns .btn').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-primary');
            });
            btnElement.classList.remove('btn-outline-primary');
            btnElement.classList.add('btn-primary');

            renderTopSellingTable(window.topSellingData[timeframe]);
        };

        // KÍCH HOẠT QUY TRÌNH FETCH ENGINE REALTIME
        fetch('/reports/api-analytics')
            .then(res => res.json())
            .then(data => {
                // 1. ĐỔ DỮ LIỆU LÊN 4 OÔ TIÊU ĐỀ (WIDGETS)
                document.getElementById('w-sales').innerText = formatCurrency(data.today_sales);
                document.getElementById('w-profit').innerText = formatCurrency(data.today_profit);
                document.getElementById('w-inv-val').innerText = formatCurrency(data.inventory_value);
                document.getElementById('w-low-stock').innerText = data.low_stock_count;

                // 2. VẼ BIỂU ĐỒ ĐƯỜNG XU HƯỚNG DOANH THU 30 NGÀY (LINE CHART)
                const trendLabels = data.sales_trend.map(i => {
                    let d = new Date(i.date);
                    return d.toLocaleDateString('en-GB', {day: '2-digit', month: 'short'});
                });
                const trendData = data.sales_trend.map(i => i.sales);
                
                new Chart(document.getElementById('trendChart'), {
                    type: 'line',
                    data: {
                        labels: trendLabels,
                        datasets: [{
                            label: 'Sales Revenue',
                            data: trendData,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.05)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.2,
                            pointRadius: 2,
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { 
                            y: { beginAtZero: true, grid: { borderDash: [3, 3], color: '#e0e6ed' } }, 
                            x: { grid: { display: false } } 
                        }
                    }
                });

                // 3. ĐỔ DỮ LIỆU KHỐI TỔNG QUAN (OVERVIEW LIST)
                const ov = data.overview;
                document.getElementById('overview-container').innerHTML = `
                    <div class="overview-item">
                        <div><div class="overview-label">Today</div><div class="overview-date">Real-time date</div></div>
                        <div><div class="overview-sales">Sales: ${formatCurrency(ov.today.total_sales)}</div><div class="overview-profit">Profit: ${formatCurrency(ov.today.total_profit)}</div></div>
                    </div>
                    <div class="overview-item">
                        <div><div class="overview-label">Last 7 Days</div><div class="overview-date">Rolling week</div></div>
                        <div><div class="overview-sales">Sales: ${formatCurrency(ov.last_7.total_sales)}</div><div class="overview-profit">Profit: ${formatCurrency(ov.last_7.total_profit)}</div></div>
                    </div>
                    <div class="overview-item">
                        <div><div class="overview-label">This Month</div><div class="overview-date">Current month logs</div></div>
                        <div><div class="overview-sales">Sales: ${formatCurrency(ov.this_month.total_sales)}</div><div class="overview-profit">Profit: ${formatCurrency(ov.this_month.total_profit)}</div></div>
                    </div>
                    <div class="overview-item">
                        <div><div class="overview-label">Last 28 Days</div><div class="overview-date">Rolling monthly system</div></div>
                        <div><div class="overview-sales">Sales: ${formatCurrency(ov.last_28.total_sales)}</div><div class="overview-profit">Profit: ${formatCurrency(ov.last_28.total_profit)}</div></div>
                    </div>
                `;

                // 4. KẾT NỐI DATA VÀO ENGINE CHUYỂN TAB MƯỢT MÀ
                window.topSellingData = data.top_selling;
                renderTopSellingTable(window.topSellingData.today); // Mặc định hiển thị tab Today

                // 5. CẬP NHẬT THANH TRẠNG THÁI KHO THUỐC
                document.getElementById('bar-in-stock').innerText = data.inventory_status.in_stock;
                document.getElementById('bar-stock-out').innerText = data.inventory_status.stock_out;

                // 6. CẬP NHẬT BẢNG THUỐC CẬN HẠN SỬ DỤNG
                let expHtml = '';
                data.expiring_soon.forEach(item => {
                    let d = new Date(item.expiry_date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'});
                    let badgeClass = item.days_left <= 30 ? 'bg-danger' : 'bg-info';
                    expHtml += `<tr>
                        <td class="fw-bold text-dark">${item.medicine_name}</td>
                        <td class="text-center fw-semibold text-secondary">${item.quantity}</td>
                        <td>${d}</td>
                        <td><span class="badge ${badgeClass} text-white px-2 py-1">${item.days_left} days</span></td>
                    </tr>`;
                });
                document.getElementById('expiring-body').innerHTML = expHtml || '<tr><td colspan="4" class="text-center py-3 text-muted">No medicines expiring within 6 months.</td></tr>';

            }).catch(err => console.error("An error occurred during chart engine compilation:", err));
    </script>
</body>
</html>