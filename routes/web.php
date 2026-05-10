<?php
require_once '../core/Router.php';

$router = new Router();

// Định nghĩa: Phương thức GET, đường dẫn '/', gọi HomeController và chạy hàm index()
$router->add('GET', '/', 'HomeController', 'index');

// Thêm 2 route cho Login (1 để hiển thị form, 1 để gửi dữ liệu)
$router->add('GET', '/login', 'AuthController', 'login');
$router->add('POST', '/login', 'AuthController', 'login');

// route cho Logout
$router->add('GET', '/logout', 'AuthController', 'logout');

$router->add('GET', '/users', 'UserController', 'index');

// Routes cho Quản lý người dùng
$router->add('GET', '/users', 'UserController', 'index');
$router->add('GET', '/users/create', 'UserController', 'create');
$router->add('POST', '/users/create', 'UserController', 'create');

//manage user
$router->add('POST', '/users/delete', 'UserController', 'delete');
$router->add('GET', '/users/edit', 'UserController', 'edit');
$router->add('POST', '/users/edit', 'UserController', 'edit');

// Routes cho module Medicine Catalog
$router->add('GET', '/medicines', 'MedicineController', 'index');
$router->add('GET', '/medicines/create', 'MedicineController', 'create');
$router->add('POST', '/medicines/create', 'MedicineController', 'create');
$router->add('GET', '/medicines/edit', 'MedicineController', 'edit');
$router->add('POST', '/medicines/edit', 'MedicineController', 'edit');
$router->add('POST', '/medicines/delete', 'MedicineController', 'delete');
$router->add('GET', '/medicines/export', 'MedicineController', 'export');
$router->add('GET', '/medicines/import', 'MedicineController', 'import');
$router->add('POST', '/medicines/import', 'MedicineController', 'import');

// Routes cho module Import (Nhập kho)
$router->add('GET', '/imports', 'ImportController', 'index');
$router->add('GET', '/imports/create', 'ImportController', 'create');
$router->add('POST', '/imports/create', 'ImportController', 'create');
$router->add('GET', '/imports/show', 'ImportController', 'show');

// Routes cho module Inventory (Tồn kho)
$router->add('GET', '/inventory', 'InventoryController', 'index');
$router->add('GET', '/inventory/export', 'InventoryController', 'export');
$router->add('POST', '/inventory/adjust', 'InventoryController', 'adjust');

// Routes cho module Return Orders (Trả hàng)
$router->add('GET', '/returns', 'ReturnController', 'index');
$router->add('GET', '/returns/create', 'ReturnController', 'create');
$router->add('POST', '/returns/create', 'ReturnController', 'create');
$router->add('GET', '/returns/show', 'ReturnController', 'show');

return $router;

