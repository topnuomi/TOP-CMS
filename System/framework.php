<?php
/**
 * @author TOP糯米 2017
 */
if (!defined('BASEPATH'))
    exit;
/**
 * 框架入口
 * @author TOP糯米
 * 我会坚持做我喜欢的事。
 * 2018-09-27 绵阳
 */

// 框架版本
const VERSION = '1.0.3';
// 默认访问位置
const DEFAULT_URL = 'Home/Index/index';
// URL分隔符
const URL_DELIMIT = '/';

class App {

    public static function start() {
        // 设置时区
        date_default_timezone_set('PRC');
        // 注册自动加载类
        require FRAMEWORK . 'Top/Loader.php';
        spl_autoload_register('Top\Loader::load');
        require FRAMEWORK . 'Top/Functions/functions.php';
        if(!defined('DEBUG')) define('DEBUG', false);
        (php_sapi_name() === 'cli') ? \Top\Command::build() : \Top\Router::build();
    }
}
// start
App::start();