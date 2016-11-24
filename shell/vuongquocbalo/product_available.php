<?php

// php product_available.php
// php /home/vuong761/public_html/shell/vuongquocbalo/product_available.php

include_once 'base.php';
include_once '../../include/simple_html_dom.php';
$categoryId = '2,3,5,6,20,21';
$websiteId = 1;
$offset = 0;
$limit = 50;
do {    
    $products = call('/products/allforupdateavailable', [
        'category_id' => $categoryId, 
        'offset' => $offset,
        'limit' => $limit
    ]);
    if (!empty($products)) {
        foreach ($products as $product) {
            try {
                $_id = $product['_id'];
                $url = $product['url_src'];
                $content = app_file_get_contents($url);
                if ($content == false) {
                    batch_info($url . ' Failed');
                    continue;
                }
                $content = strip_tags_content($content, '<script><style>', true);
                $html = str_get_html($content);
                foreach ($html->find('span[class=view-cart]') as $element) {	
                    if (!empty($element->innertext) && $element->innertext == 'Hết hàng') {
                        $ok = call('/products/onoff', ['_id' => $_id, 'field' => 'available', 'value' => '0']);
                        batch_info($product['code'] . ($ok ? ' OK' : ' ERR'));
                        break;
                    }
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                batch_info($errorMessage);
                continue;
            }
            //sleep(1*60);            
        }
    }
	$offset += $limit;
} while (!empty($products));
batch_info('Done');
exit;