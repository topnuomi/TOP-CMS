<?php

namespace Manage\Controller;

use Top\Loader;

class Field extends Manage {

    public function index($mid) {
        $mid = (int)$mid;
        $model = Loader::get('\Manage\Model\ContentModel');
        $field = Loader::get('\Manage\Model\Field');
        $where = ['model_id' => $mid];
        $this->params('type', $field->typeMap);
        $this->params('info', $model->getModelById($mid));
        $this->params('number', $field->where($where)->count());
        $this->params('list', $field->lists($where));
        $this->params('mid', $mid);
        $this->view();
    }

    public function add($mid) {
        $mid = (int)$mid;
        if (request()->isPost()) {
            $model = Loader::get('\Manage\Model\Field');
            if ($model->addField($mid)) {
                $this->showJson('字段增加成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $this->params('mid', $mid);
            $this->view();
        }
    }

    public function edit($id) {
        $id = (int)$id;
        $model = Loader::get('\Manage\Model\Field');
        if (request()->isPost()) {
            if ($model->saveField($id)) {
                $this->showJson('保存成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $info = $model->getFieldById($id);
            $this->params('info', $info);
            $this->params('id', $id);
            $this->view();
        }
    }

    public function delete() {
        $id = (int)$_POST['id'];
        $model = Loader::get('\Manage\Model\Field');
        if ($model->deleteField($id)) {
            $this->showJson('删除成功', 1);
        }
        $this->showJson($model->getError());
    }
}