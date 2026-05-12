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
}