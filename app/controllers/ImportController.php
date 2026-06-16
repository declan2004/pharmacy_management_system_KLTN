<?php
class ImportController extends Controller {
    
    // Hiển thị danh sách phiếu nhập kho
    public function index() {
        $this->authorize([1, 3]);

        $importModel = $this->model('ImportReceipt');
        $imports = $importModel->getAll();

        $data = [
            'title'    => 'Import Receipts - Pharmacy System',
            'fullName' => $_SESSION['full_name'] ?? 'User',
            'imports'  => $imports
        ];
        
        $this->view('imports/index', $data);
    }

    // Hiển thị form tạo phiếu nhập và Xử lý lưu DB
    public function create() {
        $this->authorize([1, 3]);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $supplierName = trim($_POST['supplier_name'] ?? '');
            $note = trim($_POST['note'] ?? '');

            // Bắt lỗi: Nếu bỏ trống Supplier Name hoặc Note thì báo lỗi ngay
            if (empty($supplierName) || empty($note)) {
                $error = "Supplier Name and Note / Reference are required.";
            } else {
                $items = [];
                $batchTracker = []; // Thêm mảng theo dõi Lô - Hạn sử dụng trên Form (Data Integrity)
                
                // Khởi tạo Model để gọi DB check xung đột
                $importModel = $this->model('ImportReceipt');
                
                // Chuyển đổi các mảng song song từ Form thành 1 mảng cấu trúc dễ xử lý
                if (isset($_POST['medicine_id']) && is_array($_POST['medicine_id'])) {
                    $today = date('Y-m-d'); // Lấy ngày hiện tại chuẩn hóa
                    
                    for ($i = 0; $i < count($_POST['medicine_id']); $i++) {
                        $medId = $_POST['medicine_id'][$i];
                        $batchNo = trim($_POST['batch_number'][$i]);
                        $batchNo = strtoupper($batchNo); // Chuẩn hóa mã lô luôn in hoa
                        $expiry = $_POST['expiry_date'][$i];
                        $qty = (int)$_POST['quantity'][$i];
                        $price = (float)$_POST['import_price'][$i];
                        
                        // Bỏ qua các dòng trống (nếu người dùng bấm Add Row mà không điền)
                        if (empty($medId) || empty($batchNo) || $qty <= 0) continue;

                        // BẮT LỖI NGHIỆP VỤ 1: Chặn hạn sử dụng trong quá khứ hoặc là ngày hôm nay
                        if (strtotime($expiry) <= strtotime($today)) {
                            $error = "System Error: Cannot import medicine. The expiry date ($expiry) for batch $batchNo is invalid or already expired.";
                            $items = []; // Xóa rỗng mảng items để chặn điều kiện lưu DB bên dưới
                            break;       
                        }

                        // BẮT LỖI NGHIỆP VỤ 2 (FORM INTEGRITY): Cùng Lô phải cùng Hạn sử dụng trên cùng 1 tờ phiếu nhập
                        if (isset($batchTracker[$medId][$batchNo])) {
                            if ($batchTracker[$medId][$batchNo] !== $expiry) {
                                $error = "Data Integrity Error: Batch '{$batchNo}' has conflicting expiry dates on this form. The same batch must have the exact same expiry date.";
                                $items = []; 
                                break;
                            }
                        } else {
                            $batchTracker[$medId][$batchNo] = $expiry;
                        }

                        // BẮT LỖI NGHIỆP VỤ 3 (DATABASE INTEGRITY): Kiểm tra xem lô đã tồn tại trong hệ thống chưa
                        $existingExpiry = $importModel->getBatchExpiryDate($medId, $batchNo);
                        if ($existingExpiry !== null && $existingExpiry !== $expiry) {
                            $error = "Database Conflict: Batch '{$batchNo}' already exists in the system with expiry date '{$existingExpiry}'. The date you entered ('{$expiry}') does not match.";
                            $items = []; 
                            break;
                        }

                        $items[] = [
                            'medicine_id'  => $medId,
                            'batch_number' => $batchNo, 
                            'expiry_date'  => $expiry,
                            'quantity'     => $qty,
                            'import_price' => $price
                        ];
                    }
                }
                
                if (!empty($items)) {
                    $importData = [
                        'staff_id'      => $_SESSION['user_id'], 
                        'supplier_name' => $supplierName, 
                        'note'          => $note,         
                        'items'         => $items
                    ];

                    if ($importModel->createTransaction($importData)) {
                        $_SESSION['import_success'] = "Receipt created and inventory updated successfully!";
                        header('Location: /imports');
                        exit;
                    } else {
                        $error = "System Error: Failed to process transaction. Transaction rolled back.";
                    }
                } else if (!isset($error)) {
                    $error = "Please add at least one valid medicine to the receipt.";
                }
            }
        }

        $medicineModel = $this->model('Medicine');
        $medicines = $medicineModel->getAll();

        $data = [
            'title'       => 'Create Import Receipt - Pharmacy System',
            'fullName'    => $_SESSION['full_name'] ?? 'User',
            'medicines'   => $medicines,
            'error'       => $error ?? ''
            
        ];
        
        $this->view('imports/create', $data);
    }

    // Trả về dữ liệu chi tiết của Phiếu nhập dưới dạng JSON (dùng cho Modal AJAX)
    public function show() {
        $this->authorize([1, 3]);

        if (!isset($_GET['id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No ID provided.']);
            exit;
        }

        $importModel = $this->model('ImportReceipt');
        $import = $importModel->getById($_GET['id']);
        
        if (!$import) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Import receipt not found.']);
            exit;
        }

        $details = $importModel->getDetails($_GET['id']);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'import'  => $import,
            'details' => $details
        ]);
        exit;
    }
}