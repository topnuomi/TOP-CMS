<?php

namespace Home\Model;

use Top\Model;
use Top\Loader;

class Category extends Model {
    protected $table = 'category';
    protected $pk = 'id';
    protected $map = [];

    public function category($category = '') {
        $category = (!$category) ? $this->filter($_GET['category']) : $category;
        $model = Loader::get('\Manage\Model\Category');
        if (is_numeric($category)) {
            $info = $model->getCategoryById((int)$category);
        } else {
            $info = $model->getCategoryByName($category);
        }
        if (!empty($info)) {
            return $info;
        }
        $this->error = '分类不存在';
        return false;
    }

    public function lists($categoryId, $order = 'level desc, create_time desc', $limit = false, $count = false) {
        $model = Loader::get('\Manage\Model\Article');
        $lists = $model->getContentListByCategoryId($categoryId, $order, $limit, $count);
        return $lists;
    }

    public function childCategoryIds($id) {
        $model = Loader::get('\Manage\Model\Category');
        $child = $model->getChildCategoryById($id, 'id');
        return $child;
    }
}