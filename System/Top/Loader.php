<?php
/**
 * @author TOP糯米 2017
 */

namespace Top;

/**
 * 自动加载类（一定程度上遵循PSR-0自动加载规范)
 * @author TOP糯米
 */
class Loader {
    private static $instance = [];

    private function __construct() {
    }

    public static function load($class) {
        $frameFile = FRAMEWORK . str_replace('\\', '/', $class) . '.php';
        $appFile = APP . str_replace('\\', '/', $class) . '.php';
        $baseFile = BASEPATH . str_replace('\\', '/', $class) . '.php';
        if (file_exists($appFile)) {
            require_once $appFile;
        } elseif (file_exists($frameFile)) {
            require_once $frameFile;
        } elseif (file_exists($baseFile)) {
            require_once $baseFile;
        } else {
            echo '<pre />';
            throw new \Exception($class . ' not found!');
        }
    }

    public static function get($name) {
        if (!isset(self::$instance[$name])) {
            self::$instance[$name] = new $name();
        }
        return self::$instance[$name];
    }

    public static function exists($name) {
        if (class_exists($name)) {
            return true;
        }
        return false;
    }
}