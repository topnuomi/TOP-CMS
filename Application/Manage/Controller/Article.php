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

    public function lists($categoryId) {
        $article = Loader::get('\Manage\Model\Article');
        $categoryInfo = Loader::get('\Manage\Model\Category')->getCategoryById($categoryId);
        $all = $article->getContentListByCategoryId($categoryId, false, false, true);
        $page = new Page(16, $all);
        $list = $article->getContentListByCategoryId($categoryId, 'level desc', "$page->startNum, $page->listNum");
        $fieldList = $article->getFieldByCategoryId($categoryId);
        $template = Loader::get('\Manage\Model\ContentModel')->getModelById($categoryInfo['model_id'], 'list_display');
        $this->params('field', $fieldList);
        $this->params('list', $list);
        $this->params('number', $all);
        $this->params('category', $categoryInfo);
        $this->params('page', $page->show());
        $this->view($template['list_display']);
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

    public function delete() {
        $categoryId = (int)$_POST['categoryId'];
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        (!$id) && $this->showJson('请选择文档');
        $article = Loader::get('\Manage\Model\Article');
        if ($article->deleteArticle($categoryId, $id)) {
            $this->showJson('删除成功', 1);
        }
        $this->showJson('删除失败');
    }

    /**
     * 复制/移动文档
     */
    public function move($type = 'move') {
        session('move_article', []);
        $categoryId = (int)$_POST['categoryId'];
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        (!$id) && $this->showJson('请选择文档');
        $model = Loader::get('\Manage\Model\Category');
        $modelId = $model->getCategoryById($categoryId, 'model_id')['model_id'];
        session('move_article', [
            'type' => $type,
            'categoryId' => $categoryId,
            'modelId' => $modelId,
            'ids' => $id
        ]);
        $this->showJson('成功', 1);
    }

    /**
     * 粘贴文档
     */
    public function paste() {
        $categoryId = (int)$_POST['categoryId'];
        $moveInfo = session('move_article');
        (empty($moveInfo)) && $this->showJson('剪切板为空');
        $model = Loader::get('\Manage\Model\Category');
        $modelId = $model->getCategoryById($categoryId, 'model_id')['model_id'];
        ($moveInfo['modelId'] != $modelId) && $this->showJson('文档类型不一致，无法粘贴');
        $article = $model->getModelByCategoryId($categoryId);
        if ($moveInfo['type'] == 'move') {
            $ids = $moveInfo['ids'];
            for ($i = 0; $i < count($ids); $i++) {
                if ($article->where(['id' => $ids[$i]])->update(['category_id' => $categoryId]) === false) {
                    $this->showJson('部分内容粘贴失败');
                }
            }
        } else {
            $ids = implode(',', $moveInfo['ids']);
            $articleList = $article->where("id in ($ids)")->select();
            $list = array_merge($articleList, []);
            for ($i = 0; $i < count($list); $i++) {
                unset($list[$i]['id']);
                $list[$i]['category_id'] = $categoryId;
                if ($article->insert($list[$i]) === false) {
                    $this->showJson('部分内容粘贴失败');
                }
            }
        }
        session('move_article', []);
        $this->showJson('粘贴成功', 1);
    }
}
