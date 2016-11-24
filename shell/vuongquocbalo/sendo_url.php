<?php
// php /home/vuong761/public_html/shell/vuongquocbalo/sendo_url.php
// php sendo_url.php

include_once 'base.php';
include_once '../../include/simple_html_dom.php';

$shopName = 'zanadofashion'; // vuongquocbalo, shophocsinh, zanadofashion
$shop = [
    'vuongquocbalo' => [                
        'url' => 'https://www.sendo.vn/shop/vuongquocbalo/san-pham/',
        //'url' => 'https://www.sendo.vn/shop/vuongquocbalo/tui-xach/balo/balo-nu/',
        'url_field_1' => 'url_sendo1',
        'url_field_2' => 'url_sendo2',
    ],
    'shophocsinh' => [                
        'url' => 'https://www.sendo.vn/shop/shophocsinh/san-pham/',
        'url_field_1' => 'url_sendo3',
        'url_field_2' => 'url_sendo4',
    ],
    'zanadofashion' => [                
        'url' => 'https://www.sendo.vn/shop/zanadofashion/san-pham/',
        'url_field_1' => 'url_sendo5',
        'url_field_2' => 'url_sendo6',
    ],    
];
$url = $shop[$shopName]['url'];
$urlField1 = $shop[$shopName]['url_field_1'];
$urlField2 = $shop[$shopName]['url_field_2'];
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
        batch_info("Total record: {$totalRecord}");
	}
	foreach ($html->find('a[class=name_product shop_color_hover]') as $element) {
		if (!empty($element->href)) {
			$detailUrl = trim($element->href);
            $name = trim($element->innertext);
            $code = end(explode(' ', $name));
            $code = strtoupper(trim($code));   
            if (strpos($code, 'VGO') !== false && strpos($code, 'VGO') === 0) {
                continue;
            }
            if ((strpos($code, 'SBL') !== false && strpos($code, 'SBL') === 0)
                || (strpos($name, 'Balo và túi đeo chéo 2 trong 1') !== false)
                || (strpos($name, 'Balo & túi đeo chéo 2 trong 1') !== false)
            ) {                
                $code = str_replace('SBL', 'VBL', $code);              
                $result = call('/products/update', ['code' => $code, $urlField2 => $detailUrl], $errors);
            } else {
                $result = call('/products/update', ['code' => $code, $urlField1 => $detailUrl], $errors);
            }
            if (!empty($errors[0]['message'])) {                
                batch_info("[$count] - {$detailUrl} -> {$errors[0]['message']}");
            } else {
                batch_info("[$count] - {$detailUrl} -> OK");                
            }            
            $count++;
		}
	}
	$page++;
    if ($page > 100) {
        break;
    }
} while($page <= $totalPage);
batch_info('Done');
exit;