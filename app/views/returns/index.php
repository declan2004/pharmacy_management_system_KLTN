<?php
/** * @var string $title 
 * @var string $fullName 
 * @var array $returns 
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
        .dashboard-container { padding: 25px; flex-grow: 1; }
        .custom-table th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; padding: 15px; border-bottom: 2px solid #e9ecef; }
        .custom-table td { padding: 15px; vertical-align: middle; color: #333; }
        .action-btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s; }
        .action-btn:hover { transform: translateY(-2px); }
        
        .info-label { font-size: 0.8rem; color: #6c757d; text-transform: uppercase; font-weight: bold; margin-bottom: 3px; }
        .info-value { font-weight: 600; color: #2c3e50; }
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
            <?php if (isset($_SESSION['return_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['return_success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['return_success']); ?>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="text-navy fw-bold mb-0">Return Orders</h3>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.9rem;">History of items returned to suppliers</p>
                </div>
                <div>
                    <a href="/returns/create" class="btn btn-danger shadow-sm px-4">
                        <i class="bi bi-arrow-return-left me-2"></i> Create Return Order
                    </a>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom-table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Return ID</th>
                                    <th>Return Date</th>
                                    <th>Staff In Charge</th>
                                    <th>Note</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center pe-4">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($returns)): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-5">No return orders history found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($returns as $ret): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary">#RET-<?= str_pad($ret['return_id'], 5, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($ret['return_date'])) ?></td>
                                        <td><i class="bi bi-person me-1 text-muted"></i> <?= htmlspecialchars($ret['staff_name']) ?></td>
                                        <td class="text-muted fst-italic"><?= htmlspecialchars($ret['note']) ?: '-' ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Completed</span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <button type="button" class="btn btn-light text-primary action-btn" onclick="openReturnDetails(<?= $ret['return_id'] ?>)" title="View Details">
                                                <i class="bi bi-eye"></i>
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
        </div>
    </div>

    <div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-bottom-0 px-4 py-3">
                    <h5 class="modal-title fw-bold text-navy" id="modalTitle">Return Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div id="modalLoading" class="text-center py-5">
                        <div class="spinner-border text-danger" role="status"></div>
                        <p class="mt-2 text-muted">Loading return data...</p>
                    </div>

                    <div id="modalContent" style="display: none;">
                        <div class="row mb-4 bg-white p-3 rounded shadow-sm mx-0">
                            <div class="col-md-4 border-end">
                                <div class="info-label">Return Date</div>
                                <div class="info-value" id="mdlDate"></div>
                            </div>
                            <div class="col-md-4 border-end">
                                <div class="info-label">Processed By</div>
                                <div class="info-value" id="mdlStaff"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">General Note</div>
                                <div class="info-value text-muted" id="mdlNote"></div>
                            </div>
                        </div>

                        <div class="table-responsive bg-white rounded shadow-sm border">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-uppercase" style="font-size: 0.8rem;">
                                    <tr>
                                        <th class="ps-3">Medicine Info</th>
                                        <th>Batch No.</th>
                                        <th class="text-center">Return Qty</th>
                                        <th class="pe-3">Specific Reason</th>
                                    </tr>
                                </thead>
                                <tbody id="mdlTableBody">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 bg-white border-top-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger px-4" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const returnModal = new bootstrap.Modal(document.getElementById('returnModal'));

        function openReturnDetails(id) {
            document.getElementById('modalLoading').style.display = 'block';
            document.getElementById('modalContent').style.display = 'none';
            document.getElementById('modalTitle').innerText = 'Return Order #RET-' + String(id).padStart(5, '0');
            returnModal.show();

            fetch('/returns/show?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        returnModal.hide();
                        return;
                    }

                    const retOrder = data.returnOrder;
                    const details = data.details;

                    document.getElementById('mdlDate').innerText = retOrder.return_date;
                    document.getElementById('mdlStaff').innerText = retOrder.staff_name;
                    document.getElementById('mdlNote').innerText = retOrder.note || '-';

                    let html = '';
                    details.forEach(item => {
                        html += `
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">${item.medicine_name}</div>
                                    <div class="small text-muted">${item.medicine_code}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">${item.batch_number}</span></td>
                                <td class="text-center fw-bold text-danger">${item.quantity}</td>
                                <td class="pe-3 text-muted">${item.return_reason || '-'}</td>
                            </tr>
                        `;
                    });
                    document.getElementById('mdlTableBody').innerHTML = html;

                    document.getElementById('modalLoading').style.display = 'none';
                    document.getElementById('modalContent').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching return details:', error);
                    alert("System error while fetching details.");
                    returnModal.hide();
                });
        }
    </script>
</body>
</html>