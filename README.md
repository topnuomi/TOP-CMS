# TOP-CMS
#### 全文以Home模块为例
> ### **入口文件的定义**
```
// 项目根目录
define('BASEPATH', dirname(__FILE__) . '/');
// 应用所在目录
define('APP', BASEPATH . 'Application/');
// 框架所在目录
define('FRAMEWORK', BASEPATH . 'System/');
// 加载框架
require FRAMEWORK . 'framework.php';
```
> ### **配置文件**
###### BASEPATH . Home/Config/config.php
###### Manage模块会调用此处的数据库配置
```
// 一个典型配置文件

return [
    // 以 name => value 的形式
    'session' => true, // 是否开启session
    'db' => [
        'type' => 'Mysqli', // 数据库驱动类型（暂时只有mysqli）
        'host' => '', // 主机
        'dbname' => '', // 数据库名
        'user' => '', // 用户
        'pwd' => '', // 密码
        'prefix' => 'cms_', // 表前缀
        'charset' => 'utf8', // 编码设置
        'cache_dir' => '', // 缓存目录
        'cache_time' => '', // 缓存时间
    ],
    'view' => [
        'engine' => true, // 是否开启模板引擎
        'left_tags' => '{', // 左定界符
        'right_tags' => '}', // 右定界符
        'compile_dir' => '', // 编译文件存放目录
        'view_dir' => '', // 视图文件所在位置
        'suffixes' => 'html', // 视图文件后缀
        'cache_dir' => '', // 缓存目录
        'cache_time' => '', // 缓存时间
    ],
    // 其他自定义配置，使用\TOP\Config::get(name)获取当前模块配置
];
```
___
> ### **模型定义**
```
// 一个典型的模型

namespace Home\Model;
use Top\Model;

class 模型名称 extends Model {
    protected $table = ''; // 表名（不包含前缀）
    protected $pk = ''; // 主键（缺省值为id）
    protected $map = []; // 字段映射 (key => 真实字段)
    
    // 其他自定义方法......
}
```
#### 模型的实例化
```
1、 可能直接new是最简单的使用方法
$model = new \Home\Model\模型名称;
2、 避免多次实例化这个模型
$model = \Top\Loader::get('\Home\Model\模型名称');
```
#### 模型中的连贯操作

###### 在框架中实现了类似TP的连贯操作
```
$model = \Top\Loader::get('\Home\Model\模型名称');
$where = [];
// $where = ['status' => 1, 'position' => 1];
// $where = 'status = 1 and position = 1';
// where方法接收的参数可以为数组或字符串形式
$data = $model->field('id')
        ->where($where)
        ->order('create_time desc')
        ->limit(1)
        ->select();
var_dump($data);
```
___
> ### **控制器定义**
```
// 一个典型的控制器

namespace Manage\Controller;
use Top\Controller;

class 控制器名 extends Controller {
    
    public function index() {
        
    }
}
```
#### 加载视图
view 方法接收 \[视图文件\]\[模板变量\] 两个参数，但都不是必须，默认情况下，视图文件为 '控制器/方法'。
```
public function index() {
    $this->view();
}
```
#### 为模板变量赋值

##### 两种方式：
###### params方法
```
public function index() {
    $this->params('message', 'Hello world');
    $this->view();
}
```
###### 在view方法中
```
public function index() {
    $this->view('', [
        'message' => 'Hello world'
    ]);
}
```
以上两种方式效果相同
___
> ### **视图**
###### 视图文件的本质就是一个html文档，后缀在配置文件中配置，默认为html。如果开启了模板引擎，就可以在模板文件中使用模板标签，定界符在配置文件中配置，默认为'{'和'}'。框架内置了许多常用的模板标签。
### **php**
```
{php}
    echo 'Hello world';
{/php}
```
###### 标签内的内容会被解析为原生php代码
### **{变量名}**
```
{$name}
```
echo变量名为name的变量;
### **assign** （在模板中为变量赋值）
```
{assign name="$name" value="1"}
```
将1赋值给$name
### **if** （if判断）
```
{if $a > $b}
    一些操作
{/if}

{if $a > $b}
    一些操作
{else /}
    另外一些操作
{/if}

{if $a > $b}
    一些操作
{elseif $a > $c}
    另外一些操作
{else /}
    另外一些操作
{/if}
```
if标签中的判断条件为原生php表达式
### **empty** （参考php的empty）
```
{empty name="$name"}
    一些操作
{/empty}
```
此标签解析后本质是一个if判断，所以我们也可以这样：
```
{empty name="$name"}
    一些操作
{else /}
    另外一些操作
{/empty}
```
### **notempty** （略）
与上一个标签相反
### **loop**
```
{loop $list as $key => $value}
    {$key} => {$value}
{/loop}
```
loop标签中为原生php表达式，与php中的foreach相同（本质就是foreach）
### **for**
```
{for $i = 0; $i < 10; $i++}
    {$i}
{/for}
```
for标签中为原生php表达式，与php中的for相同（本质就是for）
### **switch**
```
{switch name="$name"}
    {case value="1"}
        一些操作
    {/case}
    {case value="2"}
        一些操作
    {/case}
    {default /}
        一些操作
{/switch}
```
这个标签应该能看明白吧，不多说了......
### **自定义标签**
###### 文件位置：BASEPATH . Home/Config/tags.php
#### **简单的示例：**
```
'hello' => 'echo \'Hello\';'
```
在模板文件中
```
{hello}
```
运行结果
```
Hello
```
#### **传入参数的示例：**
```
'say:what' => 'echo \'what\''
```
在模板文件中
```
{say what="Hello"}
```
运行结果
```
Hello
```
#### **传入多个参数的示例：**
```
'sum:p1,p2' => 'echo p1 + p2;'
```
在模板文件中
```
{sum p1="1" p2="2"}
```
运行结果
```
3
```
#### **闭合标签的示例：**
```
'tag:p1,p2' => 'if (p1 > p2):',
'/(tag)' => 'endif;'
```
在模板文件中
```
{tag p1="2" p2="1"}
    p1 > p2
{/tag}
```
运行结果
```
p1 > p2
```
___
> ### **自定义路由**

# 待续......