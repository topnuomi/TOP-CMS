<?php

namespace Manage\Model;

use Top\Model;

class Rule extends Model {
    protected $table = 'users_rule';
    protected $pk = 'id';

    public function saveRule($id) {
        $data = $this->getData();
        $info = $this->find($id);
        if ($data['action'] != $info['action']) {
            if ($this->where(['action' => $data['action']])->count() > 0) {
                $this->error = '动作已存在';
                return false;
            }
        }
        if ($this->update($data, $id) !== false) {
            return true;
        }
        $this->error = '权限保存失败';
        return false;
    }

    public function addRule() {
        $data = $this->getData();
        if ($this->where(['action' => $data['action']])->count() > 0) {
            $this->error = '动作已存在';
            return false;
        }
        if ($this->insert($data)) {
            return true;
        }
        $this->error = '新增失败';
        return false;
    }

    public function getRuleById($id) {
        return $this->find($id);
    }

    public function getClassMethodsLists() {
        $dir = scandir(APP . MODULE . '/Controller/');
        $filterList = [
            '__construct',
            'params',
            'cache',
            'view',
            'message',
            'redirect',
            'showJson',
            'success',
            'error',
            'filter'
        ];
        $lists = [];
        for ($i = 2; $i < count($dir); $i++) {
            $name = explode('.', $dir[$i])[0];
            if ($name != 'Manage' && $name != 'Auth') {
                $className = '\\' . MODULE . '\\Controller\\' . $name;
                $uriName = $name;
                if (class_exists($className)) {
                    $methods = get_class_methods($className);
                    for ($j = 0; $j < count($methods); $j++) {
                        if (!in_array($methods[$j], $filterList)) {
                            $lists[$uriName][$j] = $uriName . URL_DELIMIT . $methods[$j];
                        }
                    }
                }
            }
        }
        return $lists;
    }

    public function updateRuleLists() {
        $list = $this->getClassMethodsLists();
        foreach ($list as $key => $value) {
            $pid = 0;
            $number = $this->where(['action' => $key])->count();
            if ($number == 0) {
                $pid = $this->insert([
                    'pid' => $pid,
                    'action' => $key
                ]);
            }
            if ($number > 0) {
                $pid = $this->field('id')->where(['action' => $key])->find()['id'];
            }
            $value = array_merge($value, []);
            for ($i = 0; $i < count($value); $i++) {
                if ($this->where(['action' => $value[$i]])->count() == 0) {
                    $name = '';
                    if (stristr($value[$i], 'delete')) {
                        $name = '删除';
                    }
                    if (stristr($value[$i], 'index')) {
                        $name = '首页';
                    }
                    if (stristr($value[$i], 'lists')) {
                        $name = '列表';
                    }
                    if (stristr($value[$i], 'edit')) {
                        $name = '编辑';
                    }
                    if (stristr($value[$i], 'add')) {
                        $name = '新增';
                    }
                    $this->insert([
                        'pid' => $pid,
                        'action' => $value[$i],
                        'name' => $name
                    ]);
                }
            }
        }
        return true;
    }

    public function deleteRule($id) {
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

    public function getChildRule($pid) {
        $where = ['pid' => $pid];
        $info = $this->where($where)->select();
        return $info;
    }

    public function getSelectLists() {
        $arr_ = [];
        $where = ['pid' => 0];
        $info = $this->where($where)->select();
        for ($i = 0; $i < count($info); $i++) {
            $arr_[$i][0] = $info[$i];
            $arr_[$i][1] = $this->getChildRule($info[$i]['id']);
        }
        return $arr_;
    }

    public function getTree($pid = 0, &$arr = [], $step = 0) {
        $step = $step + 2;
        $where = ['pid' => $pid];
        $info = $this->where($where)->select();
        for ($i = 0; $i < count($info); $i++) {
            $info[$i]['space'] = ($step == 2) ? '' : str_repeat('&nbsp;&nbsp;', $step);
            $arr[] = $info[$i];
            $this->getTree($info[$i]['id'], $arr, $step);
        }
        return $arr;
    }

}