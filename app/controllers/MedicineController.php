<?php
class MedicineController extends Controller {
    
    // Hiển thị danh sách danh mục thuốc
    public function index() {
        // Áp dụng RBAC: Chỉ Manager (1) và Inventory Staff (3) được phép xem danh mục
        $this->authorize([1, 3]);

        $medicineModel = $this->model('Medicine');
        $medicines = $medicineModel->getAll();

        $data = [
            'title'     => 'Medicine Catalog - Pharmacy System',
            'fullName'  => $_SESSION['full_name'] ?? 'User',
            'medicines' => $medicines
        ];
        
        $this->view('medicines/index', $data);
    }

    // Hiển thị form Thêm mới và xử lý thêm
    public function create() {
        $this->authorize([1, 3]);
        $medicineModel = $this->model('Medicine');

        // Lấy mã thuốc tiếp theo từ Database để hiển thị sẵn trên form
        $nextCode = $medicineModel->generateNextCode();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'medicine_code'     => trim($_POST['medicine_code']),
                'barcode'           => trim($_POST['barcode']),
                'medicine_name'     => trim($_POST['medicine_name']),
                'active_ingredient' => trim($_POST['active_ingredient']),
                'concentration'     => trim($_POST['concentration']),
                'unit'              => trim($_POST['unit']),
                'base_price'        => $_POST['base_price'],
                'medicine_type'     => $_POST['medicine_type'],
                'description'       => trim($_POST['description'])
            ];

