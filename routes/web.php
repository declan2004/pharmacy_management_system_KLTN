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

return $router;

