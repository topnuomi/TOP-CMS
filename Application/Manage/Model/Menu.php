<?php

namespace Manage\Model;

use Top\Model;

class Menu extends Model {
    protected $table = 'menu';
    protected $pk = 'id';
    protected $map = [
        'name' => 'menu_name'
    ];

    public function lists($where = [], $order = 'sort asc, id asc', $limit = false) {
        return $this->where($where)->order($order)->limit($limit)->select();
    }

    public function getMenuById($id) {
        $info = $this->find($id);
        return $info;
    }

    public function addMenu() {
        $data = $this->getData();
        if (!$data['menu_name']) {
            $this->error = '请输入菜单名称';
            return false;
        }
        if ($this->insert($data)) {
            return true;
        }
        $this->error = '菜单增加失败';
        return false;
    }

    public function saveMenu($id) {
        $oldInfo = $this->getMenuById($id);
        if (empty($oldInfo)) {
            $this->error = '菜单不存在';
            return false;
        }
        $data = $this->getData();
        /*if ($data['menu_name'] != $oldInfo['menu_name']) {
            $data['menu_name'] = $this->filter($data['menu_name']);
            if ($this->where(['menu_name' => $data['menu_name']])->count() > 0) {
                $this->error = '菜单名称已存在';
                return false;
            }
        }*/
        if ($data['pid'] == $id) {
            $this->error = '无法将自己作为上级菜单';
            return false;
        }
        if ($this->update($data, $id) !== false) {
            return true;
        }
        $this->error = '更新失败';
        return false;
    }

    public function deleteMenu($id) {
        if (is_array($id)) {
            for ($i = 0; $i < count($id); $i++) {
                $id[$i] = (int)$id[$i];
                $this->delete($id[$i]);
            }
            return true;
        } else {
            $id = (int)$id;
            if ($this->delete($id)) {
                return true;
            }
            $this->error = '删除失败';
            return false;
        }
    }

    public function getTree($pid = 0, &$arr = [], $step = 0) {
        $step = $step + 2;
        $info = $this->where(['pid' => $pid])->select();
        for ($i = 0; $i < count($info); $i++) {
            $info[$i]['space'] = ($step == 2) ? '' : str_repeat('&nbsp;&nbsp;', $step);
            $arr[] = $info[$i];
            $this->getTree($info[$i]['id'], $arr, $step);
        }
        return $arr;
    }
}