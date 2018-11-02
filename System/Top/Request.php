<?php
/**
 * @author TOP糯米 2017
 */

namespace Top;

/**
 * 请求类
 * @author TOP糯米
 */
class Request {
    
    public $server = [];
    
    public function __construct() {
        $this->server = (!empty($_SERVER)) ? $_SERVER : [];
    }
    
    public function method() {
        return (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] != '') ? $_SERVER['REQUEST_METHOD'] : '';
    }

    /**
     * POST
     *
     * @return boolean
     */
    public function isPost() {
        return $this->method() == 'POST';
    }

    /**
     * GET
     *
     * @return boolean
     */
    public function isGet() {
        return $this->method() == 'GET';
    }

    /**
     * PUT
     *
     * @return boolean
     */
    public function isPut() {
        return $this->method() == 'PUT';
    }

    /**
     * DELETE
     *
     * @return boolean
     */
    public function isDelete() {
        return $this->method() == 'DELETE';
    }
    
    /**
     * HEAD
     *
     * @return boolean
     */
    public function isHead() {
        return $this->method() == 'HEAD';
    }
    
    /**
     * HEAD
     *
     * @return boolean
     */
    public function isPatch() {
        return $this->method() == 'PATCH';
    }
    
    /**
     * HEAD
     *
     * @return boolean
     */
    public function isOptions() {
        return $this->method() == 'OPTIONS';
    }

    /**
     * AJAX
     *
     * @return boolean
     */
    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }

    /**
     * 创建一个请求（post或get取决于data是否有值且不为空或空数组）
     */
    public function create($url, $data = [], $header = []) {
        return ($url) ? curl($url, $data, $header) : false;
    }

    /**
     * 获取客户端IP
     * 
     * @return NULL|string|number
     */
    public function ip() {
        return get_client_ip();
    }
    
    public function __destruct() {
        
    }
}