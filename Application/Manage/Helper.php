<?php

namespace Manage;


use Top\Loader;

class Helper {

    public static function Config($name) {
        $model = Loader::get('\Manage\Model\Config');
        $info = $model->getConfigByName($name);
        return $info['value'];
    }

    public static function processionEnum($enum) {
        if ($enum) {
            $selectArray = explode("\n", $enum);
            foreach ($selectArray as $key => $value) {
                $smallSelectArray = explode(':', $value);
                $array[$smallSelectArray[0]] = $smallSelectArray[1];
            }
        }
        return $array;
    }

    public static function getFile($id) {
        $model = Loader::get('\Manage\Model\Files');
        $info = $model->getFileById($id);
        if (!empty($info)) {
            return $info;
        }
        return ['path' => 0];
    }

    public static function isLogin() {
        $info = session('user_auth');
        if (empty($info)) {
            return 0;
        }
        return $info['id'];
    }

    public static function getGroupName($id) {
        $model = Loader::get('\Manage\Model\Group');
        $info = $model->getGroupById($id);
        if (empty($info)) {
            return '无';
        }
        return $info['name'];
    }
}