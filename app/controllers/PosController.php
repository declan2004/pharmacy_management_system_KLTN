<?php
class PosController extends Controller {
    
    public function index() {
        $this->authorize([1, 2]);
        
        $data = [
            'title'    => 'POS - Point of Sale',
            'fullName' => $_SESSION['full_name'] ?? 'Staff'
        ];
        
        $this->view('pos/index', $data);
    }

    public function search() {
        $this->authorize([1, 2]);
        
        // Hứng dữ liệu từ đường hầm Header để chống trôi tham số URL
        $keyword = '';
        if (isset($_SERVER['HTTP_X_SEARCH_KEYWORD'])) {
            $keyword = urldecode($_SERVER['HTTP_X_SEARCH_KEYWORD']);
        } elseif (isset($_GET['q'])) {
            $keyword = $_GET['q'];
        }

        $posModel = $this->model('Pos');
        $medicines = $posModel->searchSellableMedicines(trim($keyword));
        
        header('Content-Type: application/json');
        echo json_encode($medicines);
        exit;
    }

    // Xử lý Thanh toán & Trừ kho 
    public function checkout() {
        $this->authorize([1, 2]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $medicineIds   = $_POST['medicine_id'] ?? [];
            $quantities    = $_POST['quantity'] ?? [];
            $prices        = $_POST['price'] ?? [];
            $paymentMethod = $_POST['payment_method'] ?? 'Cash';
            
            if (empty($medicineIds)) {
                $_SESSION['error'] = "Giỏ hàng rỗng!";
                header('Location: /pos');
                exit;
            }

            $totalAmount = 0;
            for ($i = 0; $i < count($prices); $i++) {
                $totalAmount += (float)$prices[$i] * (int)$quantities[$i];
            }

            $data = [
                'medicine_ids'   => $medicineIds,
                'quantities'     => $quantities,
                'prices'         => $prices,
                'payment_method' => $paymentMethod,
                'total_amount'   => $totalAmount,
                'pharmacist_id'  => $_SESSION['user_id'] 
            ];

            $posModel = $this->model('Pos');
            $result = $posModel->createInvoice($data);

            if ($result) {
                $_SESSION['checkout_success'] = [
                    'invoice_id' => $result,
                    'method'     => $paymentMethod,
                    'total'      => $totalAmount
                ];
            } else {
                $_SESSION['error'] = "Checkout failed due to system error.";
            }
            
            header('Location: /pos');
            exit;
        }
    }
}