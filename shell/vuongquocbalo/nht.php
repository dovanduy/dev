<?php
// php /home/vuong761/public_html/shell/vuongquocbalo/nht.php
// php nht.php 15
// php nht.php 16
// php nht.php 99
// php nht.php 101
// php nht.php 8

include_once 'base.php';
include_once '../../include/simple_html_dom.php';

function importAttrs($import = false, &$optionIds = []) {
    include_once '../../include/simple_html_dom.php';
    global $websiteId;
    $getAttrUrl = 'http://nguonhangtot.com/collections/balo-in';
    $content = app_file_get_contents($getAttrUrl);
    if ($content == false) {
        echo $getAttrUrl . ' Failed' . PHP_EOL;
        exit;
    }
    $content = strip_tags_content($content, '<script><style>', true);
    $html = str_get_html($content);
    $attrs = array(
        'Màu sắc' => [],
        'Phong cách' => [],
    );
    foreach ($html->find('ul[id=filter_group_3]') as $element) {
        $content = strip_tags_content($element->innertext, '<script><style>', true);
        $html1 = str_get_html($content);
        foreach ($html1->find('span[class=color_block]') as $element1) {
            $name = app_plan_text($element1->title);
            if ($name != 'Tất cả' && !in_array($name, $attrs['Màu sắc'])) {
                $attrs['Màu sắc'][] = $name;
            }
        }
    }
    foreach ($html->find('ul[id=filter_group_5]') as $element) {
        $content = strip_tags_content($element->innertext, '<script><style>', true);
        $html1 = str_get_html($content);
        foreach ($html1->find('label[class=filter_checkbox]') as $element1) {
            $name = app_plan_text($element1->innertext);
            if ($name != 'Tất cả' && !in_array($name, $attrs['Phong cách'])) {
                $attrs['Phong cách'][] = $name;
            }
        }
    }
    if ($import == true) {
        $fields = array(
            array(
                'name' => 'Màu sắc',
                'type' => 'text',
                'input_options' => array()
            ),
            array(
                'name' => 'Phong cách',
                'type' => 'radio',
                'input_options' => array()
            ),
        );
        foreach ($fields as $field) {
            $param = array(
                'website_id' => $websiteId,
                'name' => $field['name'],
                'type' => $field['type'],
                'input_options' => serialize($field['input_options']),
                'return_id' => 1,
            );
            $fieldId = call('/inputfields/add', $param);
            /*
            if (!empty($fieldId) && $field['name'] == 'Màu sắc' && empty($field['input_options'])) {
                foreach ($attrs['Màu sắc'] as $attrName) {
                    $param = array(
                        'website_id' => $websiteId,
                        'field_id' => $fieldId,
                        'name' => $attrName,
                        'return_id' => 1,
                    );
                    $optionId = call('/inputoptions/add', $param);
                    if (!empty($optionId)) {
                        $optionIds['Màu sắc'][$attrName] = $optionId;
                    }
                }
            }
             * 
             */
            if (!empty($fieldId) && $field['name'] == 'Phong cách' && empty($field['input_options'])) {
                foreach ($attrs['Phong cách'] as $attrName) {
                    $param = array(
                        'website_id' => $websiteId,
                        'field_id' => $fieldId,
                        'name' => $attrName,
                        'return_id' => 1,
                    );
                    $optionId = call('/inputoptions/add', $param);
                    if (!empty($optionId)) {
                        $optionIds['Phong cách'][$attrName] = $optionId;
                    }
                }
            }           
        }
    }
    return $attrs;
}

$websiteId = 1;
$categoryId = $argv[1];

$fileProductAttr = $categoryId . '_product_attrs.serialize';
$fileProductUrl = $categoryId . '_product_urls.serialize';
$fileProducts = $categoryId . '_products.serialize';
$fileFails = $categoryId . '_products_fails.serialize';

