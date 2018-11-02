<?php

namespace Addons\Editor;

use Top\Addons;

class Index extends Addons {
    protected $name = 'Editor';

    public function run($params) {
        $this->params('name', $params['name']);
        $this->params('content', $params['content']);
        $this->load();
    }

}