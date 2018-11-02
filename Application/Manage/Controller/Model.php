<?php

namespace Manage\Controller;

use Top\Loader;

class Model extends Manage {

    public function index() {
        $model = Loader::get('\Manage\Model\ContentModel');
        $this->params('number', $model->count());
        $this->params('list', $model->lists());
        $this->view();
    }

    public function add() {
        if (request()->isPost()) {
            $model = Loader::get('\Manage\Model\ContentModel');
            if ($model->addModel()) {
                $this->showJson('新增模型成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $this->view();
        }
    }

    public function delete() {
        $id = (int)$_POST['id'];
        $model = Loader::get('\Manage\Model\ContentModel');
        if ($model->deleteModel($id)) {
            $this->showJson('删除成功', 1);
        }
        $this->showJson('删除失败');
    }

    public function edit($id) {
        $id = (int)$id;
        $model = Loader::get('\Manage\Model\ContentModel');
        if (request()->isPost()) {
            if ($model->saveModel($id)) {
                $this->showJson('保存成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $this->params('info', $model->getModelById($id));
            $this->params('id', $id);
            $this->view();
        }
    }

}