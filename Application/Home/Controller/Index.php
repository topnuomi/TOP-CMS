<?php

namespace Home\Controller;

use Top\Controller;

class Index extends Controller {

    public function index() {
        // 两种为模板传值的方法
        $this->params('message', 'Hello world !');
        $this->cache()->view('Index/index', [
            'title' => 'TOP-CMS'
        ]);
    }

}
