<?php

namespace Home\Model;

use Top\Model;
use Top\Loader;

class Article extends Model {

    public function getContent($category, $id) {
        $categoryModel = Loader::get('\Manage\Model\Category');
        if (is_numeric($category)) {
            $categoryId = $categoryModel->getCategoryById($category, 'id')['id'];
        } else {
            $categoryId = $categoryModel->getCategoryByName($category, 'id')['id'];
        }
        if (!$categoryId) {
            $this->error = '分类不存在';
            return false;
        }
        $model = Loader::get('\Manage\Model\Article');
        $article = $model->getArticle($categoryId, ['id' => $id]);
        if (empty($article)) {
            $this->error = '文档不存在';
            return false;
        }
        return $article;
    }

}