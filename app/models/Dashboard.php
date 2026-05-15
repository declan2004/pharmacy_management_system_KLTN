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

        // Tổng số loại thuốc trong danh mục
        $stats['total_medicines'] = $this->db->query("SELECT COUNT(*) FROM medicines")->fetchColumn();

        // Tổng số loại thuốc đã hết hàng 
        $stats['out_of_stock'] = $this->db->query("
            SELECT COUNT(*) FROM (
                SELECT m.medicine_id, SUM(b.quantity) as total_qty 
                FROM medicines m 
                LEFT JOIN batches b ON m.medicine_id = b.medicine_id 
                GROUP BY m.medicine_id 
                HAVING total_qty IS NULL OR total_qty <= 0
            ) as oos
        ")->fetchColumn();

        // Số lô đã hết hạn nhưng vẫn còn tồn 
        $stats['expired'] = $this->db->query("SELECT COUNT(*) FROM batches WHERE expiry_date < CURRENT_DATE() AND quantity > 0")->fetchColumn();

        // Doanh thu ngày hôm nay
        $stats['today_sales'] = $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM invoices WHERE DATE(invoice_date) = CURRENT_DATE() AND status != 'Returned'")->fetchColumn();

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