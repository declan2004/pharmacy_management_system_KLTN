<?php
class InventoryController extends Controller {
    
    public function index() {
        $this->authorize([1, 3]);
        
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';

        $inventoryModel = $this->model('Inventory');
        $batches = $inventoryModel->getAllBatches($search, $status);

        // Lấy lịch sử điều chỉnh từ Session 
        $adjustments = $_SESSION['adjustment_history'] ?? [];

        $data = [
            'title'       => 'Manage Inventory - Pharmacy System',
            'fullName'    => $_SESSION['full_name'],
            'batches'     => $batches,
            'search'      => $search,
            'status'      => $status,
            'adjustments' => $adjustments 
        ];
        
        $this->view('inventory/index', $data);
    }

    // API Điều chỉnh kho
    public function adjust() {
        $this->authorize([1, 3]);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $batchId = $_POST['batch_id'];
            $newQty = $_POST['new_quantity'];
            $reason = $_POST['reason']; 
            
            // Lấy thêm các thông tin ẩn từ Form để lưu log
            $medicineName = $_POST['medicine_name'];
            $batchNumber = $_POST['batch_number'];
            $oldQty = $_POST['old_quantity'];

            $inventoryModel = $this->model('Inventory');
            if ($inventoryModel->updateQuantity($batchId, $newQty)) {
                
                // Khởi tạo mảng Session nếu chưa có
                if (!isset($_SESSION['adjustment_history'])) {
                    $_SESSION['adjustment_history'] = [];
                }

                array_unshift($_SESSION['adjustment_history'], [
                    'time'     => date('d/m/Y H:i:s'),
                    'staff'    => $_SESSION['full_name'],
                    'medicine' => $medicineName,
                    'batch'    => $batchNumber,
                    'old_qty'  => $oldQty,
                    'new_qty'  => $newQty,
                    'reason'   => $reason
                ]);

                $_SESSION['inventory_msg'] = "Stock adjusted successfully.";
            }
            header('Location: /inventory');
            exit;
        }
    }

    // Xuất báo cáo CSV
    public function export() {
        $this->authorize([1, 3]);
        $inventoryModel = $this->model('Inventory');
        $batches = $inventoryModel->getAllBatches();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=inventory_report_'.date('Ymd').'.csv');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Medicine Code', 'Medicine Name', 'Batch No', 'Expiry Date', 'Quantity', 'Unit', 'Type']);
        
        foreach ($batches as $row) {
            fputcsv($output, [
                $row['medicine_code'], $row['medicine_name'], $row['batch_number'], 
                $row['expiry_date'], $row['quantity'], $row['unit'], $row['medicine_type']
            ]);
        }
        fclose($output);
        exit;
    }
}