<?php

namespace Manage\Controller;

use Top\Controller;
use Top\Loader;

class Auth extends Controller {

    public function index() {
        if (request()->isPost()) {
            $username = $this->filter($_POST['name']);
            $password = $this->filter($_POST['psw']);
            $token = $this->filter($_POST['token']);
            if (!session('login_token') || $token != session('login_token')) {
                $this->showJson('数据校验错误');
            }
            $model = Loader::get('\Manage\Model\Auth');
            $uid = $model->login($username, $password);
            if ($uid > 0) {
                $this->showJson('登录成功，正在跳转...', 1, ['url' => u('manage')]);
            } else {
                session('login_token', null);
            }
            $this->showJson($model->getError());
        } else {
            $this->params('token', $this->token());
            $this->view();
        }
    }

    public function token() {
        $token = md5(uniqid() . time());
        session('login_token', $token);
        return $token;
    }

    public function logout() {
        session('user_auth', 0);
        $this->redirect('login');
    }
}