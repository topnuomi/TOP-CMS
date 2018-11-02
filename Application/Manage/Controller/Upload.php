<?php

namespace Manage\Controller;

use Top\Loader;

class Upload extends Manage {

    public function uploadfive() {
        $uploadDir = './Uploads/images/' . date('Y/m/d') . '/';
        $upload = \Vendor\Upload::init($uploadDir);
        $filename = $upload->uploadPicture(uniqid());
        if ($filename) {
            $picture = Loader::get('\Manage\Model\Files');
            if ($info = $picture->upload($filename)) {
                $this->showJson('上传成功', 1, $info);
            }
            $this->showJson($picture->getError());
        }
        $this->showJson($upload->getError());
    }

    public function uploadfiveFile() {
        $uploadDir = './Uploads/files/' . date('Y/m/d') . '/';
        $upload = \Vendor\Upload::init($uploadDir, ['zip', 'png', 'jpg', 'gif']);
        $filename = $upload->uploadFile(uniqid());
        if ($filename) {
            $picture = Loader::get('\Manage\Model\Files');
            if ($info = $picture->upload($filename)) {
                $this->showJson('上传成功', 1, $info);
            }
            $this->showJson($picture->getError());
        }
        $this->showJson($upload->getError());
    }

}