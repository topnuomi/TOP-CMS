<?php
/**
 * @author TOP糯米 2017
 */

namespace Top;

/**
 * 框架路由（后面再重写，以方便扩展）
 * @author TOP糯米
 */
class Router {
    private static $classMethods = [];

    private function __construct() {
    }

    /**
     * 解析URL
     * @throws \Exception
     * @return string[]|mixed[]|array[]
     */
    public static function pathinfo() {
        $s = ((isset($_GET['s'])) ? $_GET['s'] : '');
        $s = (!$s) ? ((isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : DEFAULT_URL) : $s;
        $paramArr = self::processingRule($s);
        $className = '\\' . $paramArr[0] . '\\Controller\\' . ((isset($paramArr[1]) && $paramArr[1] != '') ? $paramArr[1] : 'Index');
        $actionName = (isset($paramArr[2]) && $paramArr[2] != '') ? $paramArr[2] : 'index';
        define('CONTROLLER', ((isset($paramArr[1]) && $paramArr[1] != '') ? $paramArr[1] : 'Index')); //拿到不包含命名空间的控制器名
        $param = [];
        if (is_dir(APP . $paramArr[0])) {
            define('MODULE', $paramArr[0]);
            if (class_exists($className)) {
                self::$classMethods = get_class_methods($className);
                if (!in_array($actionName, self::$classMethods)) {
                    echo '<pre />';
                    throw new \Exception('方法 ' . $actionName . ' 未找到');
                } elseif (isset($paramArr[3]) && $paramArr[3] != '') {
                    $param = self::getParams($paramArr, $className, $actionName);
                }
            } else {
                echo '<pre />';
                throw new \Exception('控制器 ' . $className . ' 未找到');
            }
        } else {
            echo '<pre />';
            throw new \Exception('模块 ' . $paramArr[0] . ' 未找到');
        }
        define('ACTION', $actionName);
        return ['CLASS' => $className, 'FUNCTION' => $actionName, 'PARAM' => $param];
    }

    /**
     * 处理自定义路由规则
     * @param $uri
     * @return array
     */
    public static function processingRule($uri) {
        $uri = ltrim(str_replace('.html', '', $uri), URL_DELIMIT);
        $paramArr = explode(URL_DELIMIT, $uri);
        $routeFile = APP . 'route.php';
        if (file_exists($routeFile)) {
            $route = require $routeFile;
            $ruleUri = $uri;
            $name = $paramArr[0];
            if (isset($route[$name])) {
                $params = $route[$name][0];
                preg_match_all('#\[(.*?)\]#', $params, $needParams);
                unset($paramArr[0]);
                $paramArr = array_merge($paramArr, []);
                // 如果没有定义参数则替换URL中标识部分
                if (empty($needParams[1])) {
                    $ruleUri = str_replace($name, trim($route[$name][1], URL_DELIMIT), $uri);
                } else {
                    $ruleUri = trim($route[$name][1], URL_DELIMIT);
                }
                $ruleUri = trim($ruleUri, URL_DELIMIT);
                foreach ($needParams[1] as $key => $value) {
                    // 如果有可选参数且可选参数为空，则跳出本次循环
                    if (strstr($value, ':') && (!isset($paramArr[$key]) || $paramArr[$key] == '')) {
                        continue;
                    }
                    $value = str_replace(':', '', $value);
                    $ruleUri .= URL_DELIMIT . $value . URL_DELIMIT . $paramArr[$key];
                }
            }
            $paramArr = explode(URL_DELIMIT, $ruleUri);
        }
        return $paramArr;
    }

    /**
     * 根据方法名获取参数
     * @param $paramArr
     * @param $className
     * @param $actionName
     * @return array
     * @throws \ReflectionException
     */
    public static function getParams($paramArr, $className, $actionName) {
        unset($paramArr[0]);
        unset($paramArr[1]);
        unset($paramArr[2]);
        $paramName = (new \ReflectionMethod($className, $actionName))->getParameters();
        $paramNameArray = [];
        for ($i = 0; $i < count($paramName); $i++) {
            $paramNameArray[$paramName[$i]->name] = '';
        }
        $param = [];
        $paramArr = array_values($paramArr);
        for ($i = 0; $i < count($paramArr); $i = $i + 2) {
            if (isset($paramArr[$i + 1]) && $paramArr[$i + 1]) {
                $_GET[$paramArr[$i]] = $paramArr[$i + 1];
                if (isset($paramNameArray[$paramArr[$i]])) {
                    $param[$paramArr[$i]] = $paramArr[$i + 1];
                }
            }
        }
        return $param;
    }

    /**
     * 执行当前操作
     */
    public static function build() {
        $info = self::pathinfo();
        if (Config::get('session') === true) {
            session_start();
        }
        $viewName = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : serialize($_SERVER['argv']));
        $cacheInstance = \Top\Cache\ViewCache::getInstance();
        if (!$cacheInstance->check(md5($viewName)) || DEBUG === true) {
            $object = new $info['CLASS'];
            if (in_array('_init', self::$classMethods)) {
                $object->_init();
            }
            call_user_func_array([$object, $info['FUNCTION']], $info['PARAM']);
        } else {
            echo $cacheInstance->get(md5($viewName));
        }
    }
}