            if ($medicineModel->createMedicine($data)) {
                header('Location: /medicines/create?success=1');
                exit;
            } else {
                $error = "Failed to add. Code/Barcode might exist.";
            }
        }

        // Lấy 5 thuốc vừa thêm để hiển thị bảng bên dưới
        $recentMedicines = $medicineModel->getRecent(5);

        $data = [
            'title'    => 'Add New Medicine - Pharmacy System',
            'fullName' => $_SESSION['full_name'],
            'error'    => $error ?? '',
            'recent'   => $recentMedicines,
            'nextCode' => $nextCode // Truyền biến mã tự động sang View
        ];
        
        $this->view('medicines/create', $data);
    }

    // Hiển thị form Sửa và xử lý cập nhật
    public function edit() {
        $this->authorize([1, 3]); // Chỉ Manager & Inventory Staff
        $medicineModel = $this->model('Medicine');

        // Xử lý khi người dùng bấm Save (POST)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'medicine_id'       => $_POST['medicine_id'],
                'medicine_name'     => trim($_POST['medicine_name']),
                'barcode'           => trim($_POST['barcode']),
                'active_ingredient' => trim($_POST['active_ingredient']),
                'concentration'     => trim($_POST['concentration']),
                'unit'              => trim($_POST['unit']),
                'base_price'        => $_POST['base_price'],
                'medicine_type'     => $_POST['medicine_type'],
                'description'       => trim($_POST['description'])
            ];

            if ($medicineModel->updateMedicine($data)) {
                header('Location: /medicines');
                exit;
            } else {
                $error = "Failed to update medicine. Barcode might already exist.";
            }
        }

        // Lấy dữ liệu cũ để hiển thị lên form (GET)
        if (!isset($_GET['id'])) {
            header('Location: /medicines');
            exit;
        }

        $medicine = $medicineModel->getById($_GET['id']);
        if (!$medicine) {
            die("Medicine not found or has been deleted.");
        }

        $data = [
            'title'    => 'Edit Medicine - Pharmacy System',
            'fullName' => $_SESSION['full_name'],
            'medicine' => $medicine,
            'error'    => $error ?? ''
        ];
        
        $this->view('medicines/edit', $data);
    }

    // Xử lý Xóa mềm thuốc
    public function delete() {
        $this->authorize([1, 3]);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['medicine_id'])) {
            $medicineModel = $this->model('Medicine');
            $medicineModel->deleteMedicine($_POST['medicine_id']);
        }
        
        header('Location: /medicines');
        exit;
    }

    // Xuất danh sách thuốc ra file CSV
    public function export() {
        $this->authorize([1, 3]);
        $medicineModel = $this->model('Medicine');
        $medicines = $medicineModel->getAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=medicines_export_' . date('Ymd_His') . '.csv');
        $output = fopen('php://output', 'w');

        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['Medicine Code', 'Barcode', 'Medicine Name', 'Active Ingredient', 'Concentration', 'Unit', 'Base Price', 'Type', 'Description']);

        foreach ($medicines as $med) {
            // [FIX EXCEL] Thêm dấu nháy đơn vào trước Barcode
            $safeBarcode = !empty($med['barcode']) ? "'" . $med['barcode'] : '';

            fputcsv($output, [
                $med['medicine_code'],
                $safeBarcode,
                $med['medicine_name'],
                $med['active_ingredient'],
                $med['concentration'],
                $med['unit'],
                $med['base_price'],
                $med['medicine_type'],
                $med['description']
            ]);
        }
        fclose($output);
        exit;
    }

    // Hiển thị form Upload và xử lý file CSV import
    public function import() {
        $this->authorize([1, 3]);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
            $file = $_FILES['csv_file'];
            
            if ($file['error'] == 0) {
                $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                if ($fileExt == 'csv') {
                    
                    ini_set('auto_detect_line_endings', TRUE);
                    
                    $handle = fopen($file['tmp_name'], "r");
                    $medicineModel = $this->model('Medicine');
                    
                    $bom = fread($handle, 3);
                    rewind($handle);
                    
                    $firstLine = fgets($handle);
                    if ($bom === "\xEF\xBB\xBF") { $firstLine = substr($firstLine, 3); }
                    
                    $delimiters = [',', ';', "\t", '|'];
                    $delimiter = ',';
                    $maxCols = 0;
                    foreach ($delimiters as $d) {
                        $colsCount = count(str_getcsv($firstLine, $d));
                        if ($colsCount > $maxCols) {
                            $maxCols = $colsCount;
                            $delimiter = $d;
                        }
                    }
                    
                    rewind($handle);
                    if ($bom === "\xEF\xBB\xBF") { fread($handle, 3); } 
                    fgetcsv($handle, 1000, $delimiter); 
                    
                    $successCount = 0;
                    $errorDetails = []; 
                    $rowNum = 2;

                    while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                        if (empty(array_filter($data))) {
                            $rowNum++; continue;
                        }

                        if (count($data) >= 8) {
                            $code  = trim($data[0]);
                            $name  = trim($data[2]);
                            $cleanPrice = str_replace(['.', ','], '', $data[6]);
                            $price = floatval($cleanPrice);
                            $type  = strtoupper(trim($data[7]));

                            if (empty($name)) {
                                $errorDetails[] = "Row $rowNum: Medicine Name cannot be empty.";
                                $rowNum++; continue;
                            }
                            
                            // Sinh mã nếu Excel để trống
                            if (empty($code)) {
                                $code = $medicineModel->generateNextCode();
                            }
                            
                            // xóa dấu nháy đơn do export sinh ra
                            $barcode = trim($data[1]);
                            $barcode = ltrim($barcode, "'"); 

                            if (strpos($barcode, 'E+') !== false || strpos($barcode, 'e+') !== false) {
                                $errorDetails[] = "Row $rowNum: Barcode corrupted by Excel (Shows as $barcode). Please format column as Text in Excel.";
                                $rowNum++; continue;
                            }
                            $barcode = ($barcode !== '') ? $barcode : null;

                            $medData = [
                                'medicine_code'     => $code,
                                'barcode'           => $barcode,
                                'medicine_name'     => $name,
                                'active_ingredient' => trim($data[3]),
                                'concentration'     => trim($data[4]),
                                'unit'              => trim($data[5]),
                                'base_price'        => $price,
                                'medicine_type'     => $type,
                                'description'       => isset($data[8]) ? trim($data[8]) : ''
                            ];
                            
                            if ($medicineModel->createMedicine($medData)) {
                                $successCount++;
                            } else {
                                $errorDetails[] = "Row $rowNum: Failed to save. Code '$code' OR Barcode '$barcode' already exists in Database.";
                            }
                        } else {
                            $errorDetails[] = "Row $rowNum: Invalid format (found ".count($data)." columns, needed 8).";
                        }
                        $rowNum++;
                    }
                    fclose($handle);
                    
                    $_SESSION['import_success'] = $successCount;
                    if (!empty($errorDetails)) {
                        $_SESSION['import_errors'] = $errorDetails;
                    }

                    header("Location: /medicines");
                    exit;
                } else {
                    $error = "Invalid file format. Please upload a .csv file.";
                }
            } else {
                $error = "Error uploading file. Please try again.";
            }
        }

        $data = [
            'title'    => 'Import Medicines - Pharmacy System',
            'fullName' => $_SESSION['full_name'],
            'error'    => $error ?? ''
        ];
        $this->view('medicines/import', $data);
    }
}