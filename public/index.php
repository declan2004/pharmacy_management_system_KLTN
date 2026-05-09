<?php
// Bật session
session_start();

// Bật thông báo lỗi
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load các file nền tảng
require_once '../config/database.php';
require_once '../core/Database.php';
require_once '../core/Controller.php';

// Lấy URL do file .htaccess truyền vào, nếu rỗng thì mặc định là '/'
$url = isset($_GET['url']) ? $_GET['url'] : '/';

// Chạy bộ định tuyến
$router = require_once '../routes/web.php';
$router->dispatch($url);