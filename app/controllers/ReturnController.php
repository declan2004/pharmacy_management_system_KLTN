<?php
class ReturnController extends Controller {
    
    // Hiển thị danh sách phiếu trả
    public function index() {
        $this->authorize([1, 3]);
        
        $returnModel = $this->model('ReturnOrder');
        $returns = $returnModel->getAllReturns(); // Lấy dữ liệu thực tế từ DB

        $data = [
            'title'    => 'Return Orders - Pharmacy System',
            'fullName' => $_SESSION['full_name'],
            'returns'  => $returns
        ];
        
        $this->view('returns/index', $data);
    }

    // Lập phiếu trả
    public function create() {
        $this->authorize([1, 3]);
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $items = [];
            
            if (isset($_POST['batch_id']) && is_array($_POST['batch_id'])) {
                for ($i = 0; $i < count($_POST['batch_id']); $i++) {
                    $batchId = $_POST['batch_id'][$i];
                    $qty = (int)$_POST['quantity'][$i];
                    $reason = trim($_POST['return_reason'][$i]);
                    
                    if (empty($batchId) || $qty <= 0) continue;

                    $items[] = [
                        'batch_id'      => $batchId,
                        'quantity'      => $qty,
                        'return_reason' => $reason
                    ];
                }
            }

            if (!empty($items)) {
                $returnData = [
                    'staff_id' => $_SESSION['user_id'],
                    'note'     => trim($_POST['note']),
                    'items'    => $items
                ];

                $returnModel = $this->model('ReturnOrder');
                if ($returnModel->createTransaction($returnData)) {
                    $_SESSION['return_success'] = "Return Order created and stock deducted successfully!";
                    header('Location: /returns'); 
                    exit;
                } else {
                    $error = "System Error: Failed to process return transaction.";
                }
            } else {
                $error = "Please add at least one valid item to return.";
            }
        }

        $inventoryModel = $this->model('Inventory');
        $availableBatches = $inventoryModel->getAllBatches('', 'good'); 
        $expiredBatches = $inventoryModel->getAllBatches('', 'expired');
        
        $allValidBatches = array_merge($availableBatches, array_filter($expiredBatches, function($b) {
            return $b['quantity'] > 0;
        }));

        $data = [
            'title'    => 'Create Return Order',
            'fullName' => $_SESSION['full_name'],
            'batches'  => $allValidBatches,
            'error'    => $error
        ];
        
        $this->view('returns/create', $data);
    }

    // Trả về dữ liệu chi tiết của Phiếu trả dưới dạng JSON (dùng cho Modal AJAX)
    public function show() {
        $this->authorize([1, 3]);

        if (!isset($_GET['id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No ID provided.']);
            exit;
        }

        $returnModel = $this->model('ReturnOrder');
        $returnOrder = $returnModel->getById($_GET['id']);
        
        if (!$returnOrder) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Return order not found.']);
            exit;
        }

        $details = $returnModel->getDetails($_GET['id']);

        // Trả về định dạng JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success'     => true,
            'returnOrder' => $returnOrder,
            'details'     => $details
        ]);
        exit;
    }
}