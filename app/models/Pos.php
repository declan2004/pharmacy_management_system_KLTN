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
}