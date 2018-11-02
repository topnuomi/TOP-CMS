<?php
// 自定义标签

return [
    // 配置
    'config:name' => 'echo \Manage\Helper::Config(\'name\');',
    // 分类列表
    'cate:category,name' => '$__cateArr = \Home\Helper::getCategory(category); foreach ($__cateArr as $key => name):',
    // 分类名称
	'catename:id' => '$__cate = \Home\Helper::getCategoryName(id); if ($__cate): echo $__cate; endif;',
    // 导航列表
    'nav:pid,name' => '$__navi_list = \Home\Helper::getNavi(pid); foreach ($__navi_list as $key => name): ',
    // 判断是否有导航
    'hasnav:pid' => 'if (!empty(\Home\Helper::getNavi(pid))):',
    // 文档封面
	'cover:id,name' => 'name = \Manage\Helper::getFile(id); if (!empty(name)):',
    // 调取文章
    'list:category,name,row,child' => '$___lists = \Home\Helper::getArticleLists(\'category\', row, child); foreach ($___lists as $key => name):',
    // 上一篇文章
    'prev:category,id,default,name' => 'name = \Home\Helper::getPrevArticle(category, id, \'default\');',
    // 下一篇文章
    'next:category,id,default,name' => 'name = \Home\Helper::getNextArticle(category, id, \'default\');',
    // 获取文章
    'article:category,id,name' => 'name = \Home\Helper::getArticle(category, id);',

	'/(cover|hasnav)' => 'endif;',
    '/(cate|nav|list|position)' => 'endforeach;'
];
