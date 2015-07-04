<?php

return [
    '/' => 'index@index',
    'about' => 'index@about',
    'admin/(.+)/([0-9+])' => 'index@admin'
];
