<?php 
/** * @var string $title 
 * @var string $fullName 
 * @var array $invoices 
 * @var float $filteredRevenue 
 * @var array $returnHistory
 * @var array $pharmacists 
 * @var array $filters 
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
        body { background-color: var(--main-bg); font-family: 'Segoe UI', Tahoma, sans-serif; }
        
        /* SIDEBAR */
        .sidebar { width: 250px; height: 100vh; background-color: var(--sidebar-bg); position: fixed; top: 0; left: 0; color: var(--text-light); z-index: 1000; }
        .sidebar-header { padding: 20px; font-size: 1.5rem; font-weight: 700; background-color: #1a252f; display: flex; align-items: center; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; color: #7f8c8d; padding: 15px 20px 5px; font-weight: bold; letter-spacing: 1px; }
        .sidebar a { padding: 12px 20px; text-decoration: none; color: #bdc3c7; display: flex; align-items: center; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--sidebar-hover); color: #ffffff; border-left: 4px solid #3498db; }
        .sidebar a i { margin-right: 15px; font-size: 1.1rem; }
        
        /* MAIN LAYOUT */
        .main-content { margin-left: 250px; display: flex; flex-direction: column; min-height: 100vh; }
        .top-navbar { background-color: #3498db; height: 60px; display: flex; align-items: center; justify-content: flex-end; padding: 0 20px; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .content-wrapper { padding: 30px; flex: 1; }
        
        /* WIDGET REVENUE */
        .revenue-widget { 
            background: linear-gradient(135deg, #27ae60, #2ecc71); 
            border-radius: 8px; 
            color: white; 
            padding: 12px 20px; 
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.2); 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            max-width: 320px;
            margin-left: auto;
        }
        .revenue-amount { font-size: 1.6rem; font-weight: 800; margin: 0; line-height: 1.2; }
        .revenue-label { font-size: 0.75rem; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; opacity: 0.9; }
        .revenue-icon { font-size: 2.5rem; opacity: 0.5; }

        /* TABLE & CARDS */
        .history-card { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .filter-form { background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e9ecef; margin-bottom: 20px; }
        .table th { background-color: #f8f9fa; color: #2c3e50; font-weight: 600; border-bottom: 2px solid #dee2e6; }
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
            <a href="/invoices" class="active"><i class="bi bi-receipt"></i> Selling History</a>
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

        <div class="content-wrapper">
            <div class="row mb-4 align-items-center">
                <div class="col-md-6">
                    <h3 class="fw-bold text-navy-blue m-0">Selling History</h3>
                    <p class="text-muted m-0">Manage and track all completed transactions.</p>
                </div>
                <div class="col-md-6">
                    <div class="revenue-widget">
                        <div>
                            <div class="revenue-label">
                                <?= empty($filters['date']) ? 'Total Revenue' : 'Revenue: ' . date('d/m/Y', strtotime($filters['date'])) ?>
                            </div>
                            <p class="revenue-amount"><?= number_format($filteredRevenue, 0, ',', '.') ?> ₫</p>
                        </div>
                        <i class="bi bi-graph-up-arrow revenue-icon"></i>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="history-card mb-4">
                <form method="GET" action="/invoices" class="filter-form">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold">Search Invoice ID</label>
                            <input type="text" name="search" class="form-control" placeholder="Ex: 1024" value="<?= htmlspecialchars($filters['search']) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-bold">Filter by Date</label>
                            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($filters['date']) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-bold">Payment Method</label>
                            <select name="method" class="form-select">
                                <option value="">All Methods</option>
                                <option value="Cash" <?= $filters['method'] == 'Cash' ? 'selected' : '' ?>>Cash</option>
                                <option value="Bank Transfer" <?= $filters['method'] == 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                            </select>
                        </div>
                        
                        <?php if ($_SESSION['role_id'] == 1): ?>
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold">Pharmacist</label>
                            <select name="pharmacist_id" class="form-select">
                                <option value="">All Staff</option>
                                <?php foreach ($pharmacists as $ph): ?>
                                    <option value="<?= $ph['user_id'] ?>" <?= $filters['pharmacist_id'] == $ph['user_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ph['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                            <a href="/invoices" class="btn btn-light border" title="Clear Filters"><i class="bi bi-x-circle"></i></a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mt-3">
                        <thead>
                            <tr>
                                <th>Inv ID</th>
                                <th>Date & Time</th>
                                <th>Pharmacist</th>
                                <th>Payment Method</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($invoices)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-search fs-1 opacity-25"></i>
                                        <p class="mt-2 mb-0">No transactions match your criteria.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($invoices as $inv): ?>
                                    <tr>
                                        <td class="fw-bold text-primary">#<?= $inv['invoice_id'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($inv['invoice_date'])) ?></td>
                                        <td><i class="bi bi-person me-1 text-muted"></i> <?= htmlspecialchars($inv['pharmacist_name']) ?></td>
                                        <td>
                                            <?php if ($inv['payment_method'] === 'Cash'): ?>
                                                <span class="badge bg-light text-dark border"><i class="bi bi-cash text-success"></i> Cash</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border"><i class="bi bi-bank text-primary"></i> Transfer</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-bold text-danger">
                                            <?= number_format($inv['total_amount'], 0, ',', '.') ?> ₫
                                        </td>
                                        <td class="text-center">
                                            <?php if ($inv['status'] === 'Completed'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">
                                                    <i class="bi bi-check-circle me-1"></i>Completed
                                                </span>
                                            <?php elseif ($inv['status'] === 'Partially Returned'): ?>
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle px-2 py-1">
                                                    <i class="bi bi-arrow-down-up me-1"></i>Partially Returned
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle px-2 py-1">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Returned
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <button class="btn btn-sm btn-outline-primary" title="View Details" onclick="viewDetails(<?= $inv['invoice_id'] ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                
                                                <button class="btn btn-sm btn-outline-secondary" title="Print" onclick="window.open('/invoices/print?id=<?= $inv['invoice_id'] ?>', '_blank')">
                                                    <i class="bi bi-printer"></i>
                                                </button>
                                                
                                                <?php if ($inv['status'] !== 'Returned'): ?>
                                                    <button class="btn btn-sm btn-outline-warning" title="Return Items" onclick="openReturnModal(<?= $inv['invoice_id'] ?>)">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm invisible" style="pointer-events: none;">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="history-card">
                <div class="d-flex align-items-center mb-3">
                    <h5 class="fw-bold text-danger m-0"><i class="bi bi-clock-history me-2"></i>Sales Return History</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-danger border-danger">
                            <tr>
                                <th>RT ID</th>
                                <th>Return Date</th>
                                <th>Ref. Invoice</th>
                                <th>Returned Items</th>
                                <th>Processed By</th>
                                <th>Reason</th>
                                <th class="text-end">Refund Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($returnHistory)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No return records found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($returnHistory as $rh): ?>
                                    <tr>
                                        <td class="fw-bold text-danger">RT-<?= $rh['return_id'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($rh['return_date'])) ?></td>
                                        <td>
                                            <a href="#" class="text-primary fw-bold text-decoration-none" onclick="viewDetails(<?= $rh['invoice_id'] ?>)">#<?= $rh['invoice_id'] ?></a>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border border-secondary-subtle fw-normal text-wrap text-start" style="line-height: 1.5; max-width: 250px;">
                                                <?= htmlspecialchars($rh['return_details'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td><i class="bi bi-person me-1 text-muted"></i> <?= htmlspecialchars($rh['pharmacist_name']) ?></td>
                                        <td class="text-muted fst-italic">"<?= htmlspecialchars($rh['reason']) ?>"</td>
                                        <td class="text-end fw-bold text-danger">
                                            - <?= number_format($rh['refund_amount'], 0, ',', '.') ?> ₫
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

    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-navy-blue"><i class="bi bi-receipt text-primary me-2"></i>Invoice Details <span id="modalInvId" class="text-danger"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    
                    <div id="modalPrescriptionInfo"></div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr><th>#</th><th>Code</th><th>Name</th><th>Lot / EXP</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Subtotal</th></tr>
                            </thead>
                            <tbody id="modalDetailsBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modalPrintBtn"><i class="bi bi-printer me-2"></i>Print</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <form action="/invoices/process-return" method="POST">
                    <div class="modal-header bg-warning bg-opacity-10 border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold text-warning-emphasis"><i class="bi bi-arrow-counterclockwise me-2"></i>Sales Return - Invoice <span id="returnModalInvId"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="invoice_id" id="inputReturnInvId">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr><th>Name & Batch</th><th class="text-center">Available Qty</th><th class="text-center" style="width: 150px;">Return Qty *</th></tr>
                                </thead>
                                <tbody id="returnDetailsBody"></tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <label class="form-label fw-bold small">Reason</label>
                            <textarea name="return_reason" class="form-control" rows="2" required placeholder="E.g. Customer changed mind, incorrect item..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning fw-bold"><i class="bi bi-check2-circle me-1"></i>Confirm Return</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const invoiceModal = new bootstrap.Modal(document.getElementById('invoiceModal'));
        const returnModal = new bootstrap.Modal(document.getElementById('returnModal'));
        const formatVND = new Intl.NumberFormat('vi-VN');

        function viewDetails(id) {
            document.getElementById('modalInvId').innerText = '#' + id;
            document.getElementById('modalPrintBtn').onclick = () => window.open('/invoices/print?id=' + id, '_blank');
            
            // Xóa vùng dữ liệu cũ trước khi mở modal mới
            document.getElementById('modalPrescriptionInfo').innerHTML = '';
            document.getElementById('modalDetailsBody').innerHTML = '<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>';
            
            invoiceModal.show();
            
            fetch('/invoices/details?id=' + id).then(r => r.json()).then(data => {
                if (data.error || data.length === 0) {
                    document.getElementById('modalDetailsBody').innerHTML = '<tr><td colspan="7" class="text-center text-danger">No details found.</td></tr>';
                    return;
                }

                // 1. KIỂM TRA VÀ HIỂN THỊ THÔNG TIN ĐƠN THUỐC
                if (data[0].doctor_name) {
                    const pDate = new Date(data[0].prescription_date).toLocaleDateString('en-GB'); // Định dạng dd/mm/yyyy
                    
                    // LỌC RA TÊN CÁC LOẠI THUỐC ETC ĐỂ HIỂN THỊ RÕ RÀNG
                    const etcMedicines = data.filter(item => item.medicine_type === 'ETC')
                                             .map(item => item.medicine_name)
                                             .join(', ');

                    document.getElementById('modalPrescriptionInfo').innerHTML = `
                        <div class="alert alert-info py-2 mb-3 border-0 shadow-sm" style="background: #e7f3ff; color: #0c5460;">
                            <div class="d-flex align-items-start mt-1">
                                <i class="bi bi-file-earmark-medical fs-3 me-3"></i>
                                <div>
                                    <div class="fw-bold mb-1">Prescription Information</div>
                                    <div class="small">Doctor: <b>${data[0].doctor_name}</b> <span class="mx-2">|</span> Date: <b>${pDate}</b></div>
                                    <div class="small text-danger mt-1"><i class="bi bi-capsule me-1"></i>Applied to (ETC): <b>${etcMedicines}</b></div>
                                    ${data[0].diagnosis_note ? `<div class="text-muted small mt-1">Diagnosis: ${data[0].diagnosis_note}</div>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                }

                // 2. VẼ BẢNG CHI TIẾT 
                let html = '';
                data.forEach((item, i) => {
                    let d = new Date(item.expiry_date);
                    let badgeClass = item.medicine_type === 'ETC' ? 'bg-danger' : 'bg-success';
                    
                    html += `<tr>
                                <td>${i+1}</td>
                                <td><span class="badge bg-secondary">${item.medicine_code}</span></td>
                                <td>
                                    <span class="fw-bold text-dark">${item.medicine_name}</span>
                                    <span class="badge ${badgeClass} ms-1" style="font-size: 0.6rem;">${item.medicine_type}</span>
                                </td>
                                <td>Lot: ${item.batch_number}<br><small class="text-muted">EXP: ${d.toLocaleDateString('en-GB')}</small></td>
                                <td class="text-center fw-bold">${item.quantity}</td>
                                <td class="text-end">${formatVND.format(item.unit_price)} ₫</td>
                                <td class="text-end fw-bold text-danger">${formatVND.format(item.subtotal)} ₫</td>
                            </tr>`;
                });
                document.getElementById('modalDetailsBody').innerHTML = html;
            });
        }

        function openReturnModal(id) {
            document.getElementById('returnModalInvId').innerText = '#' + id;
            document.getElementById('inputReturnInvId').value = id;
            returnModal.show();
            fetch('/invoices/details?id=' + id).then(r => r.json()).then(data => {
                let html = '';
                data.forEach((item, i) => {
                    html += `<tr>
                                <td>
                                    <span class="fw-bold">${item.medicine_name}</span><br>
                                    <small>Lot: ${item.batch_number}</small>
                                    
                                    <input type="hidden" name="items[${i}][medicine_name]" value="${item.medicine_name}">
                                    <input type="hidden" name="items[${i}][batch_number]" value="${item.batch_number}">
                                    <input type="hidden" name="items[${i}][batch_id]" value="${item.batch_id}">
                                    <input type="hidden" name="items[${i}][unit_price]" value="${item.unit_price}">
                                </td>
                                <td class="text-center fw-bold fs-5">${item.quantity}</td>
                                <td>
                                    <input type="number" name="items[${i}][return_qty]" class="form-control text-center text-danger" min="0" max="${item.quantity}" value="0">
                                </td>
                             </tr>`;
                });
                document.getElementById('returnDetailsBody').innerHTML = html;
            });
        }
    </script>
</body>
</html>