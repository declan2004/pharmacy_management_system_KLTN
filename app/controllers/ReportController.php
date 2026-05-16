<?php
class ReportController extends Controller {
    
    public function index() {
        $this->authorize([1]); 
        
        $data = [
            'title' => 'Analytics Dashboard - PMS',
            'fullName' => $_SESSION['full_name'] ?? 'Manager'
        ];
        
        $this->view('reports/index', $data);
    }

    public function apiAnalytics() {
        $this->authorize([1]);
        $reportModel = $this->model('Report');
        
        header('Content-Type: application/json');
        echo json_encode($reportModel->getFullAnalytics());
        exit;
    }
}