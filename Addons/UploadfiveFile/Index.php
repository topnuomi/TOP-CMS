<?php

namespace Addons\UploadfiveFile;

use Top\Addons;

class Index extends Addons {
    protected $name = 'UploadfiveFile';

    public function run($params) {
        $this->params('name', $params['name']);
        $this->params('value', $params['value']);
        $this->params('path', $params['path']);
        $this->load();
    }
}