<?php

namespace Addons\Uploadfive;

use Top\Addons;

class Index extends Addons {
    protected $name = 'Uploadfive';

    public function run($params) {
        $this->params('name', $params['name']);
        $this->params('value', $params['value']);
        $this->params('path', $params['path']);
        $this->load();
    }
}