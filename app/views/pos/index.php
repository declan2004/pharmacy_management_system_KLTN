<?php
/** * @var string $title | @var string $fullName */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'POS - PharmaCare' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --sidebar-bg: #2c3e50; --sidebar-hover: #34495e; --main-bg: #f4f7f6; --text-light: #ecf0f1; --navy-blue: #152b48; }
        body { background-color: var(--main-bg); font-family: 'Segoe UI', Tahoma, sans-serif; overflow: hidden; }
        
        /* SIDEBAR */
        .sidebar { width: 250px; height: 100vh; background-color: var(--sidebar-bg); position: fixed; top: 0; left: 0; color: var(--text-light); z-index: 1000; }
        .sidebar-header { padding: 20px; font-size: 1.5rem; font-weight: 700; background-color: #1a252f; display: flex; align-items: center; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; color: #7f8c8d; padding: 15px 20px 5px; font-weight: bold; letter-spacing: 1px; }
        .sidebar a { padding: 12px 20px; text-decoration: none; color: #bdc3c7; display: flex; align-items: center; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--sidebar-hover); color: #ffffff; border-left: 4px solid #3498db; }
        .sidebar a i { margin-right: 15px; font-size: 1.1rem; }
        
        /* LAYOUT CHÍNH */
        .main-content { margin-left: 250px; height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { background-color: #3498db; height: 60px; min-height: 60px; display: flex; align-items: center; justify-content: flex-end; padding: 0 20px; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 10; }
        
        /* POS WRAPPER */
        .pos-wrapper { padding: 20px; flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .pos-layout { display: flex; gap: 20px; flex: 1; overflow: hidden; }
        
        /* 2 KHỐI CARD */
        .pos-card-left { flex: 6; background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; flex-direction: column; overflow: hidden; border: 1px solid #e0e0e0; }
        .pos-card-right { flex: 4; background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; flex-direction: column; overflow: hidden; border: 1px solid #e0e0e0; }
        
        /* UI COMPONENTS */
        .search-bar { box-shadow: 0 2px 10px rgba(0,0,0,0.03); border-radius: 10px; border: 1px solid #ced4da; }
        .product-grid { flex-grow: 1; overflow-y: auto; padding-right: 10px; margin-top: 15px; }
        .product-card { border: 1px solid #e0e0e0; border-radius: 10px; padding: 15px; cursor: pointer; transition: all 0.2s; }
        .product-card:hover { border-color: #3498db; background-color: #f0f8ff; box-shadow: 0 4px 10px rgba(52, 152, 219, 0.15); transform: translateY(-2px); }
        
        .cart-items { flex-grow: 1; overflow-y: auto; margin-bottom: 20px; background: #f8f9fa; border-radius: 10px; padding: 10px; border: 1px solid #e9ecef; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #ced4da; padding: 10px 0; }
        .cart-item:last-child { border-bottom: none; }
        .qty-control { width: 40px; text-align: center; border: 1px solid #ced4da; border-radius: 5px; }
        .checkout-panel { background: white; border-radius: 10px; padding-top: 10px; }
        .total-amount { font-size: 2rem; font-weight: bold; color: #e74c3c; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><i class="bi bi-capsule me-2"></i> PMS</div>
        <div class="nav-category">Main</div>
        <a href="/"><i class="bi bi-speedometer2"></i> Dashboard</a>
        
        <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): ?>
            <div class="nav-category">Sales & Pos</div>
            <a href="/pos" class="active"><i class="bi bi-cart-plus"></i> Create Invoice</a>
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
                ?>
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-5 me-2"></i>
                    <strong><?= htmlspecialchars($fullName ?? 'Staff') ?> (<?= $displayRole ?>)</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item py-2" href="/profile"><i class="bi bi-person me-2 text-muted"></i> Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</a></li>
                </ul>
            </div>
        </div>

        <div class="pos-wrapper">
            <h4 class="fw-bold text-navy-blue mb-3">Point of Sale</h4>
            
            <div class="pos-layout">
                <div class="pos-card-left">
                    <div class="input-group search-bar">
                        <span class="input-group-text bg-white border-0 text-primary"><i class="bi bi-search fs-5"></i></span>
                        <input type="text" id="searchInput" class="form-control form-control-lg border-0 bg-white shadow-none" placeholder="Scan barcode or type medicine name/code..." autocomplete="off" autofocus>
                    </div>

                    <div class="product-grid" id="productGrid">
                        <div class="text-center text-muted mt-5" id="loadingState" style="display: none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2">Loading catalog...</p>
                        </div>
                    </div>
                </div>

                <div class="pos-card-right">
                    <h5 class="fw-bold mb-3"><i class="bi bi-cart3 me-2"></i>Current Cart</h5>
                    
                    <form id="checkoutForm" action="/pos/checkout" method="POST" class="d-flex flex-column h-100">
                        <div class="cart-items" id="cartContainer">
                        </div>

                        <div class="checkout-panel">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted fw-bold">Total Items:</span>
                                <span class="fw-bold fs-5" id="totalItems">0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-bold fs-5">Grand Total:</span>
                                <span class="total-amount" id="grandTotal">0 ₫</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Payment Method</label>
                                <select name="payment_method" class="form-select form-select-lg">
                                    <option value="Cash">💵 Cash</option>
                                    <option value="Bank Transfer">🏦 Bank Transfer</option>
                                </select>
                            </div>

                            <button type="button" class="btn btn-danger btn-lg w-100 fw-bold shadow-sm" onclick="submitCheckout()">
                                <i class="bi bi-check2-circle me-2"></i> Complete Checkout
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="prescriptionModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger bg-opacity-10 border-bottom-0">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-file-medical me-2"></i>Prescription Information Required
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-danger small mb-4">
                        <i class="bi bi-exclamation-octagon-fill me-1"></i>
                        Your cart contains prescription medicines (ETC). Please enter the prescription details!
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Prescribing Doctor <span class="text-danger">*</span></label>
                        <input type="text" id="doctorName" class="form-control" placeholder="e.g., Dr. John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Prescription Date <span class="text-danger">*</span></label>
                        <input type="date" id="prescriptionDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Diagnosis / Notes</label>
                        <textarea id="diagnosisNote" class="form-control" rows="2" placeholder="e.g., Acute pharyngitis..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger fw-bold" onclick="submitWithPrescription()">
                        <i class="bi bi-check-circle me-1"></i>Confirm & Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        <?php if (isset($_SESSION['checkout_success'])): ?>
            <?php 
                $invData = $_SESSION['checkout_success']; 
                $invId = $invData['invoice_id'];
                $method = $invData['method'];
                $total = $invData['total'];
            ?>
            
            const payMethod = '<?= $method ?>';
            const totalAmt = <?= $total ?>;
            const invoiceId = '<?= $invId ?>';
            const formattedTotal = new Intl.NumberFormat('vi-VN').format(totalAmt) + ' ₫';

            window.currentCheckoutTotal = totalAmt;
            window.calcChange = function() {
                const given = document.getElementById('customerGiven').value;
                const change = given - window.currentCheckoutTotal;
                if (change >= 0) {
                    document.getElementById('changeAmount').innerText = new Intl.NumberFormat('vi-VN').format(change) + ' ₫';
                } else {
                    document.getElementById('changeAmount').innerText = 'Insufficient amount';
                }
            };

            if (payMethod === 'Bank Transfer') {
                const bankBin = '970422'; 
                const bankAccount = '0123456789'; 
                const qrUrl = `https://img.vietqr.io/image/${bankBin}-${bankAccount}-compact2.png?amount=${totalAmt}&addInfo=Payment Inv ${invoiceId}&accountName=PharmaCare`;

                Swal.fire({
                    title: 'Scan QR Code to Pay',
                    html: `
                        <p class="text-muted mb-2">Invoice <b>#${invoiceId}</b></p>
                        <h2 class="text-danger fw-bold mb-3">${formattedTotal}</h2>
                        <img src="${qrUrl}" alt="VietQR" style="width: 250px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <p class="mt-3 text-primary"><i class="bi bi-arrow-repeat spin"></i> Waiting for customer payment...</p>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#27ae60',
                    cancelButtonColor: '#95a5a6',
                    confirmButtonText: '<i class="bi bi-printer"></i> Confirm & Print',
                    cancelButtonText: 'Close',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success', title: 'Printing...', text: 'Payment confirmed. Invoice #' + invoiceId + ' sent to printer!',
                            timer: 2500, showConfirmButton: false
                        });
                        window.open(`/invoices/print?id=${invoiceId}`, '_blank');
                    }
                });

            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Checkout Successful!',
                    html: `
                        <h3 class="text-success fw-bold mt-2">${formattedTotal}</h3>
                        <p>Invoice: <b>#${invoiceId}</b></p>
                        <hr>
                        <div class="form-group text-start px-3">
                            <label class="text-muted small">Customer Given:</label>
                            <input type="number" id="customerGiven" class="form-control form-control-lg text-end" placeholder="Enter amount..." onkeyup="window.calcChange()">
                            <label class="text-muted small mt-2">Change:</label>
                            <h4 id="changeAmount" class="text-danger text-end fw-bold">0 ₫</h4>
                        </div>
                    `,
                    confirmButtonColor: '#3498db',
                    confirmButtonText: '<i class="bi bi-printer"></i> Print Invoice'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success', title: 'Printing...', text: 'Invoice #' + invoiceId + ' sent to printer successfully!',
                            timer: 2500, showConfirmButton: false
                        });
                        window.open(`/invoices/print?id=${invoiceId}`, '_blank');
                    }
                });
            }

            <?php unset($_SESSION['checkout_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Checkout Failed!',
                text: '<?= $_SESSION['error'] ?>',
                confirmButtonColor: '#e74c3c'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>

    <script>
        let cart = {}; 
        let allMedicines = []; 

        function formatVND(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
        }

        document.addEventListener('DOMContentLoaded', () => { 
            fetchMedicines(); 
            renderCart(); 
            
            // Chặn chọn ngày tương lai
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('prescriptionDate').setAttribute('max', today);
        });

        document.getElementById('checkoutForm').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') e.preventDefault();
        });

        function fetchMedicines() {
            document.getElementById('loadingState').style.display = 'block';
            document.getElementById('productGrid').innerHTML = '<div class="text-center text-muted mt-5" id="loadingState"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading catalog...</p></div>';

            fetch('/pos/search')
                .then(res => res.json())
                .then(data => {
                    if (data.error) throw new Error(data.error);
                    allMedicines = data; 
                    renderProducts(allMedicines); 
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('productGrid').innerHTML = '<div class="alert alert-danger m-3">Failed to load medicines. Please check server.</div>';
                });
        }

        document.getElementById('searchInput').addEventListener('input', function (e) {
            const val = e.target.value.trim().toLowerCase();
            
            const filteredMedicines = allMedicines.filter(med => {
                const nameMatch = med.medicine_name && med.medicine_name.toLowerCase().includes(val);
                const codeMatch = med.medicine_code && med.medicine_code.toLowerCase().includes(val);
                const barcodeMatch = med.barcode && med.barcode.toLowerCase().includes(val);
                return nameMatch || codeMatch || barcodeMatch;
            });

            renderProducts(filteredMedicines);
        });

        document.getElementById('searchInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = this.value.trim().toLowerCase();
                const filtered = allMedicines.filter(med => (med.barcode && med.barcode.toLowerCase() === val) || (med.medicine_code && med.medicine_code.toLowerCase() === val));
                if (filtered.length === 1) {
                    addToCart(filtered[0].medicine_id);
                    this.value = ''; 
                    renderProducts(allMedicines); 
                }
            }
        });

        function renderProducts(medicines) {
            const grid = document.getElementById('productGrid');
            grid.innerHTML = ''; 

            if (!Array.isArray(medicines) || medicines.length === 0) {
                grid.innerHTML = '<div class="text-center text-muted mt-5">No items match your search.</div>';
                return;
            }

            let html = '';
            medicines.forEach(med => {
                const isOTC = med.medicine_type === 'OTC';
                const badge = `<span class="badge ${isOTC ? 'bg-success' : 'bg-danger'} small">${med.medicine_type}</span>`;
                
                html += `
                    <div class="product-card mb-2" onclick="addToCart('${med.medicine_id}')">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-1 text-primary">${med.medicine_name} ${badge}</h6>
                                <small class="text-muted">Code: ${med.medicine_code}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-dark">${formatVND(med.price || 0)}</div>
                                <small class="text-success fw-bold">Stock: ${med.total_qty}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            grid.innerHTML = html;
        }

        function addToCart(id) {
            id = String(id);
            const med = allMedicines.find(m => String(m.medicine_id) === id);
            if (!med) return;

            const totalQty = parseInt(med.total_qty) || 0;

            if (cart[id]) {
                if (parseInt(cart[id].qty) < totalQty) {
                    cart[id].qty = parseInt(cart[id].qty) + 1;
                } else {
                    Swal.fire('Limit Exceeded', 'Cannot exceed available stock (' + totalQty + ')', 'warning');
                }
            } else {
                cart[id] = { ...med, qty: 1 };
            }
            renderCart();
        }

        function updateQty(id, delta) {
            id = String(id);
            const item = cart[id];
            if (!item) return;

            let newQty = parseInt(item.qty) + parseInt(delta);
            const totalQty = parseInt(item.total_qty) || 0;
            
            if (newQty <= 0) {
                delete cart[id]; 
            } else if (newQty > totalQty) {
                Swal.fire('Limit Exceeded', 'Maximum stock reached!', 'warning');
            } else {
                item.qty = newQty;
            }
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cartContainer');
            const keys = Object.keys(cart);
            
            if (keys.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted mt-5">
                        <i class="bi bi-cart-x fs-1 opacity-50"></i>
                        <p class="mt-2">Cart is empty</p>
                    </div>
                `;
                document.getElementById('totalItems').innerText = '0';
                document.getElementById('grandTotal').innerText = '0 ₫';
                return;
            }

            let html = '';
            let totalAmt = 0;
            let totalItemCount = 0;

            keys.forEach(id => {
                const item = cart[id];
                const price = parseFloat(item.price) || 0;
                const subtotal = item.qty * price;
                
                totalAmt += subtotal;
                totalItemCount += item.qty;

                const isOTC = item.medicine_type === 'OTC';
                const badge = `<span class="badge ${isOTC ? 'bg-success' : 'bg-danger'}" style="font-size: 0.65rem;">${item.medicine_type}</span>`;

                html += `
                    <div class="cart-item">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">${item.medicine_name} ${badge}</div>
                            <div class="text-muted small">${formatVND(price)} x ${item.qty}</div>
                            
                            <input type="hidden" name="medicine_id[]" value="${item.medicine_id}">
                            <input type="hidden" name="price[]" value="${price}">
                            <input type="hidden" name="quantity[]" value="${item.qty}">
                        </div>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-light border" onclick="updateQty('${id}', -1)">-</button>
                            <input type="text" class="qty-control mx-1 bg-light fw-bold" readonly value="${item.qty}">
                            <button type="button" class="btn btn-sm btn-light border" onclick="updateQty('${id}', 1)">+</button>
                            <div class="fw-bold ms-3" style="width: 80px; text-align: right;">${formatVND(subtotal)}</div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            document.getElementById('totalItems').innerText = totalItemCount;
            document.getElementById('grandTotal').innerText = formatVND(totalAmt);
            container.scrollTop = container.scrollHeight; 
        }

        // CHECKOUT LOGIC (ETC / GPP COMPLIANCE)
        
        function processFinalCheckout() {
            Swal.fire({
                title: 'Confirm Checkout?',
                text: "You are about to process this order.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Yes, checkout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading() }
                    });
                    document.getElementById('checkoutForm').submit(); 
                }
            });
        }

        function submitCheckout() {
            const keys = Object.keys(cart);
            if (keys.length === 0) {
                Swal.fire('Empty Cart', 'Please select at least one item to checkout.', 'warning');
                return;
            }
            
            let hasETC = false;
            for (let i = 0; i < keys.length; i++) {
                if (cart[keys[i]].medicine_type === 'ETC') {
                    hasETC = true;
                    break;
                }
            }

            if (hasETC) {
                const pModal = new bootstrap.Modal(document.getElementById('prescriptionModal'));
                pModal.show();
            } else {
                processFinalCheckout();
            }
        }

        function submitWithPrescription() {
            const docName = document.getElementById('doctorName').value.trim();
            const preDate = document.getElementById('prescriptionDate').value;
            const note = document.getElementById('diagnosisNote').value.trim();

            if (docName === '' || preDate === '') {
                Swal.fire('Missing Information!', 'Please enter both the Prescribing Doctor and Prescription Date.', 'error');
                return;
            }

            // KHÓA LOGIC CHẶN NGÀY TƯƠNG LAI
            const selectedDate = new Date(preDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0); 

            if (selectedDate > today) {
                Swal.fire('Invalid Date!', 'The prescription date cannot be in the future.', 'error');
                return;
            }

            const form = document.getElementById('checkoutForm');
            
            document.querySelectorAll('.prescription-hidden-input').forEach(e => e.remove());

            form.insertAdjacentHTML('beforeend', `<input type="hidden" class="prescription-hidden-input" name="doctor_name" value="${docName}">`);
            form.insertAdjacentHTML('beforeend', `<input type="hidden" class="prescription-hidden-input" name="prescription_date" value="${preDate}">`);
            form.insertAdjacentHTML('beforeend', `<input type="hidden" class="prescription-hidden-input" name="diagnosis_note" value="${note}">`);

            const modalEl = document.getElementById('prescriptionModal');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            modalInstance.hide();

            processFinalCheckout();
        }
    </script>

    <script>
    let barcodeString = "";
    let lastKeyTime = Date.now();

    // Listen for keypress events globally
    window.addEventListener('keypress', function(e) {
        let currentTime = Date.now();
        
        // If time between keystrokes is > 30ms, it's manual typing, reset the string
        if (currentTime - lastKeyTime > 30) {
            barcodeString = "";
        }
        
        // If Enter is pressed and string is long enough (Barcode usually > 8 chars)
        if (e.key === "Enter" && barcodeString.length > 8) {
            e.preventDefault(); // Prevent form submission
            
            scanBarcode(barcodeString); 
            barcodeString = ""; // Reset for the next scan
        } else if (e.key !== "Enter") {
            barcodeString += e.key; 
        }
        
        lastKeyTime = currentTime;
    });

    function scanBarcode(barcode) {
        fetch('/pos/apiScanBarcode?barcode=' + barcode)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    addToCart(data.medicine);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An unexpected error occurred while scanning.");
            });
    }
</script>
</body>
</html>