<?php
/**
 * @author TOP糯米 2017
 */

namespace Top;

/**
 * URL处理类
 * @author TOP糯米
 */
class Url {

    private function __construct() {
    }

    public static function build($url) {
        $url = trim($url, '/');
        if ($url) {
            $routeFile = APP . 'route.php';
            if (file_exists($routeFile)) {
                $route = require $routeFile;
                if (isset($route[$url])) {
                    return $url;
                }
            }
            $urlInfo = explode(URL_DELIMIT, $url);
            if (count($urlInfo) < 3) {
                if ($urlInfo[0] != MODULE) {
                    $url = MODULE . URL_DELIMIT . $url;
                }
                $urlInfo1 = explode(URL_DELIMIT, $url);
                if (!isset($urlInfo1[2]) || $urlInfo1[2] == '') {
                    $url = $url . URL_DELIMIT . 'index';
                }
            }
        }
        return $url;
    }
}