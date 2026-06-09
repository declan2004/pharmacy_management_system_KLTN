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

    public function exportExcel() {
        $this->authorize([1]); 
        $reportModel = $this->model('Report');
        $data = $reportModel->getExportData();

        // Cấu hình Header để ép trình duyệt tải file
        $filename = "PMS_Analytics_Report_" . date('Ymd') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        // Mở luồng ghi trực tiếp ra output
        $output = fopen('php://output', 'w');
        
        // Thêm ký tự UTF-8 BOM để Excel hiển thị tốt các ký tự đặc biệt
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // --- SECTION 1: REVENUE OVERVIEW ---
        fputcsv($output, ['--- THIS MONTH REVENUE OVERVIEW ---']);
        fputcsv($output, ['Total Sales (VND)', 'Total Profit (VND)']);
        fputcsv($output, [number_format($data['overview']['total_sales']), number_format($data['overview']['total_profit'])]);
        fputcsv($output, []); // Empty row for spacing

        // --- SECTION 2: TOP SELLING ---
        fputcsv($output, ['--- TOP SELLING MEDICINES THIS MONTH ---']);
        fputcsv($output, ['Medicine Name', 'Quantity Sold', 'Total Revenue (VND)']);
        if (!empty($data['topSelling'])) {
            foreach ($data['topSelling'] as $item) {
                fputcsv($output, [$item['medicine_name'], $item['total_qty'], number_format($item['total_sales'])]);
            }
        } else {
            fputcsv($output, ['No transaction data available for this period.']);
        }
        fputcsv($output, []);

        // --- SECTION 3: LOW STOCK WARNING ---
        fputcsv($output, ['--- LOW STOCK WARNING (<= 10) ---']);
        fputcsv($output, ['Medicine Code', 'Medicine Name', 'Current Stock']);
        if (!empty($data['lowStock'])) {
            foreach ($data['lowStock'] as $item) {
                fputcsv($output, [$item['medicine_code'], $item['medicine_name'], (int)$item['total_qty']]);
            }
        } else {
            fputcsv($output, ['Inventory is stable, no items are running low.']);
        }

        fclose($output);
        exit;
    }
}