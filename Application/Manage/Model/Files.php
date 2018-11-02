<?php

namespace Manage\Model;

use Top\Model;

class Files extends Model {
    protected $table = 'files';
    protected $pk = 'id';
    protected $map = [];

    public function lists($order = 'create_time desc', $limit = false) {
        return $this->order($order)->limit($limit)->select();
    }

    public function upload($filename) {
        if (file_exists($filename)) {
            $hash = md5_file($filename);
        }
        $file = $this->getFileByHash($hash);
        if (!empty($file)) {
            @unlink($filename);
            return [
                'id' => (int)$file['id'],
                'path' => $file['path']
            ];
        } else {
            $filename = ltrim($filename, '.');
            $data = [
                'path' => $filename,
                'hash' => $hash,
                'create_time' => time()
            ];
            if ($id = $this->insert($data)) {
                return [
                    'id' => $id,
                    'path' => $filename
                ];
            }
            $this->error = '文件上传失败';
            return false;
        }
    }

    public function getFileByHash($hash) {
        return $this->where(['hash' => $hash])->find();
    }

    public function getFileById($id) {
        return $this->where(['id' => $id])->find();
    }

    public function deleteFiles($id) {
        if (is_array($id)) {
            for ($i = 0; $i < count($id); $i++) {
                $info = $this->getFileById((int)$id[$i]);
                @unlink($info['path']);
                if (!$this->delete((int)$id[$i])) {
                    $this->error = '部分数据删除失败';
                    return false;
                }
            }
            return true;
        } else {
            $id = (int)$id;
            $info = $this->getFileById($id);
            @unlink('.' . $info['path']);
            if ($this->delete($id)) {
                return true;
            }
            echo $this->_sql();
            $this->error = '删除失败';
            return false;
        }
    }
}