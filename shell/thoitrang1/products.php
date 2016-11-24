<?php
// php /home/vuong761/public_html/shell/thoitrang1/products.php
// Áo Khoác Nữ: php products.php 51
// Áo Sơ Mi Nữ: php products.php 52
// Áo Thun Nữ: php products.php 53
// Áo Kiểu Nữ: php products.php 54
// Quần Short Nữ: php products.php 56
// Quần Lửng Nữ: php products.php 57
// Quần Dài Nữ: php products.php 58
// Đồ Mặc Nhà - Đồ Ngủ Nữ: php products.php 60
// Jumpsuit Nữ: php products.php 61
// Set Đồ Nữ: php products.php 62
// Đồ Lót Nữ: php products.php 65
// Đồ Bơi - Đồ Tắm Nữ: php products.php 66
// Đồ Chống Nắng Nữ: php products.php 67
// Váy: php products.php 69
// Đầm: php products.php 70
// Áo Khoác Nam: php products.php 76
// Áo Sơ Mi Nam: php products.php 77
// Áo Thun Nam: php products.php 78
// Quần Short - Quần Lửng Nam: php products.php 80
// Quần Jeans Nam: php products.php 81
// Quần Kiểu Nam: php products.php 82
// Đồ Lót Nam: php products.php 85
// Đồ Bơi - Đồ Tắm Nam: php products.php 86
// Đồ Thể Thao Nam: php products.php 87
// Đồ Thể Thao Nữ: php products.php 63

include_once 'base.php';
include_once '../../include/simple_html_dom.php';

$websiteId = 2;
$categoryId = $argv[1];
$fileProductAttr = $categoryId . '_product_attrs.serialize';
$fileProductUrl = $categoryId . '_product_urls.serialize';
$fileProducts = $categoryId . '_products.serialize';
$fileFails = $categoryId . '_products_fails.serialize';

