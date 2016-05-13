<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHtmlElement;

/**
 * Helper for ordered and unordered lists
 */
class PaginatorHelper extends AbstractHtmlElement
{    
    /**
     * Render paging html
     *     
     * @author thailvn
     * @param int $total Total record
     * @param int $limit Page size
     * @param int $displayPage How page link showing
     * @param string $url If empty then get current url     
     * @return string Paging html 
     */
    public function __invoke(array $fromQuery, $total = 0, $limit = 0, $displayPage = 10, $url = '')
    {  
        $request = $this->getView()->requestHelper();         
        $page = !empty($fromQuery['page']) ? $fromQuery['page'] : 1;
        $param = array();        
        foreach ($fromQuery as $name => $value) {
            if ($name != 'page') {
                $param[] = "{$name}={$value}";  
            }
        }
        $url = parse_url($request->getRequestUri())['path'] . '?';
        if (!empty($param)) {
            $url = $url . implode('&', $param) . '&';
        } 
        $totalPage = ceil($total / $limit);
        $delta = ceil($displayPage / 2);
        if ($totalPage > $displayPage) {
            if ($page <= $delta) {
                $start = 1;
                $end = $displayPage;
            } elseif ($page >= $totalPage - $delta) {
                $start = $totalPage - $displayPage + 1;
                $end = $totalPage;
            } else {
                $start = $page - $delta + 1;
                $end = $page + $delta;
            }
        } else {
            $start = 1;
            $end = $totalPage;
        } 
        if (domain() == 'admin') {
            return $this->htmlAdmin($url, $start, $end, $page, $limit, $total, $totalPage);
        }
        return $this->htmlWeb($url, $start, $end, $page, $limit, $total, $totalPage);        
    }
    
    public function htmlAdmin($url, $start, $end, $page, $limit, $total, $totalPage) {
        $nav = '';
        $html = '<div class="dataTables_paginate paging_bootstrap fr"><ul class="pagination">';
        if ($end > 1) {
            for ($i = $start; $i <= $end; $i++) {
                if ($i == $page) {
                    $nav .= "<li class=\"active\"><a href=\"#\">{$i}</a></li>";
                } else {
                    $nav .= "<li><a href='" . $url . "page={$i}'>{$i}</a></li>";
                }
            }
            if ($page > 1) {
                $prev = "<li class=\"prev\"><a href='" . $url . "page=" . ($page - 1) . "'>← </a></li>";
            } else {
                $prev = "";
            }
            if ($page < $totalPage) {
                $next = "<li class=\"next\"><a href='" . $url . "page=" . ($page + 1) . "'> →</a></li>";
            } else {
                $next = "";
            }

            $html .= "{$prev}";
            $html .= "{$nav}";
            $html .= "{$next}";
        }        
        $html .= '</ul>';
        if ($total > $limit) {
            $sumary = sprintf($this->getView()->translate('In total of %s entries, showing %s to %s'), $total, ($page - 1) * $limit + 1, min($page * $limit, $total));
            $html .= "<div class=\"paging_sumary\">{$sumary}</div>";
        }
        $html .= '</div>';
        return $html;
    }
    
    public function htmlWeb($url, $start, $end, $page, $limit, $total, $totalPage) {
        $nav = '';
        $html = '<ul class="pagination">';
        if ($end > 1) {
            for ($i = $start; $i <= $end; $i++) {
                if ($i == $page) {
                    $nav .= "<li class=\"active\"><a href=\"#\">{$i}</a></li>";
                } else {
                    $nav .= "<li><a href='" . $url . "page={$i}'>{$i}</a></li>";
                }
            }
            if ($page > 1) {
                $prev = "<li class=\"prev\"><a href='" . $url . "page=" . ($page - 1) . "'><i class=\"fa fa-angle-left\"></i></a></li>";
            } else {
                $prev = "";
            }
            if ($page < $totalPage) {
                $next = "<li class=\"next\"><a href='" . $url . "page=" . ($page + 1) . "'><i class=\"fa fa-angle-right\"></i></a></li>";
            } else {
                $next = "";
            }

            $html .= "{$prev}";
            $html .= "{$nav}";
            $html .= "{$next}";
        }        
        $html .= '</ul>';
        if ($total > $limit) {
            $sumary = sprintf($this->getView()->translate('In total of %s entries, showing %s to %s'), $total, ($page - 1) * $limit + 1, min($page * $limit, $total));
            $html .= "<div class=\"paging_sumary\">{$sumary}</div>";
        }      
        return $html;
    }
    
}
