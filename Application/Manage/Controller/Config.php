<?php
/**
 * @author TOP糯米 2018 我会坚持做我喜欢的事
 */

namespace Manage\Controller;

use Top\Loader;

class Config extends Manage {

    public function index($group = 1) {
        $group = (int)$group;
        $model = Loader::get('\Manage\Model\Config');
        $list = $model->getListByGroup($group);
        $this->params('list', $list);
        $this->params('group', $group);
        $this->view();
    }

    public function add() {
        if (request()->isPost()) {
            $model = Loader::get('\Manage\Model\Config');
            if ($model->add()) {
                $this->showJson('新增成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $this->view();
        }
    }

    public function edit($id) {
        $id = (int)$id;
        $model = Loader::get('\Manage\Model\Config');
        $info = $model->getConfigById($id);
        if (request()->isPost()) {
            if ($model->saveConfig($id)) {
                $this->showJson('修改配置成功', 1);
            }
            $this->showJson($model->getError());
        } else {
            $this->params('info', $info);
            $this->params('id', $id);
            $this->view();
        }
    }

    public function delete() {
        $id = (int)$_POST['id'];
        if (!$id) {
            $this->showJson('参数错误');
        }
        $model = Loader::get('\Manage\Model\Config');
        if ($model->delete($id)) {
            $this->showJson('删除成功', 1);
        }
        $this->showJson('删除失败');
    }

}