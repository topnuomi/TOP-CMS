<?php

namespace Manage\Controller;

use Top\Loader;
use Vendor\Page;

class Article extends Manage {

    public function index() {
        $model = Loader::get('\Manage\Model\Category');
        $list = $model->getTree();
        $this->params('list', $list);
        $this->view('Article/index');
    }

    public function add($categoryId) {
        $categoryId = (int)$categoryId;
        $article = Loader::get('\Manage\Model\Article');
        if (request()->isPost()) {
            if ($article->addContent($categoryId)) {
                $this->showJson('发布成功', 1);
            }
            $this->showJson($article->getError());
        } else {
            $fieldList = $article->getFieldByCategoryId($categoryId);
            $this->params('field', $fieldList);
            $this->params('category_id', $categoryId);
            $this->view();
        }
    }

    public function edit($categoryId, $id = '') {
        $categoryId = (int)$categoryId;
        $article = Loader::get('\Manage\Model\Article');
        if (request()->isPost()) {
            $id = (int)$id;
            if ($article->saveContent($categoryId, $id)) {
                $this->showJson('保存成功', 1);
            }
            $this->showJson($article->getError());
        } else {
            $info = $article->getArticle($categoryId, ['id' => $id]);
            $fieldList = $article->getFieldByCategoryId($categoryId);
            $this->params('field', $fieldList);
            $this->params('category_id', $categoryId);
            $this->params('info', $info);
            $this->params('id', $id);
            $this->view();
        }
    }

    public function lists($categoryId) {
        $article = Loader::get('\Manage\Model\Article');
        $categoryInfo = Loader::get('\Manage\Model\Category')->getCategoryById($categoryId);
        $all = $article->getContentListByCategoryId($categoryId, false, false, true);
        $page = new Page(16, $all);
        $list = $article->getContentListByCategoryId($categoryId, 'level desc', "$page->startNum, $page->listNum");
        $fieldList = $article->getFieldByCategoryId($categoryId);
        $template = Loader::get('\Manage\Model\ContentModel')->getModelById($categoryInfo['model_id'], 'list_display')['list_display'];
        $this->params('field', $fieldList);
        $this->params('list', $list);
        $this->params('number', $all);
        $this->params('category', $categoryInfo);
        $this->params('page', $page->show());
        $this->view($template);
    }

    public function delete() {
        $categoryId = (int)$_POST['categoryId'];
        $id = $_POST['id'];
        $article = Loader::get('\Manage\Model\Article');
        if ($article->deleteArticle($categoryId, $id)) {
            $this->showJson('删除成功', 1);
        }
        $this->showJson('删除失败');
    }
}