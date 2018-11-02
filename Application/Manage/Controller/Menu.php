<?php

namespace Manage\Controller;

use Top\Loader;

class Menu extends Manage {

    public function add($pid = 0) {
        $pid = (int)$pid;
        $model = Loader::get('\Manage\Model\Menu');
        if (request()->isPost()) {
            if ($model->addMenu()) {
                $this->showJson('菜单增加成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $parentInfo = $model->getMenuById($pid);
            $this->params('parent', $parentInfo);
            $this->params('pid', $pid);
            $this->params('menu', $model->getTree());
            $this->view();
        }
    }

    public function index($pid = 0) {
        $pid = (int)$pid;
        $model = Loader::get('\Manage\Model\Menu');
        $list = $model->lists(['pid' => $pid]);
        $this->params('list', $list);
        $this->params('number', $model->count());
        $this->params('parent', $model->getMenuById($pid));
        $this->view();
    }

    public function edit($id = '') {
        $id = (int)$id;
        $model = Loader::get('\Manage\Model\Menu');
        if (request()->isPost()) {
            if ($model->saveMenu($id)) {
                $this->showJson('更新成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $info = $model->getMenuById($id);
            $this->params('info', $info);
            $this->params('menu', $model->getTree());
            $this->params('pid', $info['pid']);
            $this->params('id', $id);
            $this->view();
        }
    }

    public function delete() {
        $id = $_POST['id'];
        $model = Loader::get('\Manage\Model\Menu');
        if ($model->deleteMenu($id)) {
            $this->showJson('删除成功', 1);
        }
        $this->showJson($model->getError());
    }

}