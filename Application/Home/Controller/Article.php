<?php

namespace Home\Controller;

use Top\Loader;
use Vendor\Page;

/**
 * 前台文档
 * Class Article
 * @package Home\Controller
 * 感觉这个控制器稍显臃肿啊，以后再来优化吧
 */
class Article extends Common {

    /**
     * 频道页
     * @param string $category
     */
    public function index($category = '') {
        // 当前所属分类信息
        $categoryModel = Loader::get('\Home\Model\Category');
        $category = $categoryModel->category($category);
        if (empty($category)) {
            $this->error($categoryModel->getError());
        }
        $this->params('category', $category);
        $this->cache()->view($category['index_template']);
    }

    /**
     * 列表页
     * @param string $category
     */
    public function lists($category = '') {
        // 当前所属分类信息
        $categoryModel = Loader::get('\Home\Model\Category');
        $category = $categoryModel->category($category);
        if (empty($category)) {
            $this->error($categoryModel->getError());
        }
        // 内容列表
        $total = $categoryModel->lists($category['id'], false, false, true);
        $page = new Page($category['list_row'], $total);
        $order = 'level desc, create_time desc';
        $lists = $categoryModel->lists($category['id'], $order, "$page->startNum, $page->listNum", false);
        $this->params('page', $page->homeShow());
        $this->params('lists', $lists);
        $this->params('category', $category);
        $this->cache()->view($category['list_template']);
    }

    /**
     * 详情页
     * @param string $category
     * @param string $id
     */
    public function detail($category = '', $id = '') {
        $id = (int)((!$id) ? $_GET['id'] : $id);
        $category = (!$category) ? $_GET['category'] : $category;
        // 当前所属分类信息
        $categoryModel = Loader::get('\Home\Model\Category');
        $categoryInfo = $categoryModel->category($category);
        // 当前文章内容
        $article = Loader::get('\Home\Model\Article');
        $info = $article->getContent($category, $id);
        // 文章不存在处理
        if ($info === false) {
            $this->error($article->getError());
        }
        // 当前所属分类配置的详情模板
        $categoryTemplate = $categoryInfo['detail_template'];
        // 如果当前文档单独配置了详情模板则优先使用
        $template = (!$categoryTemplate) ? $info['detail_template'] : $categoryTemplate;
        $this->params('category', $categoryInfo);
        $this->params('info', $info);
        $this->cache()->view($template);
    }

    /**
     * 子分类下所有文档
     * @param string $category
     * 当前分类下所有子分类的文档（暂时只能往下取一层）
     */
    public function all($category = '') {
        // 当前分类信息
        $categoryModel = Loader::get('\Home\Model\Category');
        $category = $categoryModel->category($category);
        // 获取分类下所有文档总数
        $total = \Home\Helper::getArticleLists($category['id'], false, true, true);
        $page = new Page($category['list_row'], $total);
        // 获取文档列表
        $lists = \Home\Helper::getArticleLists($category['id'], "$page->startNum, $page->listNum", true);
        $this->params('page', $page->homeShow());
        $this->params('lists', $lists);
        $this->params('category', $category);
        $this->cache()->view($category['list_template']);
    }

}