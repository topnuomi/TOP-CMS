<?php
/**
 * @author TOP糯米 2017
 */

namespace Top;

/**
 * 模板标签处理类
 * @author TOP糯米
 */
class Tags {
    public static $instance;
    private $processing;
    private $tags;
    public $left;
    public $right;
    private $selfTags = [
        // 原生php代码
        'php' => '<?php ',
        '/php' => ' ?>',
        // 变量直接输出
        '\$(.*?)' => 'echo \$\\1;',
        ':(.*?)' => 'echo \\1;',
        // 模板中变量赋值
        'assign:name,value' => '$name = value;',
        // if
        'empty:name' => 'if (empty(name)):',
        'notempty:name' => 'if (!empty(name)):',
        'if (.*?)' => 'if (\\1):',
        'elseif (.*?) /' => 'else if (\\1):',
        'else /' => 'else:',
        '/(if|empty|notempty)' => 'endif;',
        // foreach
        'loop (.*?)' => '$i = 0; foreach (\\1): $i++;',
        '/loop' => 'endforeach;',
        // for
        'for (.*?)' => 'for (\\1):',
        '/for' => 'endfor;',
        // switch
        'switch:name' => 'switch (\\1):',
        'case:value' => 'case \\1:',
        '/case' => 'break;',
        'default /' => 'default:',
        '/switch' => 'endswitch;',
    ];

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $config = Config::get('view');
        $this->left = (isset($config['left_tags']) && $config['left_tags']) ? $config['left_tags'] : '{';
        $this->right = (isset($config['right_tags']) && $config['right_tags']) ? $config['right_tags'] : '}';
        $this->compileDir = (isset($config['compile_dir']) && $config['compile_dir']) ? $config['compile_dir'] : 'Temps/' . MODULE . '/';
    }

    /**
     * 设置模板标签
     * @param $array
     */
    private function setTags($array) {
        foreach ($array as $key => $value) {
            $tagsInfo = explode(':', $key);
            $tag = $tagsInfo[0];
            // 第一个值不是为空（不是{:xxx}语法）
            if ($tagsInfo[0]) {
                // 存在除标签外的其他属性
                if (isset($tagsInfo[1])) {
                    $attrArr = explode(',', $tagsInfo[1]);
                    // 拼接正则表达式
                    $processingArr = [];
                    for ($i = 0; $i < count($attrArr); $i++) {
                        $processingArr[$attrArr[$i]] = '\\' . ($i + 1);
                        $tag .= '\s' . $attrArr[$i] . '="(.*?)"';
                    }
                    $keys = array_keys($processingArr);
                    $vals = array_values($processingArr);
                    $value = str_replace($keys, $vals, $value);
                }
            } else {
                // {:xxx}语法则保持原样
                $tag = $key;
            }
            // 正则界定符使用#号，避免过多的转义字符
            $this->tags[] = '#' . $this->left . $tag . $this->right . '#i';
            // 不拼接原生脚本开始结束标记符
            $this->processing[] = ($value != '<?php ' && $value != ' ?>') ? '<?php ' . $value . ' ?>' : $value;
        }
    }

    /**
     * 预处理引入视图标签（为了保证require进来的文件中的模板标签可用，必须先进行预处理）
     * @param $filename
     * @return string
     */
    public function processingViewTag($filename) {
        $tags = [
            'view:name' => 'if(!isset($____view_config)){$____view_config=\Top\Config::get(\'view\');$____suffixes=(isset($config[\'suffixes\'])&&$config[\'suffixes\'])?$config[\'suffixes\']:\'html\';}require __VIEW__.\'name.\'.$____suffixes;'
        ];
        $this->setTags($tags);
        $content = file_get_contents($filename);
        $result = preg_replace($this->tags, $this->processing, $content);
        $tempFileName = $this->compileDir . md5($filename) . '_temp.php';
        if (!is_dir($this->compileDir)) {
            mkdir($this->compileDir, 0777, true);
        }
        // 创建临时文件
        file_put_contents($tempFileName, $result);
        ob_start();
        require $tempFileName;
        // 拿到临时创建的文件内容
        $content = ob_get_contents();
        ob_clean();
        // 删除临时文件
        @unlink($tempFileName);
        return $content;
    }

    /**
     * 处理模板文件中的标签（插件模板不解析view标签）
     * @param $filename
     * @return string
     * 最终调用此方法，返回处理后的文件名
     */
    public function processing($filename, $isAddons = false) {
        if (!$isAddons) {
            $content = $this->processingViewTag($filename);
        } else {
            // 处理最终视图文件并且创建编译后的文件
            $content = file_get_contents($filename);
        }
        // 加载预设模板标签
        $this->setTags($this->selfTags);
        // 加载自定义模板标签
        // 文件位置固定
        $tagsFile = APP . MODULE . '/Config/tags.php';
        if (file_exists($tagsFile)) {
            // 文件存在，则将数组放入标签数组
            $tags = require $tagsFile;
            $this->setTags($tags);
        }
        $result = preg_replace($this->tags, $this->processing, $content);
        if (!is_dir($this->compileDir)) {
            mkdir($this->compileDir, 0777, true);
        }
        // 最终过滤内容中?\>与<?php中间的内容
        $result = preg_replace('#\?>([\r|\n|\s]*?)<\?php#', '', $result);
        $filename = $this->compileDir . md5($filename) . '.php';
        file_put_contents($filename, "<?php if(!defined('BASEPATH')) { exit; } ?>" . $result);
        return $filename;
    }
}