<?php

namespace Manage\Controller;

use Top\Controller;

class Manage extends Controller {
    public $uid = 0;
    protected $userRules;

    public function __construct() {
        $this->uid = \Manage\Helper::isLogin();
        if (!$this->uid) {
            $this->redirect('login');
        }
        $auth = session('user_auth');
        $this->params('user_auth', $auth);
        $action = MODULE . '/' . CONTROLLER . '/' . ACTION;
        $this->userRules = $auth['rules'];
        if (!in_array($action, $this->userRules)) {
            $this->error('权限不足');
        }
    }
}