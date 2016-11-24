<?php
// php products.php 23
// php products.php 77
// php products.php 78
// php products.php 64
// php products.php 65

include_once 'base.php'; 
include_once '../../include/simple_html_dom.php';   
$categoryId = $argv[1];
$productList = array(
    // Áo Khoác Nữ
    array(   
        'disable' => 0,
        'category_id' => 23,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=134&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10
    ),
    // Áo Kiểu Nữ
    array(   
        'disable' => 0,
        'category_id' => 65,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=134&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10
    ),
    // Áo Kiểu Nữ
    array(   
        'disable' => 0,
        'category_id' => 65,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=135&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10
    ),
    // Áo Sơ Mi Nữ
    array(   
        'disable' => 0,
        'category_id' => 64,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=74&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10
    ),
    // Áo Thun Nữ
    array(   
        'disable' => 0,
        'category_id' => 64,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=75&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10
    ),
    // Váy Nữ
    array(   
        'disable' => 0,
        'category_id' => 77,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=27&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10
    ),
    // Đầm
    array(   
        'disable' => 0,
        'category_id' => 78,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=28&sortlist=product_new_desc',                               
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
    $item['detail_url'] = array();
    $filename = "detail_url_{$item['category_id']}.php";
    if (file_exists($filename)) {
        $item['detail_url'] = include($filename);
    }
    if (empty($item['detail_url'])) {
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
        $detailUrl = array_map(function ($s) { return "'" . $s . "',"; }, $item['detail_url']);
        app_file_put_contents(
            $filename,
            '<?php ' . PHP_EOL . 'return [' . PHP_EOL . implode(PHP_EOL . PHP_EOL, $detailUrl) . PHP_EOL . '];'
        );
    }
}
unset($item);         
// end get detail url list

$products = array(); 
foreach ($importList as $item) {           
    // get product detail      
    $priority = count($item['detail_url']) + 1;
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
        foreach($html->find('div[class=product-name]') as $element) {                
            if (!empty($element->innertext)) {
                $product['name'] = app_plan_text($element->innertext);
                break;
            }
        }               
        foreach($html->find('div[class=std]') as $element) {                
            if (!empty($element->innertext)) {
                $product['short'] = app_plan_text($element->innertext);
                break;
            }
        } 
        foreach($html->find('div[class=overview]') as $element) {                
            if (!empty($element->innertext)) {
                $product['content'] = $element->innertext;
                $product['content'] = strip_tags($product['content'], '<div></span><ul></li><strong><b>');
                break;
            }
        }
        $product['images'] = array();                 
        foreach($html->find('img[class=img-thumb]') as $element) {  
            if (count($product['images']) >= $item['max_images']) {
                break;
            }
            if (!empty($element->src)) {                        
                $imageUrl = str_replace('360x420', '700x817', app_plan_text($element->src));
                if (empty($product['url_image'])) {
                    $product['url_image'] = $imageUrl;
                }
                if (!in_array($imageUrl, $product['images'])) {
                    $product['images'][] = $imageUrl;
                }
            }
        }  
        $product['import_colors'] = array();
        foreach($html->find('div[class=attributeconf-text attributeconf-color]') as $element) {                
            if (!empty($element->innertext)) {                        
                $subHtml = str_get_html($element->innertext);                 
                foreach($subHtml->find('img') as $element1) {                
                    if (!empty($element1->src)) {
                        $imageUrl = str_replace('360x420', '700x817', app_plan_text($element1->src));
                        $product['import_colors'][] = array(
                            'name' => app_plan_text($element1->title),                                    
                            'url_image' => app_plan_text($imageUrl)
                        );                              
                    }
                }
                break;
            }
        }
        $product['import_sizes'] = array();
        foreach($html->find('label[class=option-size]') as $element) {                
            if (!empty($element->innertext)) {                        
                $product['import_sizes'][] = array(
                    'name' => app_plan_text($element->innertext),
                    'short' => app_plan_text($element->innertext)
                );
            }
        }
        foreach($html->find('span[class=price]') as $element) {                
            if (!empty($element->innertext)) {                
                $product['price_src'] = db_float($element->innertext);
                $product['discount_percent'] = rand(15, 35);
                $product['price'] = round($product['price_src'] - ((10/100) * $product['price_src']) , -3);
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
                        $product['more'] = str_replace(['Zanado.com', 'zanado.com', 'Zanado', 'zanado'], '<a href="http://thoitrang1.vn">thoitrang1.vn</a>', $product['more']);
                        $product['more'] = str_replace(['<p><span></span></p>', '<p></p>', '<p> </p>', '<p><strong><em></em></strong></p>'], '', $product['more']);
                        $product['more'] = str_replace(['<div style="clear:both;margin: 10px auto;width: 80%;border-top: 1px solid #ddd;"></div>'], '<div style="clear:both;margin: 10px auto;width: 100%;border-top: 1px solid #ddd;"></div>', $product['more']);
                        break;
                    }
                }
            }
        }    
       
        $product['import_attributes'] = array(); 
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
                            $product['code'] = $attrValue;
                            break;
                        case 'Thương hiệu':
                            $product['brand_name'] = $attrValue;
                            break;                                   
                        case 'Tình trạng':                              
                            break;                                
                        /*
                        case 'Màu Sắc':                                                               
                        case 'Chất liệu':                             
                        case 'Kiểu dáng':                            
                        case 'Mục đích SD':                              
                        case 'Mùa phù hợp':  
                         * 
                         */                        
                        default:                                    
                            $product['import_attributes'][] = array(
                                'name' => $attrName,
                                'value' => $attrValue,
                            );
                            break;
                    }
                }                       
            }
        }        
        $product['code'] = strtoupper(ltrim($product['code'], 'S'));
        $codeSrc = strtoupper($product['code']);               
        $newCode = 'S' . $codeSrc;               
        $newName = preg_replace('/S+/', 'S', str_replace($codeSrc, $newCode, $product['name']));
        $product['code_src'] = $codeSrc;  
        $product['url_src'] = $url; 
        $product['code'] = $newCode;        
        $product['name'] = $newName;
        $product['priority'] = $priority;        
        $product['images'] = serialize($product['images']);        
        $product['import_attributes'] = serialize($product['import_attributes']);        
        $product['import_colors'] = serialize($product['import_colors']);        
        $product['import_sizes'] = serialize($product['import_sizes']);        
        $products[] = $product;
        $priority--;
        batch_info($url . ' -> Parse done');  
		//break;
    }
    // end get product detail
}

batch_info('Total product: ' . count($products));
$count = 1;
foreach ($products as $product) { 
    $product['website_id'] = 2;
    $product['add_image_to_content'] = 1;
    $_id = call('/products/add', $product);
    if ($_id) {  
        batch_info ('[' . $count . '] ' . $product['code'] . ' Done');
    } else {
        batch_info('[' . $count . '] ' . $product['code'] . ' Failed');
    }
    $count++;
}
batch_info('Done');
exit;