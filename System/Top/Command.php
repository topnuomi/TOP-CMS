<?php
/**
 * @author TOP糯米 2017
 */

namespace Top;

/**
 * 命令行模式运行程序
 * @author TOP糯米
 */
class Command {
    
    public static function build(){
        $params = getopt('m:c:a:p:');
        $params['m'] = (isset($params['m'])) ? $params['m'] : 'Home';
        $params['c'] = (isset($params['c'])) ? $params['c'] : 'Index';
        $params['a'] = (isset($params['a'])) ? $params['a'] : 'index';
        $params['p'] = (isset($params['p'])) ? $params['p'] : '';
        define('MODULE', $params['m']);
        define('CONTROLLER', $params['c']);
        define('ACTION', $params['a']);
        $paramArr = explode('.', $params['p']);
        $className = '\\' . $params['m'] . '\\Controller\\' . $params['c'];
        $paramName = (new \ReflectionMethod($className, $params['a']))->getParameters();
        $paramNameArray = [];
        $param = [];
        for ($i = 0; $i < count($paramName); $i++) {
            $paramNameArray[$paramName[$i]->name] = '';
        }
        for ($i = 0; $i < count($paramArr); $i = $i + 2) {
            if (isset($paramNameArray[$paramArr[$i]])) {
                $param[$paramArr[$i]] = $paramArr[$i + 1];
            }
        }
        $object = new $className();
        call_user_func_array([$object, $params['a']], $param);
    }
}