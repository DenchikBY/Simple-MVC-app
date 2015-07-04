<?php

use System\Route;

define('APP_PATH', __DIR__);

spl_autoload_register(function ($class) {
    include realpath(strtolower(__DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', '/', $class) . '.php'));
});

Route::init();
