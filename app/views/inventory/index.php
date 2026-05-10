<?php
/** * @var string $title 
 * @var string $fullName 
 * @var array $batches 
 * @var string $search 
 * @var string $status 
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
        
        /* Main Content & Navbar */
        .main-content { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { background-color: #3498db; height: 60px; display: flex; align-items: center; justify-content: flex-end; padding: 0 20px; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .dashboard-container { padding: 25px; flex-grow: 1; }
        
        /* UI Components */
        .filter-card { background: white; border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .custom-table th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 15px; border-bottom: 2px solid #e9ecef; }
        .custom-table td { padding: 15px; vertical-align: middle; color: #333; }
        .row-warning { background-color: #fffdf0 !important; }
        .row-danger { background-color: #fff5f5 !important; }
        .badge-otc { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .badge-etc { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><i class="bi bi-capsule me-2"></i> PharmaCare</div>
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
            <a href="/inventory" class="active"><i class="bi bi-stack"></i> Manage Inventory</a>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-navy-blue mb-0">Inventory Management</h3>
                    <p class="text-muted small mb-0 mt-1">Track and manage stock levels by specific batches</p>
                </div>
                <a href="/inventory/export" class="btn btn-outline-success shadow-sm">
                    <i class="bi bi-file-earmark-excel me-2"></i> Export Report
                </a>
            </div>

            <div class="card filter-card mb-4">
                <div class="card-body">
                    <form method="GET" action="/inventory" class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control border-start-0" name="search" placeholder="Search by name, code or batch..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="status">
                                <option value="">-- All Status --</option>
                                <option value="good" <?= $status == 'good' ? 'selected' : '' ?>>Good Stock</option>
                                <option value="expiring_soon" <?= $status == 'expiring_soon' ? 'selected' : '' ?>>Expiring Soon</option>
                                <option value="expired" <?= $status == 'expired' ? 'selected' : '' ?>>Expired Items</option>
                                <option value="out_of_stock" <?= $status == 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">Apply Filters</button>
                            <a href="/inventory" class="btn btn-light border px-3" title="Clear Filters">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-5">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom-table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Medicine</th>
                                    <th>Batch No.</th>
                                    <th>Expiry Date</th>
                                    <th class="text-center">Stock Qty</th>
                                    <th>Status</th>
                                    <th class="text-center pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($batches)): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-5">No inventory data found matching your filters.</td></tr>
                                <?php else: ?>
                                    <?php 
                                    $today = new DateTime();
                                    foreach ($batches as $b): 
                                        $exp = new DateTime($b['expiry_date']);
                                        $interval = $today->diff($exp);
                                        $days = (int)$interval->format('%R%a');
                                        
                                        $rowClass = ($b['quantity'] <= 0 || $days < 0) ? 'row-danger' : ($days <= 90 ? 'row-warning' : '');
                                        $typeClass = $b['medicine_type'] == 'OTC' ? 'badge-otc' : 'badge-etc';
                                    ?>
                                    <tr class="<?= $rowClass ?>">
                                        <td class="ps-4">
                                            <div class="fw-bold text-primary"><?= htmlspecialchars($b['medicine_name']) ?></div>
                                            <div class="small text-muted">
                                                <?= htmlspecialchars($b['medicine_code']) ?> 
                                                <span class="badge <?= $typeClass ?> ms-1" style="font-size: 0.65rem; padding: 2px 5px;"><?= $b['medicine_type'] ?></span>
                                            </div>
                                        </td>
                                        <td><code class="text-secondary"><?= htmlspecialchars($b['batch_number']) ?></code></td>
                                        <td class="<?= $days <= 90 ? 'text-danger fw-bold' : '' ?>">
                                            <?= date('d/m/Y', strtotime($b['expiry_date'])) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold fs-5"><?= number_format($b['quantity']) ?></span>
                                            <small class="text-muted d-block" style="font-size: 0.7rem;"><?= htmlspecialchars($b['unit']) ?></small>
                                        </td>
                                        <td>
                                            <?php if($b['quantity'] <= 0) echo '<span class="badge bg-danger">Out of Stock</span>';
                                                  elseif($days < 0) echo '<span class="badge bg-dark">Expired</span>';
                                                  elseif($days <= 90) echo '<span class="badge bg-warning text-dark">Soon ('.$days.'d)</span>';
                                                  else echo '<span class="badge bg-success">Good</span>'; ?>
                                        </td>
                                        <td class="text-center pe-4">
                                            <button class="btn btn-sm btn-outline-secondary px-3" onclick="openAdjustModal(<?= $b['batch_id'] ?>, '<?= addslashes($b['medicine_name']) ?>', '<?= htmlspecialchars($b['batch_number']) ?>', <?= $b['quantity'] ?>)">
                                                <i class="bi bi-pencil-square me-1"></i> Adjust
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <h4 class="fw-bold text-navy-blue mb-0"><i class="bi bi-clock-history me-2"></i>Record of Change</h4>
            </div>
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light text-uppercase" style="font-size: 0.8rem;">
                                <tr>
                                    <th class="ps-4 py-3">Timestamp</th>
                                    <th>Staff In Charge</th>
                                    <th>Medicine & Batch</th>
                                    <th class="text-center">Qty Change</th>
                                    <th class="pe-4">Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($adjustments)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4 fst-italic">No adjustments made in the current session.</td></tr>
                                <?php else: ?>
                                    <?php foreach($adjustments as $log): ?>
                                    <tr>
                                        <td class="ps-4 text-muted"><?= $log['time'] ?></td>
                                        <td><i class="bi bi-person-fill me-1 text-secondary"></i> <?= htmlspecialchars($log['staff']) ?></td>
                                        <td>
                                            <span class="fw-bold text-primary"><?= htmlspecialchars($log['medicine']) ?></span><br>
                                            <small class="text-muted">LOT: <?= htmlspecialchars($log['batch']) ?></small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?= $log['old_qty'] ?></span> 
                                            <i class="bi bi-arrow-right mx-1 text-muted"></i> 
                                            <span class="badge <?= $log['new_qty'] > $log['old_qty'] ? 'bg-success' : 'bg-danger' ?>"><?= $log['new_qty'] ?></span>
                                        </td>
                                        <td class="pe-4 fst-italic text-muted"><?= htmlspecialchars($log['reason']) ?></td>
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

    <div class="modal fade" id="adjustModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="/inventory/adjust" method="POST" class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-navy-blue">Stock Adjustment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="batch_id" id="adj_id">
                    <input type="hidden" name="medicine_name" id="adj_med_name">
                    <input type="hidden" name="batch_number" id="adj_batch_no">
                    <input type="hidden" name="old_quantity" id="adj_old_qty">

                    <div class="mb-4 text-center">
                        <label class="form-label small text-muted text-uppercase fw-bold d-block">Medicine Selected</label>
                        <div id="adj_name" class="fw-bold fs-5 text-primary"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Actual Physical Quantity</label>
                        <input type="number" class="form-control form-control-lg text-center" name="new_quantity" id="adj_qty" required min="0">
                        <div class="form-text text-center">Enter the real quantity counted on the shelf.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for Change</label>
                        <select class="form-select" name="reason" required>
                            <option value="Physical counting error">Physical counting error</option>
                            <option value="Damaged / Broken / Lost">Damaged / Broken / Lost</option>
                            <option value="Used for sampling">Used for sampling</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Confirm Adjustment</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modal = new bootstrap.Modal(document.getElementById('adjustModal'));
        function openAdjustModal(id, name, batch, qty) {
            // Hiển thị giao diện
            document.getElementById('adj_name').innerText = name + ' (LOT: ' + batch + ')';
            document.getElementById('adj_qty').value = qty;
            
            // Gán dữ liệu vào form ẩn
            document.getElementById('adj_id').value = id;
            document.getElementById('adj_med_name').value = name;
            document.getElementById('adj_batch_no').value = batch;
            document.getElementById('adj_old_qty').value = qty;
            
            modal.show();
        }
    </script>
</body>
</html>