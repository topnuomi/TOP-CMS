<?php
/**
 * @author TOP糯米 2017
 */

namespace Top;

/**
 * 插件类
 * @author TOP糯米
 */
class Addons {
    private $addonsPath;
    private $params = [];
    protected $name = '';
    protected $config = '';

    /**
     * Addons constructor.
     */
    public function __construct() {
        $this->addonsPath = BASEPATH . 'Addons/';
        $file = $this->addonsPath . $this->name . '/config.php';
        $this->config = require $file;
    }

    /**
     * 插件传递参数方法（与应用params方法分离，重新实现）
     * @param $name
     * @param $value
     */
    public function params($name, $value) {
        $this->params[$name] = $value;
    }

    /**
     * 插件加载视图的方法（与应用load方法分离，重新实现）
     * @param string $file
     * @throws \Exception
     */
    public function load($file = '') {
        $this->params['config'] = $this->config;
        if (!$file) {
            $file = 'index';
        }
        $file = $this->name . '/' . $file . '.html';
        $viewFile = $this->addonsPath . $file;
        if(file_exists($viewFile)) {
            $viewConfig = Config::get('view');
            if (!isset($viewConfig['engine'])) {
                $viewConfig['engine'] = true;
            }
            extract($this->params);
            if ($viewConfig['engine'] === true) {
                $engine = Tags::getInstance();
                $compileFile = $engine->compileDir . md5($viewFile) . '.php';
                if (!file_exists($compileFile) || DEBUG === true) {
                    $engine->processing($viewFile, true);
                }
                $viewFile = $compileFile;
            }
            require $viewFile;
        } else {
            try {
                throw new \Exception($viewFile . ' not find! <br />');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}