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
            $items = [];
            
            // Chuyển đổi các mảng song song từ Form thành 1 mảng cấu trúc dễ xử lý
            if (isset($_POST['medicine_id']) && is_array($_POST['medicine_id'])) {
                for ($i = 0; $i < count($_POST['medicine_id']); $i++) {
                    $medId = $_POST['medicine_id'][$i];
                    $batchNo = trim($_POST['batch_number'][$i]);
                    $expiry = $_POST['expiry_date'][$i];
                    $qty = (int)$_POST['quantity'][$i];
                    $price = (float)$_POST['import_price'][$i];
                    
                    // Bỏ qua các dòng trống (nếu người dùng bấm Add Row mà không điền)
                    if (empty($medId) || empty($batchNo) || $qty <= 0) continue;

                    $items[] = [
                        'medicine_id'  => $medId,
                        'batch_number' => strtoupper($batchNo), 
                        'expiry_date'  => $expiry,
                        'quantity'     => $qty,
                        'import_price' => $price
                    ];
                }
            }

            if (!empty($items)) {
                $importData = [
                    'staff_id'      => $_SESSION['user_id'], 
                    'supplier_name' => trim($_POST['supplier_name']),
                    'note'          => trim($_POST['note']),
                    'items'         => $items
                ];

                $importModel = $this->model('ImportReceipt');
                if ($importModel->createTransaction($importData)) {
                    $_SESSION['import_success'] = "Receipt created and inventory updated successfully!";
                    header('Location: /imports');
                    exit;
                } else {
                    $error = "System Error: Failed to process transaction. Transaction rolled back.";
                }
            } else {
                $error = "Please add at least one valid medicine to the receipt.";
            }
        }

        $medicineModel = $this->model('Medicine');
        $medicines = $medicineModel->getAll();

        $data = [
            'title'     => 'Create Import Receipt - Pharmacy System',
            'fullName'  => $_SESSION['full_name'],
            'medicines' => $medicines,
            'error'     => $error ?? ''
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

        // Trả về định dạng JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'import'  => $import,
            'details' => $details
        ]);
        exit;
    }
}