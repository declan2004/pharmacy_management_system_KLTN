<?php
class Pos {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function searchSellableMedicines($keyword = '') {
        try {
            $query = "SELECT m.medicine_id, m.medicine_code, m.barcode, m.medicine_name, m.base_price AS price, m.medicine_type,
                             SUM(b.quantity) as total_qty
                      FROM medicines m
                      JOIN batches b ON m.medicine_id = b.medicine_id
                      WHERE b.quantity > 0 AND b.expiry_date >= CURRENT_DATE() ";

            if ($keyword !== '') {
                $query .= " AND (m.medicine_name LIKE :kw1 
                                 OR m.medicine_code LIKE :kw2 
                                 OR m.barcode LIKE :kw3) ";
            }

            $query .= " GROUP BY m.medicine_id, m.medicine_code, m.barcode, m.medicine_name, m.base_price, m.medicine_type 
                        HAVING SUM(b.quantity) > 0 
                        ORDER BY m.medicine_name ASC";

            $stmt = $this->db->prepare($query);

            if ($keyword !== '') {
                $searchTerm = "%$keyword%";
                $stmt->execute([
                    ':kw1' => $searchTerm,
                    ':kw2' => $searchTerm,
                    ':kw3' => $searchTerm
                ]);
            } else {
                $stmt->execute();
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [['error' => 'Database SQL Error: ' . $e->getMessage()]];
        }
    }

    public function createInvoice($data) {
        try {
            $this->db->beginTransaction();

            // 1. Lưu thông tin Hóa đơn 
            $queryInvoice = "INSERT INTO invoices (total_amount, payment_method, pharmacist_id, invoice_date, status) 
                             VALUES (:total, :method, :pharmacist, NOW(), 'Completed')";
            $stmtInv = $this->db->prepare($queryInvoice);
            $stmtInv->execute([
                ':total'      => $data['total_amount'],
                ':method'     => $data['payment_method'],
                ':pharmacist' => $data['pharmacist_id']
            ]);

            $invoiceId = $this->db->lastInsertId();

            // 2. Thuật toán FEFO 
            for ($i = 0; $i < count($data['medicine_ids']); $i++) {
                $medicineId = $data['medicine_ids'][$i];
                $qtyToSell  = (int)$data['quantities'][$i];
                $unitPrice  = (float)$data['prices'][$i];

                // Tìm lô còn hàng, ưu tiên Hạn gần nhất
                $queryBatches = "SELECT batch_id, quantity, expiry_date FROM batches 
                                 WHERE medicine_id = :med_id AND quantity > 0 AND expiry_date >= CURRENT_DATE() 
                                 ORDER BY expiry_date ASC";
                $stmtBatches = $this->db->prepare($queryBatches);
                $stmtBatches->execute([':med_id' => $medicineId]);
                $batches = $stmtBatches->fetchAll(PDO::FETCH_ASSOC);

                $remainingToSell = $qtyToSell;

                foreach ($batches as $batch) {
                    if ($remainingToSell <= 0) break;

                    $batchId = $batch['batch_id'];
                    $currentBatchQty = (int)$batch['quantity'];
                    $takeFromBatch = min($remainingToSell, $currentBatchQty);

                    // Trừ kho ở lô tương ứng
                    $updateBatch = "UPDATE batches SET quantity = quantity - :take WHERE batch_id = :bid";
                    $stmtUpd = $this->db->prepare($updateBatch);
                    $stmtUpd->execute([':take' => $takeFromBatch, ':bid' => $batchId]);

                    // Lưu chi tiết hóa đơn
                    $queryDetail = "INSERT INTO invoice_details (invoice_id, batch_id, quantity, unit_price, subtotal) 
                                    VALUES (:inv_id, :bid, :qty, :price, :subtotal)";
                    $stmtDet = $this->db->prepare($queryDetail);
                    $stmtDet->execute([
                        ':inv_id'   => $invoiceId,
                        ':bid'      => $batchId,
                        ':qty'      => $takeFromBatch,
                        ':price'    => $unitPrice,
                        ':subtotal' => $takeFromBatch * $unitPrice
                    ]);

                    $remainingToSell -= $takeFromBatch;
                }

                if ($remainingToSell > 0) {
                    throw new Exception("Lỗi: Thuốc ID {$medicineId} không đủ tồn kho hợp lệ.");
                }
            }

            $this->db->commit();
            return $invoiceId;

        } catch (Exception $e) {
            $this->db->rollBack();
            die("SQL Error: " . $e->getMessage()); 
        }
    }
}