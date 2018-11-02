<?php

namespace Manage\Model;

use Top\Loader;
use Top\Model;

class Category extends Model {
    protected $table = 'category';
    protected $pk = 'id';
    protected $map = [];

    public function lists($where = [], $order = 'sort asc', $limit = false) {
        return $this->where($where)->order($order)->limit($limit)->select();
    }

    public function getCategoryById($id, $field = false) {
        return $this->field($field)->find($id);
    }

    public function getCategoryByName($name, $field = false) {
        return $this->field($field)->where(['name' => $name])->find();
    }

    public function addCategory() {
        $data = $this->getData();
        if (!$data['name']) {
            $this->error = '请输入分类标识';
            return false;
        }
        if (!$data['title']) {
            $this->error = '请输入分类名称';
            return false;
        }
        if ($this->where(['name' => $data['name']])->count() > 0) {
            $this->error = '分类标识已存在';
            return false;
        }
        if ($this->insert($data)) {
            return true;
        }
        $this->error = '添加配置失败';
        return false;
    }

    public function saveCategory($id) {
        $data = $this->getData();
        if (!$data['name']) {
            $this->error = '请输入分类标识';
            return false;
        }
        if (!$data['title']) {
            $this->error = '请输入分类名称';
            return false;
        }
        $info = $this->getCategoryById($id);
        if ($data['name'] != $info['name']) {
            if ($this->where(['name' => $data['name']])->count() > 0) {
                $this->error = '分类标识已存在';
                return false;
            }
        }
        if ($data['model_id'] != $info['model_id']) {
            $table = $this->getTableNameByCategoryId($id);
            if ($table) {
                $model = new Model($table);
                if ($model->where(['category_id' => $id])->count() > 0) {
                    $this->error = '该分类下存在内容，无法转换模型';
                    return false;
                }
            }
        }
        if ($this->update($data, $id) !== false) {
            return true;
        }
        $this->error = '保存失败';
        return false;
    }

    /**
     * 删除分类
     * @param $id
     * @return bool
     * 获取表名需要查询分类数据，所以此处先删除记录
     */
    public function deleteCategory($id) {
        if (is_array($id)) {
            for ($i = 0; $i < count($id); $i++) {
                $id[$i] = (int)$id[$i];
                $model = $this->getModelByCategoryId($id[$i]);
                $model->where(['category_id' => $id[$i]])->delete();
                $this->delete($id[$i]);
            }
            return true;
        } else {
            $model = $this->getModelByCategoryId($id);
            if ($model->where(['category_id' => $id])->delete()) {
                if ($this->delete((int)$id)) {
                    return true;
                }
            }
            $this->error = '删除失败';
            return false;
        }
    }

    public function getTree($pid = 0, &$arr = [], $step = 0) {
        $step = $step + 2;
        $where = ['pid' => $pid];
        $info = $this->where($where)->order('sort asc')->select();
        for ($i = 0; $i < count($info); $i++) {
            $info[$i]['space'] = ($step == 2) ? '' : str_repeat('&nbsp;&nbsp;', $step);
            $arr[] = $info[$i];
            $this->getTree($info[$i]['id'], $arr, $step);
        }
        return $arr;
    }

    public function getTableNameByCategoryId($id) {
        $modeId = $this->getCategoryById($id, 'model_id')['model_id'];
        $model = Loader::get('\Manage\Model\ContentModel');
        return $model->getModelById($modeId, 'model_table')['model_table'];
    }

    public function getModelByCategoryId($id) {
        $table = $this->getTableNameByCategoryId($id);
        return new Model($table);
    }


    public function getChildCategoryById($id, $field = false) {
        $child = $this->field($field)->where(['pid' => $id])->select();
        return $child;
    }
}