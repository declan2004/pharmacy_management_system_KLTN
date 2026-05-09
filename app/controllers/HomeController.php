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
        
        $this->view('home', $data);
    }
}