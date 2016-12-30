<?php
// php /home/vuong761/public_html/shell/vuongquocbalo/lazada_url.php
// php lazada_url.php

include_once 'base.php';
include_once '../../include/simple_html_dom.php';

$url = 'http://www.lazada.vn/vuong-quoc-balo';
$page = 1;
$limit = 36;
$totalPage = 6;
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
                            if (strpos($code, '(') !== false) {
                                 $explodeCode = explode('(', $code);
                                 $code = $explodeCode[0];
                            }      
                            $code = trim($code);
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
            batch_info("[$count] - {$detailUrl} - [{$code}] -> {$errors[0]['message']}");
        } else {
            batch_info("[$count] - {$detailUrl} -> OK");                
        } 
        $count++;
    }
    batch_info('Done');
    exit;
}
batch_info('Done');
exit;