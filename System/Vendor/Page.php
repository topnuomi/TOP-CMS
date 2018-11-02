<?php
/**
 * @author TOP糯米 2017
 */

namespace Vendor;

/**
 * 分页类
 * @author TOP糯米
 */
class Page {
    public $startNum = 10;
    public $listNum = 10;
    private $allNum = 0;
    private $totalPage = 0;
    private $p;

    /**
     * 实例化时传入分页记录数、全部记录数
     * @param int $listNum
     * @param int $allNum
     */
    public function __construct($listNum, $allNum) {
        $this->p = (isset($_GET['p']) && (int)$_GET['p']) ? (int)$_GET['p'] : 1;
        $this->listNum = $listNum;
        $this->allNum = $allNum;
        $this->totalPage = ceil($this->allNum / $this->listNum);
        $this->startNum = ($this->p - 1) * $this->listNum;
    }

    /**
     * 获取总页数
     * @return number
     */
    public function getTotalPage() {
        return $this->totalPage;
    }

    /**
     * 获取分页HTML
     * @return string
     */
    public function show() {
        $html = '<div class="page">';
        $uri_string = (isset($_GET['s'])) ? $_GET['s'] : u(DEFAULT_URL);
        $m = [];
        preg_match('/\/p\/(.*?).html/i', $uri_string, $m);
        $uri_string = '/' . ltrim(((!empty($m)) ? str_replace($m[0], '', $uri_string) : explode('.', $uri_string)[0]), '/');
        $html .= ($this->p != 1) ? '<a class="prev" href="' . u($uri_string . '/p/' . ($this->p - 1) . '.html') . '">&lt;&lt;</a>' : '';
        for ($i = 0; $i < $this->totalPage; $i++) {
            if ($this->p == ($i + 1)) {
                $html .= '<span class="current">' . ($i + 1) . '</span>';
            } else {
                $html .= '<a class="num" href="' . u($uri_string . '/p/' . ($i + 1)) . '">' . ($i + 1) . '</a>';
            }
        }
        $html .= ($this->p < $this->totalPage) ? '<a class="next" href="' . u($uri_string . '/p/' . ($this->p + 1)) . '">&gt;&gt;</a>' : '';
        return $html . '</div>';
    }

    /**
     * 获取分页HTML
     * @return string
     */
    public function homeShow() {
        $html = '<div class=\'met_pager\'>';
        $uri_string = (isset($_GET['s'])) ? $_GET['s'] : u(DEFAULT_URL);
        $m = [];
        $uri_string = '/' . ltrim($uri_string, '/');
        $pString = '&p=';
        $html .= ($this->p != 1) ? '<a href="' . $uri_string . $pString . ($this->p - 1) . '">上一页</a>' : '';
        for ($i = 0; $i < $this->totalPage; $i++) {
            if ($this->p == ($i + 1)) {
                $html .= '<a class="Ahover">' . ($i + 1) . '</a>';
            } else {
                $html .= '<a href="' . $uri_string . $pString . ($i + 1) . '"' . ($i + 1) . '</a>';
            }
        }
        $html .= ($this->p < $this->totalPage) ? '<a href="' . $uri_string . $pString . ($this->p + 1) . '">下一页</a>' : '';
        return $html . '</div>';
    }
}