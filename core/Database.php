<?php
// Gọi file cấu hình vào để lấy thông tin
require_once '../config/database.php';

class Database {
    private static $instance = null;
    private $conn;

    // Hàm khởi tạo kết nối (chỉ chạy 1 lần)
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // Chống SQL Injection
            ];
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Lỗi kết nối Database: " . $e->getMessage());
        }
    }

    // Hàm lấy instance duy nhất của Database (Singleton)
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Hàm trả về đối tượng kết nối PDO
    public function getConnection() {
        return $this->conn;
    }
}

//File này sẽ sử dụng các thông tin cấu hình ở trên để tạo một kết nối bảo mật (PDO) đến Database
//Sử dụng chuẩn Singleton Pattern để hệ thống không bị tạo quá nhiều kết nối thừa gây chậm web.