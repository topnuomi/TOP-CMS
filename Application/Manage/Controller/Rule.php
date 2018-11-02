<?php

namespace Manage\Controller;

use Top\Loader;

class Rule extends Manage {

    public function add() {
        $model = Loader::get('\Manage\Model\Rule');
        if (request()->isPost()) {
            if ($model->addRule()) {
                $this->showJson('新增成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $this->params('tree', $model->getTree());
            $this->view();
        }
    }

    public function edit($id) {
        $id = (int)$id;
        $model = Loader::get('\Manage\Model\Rule');
        if (request()->isPost()) {
            if ($model->saveRule($id)) {
                $this->showJson('保存成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $this->params('tree', $model->getTree());
            $this->params('info', $model->getRuleById($id));
            $this->view();
        }
    }

    public function index() {
        $model = Loader::get('\Manage\Model\Rule');
        $this->params('number', $model->count());
        $this->params('tree', $model->getTree());
        $this->view();
    }

    public function delete($id = '') {
        $id = $_POST['id'];
        $model = Loader::get('\Manage\Model\Rule');
        if ($model->deleteRule($id)) {
            $this->showJson('删除成功', 1);
        }
        $this->showJson($model->getError());
    }

    public function updateRule() {
        $model = Loader::get('\Manage\Model\Rule');
        if ($model->updateRuleLists()) {
            $this->showJson('更新权限列表成功', 1);
        }
        $this->showJson('更新失败');
    }

}