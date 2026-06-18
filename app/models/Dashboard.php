<?php
class Dashboard {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getQuickStats() {
        $stats = [
            'total_medicines' => 0,
            'out_of_stock' => 0,
            'expired' => 0,
            'today_sales' => 0
        ];

        // Tổng số loại thuốc đang kinh doanh (Loại trừ thuốc đã xóa mềm)
        $stats['total_medicines'] = $this->db->query("SELECT COUNT(*) FROM medicines WHERE deleted_at IS NULL")->fetchColumn();

        // Tổng số loại thuốc đã hết hàng (Không có lô nào quantity > 0)
        $queryOutStock = "
            SELECT COUNT(m.medicine_id) as out_of_stock
            FROM medicines m
            WHERE NOT EXISTS (
                SELECT 1 
                FROM batches b 
                WHERE b.medicine_id = m.medicine_id AND b.quantity > 0
            ) 
            AND m.deleted_at IS NULL
        ";
        $stmtOutStock = $this->db->prepare($queryOutStock);
        $stmtOutStock->execute();
        
        // Tổng số loại thuốc trong danh mục
        $stats['total_medicines'] = $this->db->query("SELECT COUNT(*) FROM medicines WHERE deleted_at IS NULL")->fetchColumn();

        // Đếm số LÔ THUỐC đã hết hàng (Stock Qty = 0) trong bảng batches
        $stats['out_of_stock'] = $this->db->query("SELECT COUNT(*) FROM batches WHERE quantity = 0")->fetchColumn();

        // Số lô đã hết hạn nhưng vẫn còn tồn 
        $stats['expired'] = $this->db->query("SELECT COUNT(*) FROM batches WHERE expiry_date < CURRENT_DATE() AND quantity > 0")->fetchColumn();

        return $stats;
    }
    // 2. Lấy danh sách tồn kho
    public function getInventoryOverview() {
        $query = "SELECT m.medicine_name, b.batch_number, b.quantity, b.expiry_date, b.status, m.base_price, m.medicine_type 
                  FROM batches b 
                  JOIN medicines m ON b.medicine_id = m.medicine_id 
                  WHERE b.quantity > 0
                  ORDER BY b.expiry_date ASC 
                  LIMIT 10";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Quét Cảnh báo (Cận date < 6 tháng hoặc Tồn kho < 10)
    public function getWarnings() {
        $query = "SELECT m.medicine_name, b.batch_number, b.quantity, b.expiry_date, m.medicine_type,
                         DATEDIFF(b.expiry_date, CURRENT_DATE()) as days_left 
                  FROM batches b 
                  JOIN medicines m ON b.medicine_id = m.medicine_id 
                  WHERE (b.expiry_date <= DATE_ADD(CURRENT_DATE(), INTERVAL 6 MONTH) AND b.quantity > 0) 
                     OR (b.quantity > 0 AND b.quantity <= 10)
                  ORDER BY b.expiry_date ASC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
}