$domain = 'http://nguonhangtot.com';
$productList = array(
    array(
        'disable' => 0,
        'category_id' => 15,
        'url' => 'http://nguonhangtot.com/collections/balo-in',
        'id' => '1000151735',
        'short' => implode(PHP_EOL, [
            "Chất liệu: simili giả da chống thấm tốt",
            "Dây đeo tháo rời",
            "Công nghệ in Nhật Bản cho hình in đẹp"
        ]),
        'content' => implode(PHP_EOL, [
            "<p>- Ba lô size lớn có kích thước ngang 32 x cao 41.5 x rộng 14.5 (cm), có 2 ngăn để vừa laptop 14\", có chổ để bình nước</p>",
            "<p>- Ba lô size nhỏ có kích thước ngang 26 x cao 32 x rộng 9 (cm), để vừa giấy A4, Laptop 12\" hoặc máy tính bảng</p>",
            "<p>- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.</p>",
            "<p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng</p>"
        ]),
        'detail_url' => array(),
        'size_id' => array(1, 2),
        'max_images' => 4,
        'price_src' => '175000',
        'original_price' => '175000',
        'price' => '159000',
        'discount_percent' => '9',
        'default_size_id' => '2',
    ),
    array(
        'disable' => 0,
        'category_id' => 16,
        'url' => 'http://nguonhangtot.com/collections/balo-day-rut',
        'id' => '1000469360',
        'short' => implode(PHP_EOL, [
            "Chất liệu simili 100%, không thấm nước, không bong tróc.",
            "Hàng Việt Nam xuất khẩu, chất lượng đảm bảo.",
            "Công nghệ in Nhật Bản cho hình in đẹp."
        ]),
        'content' => implode(PHP_EOL, [
            "<p>- Kích thước 29 x 40 (cm)</p>",
            "<p>- Ba lô dây rút hàng Việt Nam xuất khẩu, chất lượng đảm bảo</p>",
            "<p>- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.</p>",
            "<p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng</p>"
        ]),
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 4,
        'price_src' => '99000',
        'original_price' => '99000',
        'price' => '69000',
        'discount_percent' => '30',
        'default_size_id' => '0',
    ),
    array(
        'disable' => 0,
        'category_id' => 99,
        'url' => 'http://nguonhangtot.com/collections/tui-cheo-nu',
        'id' => '1000469175',
        'short' => implode(PHP_EOL, [            
            "Chất liệu simili 100%, không thấm nước, không bong tróc",
            "Hàng Việt Nam xuất khẩu, chất lượng đảm bảo",
            "Công nghệ in Nhật Bản cho hình in đẹp"
        ]),
        'content' => implode(PHP_EOL, [
            "<p>- <strong>Kích thước ngang 24 x 17 (cm)</strong></p>",
            "<p>- Phù hợp đựng tiền, điện thoại, máy tính bảng nhỏ, sổ tay, đồ trang điểm, các vật dụng cá nhân cho nữ, ...</p>",
            "<p>- <strong>Hàng Việt Nam xuất khẩu</strong>, chất lượng đảm bảo.</p>",  
            "<p>- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.</p>",               
            "<p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.</p>",
        ]),
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 2,
        'price_src' => '80000',
        'original_price' => '80000',
        'price' => '80000',
        'discount_percent' => '0',
        'default_size_id' => '0'
    ),
    array(
        'disable' => 0,
        'category_id' => 8,
        'url' => 'http://nguonhangtot.com/collections/tui-cheo-hop',
        'id' => '1000469402',
        'short' => implode(PHP_EOL, [
            "Thiết kế tiện dụng.",
            "Chất liệu simili 100%, không thấm nước, không bong tróc.",
            "Hàng Việt Nam xuất khẩu, chất lượng đảm bảo.",
            "Công nghệ in Nhật Bản cho hình in đẹp."
        ]),
        'content' => implode(PHP_EOL, [
            "<p>- <strong>Túi chéo nhỏ kích thước ngang 34 x cao 25 x rộng 9 (cm)</strong></p>",
            "<p>- Sử dụng đựng tập vở, tài liệu, giấy A4, Laptop 12\" hoặc máy tính bảng</p>", 
            "<p>- Có 1 ngăn lớn, phù hợp đi học thêm, đi làm, đi chơi.</p>",
            "",
            "<p>- <strong>Túi chéo lớn kích thước ngang 39 x cao 30 x rộng 13 (cm)</strong></p>",
            "<p>- Sử dụng đựng tập vở, tài liệu, giấy A4, Laptop 14\" hoặc máy tính bảng.</p>",
            "<p>- Có 1 ngăn lớn và 1 ngăn riêng đựng Laptop riêng biệt, có chổ để bình nước, phù hợp đi học thêm, đi làm, đi chơi.</p>",
            "",
            "<p>- <strong>Hàng Việt Nam xuất khẩu</strong>, chất lượng đảm bảo.</p>",  
            "<p>- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.</p>",               
            "<p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.</p>",
        ]),
        'detail_url' => array(),
        'size_id' => array(1, 2),
        'max_images' => 4,
        'price_src' => '185000',
        'original_price' => '185000',
        'price' => '185000',
        'discount_percent' => '0',
        'default_size_id' => '1'
    ),
    array(
        'disable' => 0,
        'category_id' => 102,
        'url' => 'http://nguonhangtot.com/collections/goi-tron',
        'id' => '1000469343',
        'short' => implode(PHP_EOL, [
            "Kích thước gối ngang 38 x cao 7 x rộng 38 (cm)",
            "Chất liệu vải nhung mịn, ruột gối bên trong là gòn bi không xẹp, in hình độc đáo",
            "Công nghệ in Nhật Bản, bền màu trong thời gian dài sử dụng"
        ]),
        'content' => implode(PHP_EOL, [
            "<p>- Chiếc gối là một vật dụng thân thiết gắn liền với cuộc sống hằng ngày của chúng ta. Ngày nay kiểu dáng của những chiếc gối đã không còn nằm trong khuôn khổ cũ. Mẫu mã được thanh đổi liên tục và phù hợp với trào lưu. Gối in hình vừa có thể dùng vừa làm quà tặng tới bạn bè, người thân đều được!</p>",
            "<p>- Giấc ngủ của bạn sẽ trở nên êm đềm hơn khi trên chiếc gối in hình ảnh mà bạn yêu thích. Hình những con vật dễ thương, những nhân vật hoạt hình mà bạn yêu thích, ca sĩ nhóm nhạc mà bạn thần tượng….. tất cả đều có thể in lên chiếc gối quen thuộc của bạn.</p>",
            "",
            "<p>- <strong>Kích thước gối ngang 38 x cao 7 x rộng 38 (cm)</strong></p>",
            "<p>- Chất liệu vải nhung mịn, ruột gối bên trong là gòn bi không xẹp, in hình độc đáo</p>",             
            "<p>- Công nghệ in Nhật Bản, bền màu trong thời gian dài sử dụng.</p>",
        ]),
        'detail_url' => array(),
        'size_id' => array(),
        'max_images' => 4,
        'price_src' => '150000',
        'original_price' => '150000',
        'price' => '150000',
        'discount_percent' => '0',
        'default_size_id' => '0'
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

$attrs = importAttrs(true, $optionIds);

batch_info('BEGIN: Get attributes');
if (file_exists($fileProductAttr)) {
    $productAttrs = unserialize(app_file_get_contents($fileProductAttr));    
} else {
    $productAttrs = [
        'Màu sắc' => [],
        'Phong cách' => [],
    ];
    foreach ($importList as &$item) {
        foreach ($attrs['Phong cách'] as $attrName) {
            $url = urlencode("http://nguonhangtot.com/search?q=filter=((collectionid:product={$item['id']})&&((tag:product=phongcach_" . urlencode($attrName) . ")))&sortby=manual");
            $url = ("http://nguonhangtot.com/search?q=filter=((collectionid:product={$item['id']})&&((tag:product=phongcach_" . urlencode($attrName) . ")))");
            //echo $url; exit;
            $content = app_file_get_contents($url);
            $content = strip_tags_content($content, '<script><style>', true);
            $html = str_get_html($content);
            $totalRecord = 0;
            foreach ($html->find('p[class=subtext nomargin text-right]') as $element) {
                $spanHtml = str_get_html($element->innertext);
                foreach ($spanHtml->find('span') as $spanElement) {
                    $totalRecord = db_int($spanElement->innertext);
                    break;
                }
            }
            $totalPage = ceil($totalRecord / 20);
            if ($totalPage > 1) {
                for ($page = 1; $page <= $totalPage; $page++) {
                    $content = app_file_get_contents($url . '&page=' . $page);
                    if ($content == false) {
                        batch_info($url . ' Failed');
                        continue;
                    }
                    $subHtml = str_get_html($content);
                    foreach ($subHtml->find('div[class=product-title]') as $element) {
                        $aHtml = str_get_html($element->innertext);
                        foreach ($aHtml->find('a') as $aElement) {
                            if (!empty($aElement->href)) {
                                $productAttrs['Phong cách'][$domain . app_plan_text($aElement->href)][] = $attrName;
                                break;
                            }
                        }
                    }
                }
            } else {
                foreach ($html->find('div[class=product-title]') as $element) {
                    $aHtml = str_get_html($element->innertext);
                    foreach ($aHtml->find('a') as $aElement) {
                        if (!empty($aElement->href)) {
                            $productAttrs['Phong cách'][$domain . app_plan_text($aElement->href)][] = $attrName;
                            break;
                        }
                    }
                }
                batch_info($url . ' Done');
            }        
        }

        foreach ($attrs['Màu sắc'] as $attrName) {
            $url = "http://nguonhangtot.com/search?q=filter=((collectionid:product={$item['id']})&&((tag:product=mausac_" . urlencode($attrName) . ")))&sortby=manual";
            $content = app_file_get_contents($url);
            $content = strip_tags_content($content, '<script><style>', true);
            $html = str_get_html($content);
            $totalRecord = 0;
            foreach ($html->find('p[class=subtext nomargin text-right]') as $element) {
                $spanHtml = str_get_html($element->innertext);
                foreach ($spanHtml->find('span') as $spanElement) {
                    $totalRecord = db_int($spanElement->innertext);
                    break;
                }
            }
            $totalPage = ceil($totalRecord / 20);
            if ($totalPage > 1) {
                for ($page = 1; $page <= $totalPage; $page++) {
                    $content = app_file_get_contents($url . '&page=' . $page);
                    if ($content == false) {
                        batch_info($url . ' Failed');
                        continue;
                    }
                    $subHtml = str_get_html($content);
                    foreach ($subHtml->find('div[class=product-title]') as $element) {
                        $aHtml = str_get_html($element->innertext);
                        foreach ($aHtml->find('a') as $aElement) {
                            if (!empty($aElement->href)) {
                                $productAttrs['Màu sắc'][$domain . app_plan_text($aElement->href)][] = $attrName;
                                break;
                            }
                        }
                    }
                }
            } else {
                foreach ($html->find('div[class=product-title]') as $element) {
                    $aHtml = str_get_html($element->innertext);
                    foreach ($aHtml->find('a') as $aElement) {
                        if (!empty($aElement->href)) {
                            $productAttrs['Màu sắc'][$domain . app_plan_text($aElement->href)][] = $attrName;
                            break;
                        }
                    }
                }
                batch_info($url . ' Done');
            }        
        }   
    }
    app_file_put_contents($fileProductAttr, serialize($productAttrs));  
}
batch_info('END: Get attributes');

// end get product attr list
// get detail url list
batch_info('BEGIN: Get Detail Url');
if (file_exists($fileProductUrl)) {
    $importList = unserialize(app_file_get_contents($fileProductUrl));    
} else {
    foreach ($importList as &$item) {
        $content = app_file_get_contents($item['url']);
        if ($content == false) {
            echo $item['url'] . ' Failed' . PHP_EOL;
            continue;
        }
        $content = strip_tags_content($content, '<script><style>', true);
        $html = str_get_html($content);
        $totalRecord = 0;
        foreach ($html->find('p[class=subtext nomargin text-right]') as $element) {
            $spanHtml = str_get_html($element->innertext);
            foreach ($spanHtml->find('span') as $spanElement) {
                $totalRecord = db_int($spanElement->innertext);
                break;
            }
        }
        $totalPage = ceil($totalRecord / 20);
        if ($totalPage > 1) {
            for ($page = 1; $page <= $totalPage; $page++) {
                $content = app_file_get_contents($item['url'] . '?page=' . $page);
                if ($content == false) {
                    batch_info($item['url'] . ' Failed');
                    continue;
                }
                $subHtml = str_get_html($content);
                foreach ($subHtml->find('div[class=product-title]') as $element) {
                    $aHtml = str_get_html($element->innertext);
                    foreach ($aHtml->find('a') as $aElement) {
                        if (!empty($aElement->href)) {
                            $item['detail_url'][] = $domain . app_plan_text($aElement->href);
                            break;
                        }
                    }
                }
                batch_info($item['url'] . '?page=' . $page . ' Done'); 
            }
        } else {
            foreach ($html->find('div[class=product-title]') as $element) {
                $aHtml = str_get_html($element->innertext);
                foreach ($aHtml->find('a') as $aElement) {
                    if (!empty($aElement->href)) {
                        $item['detail_url'][] = $domain . app_plan_text($aElement->href);
                        break;
                    }
                }
            }
            batch_info($item['url'] . ' Done');
        }
    }
    unset($item);
    app_file_put_contents($fileProductUrl, serialize($importList));    
}
batch_info('END: Get Detail Url');
// end get detail url list

batch_info('BEGIN: Parse Product Detail');
if (file_exists($fileProducts)) {
    $products = unserialize(app_file_get_contents($fileProducts));  
} else {
    $products = array();
    foreach ($importList as $item) {
        $product = array(
            'website_id' => $websiteId,
            'category_id' => $item['category_id'],
            'short' => $item['short'],
            'content' => $item['content'],
            'price_src' => $item['price_src'],
            'original_price' => $item['original_price'],
            'price' => $item['price'],
            'discount_percent' => $item['discount_percent'],
            'default_size_id' => $item['default_size_id'],
            'size_id' => $item['size_id'],
            'import_attributes' => [],
            'import_prices' => [],
        );
                
        // get product detail    
        foreach ($item['detail_url'] as $url) {
            $product['import_attributes'] = [];
            $product['url_src'] = $url;
            $content = app_file_get_contents($url);
            if ($content == false) {
                batch_info($url . ' Failed');
                continue;
            }
            $content = strip_tags_content($content, '<script><style>', true);
            $html = str_get_html($content);
            foreach ($html->find('div[class=page_title]') as $element) {
                if (!empty($element->innertext)) {
                    $product['name'] = app_plan_text($element->innertext);
                    break;
                }
            }
            foreach ($html->find('span[class=sku_code]') as $element) {
                if (!empty($element->innertext)) {
                    $product['code_src'] = app_plan_text($element->innertext);
                    $product['code'] = 'V' . $product['code_src'];
                    $product['name'] = str_replace($product['code_src'], $product['code'], $product['name']);
                }
            }
            $product['images'] = array();
            foreach ($html->find('div[class=product-image product-main-image]') as $element) {
                if (!empty($element->innertext)) {
                    $subHtml = str_get_html($element->innertext);
                    foreach ($subHtml->find('div[class=pd_slide nopadding]') as $element1) {
                        if (!empty($element1->innertext)) {
                            $subHtml2 = str_get_html($element1->innertext);
                            foreach ($subHtml2->find('img') as $element2) {
                                if (!empty($element2->src)) {
                                    $imageUrl = 'http:' . app_plan_text($element2->src);
                                    if (empty($product['images'])) {
                                        $product['url_image'] = $imageUrl;
                                    }
                                    $product['images'][] = $imageUrl;
                                    if (count($product['images']) >= $item['max_images']) {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (isset($productAttrs['Màu sắc'][$url])) {
                $product['import_attributes'][] = [
                    'name' => 'Màu sắc',
                    'value' => implode(', ', $productAttrs['Màu sắc'][$url])
                ];
            }
            if (isset($productAttrs['Phong cách'][$url])) {
                foreach ($productAttrs['Phong cách'][$url] as $attrName) {
                    $product['import_attributes'][] = [
                        'name' => 'Phong cách',
                        'value' => $attrName,
                        'option_id' => isset($optionIds['Phong cách'][$attrName]) ? $optionIds['Phong cách'][$attrName] : 0
                    ];            
                }       
            }   
            if ($categoryId == 15) { // Balo
                $product['import_prices'] = [
                    ['color_id' => 0, 'size_id' => 1, 'price' => '205000'],
                    ['color_id' => 0, 'size_id' => 2, 'price' => '175000'],
                ];
            } else if ($categoryId == 8) { // Tui cheo hop
                $product['import_prices'] = [
                    ['color_id' => 0, 'size_id' => 1, 'price' => '185000'],
                    ['color_id' => 0, 'size_id' => 2, 'price' => '130000'],
                ];
            }
            $products[] = $product;
            batch_info($url . ' Parse Done');
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

foreach ($products as &$product) {    
    $product['website_id'] = $websiteId;
    $product['priority'] = $priority;
    $product['add_image_to_content'] = 1;
    if (isset($product['images'])) {
        $product['images'] = serialize($product['images']);
    }
    if (isset($product['size_id'])) {
        $product['size_id'] = serialize($product['size_id']);
    }
    if (isset($product['import_attributes'])) {
        $product['import_attributes'] = serialize($product['import_attributes']);
    }
    if (isset($product['import_prices'])) {
        $product['import_prices'] = serialize($product['import_prices']);
    }
    //if (empty($product['original_price'])) {
        switch ($categoryId) {
            case 15:
                $product['original_price'] = 175000;
                break;
            case 16:
                $product['original_price'] = 99000;
                break;
            case 99: // Tui cheo mini
                $product['name'] = str_replace(['Túi nữ', 'TC '], ['Túi chéo nữ ', 'Túi chéo '], $product['name']);
                $product['original_price'] = 80000;
                break;
            case 8: // Tui cheo hop
                $product['name'] = str_replace(['Túi nữ', 'TC '], ['Túi chéo nữ ', 'Túi chéo '], $product['name']);
                $product['original_price'] = 185000;
                break;
        }        
    //}
    if (empty($product['default_size_id']) && $categoryId == 15) {
        $product['default_size_id'] = 2;
    }
    if (empty($product['url_src'])) {
        foreach ($importList[0]['detail_url'] as $url) {
            if (strpos(strtolower($url), '-' . strtolower($product['code_src']))) {
                $product['url_src'] = $url;
                break;
            }
        }
    }    
    $_id = call('/products/add', $product);
    
    if ($_id) {  
        batch_info ('[' . $count . '] ' . $product['code'] . ' Done');
    } else {
        batch_info('[' . $count . '] ' . $product['code'] . ' Failed');
        batch_info($product['url_src']);
        $fails[] = $product;
    }
    $count++; 
    $priority--;
}
unset($product);
app_file_put_contents($fileFails, serialize($fails));
batch_info('END: Import Product');
batch_info('Done');
exit;