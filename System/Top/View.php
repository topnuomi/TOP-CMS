<?php
/**
 * @author TOP糯米 2017
 */

namespace Top;

/**
 * 视图基类
 * @author TOP糯米
 */
class View {
    private static $instance;
    private static $params = [];

    private function __construct() {
    }

    /**
     * 设置要传入视图的参数
     * @param string $name
     * @param $value
     */
    public static function setParams($name, $value) {
        self::$params[$name] = $value;
    }

    /**
     * 判断文件是否存在，不存在则创建
     * @param $fileName
     * @return bool
     */
    public static function checkFileName($fileName) {
        if (!file_exists($fileName)) {
            //取出目录
            $dirArr = explode('/', $fileName);
            $beforeFile = $dirArr[count($dirArr) - 1]; //当前准备渲染的文件名
            unset($dirArr[count($dirArr) - 1]);
            $dir = implode('/', $dirArr) . '/';
            if (!is_dir($dir)) {
                //创建目录
                mkdir($dir, 0777, true);
            }
            //创建文件
            file_put_contents($dir . $beforeFile, '');
        }
        return true;
    }

    /**
     * 加载视图文件
     * @param string $file
     * @param boolean $cache
     * @return boolean
     */
    public static function load($file, $cache, $params = []) {
        $config = Config::get('view');
        $viewDir = (isset($config['view_dir']) && $config['view_dir']) ? BASEPATH . $config['view_dir'] : APP . MODULE . '/View/';
        define('__VIEW__', $viewDir);
        $suffixes = (isset($config['suffixes']) && $config['suffixes']) ? $config['suffixes'] : 'html';
        $fileName = $viewDir . $file . '.' . $suffixes;
        $uri = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : serialize($_SERVER['argv']));
        //如果当前视图文件不存在
        self::checkFileName($fileName);
        if (!isset($config['engine'])) {
            $config['engine'] = true;
        }
        if ($config['engine'] === true) {
            $engine = Tags::getInstance();
            $compileFile = $engine->compileDir . md5($fileName) . '.php';
            if (!file_exists($compileFile) || DEBUG === true) {
                $engine->processing($fileName);
            }
            $fileName = $compileFile;
        }
        extract(array_merge(self::$params, $params));
        if ($cache === true && DEBUG === false) {
            $cacheInstance = \Top\Cache\ViewCache::getInstance();
            if (!$cacheInstance->check(md5($fileName))) { //检查缓存文件状态
                ob_start();
                require $fileName;
                $content = ob_get_contents();
                ob_clean();
                $cacheInstance->set(md5($uri), $content); //利用缓冲区拿到静态内容写入文件缓存
            }
            echo $cacheInstance->get(md5($uri)); //取缓存
        } else {
            require $fileName; //直接拿文件
        }
    }
}