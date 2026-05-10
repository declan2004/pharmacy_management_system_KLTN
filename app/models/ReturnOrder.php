<?php
class ReturnOrder {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Xử lý tạo Phiếu trả hàng và Trừ tồn kho (Transaction)
    public function createTransaction($data) {
        try {
            // 1. BẮT ĐẦU TRANSACTION
            $this->db->beginTransaction();

            // 2. LƯU VÀO BẢNG return_orders
            $queryOrder = "INSERT INTO return_orders (staff_id, note) VALUES (:staff_id, :note)";
            $stmtOrder = $this->db->prepare($queryOrder);
            $stmtOrder->execute([
                ':staff_id' => $data['staff_id'],
                ':note'     => $data['note']
            ]);
            
            $returnId = $this->db->lastInsertId();

            $queryDetail = "INSERT INTO return_details (return_id, batch_id, quantity, return_reason) 
                            VALUES (:return_id, :batch_id, :quantity, :return_reason)";
            $stmtDetail = $this->db->prepare($queryDetail);

            $queryUpdateBatch = "UPDATE batches SET quantity = quantity - :quantity WHERE batch_id = :batch_id";
            $stmtUpdateBatch = $this->db->prepare($queryUpdateBatch);

            // 3. XỬ LÝ TỪNG DÒNG THUỐC TRẢ
            foreach ($data['items'] as $item) {
                // 3.1. Lưu vào bảng return_details
                $stmtDetail->execute([
                    ':return_id'     => $returnId,
                    ':batch_id'      => $item['batch_id'],
                    ':quantity'      => $item['quantity'],
                    ':return_reason' => $item['return_reason']
                ]);

                // 3.2. TRỪ SỐ LƯỢNG TỒN KHO TRONG BẢNG batches
                $stmtUpdateBatch->execute([
                    ':quantity' => $item['quantity'],
                    ':batch_id' => $item['batch_id']
                ]);
            }

            // 4. LƯU DỮ LIỆU
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Lấy danh sách tất cả các phiếu trả hàng
    public function getAllReturns() {
        $query = "SELECT r.*, u.full_name as staff_name 
                  FROM return_orders r 
                  JOIN users u ON r.staff_id = u.user_id 
                  ORDER BY r.return_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy thông tin chung của 1 phiếu trả theo ID
    public function getById($id) {
        $query = "SELECT r.*, u.full_name as staff_name 
                  FROM return_orders r 
                  JOIN users u ON r.staff_id = u.user_id 
                  WHERE r.return_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Lấy danh sách các lô thuốc bị trả của phiếu đó
    public function getDetails($id) {
        $query = "SELECT rd.*, m.medicine_code, m.medicine_name, b.batch_number 
                  FROM return_details rd
                  JOIN batches b ON rd.batch_id = b.batch_id
                  JOIN medicines m ON b.medicine_id = m.medicine_id
                  WHERE rd.return_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }
}