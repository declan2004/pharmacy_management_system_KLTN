<?php
class Medicine {
    private $db;

    public function __construct() {
        // Khởi tạo kết nối database
        $this->db = Database::getInstance()->getConnection();
    }

    // Lấy danh sách toàn bộ danh mục thuốc
    public function getAll() {
        $query = "SELECT * FROM medicines 
                  WHERE deleted_at IS NULL 
                  ORDER BY medicine_id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Thêm danh mục thuốc mới
    public function createMedicine($data) {
        $query = "INSERT INTO medicines 
                  (medicine_code, barcode, medicine_name, active_ingredient, concentration, unit, base_price, medicine_type, description) 
                  VALUES 
                  (:medicine_code, :barcode, :medicine_name, :active_ingredient, :concentration, :unit, :base_price, :medicine_type, :description)";
        
        $stmt = $this->db->prepare($query);
        
        // Xử lý Barcode: Nếu người dùng không nhập thì gán thành NULL để không bị lỗi trùng lặp (UNIQUE) ở Database
        $barcode = !empty($data['barcode']) ? $data['barcode'] : null;

        $stmt->bindParam(':medicine_code', $data['medicine_code']);
        $stmt->bindParam(':barcode', $barcode);
        $stmt->bindParam(':medicine_name', $data['medicine_name']);
        $stmt->bindParam(':active_ingredient', $data['active_ingredient']);
        $stmt->bindParam(':concentration', $data['concentration']);
        $stmt->bindParam(':unit', $data['unit']);
        $stmt->bindParam(':base_price', $data['base_price']);
        $stmt->bindParam(':medicine_type', $data['medicine_type']);
        $stmt->bindParam(':description', $data['description']);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Nếu lỗi (ví dụ trùng mã thuốc), trả về false để Controller báo lỗi cho người dùng
            return false;
        }
    }

    // Lấy danh sách 5 thuốc vừa thêm gần đây nhất
    public function getRecent($limit = 5) {
        $query = "SELECT * FROM medicines 
                  WHERE deleted_at IS NULL 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy thông tin 1 loại thuốc theo ID 
    public function getById($id) {
        $query = "SELECT * FROM medicines WHERE medicine_id = :id AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Cập nhật thông tin thuốc
    public function updateMedicine($data) {
        $query = "UPDATE medicines 
                  SET medicine_name = :medicine_name, 
                      barcode = :barcode, 
                      active_ingredient = :active_ingredient, 
                      concentration = :concentration, 
                      unit = :unit, 
                      base_price = :base_price, 
                      medicine_type = :medicine_type, 
                      description = :description
                  WHERE medicine_id = :medicine_id";
        
        $stmt = $this->db->prepare($query);
        
        $barcode = !empty($data['barcode']) ? $data['barcode'] : null;

        // Lưu ý: Không cho phép sửa medicine_code để đảm bảo tính toàn vẹn dữ liệu
        $stmt->bindParam(':medicine_name', $data['medicine_name']);
        $stmt->bindParam(':barcode', $barcode);
        $stmt->bindParam(':active_ingredient', $data['active_ingredient']);
        $stmt->bindParam(':concentration', $data['concentration']);
        $stmt->bindParam(':unit', $data['unit']);
        $stmt->bindParam(':base_price', $data['base_price']);
        $stmt->bindParam(':medicine_type', $data['medicine_type']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':medicine_id', $data['medicine_id'], PDO::PARAM_INT);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Xóa mềm thuốc (Soft Delete)
    public function deleteMedicine($id) {
        // Cập nhật trường deleted_at thành thời gian hiện tại
        $query = "UPDATE medicines SET deleted_at = CURRENT_TIMESTAMP WHERE medicine_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Barcode POS
    public function getMedicineByBarcode($barcode) {
    // Prevent SQL Injection using prepared statements
    $stmt = $this->db->prepare("SELECT * FROM medicines WHERE barcode = ? AND deleted_at IS NULL");
    $stmt->execute([$barcode]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}