<?php
// 自定义标签

$menu = '$__menu_model=\Top\Loader::get(\'\Manage\Model\Menu\');$__menu_list=$__menu_model->lists([\'pid\'=>parent, \'display\'=>1]);foreach($__menu_list as value){';
$hasmenu = '$__menu_model=\Top\Loader::get(\'\Manage\Model\Menu\');if(!empty($__menu_model->lists([\'pid\'=>value, \'display\'=>1]))){';
$enum = 'to = \Manage\Helper::processionEnum(from);';

return [
    // 菜单列表
    'menu:parent,value' => $menu,
    // 是否含有子菜单
    'hasmenu:value' => $hasmenu,
    '/(menu|hasmenu)' => '}',
    // 获取配置
    'config:name' => 'echo \Manage\Helper::Config(\'name\');',
    'enum:from,to' => $enum,
];