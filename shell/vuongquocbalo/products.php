<?php

// php products.php 3
// php products.php 2
// php products.php 20
// php products.php 6
// php products.php 5
// php products.php 21

include_once 'base.php';
include_once '../../include/simple_html_dom.php';

$websiteId = 1;
$categoryId = $argv[1];
$fileProductAttr = $categoryId . '_product_attrs.serialize';
$fileProductUrl = $categoryId . '_product_urls.serialize';
$fileProducts = $categoryId . '_products.serialize';
$fileFails = $categoryId . '_products_fails.serialize';

$productList = array(
    // Túi Xách Nữ
    array(
        'disable' => 0,
        'category_id' => 3,
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=166&sortlist=product_new_desc',
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 33,
        'website_discount_percent' => 5,
    ),
    // Ba Lô Nữ
    array(
        'disable' => 0,
        'category_id' => 2,
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=167&sortlist=product_new_desc',
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 30,
        'website_discount_percent' => 5,
    ),
    // Bóp Ví Nữ
    array(
        'disable' => 0,
        'category_id' => 20,
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=56&sortlist=product_new_desc',
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 35,
        'website_discount_percent' => 5,
    ),
    // Túi Xách Nam
    array(
        'disable' => 0,
        'category_id' => 6,
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=164&sortlist=product_new_desc',
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 30,
        'website_discount_percent' => 5,
    ),
    // Ba Lô Nam
    array(
        'disable' => 0,
        'category_id' => 5,
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=165&sortlist=product_new_desc',
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 30,
        'website_discount_percent' => 5,
    ),
    // Bóp Ví Nam
    array(
        'disable' => 0,
        'category_id' => 21,
        'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=35&sortlist=product_new_desc',
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 10,
        'discount_percent' => 35,
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
                            $product['more'] = str_replace(['Zanado.com', 'zanado.com', 'Zanado', 'zanado'], '<a href="http://vuongquocbalo.com">vuongquocbalo.com</a>', $product['more']);
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
            $product['code'] = strtoupper(ltrim($product['code'], 'V'));
            $codeSrc = strtoupper($product['code']);
            $newCode = 'V' . $codeSrc;
            $newName = preg_replace('/V+/', 'V', str_replace($codeSrc, $newCode, $product['name']));
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
