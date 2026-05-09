<?php
class User {
    private $db;

    public function __construct() {
        // Khởi tạo kết nối database thông qua Singleton
        $this->db = Database::getInstance()->getConnection();
    }

    // 1. Hàm xác thực người dùng (Đăng nhập)
    public function authenticate($username, $password) {
        $query = "SELECT * FROM users WHERE username = :username AND status = 'Active' LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch();

        // Kiểm tra password: 
        if ($user) {
            if (password_verify($password, $user['password_hash']) || $password === $user['password_hash']) {
                return $user; 
            }
        }

        return false; 
    }
    // 2. Lấy tất cả người dùng kèm tên vai trò 
    public function getAll() {
        $query = "SELECT u.*, r.role_name 
                  FROM users u 
                  JOIN roles r ON u.role_id = r.role_id 
                  WHERE u.deleted_at IS NULL 
                  ORDER BY u.user_id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 3. Lấy danh sách vai trò 
    public function getRoles() {
        $stmt = $this->db->prepare("SELECT * FROM roles");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 4. Thêm người dùng mới vào Database với mật khẩu được mã hóa an toàn
    public function createUser($data) {
        $query = "INSERT INTO users (role_id, username, password_hash, full_name, status) 
                  VALUES (:role_id, :username, :password, :full_name, :status)";
        $stmt = $this->db->prepare($query);
        
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bindParam(':role_id', $data['role_id']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':status', $data['status']);
        
        return $stmt->execute();
    }

    // 5. Xóa người dùng (Soft Delete )
    public function deleteUser($id) {
        $query = "UPDATE users SET deleted_at = NOW(), status = 'Inactive' WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $id);
        return $stmt->execute();
    }

    // 6. Lấy thông tin 1 user dựa vào ID
    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE user_id = :id AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // 7. Cập nhật thông tin user
    public function updateUser($data) {
        // Nếu người dùng có nhập mật khẩu mới thì update cả mật khẩu
        if (!empty($data['password'])) {
            $query = "UPDATE users 
                      SET role_id = :role_id, full_name = :full_name, status = :status, password_hash = :password 
                      WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            // Nếu bỏ trống mật khẩu thì giữ nguyên mật khẩu cũ
            $query = "UPDATE users 
                      SET role_id = :role_id, full_name = :full_name, status = :status 
                      WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
        }
        
        $stmt->bindParam(':role_id', $data['role_id']);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':user_id', $data['user_id']);
        
        return $stmt->execute();
    }
}