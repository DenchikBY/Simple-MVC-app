<?php

use System\Route;

define('APP_PATH', __DIR__);

function __autoload($path)
{
    include realpath(__DIR__ . '/' . $path . '.php');
}

Route::init();
