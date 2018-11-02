<?php

namespace Manage\Model;

use Top\Loader;
use Top\Model;

class Article extends Model {

    public function getFieldByCategoryId($categoryId) {
        $category = Loader::get('\Manage\Model\Category');
        $modelId = $category->getCategoryById($categoryId)['model_id'];
        $model = Loader::get('\Manage\Model\Field');
        $fieldList = $model->where(['model_id' => $modelId])->order('sort asc')->select();
        return $fieldList;
    }

    public function addContent($categoryId) {
        $category = Loader::get('\Manage\Model\Category');
        $article = $category->getModelByCategoryId($categoryId);
        $data = $article->getData();
        $data['category_id'] = $categoryId;
        $data['create_time'] = time();
        /*foreach ($data as $k => $v) {
            $data[$k] = $this->filter($v);
        }*/
        if ($article->insert($data)) {
            return true;
        }
        $this->error = '发布失败';
        return false;
    }

    public function saveContent($categoryId, $id) {
        $category = Loader::get('\Manage\Model\Category');
        $article = $category->getModelByCategoryId($categoryId);
        $data = $article->getData();
        /*foreach ($data as $k => $v) {
            $data[$k] = $this->filter($v);
        }*/
        $data['category_id'] = $categoryId;
        if ($article->where(['id' => $id])->update($data) !== false) {
            return true;
        }
        $this->error = '保存失败';
        return false;
    }

    public function deleteArticle($categoryId, $id) {
        $category = Loader::get('\Manage\Model\Category');
        $article = $category->getModelByCategoryId($categoryId);
        if (is_array($id)) {
            for ($i = 0; $i < count($id); $i++) {
                if ($article->where(['id' => $id[$i]])->delete() === false) {
                    $this->error = '删除失败';
                    return false;
                }
            }
            return true;
        } else {
            $id = (int)$id;
            if ($article->where(['id' => $id])->delete()) {
                return true;
            }
            $this->error = '删除失败';
            return false;
        }
    }

    public function getContentListByCategoryId($categoryId, $order = 'level desc', $limit = false, $count = false) {
        $category = Loader::get('\Manage\Model\Category');
        if (is_array($categoryId)) {
            $article = $category->getModelByCategoryId($categoryId[0]);
            $ids = implode(',', $categoryId);
            $where = 'category_id in (' . $ids . ')';
        } else {
            $article = $category->getModelByCategoryId($categoryId);
            $where = ['category_id' => $categoryId];
        }
        if ($count) {
            return $article->where($where)->count();
        } else {
            return $article->where($where)->order($order)->limit($limit)->select();
        }
    }

    public function getArticle($categoryId, $where) {
        $category = Loader::get('\Manage\Model\Category');
        $table = $category->getTableNameByCategoryId($categoryId);
        $article = new Model($table);
        return $article->where($where)->find();
    }
}