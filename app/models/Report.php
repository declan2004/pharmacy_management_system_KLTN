<?php
class Report {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Hàm hỗ trợ 1: Tính chính xác Doanh Thu và Lợi Nhuận
    private function getSalesAndProfit($dateCondition) {
        $query = "
            SELECT 
                COALESCE(SUM(id.subtotal), 0) AS total_sales,
                COALESCE(SUM(id.subtotal - (id.quantity * COALESCE(imp.avg_import_price, 0))), 0) AS total_profit
            FROM invoice_details id
            JOIN invoices i ON id.invoice_id = i.invoice_id
            LEFT JOIN (
                SELECT batch_id, AVG(import_price) as avg_import_price 
                FROM import_details 
                GROUP BY batch_id
            ) imp ON id.batch_id = imp.batch_id
            WHERE i.status != 'Returned' AND $dateCondition
        ";
        return $this->db->query($query)->fetch(PDO::FETCH_ASSOC);
    }

    // Hàm hỗ trợ 2: Lấy Top Selling 
    private function getTopSellingQuery($dateCondition) {
        $query = "
            SELECT m.medicine_name, SUM(d.quantity) as total_qty, SUM(d.subtotal) as total_sales
            FROM invoice_details d
            JOIN batches b ON d.batch_id = b.batch_id 
            JOIN medicines m ON b.medicine_id = m.medicine_id
            JOIN invoices i ON d.invoice_id = i.invoice_id 
            WHERE i.status != 'Returned' AND $dateCondition
            GROUP BY m.medicine_id 
            ORDER BY total_qty DESC LIMIT 5
        ";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFullAnalytics() {
        $data = [];

        // 1. TOP WIDGETS
        $todayData = $this->getSalesAndProfit("DATE(i.invoice_date) = CURRENT_DATE()");
        $data['today_sales'] = $todayData['total_sales'];
        $data['today_profit'] = $todayData['total_profit']; 

        // Inventory Value
        $data['inventory_value'] = $this->db->query("
            SELECT COALESCE(SUM(b.quantity * imp.avg_import_price), 0) 
            FROM batches b 
            LEFT JOIN (SELECT batch_id, AVG(import_price) as avg_import_price FROM import_details GROUP BY batch_id) imp ON b.batch_id = imp.batch_id
            WHERE b.quantity > 0
        ")->fetchColumn();

        // Low Stock Items
        $data['low_stock_count'] = $this->db->query("
            SELECT COUNT(*) FROM (
                SELECT m.medicine_id, SUM(b.quantity) as total_qty FROM medicines m 
                LEFT JOIN batches b ON m.medicine_id = b.medicine_id 
                GROUP BY m.medicine_id HAVING total_qty <= 10 OR total_qty IS NULL
            ) as low_stock
        ")->fetchColumn();

        // 2. SALES TREND (Last 30 Days)
        $data['sales_trend'] = $this->db->query("
            SELECT DATE(invoice_date) as date, SUM(total_amount) as sales 
            FROM invoices WHERE invoice_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY) AND status != 'Returned'
            GROUP BY DATE(invoice_date) ORDER BY date ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // 3. SALES OVERVIEW 
        $data['overview'] = [
            'today'      => $todayData,
            'last_7'     => $this->getSalesAndProfit("i.invoice_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)"),
            'this_month' => $this->getSalesAndProfit("MONTH(i.invoice_date) = MONTH(CURRENT_DATE()) AND YEAR(i.invoice_date) = YEAR(CURRENT_DATE())"),
            'last_28'    => $this->getSalesAndProfit("i.invoice_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 28 DAY)")
        ];

        // 4. TOP SELLING MEDICINES
        $data['top_selling'] = [
            'today'      => $this->getTopSellingQuery("DATE(i.invoice_date) = CURRENT_DATE()"),
            'last_7'     => $this->getTopSellingQuery("i.invoice_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)"),
            'this_month' => $this->getTopSellingQuery("MONTH(i.invoice_date) = MONTH(CURRENT_DATE()) AND YEAR(i.invoice_date) = YEAR(CURRENT_DATE())"),
            'last_28'    => $this->getTopSellingQuery("i.invoice_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 28 DAY)")
        ];

        // 5. INVENTORY STATUS 
        $inStock = $this->db->query("SELECT COUNT(*) FROM batches WHERE quantity > 0")->fetchColumn();
        $stockOut = $this->db->query("SELECT COUNT(*) FROM batches WHERE quantity <= 0")->fetchColumn();
        $data['inventory_status'] = [
            'in_stock' => (int)$inStock,
            'stock_out' => (int)$stockOut
        ];

        // 6. EXPIRING SOON
        $data['expiring_soon'] = $this->db->query("
            SELECT m.medicine_name, b.quantity, b.expiry_date, DATEDIFF(b.expiry_date, CURRENT_DATE()) as days_left 
            FROM batches b JOIN medicines m ON b.medicine_id = m.medicine_id 
            WHERE b.quantity > 0 AND b.expiry_date <= DATE_ADD(CURRENT_DATE(), INTERVAL 6 MONTH) 
            ORDER BY b.expiry_date ASC LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }
}