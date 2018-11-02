<?php
// 还没完善,很多应该有的限制都没做

// error_reporting(0);
header('content-type: text/html; charset=utf-8');
// 项目根目录
define('BASEPATH', dirname(__FILE__) . '/../');
// 应用所在目录
define('APP', BASEPATH . 'Application/');
// 框架所在目录
define('FRAMEWORK', BASEPATH . 'System/');
// DEBUG模式 （不生成缓存文件并且每次编译模板文件）
define('DEBUG', false);
// 加载框架
require FRAMEWORK . 'framework.php';