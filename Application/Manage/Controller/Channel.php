<?php

namespace Manage\Controller;


use Top\Loader;

class Channel extends Manage {

    public function index($pid = 0) {
        $pid = (int)$pid;
        $model = Loader::get('\Manage\Model\Channel');
        $parent = $model->getChannelById($pid);
        $lists = $model->lists(['pid' => $pid]);
        $this->view('', [
            'list' => $lists,
            'parent' => $parent,
            'pid' => $pid,
            'number' => $model->count()
        ]);
    }

    public function add($id = 0) {
        $model = Loader::get('\Manage\Model\Channel');
        if (request()->isPost()) {
            if ($model->addChannel()) {
                $this->showJson('新增导航成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $this->params('id', $id);
            $this->params('tree', $model->getTree());
            $this->view();
        }
    }

    public function edit($id) {
        $id = (int)$id;
        $model = Loader::get('\Manage\Model\Channel');
        $info = $model->getChannelById($id);
        if (empty($info)) {
            $this->error('导航不存在');
        }
        if (request()->isPost()) {
            if ($model->saveChannel($id)) {
                $this->showJson('保存成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $this->params('info', $info);
            $this->params('pid', $info['pid']);
            $this->params('tree', $model->getTree());
            $this->view();
        }
    }

    public function delete() {
        $id = $_POST['id'];
        $model = Loader::get('\Manage\Model\Channel');
        if ($model->deleteChannel($id)) {
            $this->showJson('删除成功', 1);
        }
        $this->showJson($model->getError());
    }
}