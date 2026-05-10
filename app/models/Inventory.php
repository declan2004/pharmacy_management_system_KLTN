<?php
class Inventory {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Lấy tồn kho có hỗ trợ tìm kiếm và lọc
    public function getAllBatches($search = '', $statusFilter = '') {
        $query = "SELECT b.*, m.medicine_code, m.medicine_name, m.medicine_type, m.unit 
                  FROM batches b 
                  JOIN medicines m ON b.medicine_id = m.medicine_id WHERE 1=1";
        
        $params = [];
        
        if (!empty($search)) {
            // Đặt tên tham số riêng biệt cho từng cột để tránh lỗi HY093 của PDO
            $query .= " AND (m.medicine_name LIKE :search1 OR m.medicine_code LIKE :search2 OR b.batch_number LIKE :search3)";
            $params[':search1'] = "%$search%";
            $params[':search2'] = "%$search%";
            $params[':search3'] = "%$search%";
        }

        // Lọc theo trạng thái
        if ($statusFilter == 'out_of_stock') {
            $query .= " AND b.quantity <= 0";
        } elseif ($statusFilter == 'expired') {
            $query .= " AND b.expiry_date < CURDATE()";
        } elseif ($statusFilter == 'expiring_soon') {
            $query .= " AND b.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY) AND b.quantity > 0";
        }

        $query .= " ORDER BY b.expiry_date ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    // Cập nhật số lượng (Điều chỉnh kho)
    public function updateQuantity($batchId, $newQty) {
        $query = "UPDATE batches SET quantity = :qty WHERE batch_id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':qty' => $newQty, ':id' => $batchId]);
    }
}