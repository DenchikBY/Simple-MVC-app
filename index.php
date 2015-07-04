<?php

use System\Route;

define('APP_PATH', __DIR__);

function __autoload($path)
{
    include realpath(strtolower(__DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', '/', $path) . '.php'));
}

Route::init();
