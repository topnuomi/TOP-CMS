<?php
/**
 * @author TOP糯米 2017
 */

namespace Top;

/**
 * 项目配置获取
 * @author TOP糯米
 */
class Config {

    private static $config = [];

    private function __construct() {
    }

    /**
     * 获取配置
     * @param $name
     * @return array
     * @throws \Exception
     */
    public static function get($name) {
        $file = APP . MODULE . '/Config/config.php';
        if (file_exists($file)) {
            if (!isset(self::$config[md5($name)])) {
                self::$config[md5($name)] = require $file;
            }
            return isset(self::$config[md5($name)][$name]) ? self::$config[md5($name)][$name] : [];
        } else {
            echo '<pre />';
            throw new \Exception('找不到配置文件' . $file);
        }
    }
}