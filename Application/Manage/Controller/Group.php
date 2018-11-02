<?php

namespace Manage\Controller;

use Top\Loader;

class Group extends Manage {

    public function index() {
        $model = Loader::get('\Manage\Model\Group');
        $this->params('number', $model->count());
        $this->params('list', $model->lists());
        $this->view();
    }

    public function add() {
        if (request()->isPost()) {
            $model = Loader::get('\Manage\Model\Group');
            if ($model->addGroup()) {
                $this->showJson('新增成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $this->view();
        }
    }

    public function edit($id) {
        $id = (int)$id;
        $model = Loader::get('\Manage\Model\Group');
        $info = $model->getGroupById($id);
        if (empty($info)) {
            $this->showJson('角色不存在');
        }
        if (request()->isPost()) {
            if ($model->saveGroup($id)) {
                $this->showJson('保存成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $this->params('info', $info);
            $this->view();
        }
    }

    /**
     * @param string $id
     */
    public function delete($id = '') {
        $id = $_POST['id'];
        $model = Loader::get('\Manage\Model\Group');
        if ($model->deleteGroup($id)) {
            $this->showJson('删除成功', 1);
        }
        $this->showJson($model->getError());
    }

    public function distributionRule($id) {
        $model = Loader::get('\Manage\Model\Rule');
        $group = Loader::get('\Manage\Model\Group');
        if (request()->isPost()) {
            if ($group->saveRule($id)) {
                $this->showJson('保存成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $info = $group->getGroupById($id);
            $this->params('rules', explode(',', $info['rules']));
            $this->params('id', $id);
            $this->params('list', $model->getSelectLists());
            $this->view();
        }
    }
}