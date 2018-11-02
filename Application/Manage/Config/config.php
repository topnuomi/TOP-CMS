<?php
$home = require APP . 'Home/Config/config.php';
return [
    'session' => true,
    'session_flag' => 'manage',
    'db' => $home['db']
];
