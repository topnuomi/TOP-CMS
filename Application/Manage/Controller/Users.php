<?php

namespace Manage\Controller;

use Top\Loader;
use Vendor\Page;

class Users extends Manage {

    /**
     *
     */
    public function index() {
        $model = Loader::get('\Manage\Model\Users');
        $number = $model->count();
        $page = new Page(20, $number);
        $this->params('number', $number);
        $this->params('list', $model->lists([], 'sort asc, id desc', "$page->startNum, $page->listNum"));
        $this->params('page', $page->show());
        $this->view();
    }

    /**
     *
     */
    public function add() {
        if (request()->isPost()) {
            $model = Loader::get('\Manage\Model\Users');
            if ($model->addUsers()) {
                $this->showJson('新增成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $group = Loader::get('\Manage\Model\Group');
            $this->params('group', $group->lists());
            $this->view();
        }
    }

    /**
     * @param $id
     */
    public function edit($id) {
        $id = (int)$id;
        $model = Loader::get('\Manage\Model\Users');
        $info = $model->getUsersById($id);
        if (empty($info)) {
            $this->error('用户不存在');
        }
        if (request()->isPost()) {
            if ($model->saveUsers($id)) {
                $this->showJson('保存成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $group = Loader::get('\Manage\Model\Group');
            $this->params('group', $group->lists());
            $this->params('info', $info);
            $this->view();
        }
    }

    /**
     * @param string $id
     */
    public function delete($id = '') {
        $id = $_POST['id'];
        $model = Loader::get('\Manage\Model\Users');
        if ($model->deleteUsers($id)) {
            $this->showJson('删除成功', 1);
        }
        $this->showJson($model->getError());
    }
}