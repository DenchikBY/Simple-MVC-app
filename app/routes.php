<?php

return [
    '/' => 'index@index',
    'admin/{category:(page|index)}/{id:\d+?}' => 'index@admin',
    'posts/{post}/comments/{comment}' => 'index@post',
    '{controller}/{action}/{param1?}',
];
