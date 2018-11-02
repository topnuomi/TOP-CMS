<?php
/**
 * @author TOP糯米 2017
 */

namespace Top;

/**
 * 控制器基类
 * @author TOP糯米
 */
class Controller {
    private $viewCache = false;

    public function __construct() {
    }

    /**
     * 设置要传入视图的变量
     * @param string $name
     * @param $value
     */
    public function params($name, $value) {
        View::setParams($name, $value);
    }

    public function cache($cache = true) {
        $this->viewCache = $cache;
        return $this;
    }

    /**
     * 加载一个视图文件
     * @param string $file
     * @param array $params
     */
    public function view($file = '', $params = []) {
        $file = ($file == '') ? CONTROLLER . '/' . ACTION : $file;
        View::load($file, $this->viewCache, $params);
    }

    /**
     * 弹出一个提示
     * @param string $msg
     * @param string $url
     */
    public function message($msg, $url = '') {
        $jump = ($url) ? 'window.location.href="' . u($url) . '";' : 'window.history.back(-1);';
        echo '<script>alert(\'' . $msg . '\');' . $jump . '</script>';
        exit;
    }

    /**
     * 跳转到一个地址
     * @param string $url
     */
    public function redirect($url) {
        header('Location: ' . u($url));
        exit;
    }

    /**
     * 输出JSON
     * @param string $result
     * @param string $status
     * @param int $code
     * @param array $ext
     */
    public function showJson($result, $status = 0, $ext = []) {
        echo json_encode(['result' => $result, 'status' => $status, 'ext' => $ext]);
        exit;
    }

    /**
     * 成功提示
     * @param $message
     * @param string $url
     * @param int $code
     */
    public function success($message, $url = '', $code = 1) {
        if (request()->isAjax()) {
            $this->showJson($message, $code, ['url' => $url]);
        } else {
            // 非AJAX的成功提示
            $this->message($message, $url);
        }
    }

    /**
     * 失败提示
     * @param $message
     * @param string $url
     * @param int $code
     */
    public function error($message, $url = '', $code = 0) {
        if (request()->isAjax()) {
            $this->showJson($message, $code, ['url' => $url]);
        } else {
            // 非AJAX的失败提示
            // $this->showJson($message, $url, 0);
            $this->message($message, $url);
        }
    }

    /**
     * 过滤字符串
     */
    public function filter($str) {
        return filter($str);
    }
}