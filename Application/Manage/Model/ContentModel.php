<?php

namespace Manage\Model;

use Top\Loader;
use Top\Model;

class ContentModel extends Model {
    protected $table = 'model';
    protected $pk = 'id';
    protected $map = [];

    public function lists($where = [], $order = 'sort asc', $limit = false) {
        return $this->where($where)->order($order)->limit($limit)->select();
    }

    public function getModelById($id, $field = false) {
        return $this->field($field)->find($id);
    }

    public function addModel() {
        $data = $this->getData();
        $data['create_time'] = time();
        $prefix = \Top\Config::get('db')['prefix'];
        $tableName = $prefix . $this->filter($data['model_table']);
        if ($this->checkTableExists($tableName)) {
            $this->error = '数据表已存在';
            return false;
        } else {
            if ($this->createTable($tableName, $data['model_engine'])) {
                if ($id = $this->insert($data)) {
                    $template = 'detail_template';
                    $level = 'level';
                    $fieldTemplate = [
                        'model_id' => $id,
                        'field_name' => $template,
                        'field_zhname' => '详情模板',
                        'field_type' => 'char(32)',
                        'type' => 1,
                        'notice' => '详情模板',
                        'is_null' => 1,
                        'sort' => 10,
                        'list_display' => 0
                    ];
                    $fieldLevel = [
                        'model_id' => $id,
                        'field_name' => $level,
                        'field_zhname' => '优先级',
                        'field_type' => 'int',
                        'type' => 2,
                        'notice' => '越大越靠前',
                        'is_null' => 1,
                        'sort' => 10,
                        'list_display' => 1
                    ];
                    $fieldModel = Loader::get('\Manage\Model\Field');
                    if ($fieldModel->insert($fieldTemplate) && $fieldModel->insert($fieldLevel)) {
                        $this->query("ALTER TABLE `{$tableName}` ADD COLUMN `{$template}` {$fieldTemplate['field_type']} DEFAULT NULL COMMENT '详情模板';");
                        $this->query("ALTER TABLE `{$tableName}` ADD COLUMN `{$level}` {$fieldLevel['field_type']} DEFAULT NULL COMMENT '优先级';");
                        return true;
                    }
                    $this->error = '字段数据写入失败';
                    return false;
                }
                $this->error = '数据写入失败';
                return false;
            } else {
                $this->error = '数据表创建失败';
                return false;
            }
        }
    }

    public function saveModel($id) {
        $data = $this->getData();
        $data['model_table'] = $this->filter($data['model_table']);
        $prefix = \Top\Config::get('db')['prefix'];
        $realTableName = $this->getRealTableName($id);
        $newTableName = $prefix . $data['model_table'];
        if ($this->checkTableExists($realTableName)) { // 数据表存在，准备更新数据表
            if ($realTableName != $newTableName) { // 如果输入与旧表名不一致，检查新表名是否存在
                if ($this->checkTableExists($newTableName)) {
                    $this->error = '数据表已存在';
                    return false;
                }
            }
            $engine = $this->filter($data['model_engine']);
            $changeName = "ALTER TABLE `{$realTableName}` RENAME `{$newTableName}`";
            $changeEngine = "ALTER TABLE `{$newTableName}` ENGINE = {$engine}";
            if ($this->query($changeName) === false || $this->query($changeEngine) === false) {
                $this->error = '数据表更新失败';
                return false;
            }
        }
        if ($this->update($data, $id) !== false) {
            return true;
        }
        $this->error = '保存模型失败';
        return false;
    }

    public function getRealTableName($mid) {
        $prefix = \Top\Config::get('db')['prefix'];
        $tableName = $this->field('model_table')->where(['id' => $mid])->find()['model_table'];
        return $prefix . $tableName;
    }

    public function deleteModel($id) {
        $realTableName = $this->getRealTableName($id);
        $sql = "DROP TABLE `{$realTableName}`";
        if ($this->query($sql)) {
            if ($this->delete($id)) {
                $field = Loader::get('\Manage\Model\Field');
                if ($field->where(['model_id' => $id])->delete() !== false) {
                    return true;
                }
            }
        }
        $this->error = '模型删除失败';
        return false;
    }

    /**
     * 创建数据表
     * @param $mid
     * @return bool
     */
    public function createTable($tableName, $engine) {
        $sql = <<<EOF
CREATE TABLE `{$tableName}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `detail_template` char(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE={$engine}
EOF;
        if ($this->query($sql)) {
            return true;
        }
        return false;
    }

    public function checkTableExists($tableName) {
        $dbname = \Top\Config::get('db')['dbname'];
        $result = $this->query("SELECT table_name FROM information_schema.TABLES WHERE table_name ='$tableName' and table_schema = '$dbname'");
        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

}