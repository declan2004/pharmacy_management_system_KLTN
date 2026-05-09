<?php
class UserController extends Controller {
    public function index() {
        // Chỉ Manager (role_id = 1) mới được vào đây
        $this->authorize([1]);

        $userModel = $this->model('User');
        $users = $userModel->getAll();

        $data = [
            'title' => 'User Management - Pharmacy System',
            'fullName' => $_SESSION['full_name'],
            'users' => $users
        ];
        
        $this->view('users/index', $data);
    }

    // Hiển thị form và xử lý tạo tài khoản mới
    public function create() {
        // Chỉ Manager mới được phép truy cập
        $this->authorize([1]); 
        
        $userModel = $this->model('User');

        // Nếu người dùng bấm nút Submit form (POST)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'full_name' => trim($_POST['full_name']),
                'username'  => trim($_POST['username']),
                'password'  => $_POST['password'],
                'role_id'   => $_POST['role_id'],
                'status'    => $_POST['status']
            ];

            // Gọi Model để insert vào DB
            if ($userModel->createUser($data)) {
                // Tạo thành công, điều hướng về lại danh sách
                header('Location: /users');
                exit;
            } else {
                $error = "Failed to create user. Username might already exist.";
            }
        }

        // Nếu truy cập bình thường (GET), hiển thị form
        $roles = $userModel->getRoles();
        
        $data = [
            'title'    => 'Add New Staff - Pharmacy System',
            'fullName' => $_SESSION['full_name'],
            'roles'    => $roles,
            'error'    => $error ?? ''
        ];
        
        $this->view('users/create', $data);
    }

    // Xử lý khi manager bấm nút Xóa
    public function delete() {
        $this->authorize([1]);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
            $userId = $_POST['user_id'];
            
            // Không cho phép Admin tự xóa chính mình
            if ($userId == $_SESSION['user_id']) {
                die("Error: You cannot delete your own account.");
            }

            $userModel = $this->model('User');
            $userModel->deleteUser($userId);
        }

        header('Location: /users');
        exit;
    }

    // Hiển thị form Edit và xử lý cập nhật
    public function edit() {
        $this->authorize([1]); // Chỉ Manager
        $userModel = $this->model('User');

        // Xử lý khi người dùng bấm nút Save (POST)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'user_id'   => $_POST['user_id'],
                'full_name' => trim($_POST['full_name']),
                'password'  => $_POST['password'], // Bỏ trống = không đổi
                'role_id'   => $_POST['role_id'],
                'status'    => $_POST['status']
            ];

            if ($userModel->updateUser($data)) {
                header('Location: /users');
                exit;
            }
        }

        // Khi load trang (GET), lấy ID từ thanh địa chỉ (VD: /users/edit?id=5)
        if (!isset($_GET['id'])) {
            header('Location: /users');
            exit;
        }

        $userId = $_GET['id'];
        $editUser = $userModel->getUserById($userId);
        
        if (!$editUser) {
            die("User not found or has been deleted.");
        }

        $roles = $userModel->getRoles();
        
        $data = [
            'title'    => 'Edit Staff - Pharmacy System',
            'fullName' => $_SESSION['full_name'],
            'roles'    => $roles,
            'editUser' => $editUser
        ];
        
        $this->view('users/edit', $data);
    }
}