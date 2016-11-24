<?php
// php /home/vuong761/public_html/shell/vuongquocbalo/sendo_bought.php
// php sendo_bought.php

include_once 'base.php';
include_once '../../include/simple_html_dom.php';

//$url = 'https://www.sendo.vn/shop/vuongquocbalo/tui-xach/balo/balo-nam/';
$url = 'https://www.sendo.vn/shop/vuongquocbalo/san-pham/';
$url = 'https://www.sendo.vn/shop/vuongquocbalo/san-pham/?sortType=norder_30_desc';
$page = 1;
$productUrl = [];
$totalRecord = 0;
$totalPage = 0;
$limit = 40;
$count = 1;
do {
	$content = app_file_get_contents($url . '&p=' . $page);	    
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
    
     
    foreach ($html->find('div[class=overflow_box content_item_hover]') as $element) {
        $code = '';
        $bought = 0;        
        $html2 = str_get_html($element->innertext);        
        foreach ($html2->find('a[class=name_product shop_color_hover]') as $element2) {
            if (!empty($element2->href)) {
                $detailUrl = trim($element2->href);
                $code = explode(' ', trim($element2->innertext));            
                $code = trim(end($code));
                break;
            }
        }
        if (!empty($code)) {
            foreach ($html2->find('span[class=luotmua tool-tip]') as $element2) {
                $bought = trim(strip_tags($element2->innertext)); //echo $bought;
                break;
            }
        }
        if (!empty($code) && !empty($bought)) {
            if (strpos($code, 'SBL') !== false && strpos($code, 'SBL') === 0) {                
                $code = str_replace('SBL', 'VBL', $code);              
                $result = call('/products/update', [
                    'code' => $code, 
                    'count_bought' => $bought, 
                    'url_sendo1' => $detailUrl
                ]);
            } else {
                $result = call('/products/update', [
                    'code' => $code, 
                    'count_bought' => $bought, 
                    'url_other' => $detailUrl
                ]);
            }
            batch_info($code . '-' . $bought);
        }
    }    
	$page++; 
    if ($page > 1) {
        break;
    }
} while($page <= $totalPage);
batch_info('Done');
exit;