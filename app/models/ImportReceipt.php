<?php
class ImportReceipt {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Lấy danh sách tất cả phiếu nhập, kèm theo tên nhân viên thực hiện
    public function getAll() {
        $query = "SELECT ir.*, u.full_name as staff_name 
                  FROM import_receipts ir 
                  JOIN users u ON ir.staff_id = u.user_id 
                  ORDER BY ir.import_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Xử lý tạo Phiếu nhập, Chi tiết nhập và Sinh Lô thuốc (Transaction)
    public function createTransaction($data) {
        try {
            // 1. BẮT ĐẦU TRANSACTION
            $this->db->beginTransaction();

            // Tính tổng tiền của cả phiếu
            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $totalAmount += ($item['quantity'] * $item['import_price']);
            }

            // 2. LƯU VÀO BẢNG import_receipts (Phiếu cha)
            $queryReceipt = "INSERT INTO import_receipts (staff_id, supplier_name, total_amount, note, import_date) 
                             VALUES (:staff_id, :supplier_name, :total_amount, :note, NOW())";
            $stmtReceipt = $this->db->prepare($queryReceipt);
            $stmtReceipt->execute([
                ':staff_id'      => $data['staff_id'],
                ':supplier_name' => $data['supplier_name'],
                ':total_amount'  => $totalAmount,
                ':note'          => $data['note']
            ]);
            
            $importId = $this->db->lastInsertId(); // Lấy ID phiếu nhập vừa tạo

            // Chuẩn bị sẵn các câu lệnh SQL (Đã xóa subtotal và đổi batch_number thành batch_id)
            $queryCheckBatch = "SELECT batch_id FROM batches WHERE medicine_id = :medicine_id AND batch_number = :batch_number";
            $stmtCheckBatch = $this->db->prepare($queryCheckBatch);

            $queryUpdateBatch = "UPDATE batches SET quantity = quantity + :quantity WHERE batch_id = :batch_id";
            $stmtUpdateBatch = $this->db->prepare($queryUpdateBatch);

            $queryInsertBatch = "INSERT INTO batches (medicine_id, batch_number, expiry_date, quantity) 
                                 VALUES (:medicine_id, :batch_number, :expiry_date, :quantity)";
            $stmtInsertBatch = $this->db->prepare($queryInsertBatch);

            $queryDetail = "INSERT INTO import_details (import_id, medicine_id, batch_id, quantity, import_price) 
                            VALUES (:import_id, :medicine_id, :batch_id, :quantity, :import_price)";
            $stmtDetail = $this->db->prepare($queryDetail);

            // 3. XỬ LÝ TỪNG DÒNG THUỐC
            foreach ($data['items'] as $item) {
                
                // 3.1. XỬ LÝ BẢNG BATCHES TRƯỚC (Để lấy batch_id)
                $stmtCheckBatch->execute([
                    ':medicine_id'  => $item['medicine_id'],
                    ':batch_number' => $item['batch_number']
                ]);
                $existingBatch = $stmtCheckBatch->fetch();

                $currentBatchId = null;

                if ($existingBatch) {
                    // Nếu Lô ĐÃ TỒN TẠI -> Cộng dồn số lượng và lấy ID cũ
                    $stmtUpdateBatch->execute([
                        ':quantity' => $item['quantity'],
                        ':batch_id' => $existingBatch['batch_id']
                    ]);
                    $currentBatchId = $existingBatch['batch_id'];
                } else {
                    // Nếu Lô CHƯA TỒN TẠI -> Tạo lô mới và lấy ID mới sinh ra
                    $stmtInsertBatch->execute([
                        ':medicine_id'  => $item['medicine_id'],
                        ':batch_number' => $item['batch_number'],
                        ':expiry_date'  => $item['expiry_date'],
                        ':quantity'     => $item['quantity']
                    ]);
                    $currentBatchId = $this->db->lastInsertId();
                }

                // 3.2. LƯU VÀO BẢNG IMPORT_DETAILS (Sử dụng batch_id vừa lấy được)
                $stmtDetail->execute([
                    ':import_id'    => $importId,
                    ':medicine_id'  => $item['medicine_id'],
                    ':batch_id'     => $currentBatchId, 
                    ':quantity'     => $item['quantity'],
                    ':import_price' => $item['import_price']
                ]);
            }

            // 4.  XÁC NHẬN LƯU DỮ LIỆU
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Lấy thông tin chung của 1 phiếu nhập theo ID
    public function getById($id) {
        $query = "SELECT ir.*, u.full_name as staff_name 
                  FROM import_receipts ir 
                  JOIN users u ON ir.staff_id = u.user_id 
                  WHERE ir.import_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Lấy danh sách các loại thuốc (Details) của phiếu đó
    public function getDetails($id) {
        $query = "SELECT id.*, m.medicine_name, m.medicine_code, b.batch_number, b.expiry_date 
                  FROM import_details id
                  JOIN medicines m ON id.medicine_id = m.medicine_id
                  JOIN batches b ON id.batch_id = b.batch_id
                  WHERE id.import_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }
}