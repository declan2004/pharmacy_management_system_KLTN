<?php
class HomeController extends Controller {
    
    public function index() {
        // Kiểm tra xem đã có session user_id chưa (đã đăng nhập chưa)
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $fullName = $_SESSION['full_name'] ?? 'User';

        $data = [
            'title' => 'Dashboard - Pharmacy Management System',
            'fullName' => $fullName
        ];
        
        $this->view('dashboard/index', $data);
    }

    // THÊM MỚI: API Endpoint trả về dữ liệu Real-time (JSON) cho Dashboard
    public function apiStats() {
        // Kiểm tra bảo mật: Phải đăng nhập mới được gọi API này
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        // Gọi Model Dashboard 
        $dashboardModel = $this->model('Dashboard');
        
        // Lấy toàn bộ dữ liệu thống kê
        $data = [
            'stats' => $dashboardModel->getQuickStats(),
            'inventory' => $dashboardModel->getInventoryOverview(),
            'warnings' => $dashboardModel->getWarnings()
        ];
        
        // Trả dữ liệu về dưới dạng JSON cho Javascript xử lý
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}