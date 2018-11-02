<?php

namespace Manage\Controller;

use Top\Loader;
use Vendor\Page;

class Files extends Manage {

    public function index() {
        $model = Loader::get('\Manage\Model\Files');
        $number = $model->count();
        $page = new Page(30, $number);
        $list = $model->lists('create_time desc', "$page->startNum, $page->listNum");
        $this->params('page', $page->show());
        $this->params('list', $list);
        $this->params('number', $number);
        $this->view();
    }

    public function add() {
        $this->view();
    }

    public function delete() {
        $id = $_POST['id'];
        if (request()->isPost()) {
            $model = Loader::get('\Manage\Model\Files');
            if ($model->deleteFiles($id)) {
                $this->showJson('删除成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $this->view();
        }
    }
}