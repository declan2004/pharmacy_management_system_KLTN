<?php
class InvoiceController extends Controller {
    
    public function index() {
        $this->authorize([1, 2]);
        $invoiceModel = $this->model('Invoice');

        $filters = [
            'search' => $_GET['search'] ?? '', 'date' => $_GET['date'] ?? '',
            'method' => $_GET['method'] ?? '', 'pharmacist_id' => $_GET['pharmacist_id'] ?? ''
        ];
        $roleId = $_SESSION['role_id'];
        $userId = $_SESSION['user_id'];

        $invoices = $invoiceModel->getAllInvoices($roleId, $userId, $filters);
        $filteredRevenue = $invoiceModel->getFilteredRevenue($roleId, $userId, $filters);
        
        // GỌI DATA LỊCH SỬ TRẢ HÀNG
        $returnHistory = $invoiceModel->getSalesReturnHistory($roleId, $userId);

        $pharmacists = ($roleId == 1) ? $invoiceModel->getPharmacists() : [];
        
        $data = [
            'title' => 'Selling History - PMS',
            'fullName' => $_SESSION['full_name'] ?? 'Staff',
            'invoices' => $invoices,
            'filteredRevenue' => $filteredRevenue,
            'returnHistory' => $returnHistory, 
            'pharmacists' => $pharmacists,
            'filters' => $filters
        ];
        $this->view('invoices/index', $data);
    }

    // API trả về JSON chi tiết hóa đơn
    public function details() {
        $this->authorize([1, 2]);
        
        if (!isset($_GET['id'])) {
            echo json_encode(['error' => 'Missing Invoice ID']);
            exit;
        }

        $invoiceId = $_GET['id'];
        $invoiceModel = $this->model('Invoice');
        $details = $invoiceModel->getInvoiceDetails($invoiceId);
        
        header('Content-Type: application/json');
        echo json_encode($details);
        exit;
    }

    // Xuất trang in hóa đơn 
    public function print() {
        $this->authorize([1, 2]);
        
        if (!isset($_GET['id'])) {
            die("Lỗi: Không tìm thấy mã Hóa đơn cần in!");
        }

        $invoiceId = $_GET['id'];
        $invoiceModel = $this->model('Invoice');

        $invoice = $invoiceModel->getInvoiceById($invoiceId);
        $details = $invoiceModel->getInvoiceDetails($invoiceId);

        if (!$invoice) {
            die("Lỗi: Hóa đơn không tồn tại!");
        }

        $data = [
            'invoice' => $invoice,
            'details' => $details
        ];
        $this->view('invoices/print', $data);
    }

    // Xử lý Form Khách trả hàng
    public function processReturn() {
        $this->authorize([1, 2]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoiceId = $_POST['invoice_id'] ?? null;
            $items = $_POST['items'] ?? [];
            $reason = $_POST['return_reason'] ?? '';

            if (!$invoiceId || empty($items)) {
                $_SESSION['error'] = "Invalid data submitted.";
                header('Location: /invoices'); exit;
            }

            $invoiceModel = $this->model('Invoice');
            $result = $invoiceModel->processSalesReturn($invoiceId, $items, $reason, $_SESSION['user_id']);

            if ($result) $_SESSION['success'] = "Return processed successfully. Stock updated.";
            else $_SESSION['error'] = "Failed to process return. Please check quantities.";
            
            header('Location: /invoices'); exit;
        }
    }
}