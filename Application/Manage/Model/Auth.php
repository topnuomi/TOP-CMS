<?php

namespace Manage\Model;

use Top\Loader;
use Top\Model;

class Auth extends Model {

    public function login($username, $password) {
        $username = $this->filter($username);
        $password = $this->filter($password);
        if (!$username) {
            $this->error = '请输入用户名';
            return -1;
        }
        if (!$password) {
            $this->error = '请输入密码';
            return -2;
        }
        $model = Loader::get('\Manage\Model\Users');
        $userInfo = $model->where(['username' => $username])->find();
        if (empty($userInfo)) {
            $this->error = '用户不存在';
            return -3; // 用户不存在
        }
        $salt = $userInfo['salt'];
        $password = md5($password . $salt);
        if ($password !== $userInfo['password']) {
            $this->error = '密码错误';
            return -4; // 密码错误
        }
        if ($userInfo['status'] == 0) {
            $this->error = '用户被禁用';
            return -5; // 用户被禁用
        }
        if ($model->login($userInfo['id'])) {
            return $userInfo['id'];
        } else {
            $this->error = '未知错误';
            return 0; // 登录失败
        }
    }
}