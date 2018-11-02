<?php

namespace Manage\Model;

use Top\Model;

class Config extends Model {
    protected $table = 'config';
    protected $pk = 'id';
    protected $map = [];

    public function getListByGroup($group_id, $order = 'sort asc, id asc') {
        return $this->where(['group_id' => $group_id])->order($order)->select();
    }

    public function getConfigById($id) {
        return $this->find($id);
    }

    public function getConfigByName($name) {
        return $this->field('value')->where(['name' => $name])->find();
    }

    public function add() {
        $data = $this->getData();
        if (!$data['name']) {
            $this->error = '请输入配置名称';
            return false;
        }
        $data['name'] = $this->filter($data['name']);
        if ($this->where(['name' => $data['name']])->count() > 0) {
            $this->error = '配置名称已存在';
            return false;
        }
        $data['create_time'] = time();
        if ($this->insert($data)) {
            return true;
        }
        $this->error = '添加配置失败';
        return false;
    }

    public function saveConfig($id) {
        $data = $this->getData();
        if (!$data['name']) {
            $this->error = '请输入配置名称';
            return false;
        }
        $oldInfo = $this->getConfigById($id);
        if ($data['name'] != $oldInfo['name']) {
            $data['name'] = $this->filter($data['name']);
            if ($this->where(['name' => $data['name']])->count() > 0) {
                $this->error = '配置名称已存在';
                return false;
            }
        }
        if ($this->update($data, $id) !== false) {
            return true;
        }
        $this->error = '修改失败';
        return false;
    }

}