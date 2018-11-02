<?php
/**
 * @author TOP糯米 2017
 */

namespace Top\Cache;

use Top\Config;

/**
 * 数据库缓存
 * @author TOP糯米
 */
class DbCache {
    private static $instance;
    private static $config;
    private static $identifying;

    /**
     * DbCache constructor.
     * @throws \Exception
     */
    private function __construct() {
        self::$config = Config::get('db');
        self::$config['cache_dir'] = (!self::$config['cache_dir']) ? 'Temps/Cache/Db/' : self::$config['cache_dir'];
        self::$config['cache_time'] = (self::$config['cache_time'] == '' || !isset(self::$config['cache_time'])) ? 12 : self::$config['cache_time'];
    }

    /**
     * 获取当前类实例
     * @param string $identifying
     * @return DbCache
     * @throws \Exception
     */
    public static function getInstance($identifying = '') {
        if (!self::$instance) {
            self::$instance = new self();
        }
        self::$identifying = ((!$identifying) ? '' : $identifying . '/');
        return self::$instance;
    }

    /**
     *  检查缓存是否有效
     * @param string $name
     * @return boolean
     */
    public function check($name) {
        $dir = self::$config['cache_dir'] . self::$identifying;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
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
     *  设置一个缓存
     * @param string $name
     * @param $value
     * @return boolean
     */
    public function set($name, $value) {
        $dir = self::$config['cache_dir'] . self::$identifying;
        if (file_put_contents($dir . $name, serialize($value))) {
            return true;
        }
        return false;
    }

    /**
     * 获取一个缓存
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function get($name) {
        $dir = self::$config['cache_dir'] . self::$identifying;
        $fileName = $dir . $name;
        if (file_exists($fileName)) {
            return unserialize(file_get_contents($fileName));
        } else {
            throw new \Exception($fileName . ' not found!');
        }
    }

    /**
     * 删除缓存
     * @param string $name
     * @param bool $cleanIdentifying
     * @return bool
     */
    public function clean($name = '', $cleanIdentifying = false) {
        $dir = self::$config['cache_dir'];
        if ($name) {
            $fileName = $dir . $name;
            if (@unlink($fileName)) {
                return true;
            }
            return false;
        } elseif ($cleanIdentifying) {
            remove_dir($dir . self::$identifying);
            return true;
        } elseif (!$cleanIdentifying) {
            remove_dir($dir);
            return true;
        }
    }
}