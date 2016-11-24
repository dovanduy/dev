<?php
// php /home/vuong761/public_html/shell/thoitrang1/sendo_url.php
// php sendo_url.php

include_once 'base.php';
include_once '../../include/simple_html_dom.php';

//$url = 'https://www.sendo.vn/shop/vuongquocbalo/tui-xach/balo/balo-nam/';
$url = 'https://www.sendo.vn/shop/zanadofashion/san-pham/';
$page = 1;
$productUrl = [];
$totalRecord = 0;
$totalPage = 0;
$limit = 40;
$count = 1;
do {
	$content = app_file_get_contents($url . '/?p=' . $page);	
	$content = strip_tags_content($content, '<script><style>', true);
	if ($content == false) {
		batch_info($url . ' Failed'); 
        exit;
	}
	$html = str_get_html($content);
	if (empty($totalRecord)) {
		foreach ($html->find('div[class=amount-pr-shop]') as $element) {
			$html2 = str_get_html($element->innertext);
			foreach ($html2->find('strong') as $element) {
				if (!empty($element->innertext)) {
					$totalRecord = intval($element->innertext);	
					$totalPage = ceil($totalRecord/$limit) + 1;					
				}
			}
		}
	}
	foreach ($html->find('a[class=name_product shop_color_hover]') as $element) {
		if (!empty($element->href) && !empty($element->innertext)) {
			$detailUrl = trim($element->href);
            $name = explode(' ', trim($element->innertext));
            if (!empty($name) && is_array($name)) {
                $code = end($name);
                $result = call('/products/update', ['code' => $code, 'url_other' => $detailUrl]);
                batch_info($result);
                $count++;
            }
		}
	}
	$page++; 
    if ($page >= 6) {
        break;
    }
} while($page <= $totalPage);
batch_info('Done');
exit;