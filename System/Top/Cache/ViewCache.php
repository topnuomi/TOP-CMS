<?php
/**
 * @author TOP糯米 2017
 */

namespace Top\Cache;

use Top\Config;

/**
 * 静态文件缓存
 * @author TOP糯米
 */
class ViewCache {
    private static $instance;
    private static $config;

    /**
     * ViewCache constructor.
     * @throws \Exception
     */
    private function __construct() {
        self::$config = Config::get('view');
        self::$config['cache_dir'] = ((isset(self::$config['cache_dir']) && self::$config['cache_dir']) ? self::$config['cache_dir'] : 'Temps/Cache/View/') . MODULE . '/' . ACTION . '/';
        self::$config['cache_time'] = (isset(self::$config['cache_time']) && self::$config['cache_time']) ? self::$config['cache_time'] : 12;
    }

    /**
     * 获取当前类实例
     * @return ViewCache
     * @throws \Exception
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 检查缓存是否有效
     * @param string $name
     * @return boolean
     */
    public function check($name) {
        $dir = self::$config['cache_dir'];
        $t = self::$config['cache_time'];
        $fileName = $dir . $name;
        if (file_exists($fileName)) {
            $time = filemtime($fileName);
            if (time() - $time > $t) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 设置一个缓存
     * @param string $name
     * @param $value
     * @return boolean
     */
    public function set($name, $value) {
        $dir = self::$config['cache_dir'];
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if (file_put_contents($dir . $name, $value)) {
            return true;
        }
        return false;
    }

    /**
     * 获取一个缓存
     * @param string $name
     * @throws \Exception
     * @return mixed
     */
    public function get($name) {
        $dir = self::$config['cache_dir'];
        $fileName = $dir . $name;
        if (file_exists($fileName)) {
            return file_get_contents($fileName);
        } else {
            throw new \Exception($fileName . ' not found!');
        }
    }

    /**
     * 删除缓存
     * @param string $name
     * @return bool
     * @throws \Exception
     */
    public function clean($name = '') {
        $dir = Config::get('view')['cache_dir'] . $name . '/';
        remove_dir($dir);
        return true;
    }
}