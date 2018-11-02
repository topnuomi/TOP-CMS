<?php
// 自定义路由规则

return [
    'detail' => [
        '[category][id]',
        'Home/Article/detail'
    ],
    'index' => [
        null,
        'Home/Index/index'
    ],
    'lists' => [
        '[category]',
        'Home/Article/lists'
    ],
    'all' => [
        '[category]',
        'Home/Article/all'
    ],
    'page' => [
        '[category]',
        'Home/Article/index'
    ],
    'search' => [
        '[keywords][:category]',
        'Home/Article/search'
    ],
    'manage' => [
        null,
        'Manage'
    ],
    'login' => [
        null,
        'Manage/Auth/index'
    ],

    // 其他新增路由
    
];