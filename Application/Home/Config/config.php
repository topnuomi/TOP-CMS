<?php

return [
    'session' => true,
    'db' => [
        'type' => 'Mysqli',
        'host' => '',
        'dbname' => '',
        'user' => '',
        'pwd' => '',
        'prefix' => 'cms_',
        'charset' => 'utf8',
        'cache_dir' => '',
        'cache_time' => '',
    ],
    'view' => [
        'engine' => true,
        'left_tags' => '{',
        'right_tags' => '}',
        'compile_dir' => '',
        'view_dir' => '',
        'suffixes' => 'html',
        'cache_dir' => '',
        'cache_time' => '',
    ]
];
