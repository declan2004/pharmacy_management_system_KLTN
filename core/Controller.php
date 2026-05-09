<?php
class Controller {
    
    // Hàm gọi Model để tương tác với Database
    public function model($model) {
        if (file_exists('../app/models/' . $model . '.php')) {
            require_once '../app/models/' . $model . '.php';
            return new $model();
        }
        die("Error: Model {$model} does not exist.");
    }

    // Hàm gọi View để hiển thị giao diện HTML
    public function view($view, $data = []) {
        if (file_exists('../app/views/' . $view . '.php')) {
            // Tách mảng data thành các biến độc lập
            extract($data);
            require_once '../app/views/' . $view . '.php';
        } else {
            die("Error: View {$view} does not exist.");
        }
    }

    // Hàm kiểm tra quyền truy cập (RBAC)
    public function authorize($roles = []) {
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // 2. Kiểm tra vai trò (Dựa trên role_id trong database)
        // 1: Manager, 2: Pharmacist, 3: Inventory Staff
        $userRole = $_SESSION['role_id'];
        
        if (!empty($roles) && !in_array($userRole, $roles)) {
            // Nếu sai quyền, trả về thông báo lỗi hoặc điều hướng
            die("<h1 style='text-align:center; color:red; margin-top:100px;'>403 Forbidden: You do not have permission to access this page.</h1>");
        }
    }
}