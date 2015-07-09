<?php

if (!function_exists('url')) {
    function url($path)
    {
        return \System\Route::getBaseUrl() . '/' . ltrim($path, '/');
    }
}

function isAssoc(array $array) {
    return (bool)count(array_filter(array_keys($array), 'is_string'));
}