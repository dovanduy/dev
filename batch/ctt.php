<?php
// php ctt.php 8
// php ctt.php 17
// php ctt.php 18
// php ctt.php 19

include_once 'base.php'; 
include_once '../include/simple_html_dom.php';   
$categoryId = $argv[1];
$productList = array(
    // Túi Xách Nữ
    array(   
        'disable' => 0,
        'category_id' => 8,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=166&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10
    ),
    // Ba Lô Nữ
    array(   
        'disable' => 0,
        'category_id' => 17,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=167&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10
    ),
    // Túi Xách Nam
    array(   
        'disable' => 0,
        'category_id' => 19,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=164&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10
    ),
    // Ba Lô Nam
    array(   
        'disable' => 0,
        'category_id' => 18,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=165&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10
    ),
);
$importList = array();
foreach ($productList as $item) {    
    if ($item['disable'] === 0 && $item['category_id'] == $categoryId) {
        $importList[] = $item;
    }
}
if (empty($importList)) {
    echo 'List is empty!';
    exit;
}
// get detail url list
foreach ($importList as &$item) {
    $page = 1;
    $stop = false;
    do {
        $content = app_file_get_contents($item['url'] . '&p=' . $page);
        if ($content == false) {
            batch_info($item['url'] . ' Failed');
            continue;
        }
        $html = str_get_html($content);
        foreach($html->find('a[class=product-image]') as $element) { 
            if (!empty($element->href)) {
                $detailUrl = trim($element->href); 
                if (!in_array($detailUrl, $item['detail_url'])) {                         
                    $item['detail_url'][] = $detailUrl;
                } else {
                    $stop = true;
                }
            }                    
        }
        $page++;
    } while ($stop == false);
}
unset($item);         
// end get detail url list

$products = array();        
foreach ($importList as $item) {           
    // get product detail                     
    foreach ($item['detail_url'] as $url) {
        $product = array(
            'category_id' => $item['category_id']
        );                
        $content = app_file_get_contents($url);
        if ($content == false) {
            batch_info($url . ' Failed');
            continue;
        }
        $content = strip_tags_content($content, '<script><style>', true);
        $html = str_get_html($content); 
        foreach($html->find('span[class=price]') as $element) {                
            if (!empty($element->innertext)) {                
                $product['price_src'] = db_float($element->innertext);
                $product['discount_percent'] = 30;
                $product['price'] = round($product['price_src'] - ((5/100) * $product['price_src']) , -3);
                $product['original_price'] = round($product['price'] + (($product['discount_percent']/100)*$product['price']), -3);
                break;
            }
        }    
        foreach($html->find('div[class=box-collateral box-despr]') as $element) {                
            if (!empty($element->innertext)) { 
                $subHtml = str_get_html($element->innertext); 
                foreach($subHtml->find('div[class=block-primg]') as $element1) {                
                    if (!empty($element1->innertext)) {
                        $product['more'] = strip_tags($element1->innertext, '<p><b><span><strong><div>');
                        $product['more'] = str_replace(['Zanado.com', 'zanado.com', 'Zanado', 'zanado'], '<a href="http://vuongquocbalo.com">vuongquocbalo.com</a>', $product['more']);
                        $product['more'] = str_replace(['<p><span></span></p>', '<p></p>', '<p> </p>', '<p><strong><em></em></strong></p>'], '', $product['more']);
                        $product['more'] = str_replace(['<div style="clear:both;margin: 10px auto;width: 80%;border-top: 1px solid #ddd;"></div>'], '<div style="clear:both;margin: 10px auto;width: 100%;border-top: 1px solid #ddd;"></div>', $product['more']);
                        break;
                    }
                }
            }
        }    
       
        foreach($html->find('div[class=product-attribute]') as $element) {
            if (!empty($element->innertext)) {
                $subHtml = str_get_html($element->innertext);                 
                foreach($subHtml->find('div[class=attribute-title]') as $element1) {                
                    if (!empty($element1->innertext)) {
                        $attrName = app_plan_text($element1->innertext);
                        break;
                    }
                }
                foreach($subHtml->find('div[class=attribute-text]') as $element1) {                
                    if (!empty($element1->innertext)) {
                        $attrValue = app_plan_text($element1->innertext);
                        break;
                    }
                }                       
                if (!empty($attrName) && !empty($attrValue)) {
                    switch ($attrName) {
                        case 'Mã SP':
                            $product['code'] = 'V' . $attrValue;
                            break;
                    }
                }                   
            }
        } 
        $product['url_src'] = $url;        
        $products[] = $product;
        batch_info($url . ' -> Parse done');
    }
    // end get product detail
}
$offset = 0;
$limit = 30;
batch_info('Total product: ' . count($products));
do {
	batch_info('Updating record ' . $offset . ' - ' . ($offset + $limit - 1));
    $updates = array_slice($products, $offset, $limit);
    if (!empty($updates)) {
        $result = call('/products/updateprice', ['products' => json_encode($updates)]);
        if (is_array($result) && !empty($result)) {
            foreach ($result as $code => $value) {
                batch_info($code . ' : ' . $value);
            }
        }        
    }
	if ($updates == false) {
		batch_info('Update Error');
	}
	$offset += $limit;	
} while(!empty($updates) && $result != false); 
batch_info('Done');
exit;