$productList = array(
    // Áo Khoác Nữ
    array(   
        'disable' => 0,
        'category_id' => 51,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=134&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 35,
        'website_discount_percent' => 5,
    ),
    // Áo Sơ Mi Nữ
    array(   
        'disable' => 0,
        'category_id' => 52,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=74&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 35,
        'website_discount_percent' => 5,
    ),
    // Áo Thun Nữ
    array(   
        'disable' => 0,
        'category_id' => 53,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=75&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 35,
        'website_discount_percent' => 5,
    ),
    // Áo Kiểu Nữ
    array(   
        'disable' => 0,
        'category_id' => 54,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=135&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 35,
        'website_discount_percent' => 5,
    ),
    
    // Quần Short Nữ
    array(   
        'disable' => 0,
        'category_id' => 56,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=71&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 30,
        'website_discount_percent' => 5,
    ),    
    // Quần Lửng Nữ
    array(   
        'disable' => 0,
        'category_id' => 57,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=136&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 30,
        'website_discount_percent' => 5,
    ),
    // Quần Dài Nữ
    array(   
        'disable' => 0,
        'category_id' => 58,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=148&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 30,
        'website_discount_percent' => 5,
    ),
    
    // Đồ Mặc Nhà - Đồ Ngủ Nữ
    array(   
        'disable' => 0,
        'category_id' => 60,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=142&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 28,
        'website_discount_percent' => 5,
    ),
    // Jumpsuit Nữ
    array(   
        'disable' => 0,
        'category_id' => 61,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=143&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 32,
        'website_discount_percent' => 5,
    ),
    // Set Đồ Nữ
    array(   
        'disable' => 0,
        'category_id' => 62,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=238&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 32,
        'website_discount_percent' => 5,
    ),
    
    // Đồ Lót Nữ
    array(   
        'disable' => 0,
        'category_id' => 65,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=163&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 33,
        'website_discount_percent' => 5,
    ),    
    
    // Đồ Bơi - Đồ Tắm Nữ
    array(   
        'disable' => 0,
        'category_id' => 66,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=105&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 33,
        'website_discount_percent' => 5,
    ),
    // Đồ Chống Nắng Nữ
    array(   
        'disable' => 0,
        'category_id' => 67,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=184&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 33,
        'website_discount_percent' => 5,
    ),
    
    // Váy
    array(   
        'disable' => 0,
        'category_id' => 69,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=27&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 34,
        'website_discount_percent' => 5,
    ),
    // Đầm
    array(   
        'disable' => 0,
        'category_id' => 70,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=28&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 34,
        'website_discount_percent' => 5,
    ),
    /*******************/
    // Áo Khoác Nam
    array(   
        'disable' => 0,
        'category_id' => 76,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=51&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 30,
        'website_discount_percent' => 5,
    ),
    // Áo Sơ Mi Nam
    array(   
        'disable' => 0,
        'category_id' => 77,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=49&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 30,
        'website_discount_percent' => 5,
    ),
    // Áo Thun Nam
    array(   
        'disable' => 0,
        'category_id' => 78,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=50&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 30,
        'website_discount_percent' => 5,
    ),
    
    // Quần Short - Quần Lửng Nam
    array(   
        'disable' => 0,
        'category_id' => 80,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=41&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 35,
        'website_discount_percent' => 5,
    ),    
    // Quần Jeans Nam
    array(   
        'disable' => 0,
        'category_id' => 81,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=247&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 35,
        'website_discount_percent' => 5,
    ),
    // Quần Kiểu Nam
    array(   
        'disable' => 0,
        'category_id' => 82,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=248&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 35,
        'website_discount_percent' => 5,
    ),
    
    // Đồ Lót Nam
    array(   
        'disable' => 0,
        'category_id' => 85,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=162&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 29,
        'website_discount_percent' => 5,
    ),
    // Đồ Bơi - Đồ Tắm Nam
    array(   
        'disable' => 0,
        'category_id' => 86,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=104&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 29,
        'website_discount_percent' => 5,
    ),
    
    // Đồ Thể Thao Nữ
    array(   
        'disable' => 0,
        'category_id' => 63,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=141&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 32,
        'website_discount_percent' => 5,
    ),
    
    // Đồ Thể Thao Nam
    array(   
        'disable' => 0,
        'category_id' => 87,                         
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=24&sortlist=product_new_desc',                               
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 32,
        'website_discount_percent' => 5,
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
batch_info('BEGIN: Get Detail Url');
if (file_exists($fileProductUrl)) {
    $importList = unserialize(app_file_get_contents($fileProductUrl));
} else {
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
            foreach ($html->find('a[class=product-image]') as $element) {
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
    app_file_put_contents($fileProductUrl, serialize($importList));
    batch_info('Parse detail url -> Done');
}
batch_info('END: Get Detail Url');
// end get detail url list

batch_info('BEGIN: Parse Product Detail');
if (file_exists($fileProducts)) {
    $products = unserialize(app_file_get_contents($fileProducts));
} else {
    $products = array();
    foreach ($importList as $item) {
        if (!isset($item['website_discount_percent'])) {
            $item['website_discount_percent'] = 5;
        }
        // get product detail 
        foreach ($item['detail_url'] as $url) {
            $product = array(
                'category_id' => $item['category_id'],
                'discount_percent' => $item['discount_percent'],
            );
            $content = app_file_get_contents($url);
            if ($content == false) {
                batch_info($url . ' Failed');
                continue;
            }
            $content = strip_tags_content($content, '<script><style>', true);
            $html = str_get_html($content);
            foreach ($html->find('div[class=product-name]') as $element) {
                if (!empty($element->innertext)) {
                    $product['name'] = app_plan_text($element->innertext);
                    break;
                }
            }
            foreach ($html->find('div[class=std]') as $element) {
                if (!empty($element->innertext)) {
                    $product['short'] = app_plan_text($element->innertext);
                    break;
                }
            }
            foreach ($html->find('div[class=overview]') as $element) {
                if (!empty($element->innertext)) {
                    $product['content'] = $element->innertext;
                    $product['content'] = strip_tags($product['content'], '<div></span><ul></li><strong><b>');
                    break;
                }
            }
            $product['images'] = array();
            foreach ($html->find('img[class=img-thumb]') as $element) {
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
            foreach ($html->find('div[class=attributeconf-text attributeconf-color]') as $element) {
                if (!empty($element->innertext)) {
                    $subHtml = str_get_html($element->innertext);
                    foreach ($subHtml->find('img') as $element1) {
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
            foreach ($html->find('label[class=option-size]') as $element) {
                if (!empty($element->innertext)) {
                    $product['import_sizes'][] = array(
                        'name' => app_plan_text($element->innertext),
                        'short' => app_plan_text($element->innertext)
                    );
                }
            }
            foreach ($html->find('span[class=price]') as $element) {
                if (!empty($element->innertext)) {
                    $product['price_src'] = db_float($element->innertext);
                    $product['price'] = round($product['price_src'] - (($item['website_discount_percent'] / 100) * $product['price_src']), -3);
                    $product['original_price'] = round($product['price'] + (($product['discount_percent'] / 100) * $product['price']), -3);
                    break;
                }
            }
            foreach ($html->find('div[class=box-collateral box-despr]') as $element) {
                if (!empty($element->innertext)) {
                    $subHtml = str_get_html($element->innertext);
                    foreach ($subHtml->find('div[class=block-primg]') as $element1) {
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
            foreach ($html->find('div[class=product-attribute]') as $element) {
                if (!empty($element->innertext)) {
                    $subHtml = str_get_html($element->innertext);
                    foreach ($subHtml->find('div[class=attribute-title]') as $element1) {
                        if (!empty($element1->innertext)) {
                            $attrName = app_plan_text($element1->innertext);
                            break;
                        }
                    }
                    foreach ($subHtml->find('div[class=attribute-text]') as $element1) {
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
                                $product['import_attributes'][] = [
                                    'name' => $attrName,
                                    'value' => $attrValue,
                                ];
                                break;
                        }
                    }
                }
            }
            $product['code'] = strtoupper(ltrim($product['code'], 'T'));
            $codeSrc = strtoupper($product['code']);
            $newCode = 'T' . $codeSrc;
            $newName = preg_replace('/T+/', 'T', str_replace($codeSrc, $newCode, $product['name']));
            $product['code_src'] = $codeSrc;
            $product['url_src'] = $url;
            $product['code'] = $newCode;
            $product['name'] = $newName;
            $product['images'] = serialize($product['images']);
            $product['import_attributes'] = serialize($product['import_attributes']);
            $product['import_colors'] = serialize($product['import_colors']);
            $product['import_sizes'] = serialize($product['import_sizes']);
            $products[] = $product;
            batch_info($url . ' -> Parse done');
            //break;
        }
        // end get product detail
    }
    app_file_put_contents($fileProducts, serialize($products));
}
batch_info('END: Parse Product Detail');

if (file_exists($fileFails)) {
    $products = unserialize(app_file_get_contents($fileFails));
}
batch_info('BEGIN: Import Product');
batch_info('Total product: ' . count($products));
$count = 1;
$priority = count($products);
$fails = [];
foreach ($products as $product) {   
    $product['website_id'] = $websiteId;
    $product['priority'] = $priority;
    $product['add_image_to_content'] = 1;                    
    $_id = call('/products/add', $product, $errors);
    if ($_id) {
        batch_info('[' . $count . '] ' . $product['code'] . ' Done');
    } else {
        batch_info('[' . $count . '] ' . $product['code'] . ' Failed');
        batch_info($product['url_src']);
        $fails[] = $product;
    }
    $priority--;
    $count++;
}
app_file_put_contents($fileFails, serialize($fails));
batch_info('END: Import Product');
batch_info('Done');
exit;
