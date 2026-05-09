<?php
class AuthController extends Controller {
    
    public function login() {
        // Kiểm tra xem người dùng có đang gửi dữ liệu từ form lên không (phương thức POST)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            // Gọi Model User để kiểm tra thông tin trong Database
            $userModel = $this->model('User');
            $loggedInUser = $userModel->authenticate($username, $password);

            if ($loggedInUser) {
                $_SESSION['user_id'] = $loggedInUser['user_id'];
                $_SESSION['role_id'] = $loggedInUser['role_id'];
                $_SESSION['full_name'] = $loggedInUser['full_name'];
                header('Location: /');
                exit;
            } else {
                $data = [
                    'title' => 'Login - Pharmacy Management System',
                    'error' => 'Invalid username or password!' 
                ];
                $this->view('auth/login', $data);
                return;
            }
        }

        // Nếu chỉ là truy cập bình thường (GET), hiển thị form rỗng
        $data = [
            'title' => 'Login - Pharmacy Management System',
            'error' => '' 
        ];
        $this->view('auth/login', $data);
    }
public function logout() {
        session_unset();
        session_destroy();

        header('Location: /login');
        exit;
    }
    }