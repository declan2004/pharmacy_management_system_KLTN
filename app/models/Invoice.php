<?php
class Invoice {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // filters
    public function getAllInvoices($roleId, $userId, $filters = []) {
        $query = "SELECT i.invoice_id, i.invoice_date, i.total_amount, i.payment_method, i.status, u.full_name as pharmacist_name
                  FROM invoices i
                  JOIN users u ON i.pharmacist_id = u.user_id 
                  WHERE 1=1 "; 
        
        $params = [];

        // Phân quyền 
        if ($roleId != 1) { 
            $query .= " AND i.pharmacist_id = :user_id ";
            $params[':user_id'] = $userId;
        } 
        // Nếu là Quản lý và có chọn lọc theo nhân viên
        else if (!empty($filters['pharmacist_id'])) {
            $query .= " AND i.pharmacist_id = :filter_pharma ";
            $params[':filter_pharma'] = $filters['pharmacist_id'];
        }

        // Lọc theo Search (Mã hóa đơn)
        if (!empty($filters['search'])) {
            $query .= " AND i.invoice_id = :search ";
            $params[':search'] = $filters['search'];
        }

        // Lọc theo Ngày bán
        if (!empty($filters['date'])) {
            $query .= " AND DATE(i.invoice_date) = :date ";
            $params[':date'] = $filters['date'];
        }

        // Lọc theo Phương thức thanh toán
        if (!empty($filters['method'])) {
            $query .= " AND i.payment_method = :method ";
            $params[':method'] = $filters['method'];
        }
        
        $query .= " ORDER BY i.invoice_date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tính tổng doanh thu linh hoạt theo bộ lọc 
    public function getFilteredRevenue($roleId, $userId, $filters = []) {
        $query = "SELECT SUM(i.total_amount) as total 
                  FROM invoices i
                  WHERE 1=1 AND i.status IN ('Completed', 'Partially Returned')";
        
        $params = [];

        // Phân quyền
        if ($roleId != 1) { 
            $query .= " AND i.pharmacist_id = :user_id ";
            $params[':user_id'] = $userId;
        } else if (!empty($filters['pharmacist_id'])) {
            $query .= " AND i.pharmacist_id = :filter_pharma ";
            $params[':filter_pharma'] = $filters['pharmacist_id'];
        }

        // Các bộ lọc
        if (!empty($filters['search'])) {
            $query .= " AND i.invoice_id = :search ";
            $params[':search'] = $filters['search'];
        }
        if (!empty($filters['date'])) {
            $query .= " AND DATE(i.invoice_date) = :date ";
            $params[':date'] = $filters['date'];
        }
        if (!empty($filters['method'])) {
            $query .= " AND i.payment_method = :method ";
            $params[':method'] = $filters['method'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() ?: 0; 
    }

    // Lấy danh sách nhân viên để hiển thị lên Filter Dropdown
    public function getPharmacists() {
        $query = "SELECT user_id, full_name FROM users WHERE role_id IN (1, 2) ORDER BY full_name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết các mặt hàng trong một hóa đơn
    public function getInvoiceDetails($invoiceId) {
        // THÊM d.batch_id VÀO ĐỂ LÀM LOGIC TRẢ HÀNG
        $query = "SELECT d.quantity, d.unit_price, d.subtotal, d.batch_id, 
                         m.medicine_code, m.medicine_name, 
                         b.batch_number, b.expiry_date 
                  FROM invoice_details d
                  JOIN batches b ON d.batch_id = b.batch_id
                  JOIN medicines m ON b.medicine_id = m.medicine_id
                  WHERE d.invoice_id = :inv_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':inv_id' => $invoiceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin chung của một Hóa đơn 
    public function getInvoiceById($invoiceId) {
        $query = "SELECT i.invoice_id, i.invoice_date, i.total_amount, i.payment_method, u.full_name as pharmacist_name
                  FROM invoices i
                  JOIN users u ON i.pharmacist_id = u.user_id 
                  WHERE i.invoice_id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $invoiceId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Sales Return
    public function processSalesReturn($invoiceId, $items, $reason, $pharmacistId) {
        try {
            $this->db->beginTransaction();
            $totalRefund = 0;
            $hasReturn = false;
            
            // MẢNG CHỨA CHI TIẾT TRẢ HÀNG
            $detailsArray = [];

            foreach ($items as $item) {
                $returnQty = (int)$item['return_qty'];
                $batchId = (int)$item['batch_id'];
                $unitPrice = (float)$item['unit_price'];
                
                // Lấy thêm tên thuốc và mã lô từ giao diện gửi lên
                $medName = $item['medicine_name'] ?? 'Unknown Item';
                $batchNum = $item['batch_number'] ?? 'N/A';

                if ($returnQty > 0) {
                    $hasReturn = true;
                    $refundAmount = $returnQty * $unitPrice;
                    $totalRefund += $refundAmount;

                    // Ghi nhận vào chuỗi chi tiết
                    $detailsArray[] = "$medName (Lot: $batchNum) x $returnQty";

                    $stmtBatch = $this->db->prepare("UPDATE batches SET quantity = quantity + :qty WHERE batch_id = :batch_id");
                    $stmtBatch->execute([':qty' => $returnQty, ':batch_id' => $batchId]);

                    $stmtDetail = $this->db->prepare("UPDATE invoice_details SET quantity = quantity - :qty, subtotal = subtotal - :refund WHERE invoice_id = :inv_id AND batch_id = :batch_id");
                    $stmtDetail->execute([':qty' => $returnQty, ':refund' => $refundAmount, ':inv_id' => $invoiceId, ':batch_id' => $batchId]);
                }
            }

            if (!$hasReturn) {
                $this->db->rollBack();
                return false; 
            }

            // Gộp mảng thành chuỗi (VD: "Panadol (Lot: LOT-001) x 2, Thuốc ho (Lot: B02) x 1")
            $returnDetailsString = implode(' | ', $detailsArray);

            // CẬP NHẬT LỆNH INSERT: Thêm return_details vào
            $stmtLog = $this->db->prepare("INSERT INTO sales_returns (invoice_id, return_details, refund_amount, reason, pharmacist_id) VALUES (:inv, :details, :refund, :reason, :pharma)");
            $stmtLog->execute([
                ':inv' => $invoiceId, 
                ':details' => $returnDetailsString, 
                ':refund' => $totalRefund, 
                ':reason' => $reason, 
                ':pharma' => $pharmacistId
            ]);

            // ... (Phần logic tính toán tổng tiền và đổi status giữ nguyên) ...
            $stmtCheck = $this->db->prepare("SELECT total_amount FROM invoices WHERE invoice_id = :inv_id");
            $stmtCheck->execute([':inv_id' => $invoiceId]);
            $currentTotal = (float)$stmtCheck->fetchColumn();

            $newTotal = $currentTotal - $totalRefund;
            $newStatus = ($newTotal <= 0) ? 'Returned' : 'Partially Returned';

            $stmtInv = $this->db->prepare("UPDATE invoices SET total_amount = :new_total, status = :status WHERE invoice_id = :inv_id");
            $stmtInv->execute([':new_total' => $newTotal, ':status' => $newStatus, ':inv_id' => $invoiceId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    //Lấy lịch sử return
    public function getSalesReturnHistory($roleId, $userId) {
        $query = "SELECT r.return_id, r.invoice_id, r.return_date, r.return_details, r.refund_amount, r.reason, u.full_name as pharmacist_name
                  FROM sales_returns r
                  JOIN users u ON r.pharmacist_id = u.user_id 
                  WHERE 1=1 ";
        
        $params = [];
        if ($roleId != 1) { 
            $query .= " AND r.pharmacist_id = :user_id ";
            $params[':user_id'] = $userId;
        }
        $query .= " ORDER BY r.return_date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}