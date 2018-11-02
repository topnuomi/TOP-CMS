<?php

namespace Manage\Model;

use Top\Loader;
use Top\Model;

class Field extends Model {
    protected $table = 'model_field';
    protected $pk = 'id';
    public $typeMap = [
        1 => '字符',
        2 => '数字',
        3 => '枚举',
        4 => '单选',
        5 => '文本框',
        6 => '编辑器',
        7 => '上传图片',
        8 => '上传附件'
    ];

    public function lists($where = [], $order = 'sort asc') {
        return $this->where($where)->order($order)->select();
    }

    public function getFieldById($id) {
        return $this->find($id);
    }

    public function addField($mid) {
        $model = Loader::get('\Manage\Model\ContentModel');
        $realTableName = $model->getRealTableName($mid);
        if ($model->checkTableExists($realTableName)) {
            $data = $this->getData();
            $data['model_id'] = $mid;
            $notNull = ($data['is_null']) ? '' : 'NOT NULL';
            if (strtolower($data['default_value']) == 'null') {
                $default = 'NULL';
            } else {
                $default = ((is_numeric($data['default_value'])) ? $data['default_value'] : '\'' . $data['default_value'] . '\'');
            }
            $defaultValue = (!$data['is_null'] && $default == 'NULL') ? '' : (($data['default_value'] !== '') ? "DEFAULT " . $default : '');
            $sql = "ALTER TABLE `{$realTableName}` ADD COLUMN `{$data['field_name']}` {$data['field_type']} {$notNull} {$defaultValue} COMMENT '{$data['field_zhname']}'";
            if ($this->query($sql)) {
                if ($this->insert($data) !== false) {
                    return true;
                } else {
                    $this->error = '失败，数据写入失败';
                    return false;
                }
            } else {
                $this->error = '失败，请检查数据类型或字段是否存在';
                return false;
            }
        }
        $this->error = '新增字段失败';
        return false;
    }

    public function saveField($id) {
        $info = $this->getFieldById($id);
        if (empty($info)) {
            $this->error = '字段不存在';
            return false;
        }
        $data = $this->getData();
        $model = Loader::get('\Manage\Model\ContentModel');
        $realTableName = $model->getRealTableName($info['model_id']);
        $notNull = ($data['is_null']) ? '' : 'NOT NULL';
        if (strtolower($data['default_value']) == 'null') {
            $default = 'NULL';
        } else {
            $default = ((is_numeric($data['default_value'])) ? $data['default_value'] : '\'' . $data['default_value'] . '\'');
        }
        $defaultValue = (!$data['is_null'] && $default == 'NULL') ? '' : (($data['default_value'] !== '') ? "DEFAULT " . $default : '');
        $sql = "ALTER TABLE `{$realTableName}` CHANGE `{$info['field_name']}` `{$info['field_name']}` {$data['field_type']} {$notNull} {$defaultValue} COMMENT '{$data['field_zhname']}'";
        if ($this->query($sql) !== false) {
            if ($this->update($data, $id) !== false) {
                return true;
            } else {
                $this->error = '失败，数据写入失败';
                return false;
            }
        } else {
            $this->error = '失败，请检查数据类型或字段是否存在';
            return false;
        }
    }

    public function deleteField($id) {
        $info = $this->where(['id' => $id])->find();
        $model = Loader::get('\Manage\Model\ContentModel');
        $realTableName = $model->getRealTableName($info['model_id']);
        $sql = "ALTER TABLE `{$realTableName}` DROP COLUMN `{$info['field_name']}`";
        if ($this->query($sql)) {
            if ($this->delete($id)) {
                return true;
            }
        }
        $this->error = '字段删除失败';
        return false;
    }
}