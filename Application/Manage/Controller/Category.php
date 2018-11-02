<?php

namespace Manage\Controller;

use Top\Loader;

class Category extends Manage {

    public function index() {
        $model = Loader::get('\Manage\Model\Category');
        $list = $model->getTree();
        $this->params('number', $model->count());
        $this->params('list', $list);
        $this->view();
    }

    public function add($pid = 0) {
        $pid = (int)$pid;
        $model = Loader::get('\Manage\Model\Category');
        if (request()->isPost()) {
            if ($model->addCategory()) {
                $this->showJson('添加分类成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $channelModel = Loader::get('\Manage\Model\Channel');
            $this->params('channel', $channelModel->lists(['pid' => 0]));
            $list = $model->getTree();
            $modelObject = Loader::get('\Manage\Model\ContentModel');
            $this->params('list', $list);
            $this->params('pid', $pid);
            $this->params('model_list', $modelObject->lists());
            $this->view();
        }
    }

    public function edit($id = '') {
        $id = (int)$id;
        $model = Loader::get('\Manage\Model\Category');
        if (request()->isPost()) {
            if ($model->saveCategory($id)) {
                $this->showJson('保存分类成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $channelModel = Loader::get('\Manage\Model\Channel');
            $this->params('channel', $channelModel->lists(['pid' => 0]));
            $info = $model->getCategoryById($id);
            $list = $model->getTree();
            $modelObject = Loader::get('\Manage\Model\ContentModel');
            $this->params('list', $list);
            $this->params('info', $info);
            $this->params('id', $id);
            $this->params('model_list', $modelObject->lists());
            $this->view();
        }
    }

    public function delete() {
        $id = $_POST['id'];
        $model = Loader::get('\Manage\Model\Category');
        if ($model->deleteCategory($id)) {
            $this->showJson('删除成功', 1);
        }
        $this->showJson($model->getError());
    }
}