<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use System\Route;

define('BASE_PATH', __DIR__);
define('APP_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'app');
define('SYSTEM_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'system');

include SYSTEM_PATH . DIRECTORY_SEPARATOR . 'helpers.php';
spl_autoload_register(function ($class) {
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', '/', $class) . '.php';
    $filePath = str_replace(['System', 'App'], ['system', 'app'], $filePath);
    if (file_exists($filePath)) {
        include $filePath;
    }
});

Route::init();
