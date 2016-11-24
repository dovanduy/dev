<?php
// php /home/vuong761/public_html/shell/vuongquocbalo/lazada_url.php
// php lazada_url.php

include_once 'base.php';
include_once '../../include/simple_html_dom.php';

$url = 'http://www.lazada.vn/vuong-quoc-balo';
$page = 1;
$limit = 36;
$totalPage = 4;
$result = [];
$productUrl = [];
do {    
	$content = app_file_get_contents($url . "/?itemperpage={$limit}&page={$page}");	
	$content = strip_tags_content($content, '<script><style><noscript>', true);
	if ($content == false) {
		batch_info($url . ' Failed'); 
        exit;
	}
	$html = str_get_html($content);	  
	if (!is_object($html) || $html == null) {        
        batch_info("Page {$page} NULL");
		$page++;
		continue;
	}
    batch_info($url . "/?itemperpage={$limit}&page={$page}");
	foreach ($html->find('div[data-qa-locator=product-item]') as $element) {		
		if (!empty($element->innertext)) {
			$subHtml = str_get_html($element->innertext);            
			foreach ($subHtml->find('a') as $element1) {
                $detailUrl = str_replace('?mp=1', '', trim($element1->href));               
                $subHtml2 = str_get_html($element1->innertext);
                foreach ($subHtml2->find('span[class=product-card__name]') as $element2) {
                    $name = $element2->innertext;
                    break;
                }
                if (!empty($name)) {
                    $explode = explode(' ', $name);
                    foreach ($explode as $value) {
                        $value = strtolower($value);
                        if (strpos($value, 'vzid') !== false
                            || strpos($value, 'vbl') !== false
                            || strpos($value, 'vbd') !== false
                            || strpos($value, 'vtc') !== false
                            || strpos($value, 'vtu') !== false
                            || strpos($value, 'vpa') !== false
                        ) {
                            $code = trim(strtoupper($value));
                            break;
                        }
                    }           
                }                                               
			}
            if (!empty($detailUrl) && !empty($code) && !empty($name)) {
                $result[] = [
                    'code' => $code,
                    'name' => $name,
                    'url' => $detailUrl
                ];
            }
		}
	}
	$page++;
} while($page <= $totalPage);
batch_info('Total: ' . count($result));
if (!empty($result)) {
    $count = 1;
    foreach ($result as $item) {
        $code = $item['code'];
        $detailUrl = $item['url'];
        $name = $item['name'];
        if ((strpos($name, 'Balo và túi đeo chéo 2 trong 1') !== false)
            || (strpos($name, 'Balo & túi đeo chéo 2 trong 1') !== false)
            || (strpos($name, 'Balo &amp; túi đeo chéo 2 trong 1') !== false)
        ) {
            $ok = call('/products/update', ['code' => $code, 'url_lazada2' => $detailUrl], $errors);
        } else {
            $ok = call('/products/update', ['code' => $code,  'url_lazada' => $detailUrl], $errors);
        }        
        if (!empty($errors[0]['message'])) {                
            batch_info("[$count] - {$detailUrl} -> {$errors[0]['message']}");
        } else {
            batch_info("[$count] - {$detailUrl} -> OK");                
        } 
        $count++;
    }
    batch_info('Done');
    exit;

    $attribute = [];
    foreach ($result as $code => $detailUrl) {
        $attribute[$code] = [];
        $content = app_file_get_contents($detailUrl);	
        $content = strip_tags_content($content, '<script><style>', true);
        if ($content == false) {            
            continue;
        }
        $html = str_get_html($content);
        foreach ($html->find('table[class=specification-table]') as $element) {
            if (!empty($element->innertext)) {
                $subHtml = str_get_html($element->innertext);
                foreach ($subHtml->find('tr') as $row) {
                    if (isset($row->children()[0]) && $row->children()[1]) {
                        switch ($row->children()[0]->plaintext) {                            
                            case 'Kích thước sản phẩm (D x R x C cm)':
                                $attribute[$code][7] = $row->children()[1]->plaintext;
                                break;
                            case 'Trọng lượng (KG)':
                                $weight = (1000) * $row->children()[1]->plaintext;
                                if ($weight <= 200) {
                                    $weight = 29;
                                } elseif ($weight <= 250) {
                                    $weight = 30;                                
                                } elseif ($weight <= 300) {
                                    $weight = 31;                                
                                } elseif ($weight <= 350) {
                                    $weight = 32;
                                } elseif ($weight <= 400) {
                                    $weight = 33;
                                } elseif ($weight <= 450) {
                                    $weight = 34;
                                } elseif ($weight <= 500) {
                                    $weight = 35;
                                } elseif ($weight <= 550) {
                                    $weight = 36;
                                } elseif ($weight <= 600) {
                                    $weight = 37;
                                } elseif ($weight <= 650) {
                                    $weight = 38;
                                } elseif ($weight <= 700) {
                                    $weight = 39;
                                } elseif ($weight <= 750) {
                                    $weight = 40;
                                } elseif ($weight <= 800) {
                                    $weight = 41;
                                } elseif ($weight <= 850) {
                                    $weight = 42;
                                } elseif ($weight <= 900) {
                                    $weight = 43;
                                } elseif ($weight <= 950) {
                                    $weight = 44;
                                } else {
                                    $weight = 45;
                                }
                                $attribute[$code][15] = $weight;
                                break;
                            case 'Sản xuất tại':
                                if ($row->children()[1]->plaintext == 'Việt Nam') {
                                    $attribute[$code][14] = 46;
                                } elseif ($row->children()[1]->plaintext == 'Trung Quốc') {
                                    $attribute[$code][14] = 47;
                                }                                
                                break;
                        }
                    }                    
                }
            }
        }        
        if (!empty($attribute[$code])) {
            $ok = call('/products/saveattribute', [
                    'only_update' => 1, 
                    'product_code' => $code, 
                    'field' => $attribute[$code]
                ]
            );
            if ($ok) {
                batch_info($code . ' -> OK');
            } else {
                batch_info($code . ' -> FAIL');
            }
        }    
    }
}
batch_info('Done');
exit;