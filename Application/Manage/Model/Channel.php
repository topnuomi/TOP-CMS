<?php

namespace Manage\Model;

use Top\Model;

class Channel extends Model {
    protected $table = 'channel';
    protected $pk = 'id';
    protected $map = [];

    public function lists($where = [], $order = 'sort asc, id desc', $limit = false) {
        return $this->where($where)->order($order)->limit($limit)->select();
    }

    public function getChannelById($id) {
        return $this->find($id);
    }

    public function addChannel() {
        $data = $this->getData();
        if (!$data['name']) {
            $this->error = '导航名称不能为空';
            return false;
        }
        if ($this->insert($data)) {
            return true;
        }
        $this->error = '新增导航失败';
        return false;
    }

    public function saveChannel($id) {
        $data = $this->getData();
        if (!$data['name']) {
            $this->error = '导航名称不能为空';
            return false;
        }
        if ($data['pid'] == $id) {
            $this->error = '无法将自己作为上级导航';
            return false;
        }
        if ($this->update($data, $id) !== false) {
            return true;
        }
        $this->error = '保存失败';
        return false;
    }

    public function deleteChannel($id = '') {
        if (empty($id)) {
            $this->error = '参数错误';
            return false;
        }
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