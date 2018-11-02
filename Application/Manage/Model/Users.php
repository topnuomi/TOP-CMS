<?php

namespace Manage\Model;

use Top\Loader;
use Top\Model;

class Users extends Model {
    protected $table = 'users';
    protected $pk = 'id';
    protected $map = [];

    public function lists($where = [], $order = 'sort asc, id desc', $limit = false) {
        return $this->where($where)->order($order)->limit($limit)->select();
    }

    public function getUsersById($id) {
        return $this->find($id);
    }

    public function addUsers() {
        $data = $this->getData();
        if (!$data['username']) {
            $this->error = '请输入用户名';
            return false;
        }
        $salt = mb_substr(md5(uniqid()), 0, 6);
        if ($data['password'] != $_POST['repassword']) {
            $this->error = '两次输入的密码不一致';
            return false;
        }
        $data['salt'] = $salt;
        $data['password'] = md5($data['password'] . $salt);
        $data['create_time'] = time();
        $data['create_ip'] = get_client_ip();
        if ($this->insert($data)) {
            return true;
        }
        $this->error = '新增用户失败';
        return false;
    }

    public function saveUsers($id) {
        $data = $this->getData();
        $info = $this->getUsersById($id);
        if (!$data['username']) {
            $this->error = '请输入用户名';
            return false;
        }
        if ($data['username'] != $info['username']) {
            if ($this->where(['username' => $data['username']])->count() > 0) {
                $this->error = '用户名已存在';
                return false;
            }
        }
        if ($data['password']) {
            if ($data['password'] != $_POST['repassword']) {
                $this->error = '两次输入的密码不一致';
                return false;
            }
            $salt = mb_substr(md5(uniqid()), 0, 6);
            $data['salt'] = $salt;
            $data['password'] = md5($data['password'] . $salt);
        } else {
            unset($data['password']);
        }
        if ($this->update($data, $id) !== false) {
            return true;
        }
        $this->error = '保存失败';
        return false;
    }

    public function deleteUsers($id) {
        if (is_array($id)) {
            for ($i = 0; $i < count($id); $i++) {
                if ($this->delete((int)$id[$i]) === false) {
                    $this->error = '部分数据删除失败';
                    return false;
                }
            }
            return true;
        } else {
            if ($this->delete((int)$id)) {
                return true;
            }
            $this->error = '删除失败';
            return false;
        }
    }

    public function login($id) {
        $data = $this->getUsersById($id);
        if (empty($data)) {
            $this->error = '用户不存在';
            return false;
        }
        $this->update([
            'last_login_time' => time(),
            'last_login_ip' => get_client_ip()
        ], $id);
        $group = Loader::get('\Manage\Model\Group');
        $rule_ids = $group->field('rules')->where(['id' => $data['group_id']])->find()['rules'];
        $rule = Loader::get('\Manage\Model\Rule');
        $rulesLists = $rule->field('action')->where("id in ({$rule_ids})")->order('id desc')->select();
        $rules = [];
        foreach ($rulesLists as $value) {
            $rules[] = MODULE . '/' . $value['action'];
        }
        $auth = [
            'id' => $data['id'],
            'name' => $data['username'],
            'time' => time(),
            'rules' => $rules
        ];
        session('user_auth', $auth);
        return true;
    }

}