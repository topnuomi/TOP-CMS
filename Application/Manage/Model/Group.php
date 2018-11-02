<?php

namespace Manage\Model;

use Top\Model;

class Group extends Model {
    protected $table = 'users_group';
    protected $pk = 'id';
    protected $map = [];

    public function lists($where = [], $order = 'id desc', $limit = false) {
        return $this->where($where)->order($order)->limit($limit)->select();
    }

    public function getGroupById($id) {
        return $this->find($id);
    }

    public function addGroup() {
        $data = $this->getData();
        if (!$data['name']) {
            $this->error = '请输入角色名称';
            return false;
        }
        if ($this->where(['name' => $data['name']])->count() > 0) {
            $this->error = '角色名称已存在';
            return false;
        }
        if ($this->insert($data)) {
            return true;
        }
        $this->error = '新增失败';
        return false;
    }

    public function saveGroup($id) {
        $data = $this->getData();
        $info = $this->getGroupById($id);
        if (!$data['name']) {
            $this->error = '请输入角色名称';
            return false;
        }
        if ($data['name'] != $info['name']) {
            if ($this->where(['name' => $data['name']])->count() > 0) {
                $this->error = '角色名称已存在';
                return false;
            }
        }
        if ($this->update($data, $id) !== false) {
            return true;
        }
        $this->error = '保存失败';
        return false;
    }

    public function saveRule($id) {
        $rule = (isset($_POST['rule']) && !empty($_POST['rule'])) ? $_POST['rule'] : '';
        $rule = $this->filter(implode(',', $rule));
        $data = [
            'rules' => $rule,
        ];
        if ($this->update($data, $id) !== false) {
            return true;
        }
        $this->error = '权限更新失败';
        return false;
    }

    public function deleteGroup($id) {
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
}