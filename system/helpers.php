<?php

if (!function_exists('url')) {
    function url($path)
    {
        return \System\Route::getBaseUrl() . '/' . ltrim($path, '/');
    }
}
