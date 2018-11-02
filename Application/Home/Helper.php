<?php

namespace Home;

use Top\Loader;

class Helper {

    public static function getCategory($category = 0) {
        $model = Loader::get('\Manage\Model\Category');
        $data = $model->where(['pid' => $category, 'status' => 1])->select();
        return $data;
    }

    public static function getCategoryName($category = 0) {
        return self::category($category)['title'];
    }

    public static function category($category = 0) {
        $model = Loader::get('\Manage\Model\Category');
        return $model->getCategoryById($category);
    }

    public static function position($categoryId, $limit) {
        $model = Loader::get('\Home\Model\Article');
        $lists = $model->getPosition($categoryId, $limit, true);
        return $lists;
    }


    public static function getNavi($pid = 0) {
        $model = Loader::get('\Manage\Model\Channel');
        return $model->lists(['pid' => $pid]);
    }

    public static function getArticleLists($categoryId, $limit, $child = false, $count = false) {
        // 当前所属分类信息
        $categoryModel = Loader::get('\Home\Model\Category');
        if ($child) {
            $idsArray = $categoryModel->field('id')->where(['pid' => $categoryId])->select();
            $ids = [];
            for ($i = 0; $i < count($idsArray); $i++) {
                $ids[$i] = $idsArray[$i]['id'];
            }
        } else {
            $ids = $categoryId;
        }
        if ($count === false) {
            // 内容列表
            $lists = $categoryModel->lists($ids, 'level desc, create_time desc', $limit);
            return $lists;
        } else {
            $count = $categoryModel->lists($ids, 'level desc, create_time desc', $limit, true);
            return $count;
        }
    }

    public static function getPrevArticle($categoryId, $id, $default = '没有了') {
        $model = Loader::get('\Manage\Model\Article');
        $article = $model->getArticle($categoryId, 'id > ' . $id . ' and category_id = ' . $categoryId);
        if (empty($article)) {
            return [
                'url' => 'javascript:;',
                'title' => $default
            ];
        }
        $article['url'] = u('detail', [$categoryId, $article['id']]);
        return $article;

    }

    public static function getNextArticle($categoryId, $id, $default = '没有了') {
        $model = Loader::get('\Manage\Model\Article');
        $article = $model->getArticle($categoryId, 'id < ' . $id . ' and category_id = ' . $categoryId);
        if (empty($article)) {
            return [
                'url' => 'javascript:;',
                'title' => $default
            ];
        }
        $article['url'] = u('detail', [$categoryId, $article['id']]);
        return $article;
    }

    public static function getArticle($categoryId, $id) {
        $model = Loader::get('\Manage\Model\Article');
        $article = $model->getArticle($categoryId, ['id' => $id]);
        return $article;
    }

}