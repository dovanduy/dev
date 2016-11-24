<?php

// php sendo.php

include_once 'base.php';
include_once '../../include/simple_html_dom.php';

$url = 'https://www.sendo.vn/shop/vuongquocbalo/tui-xach/balo/balo-nam/';
$url = 'https://www.sendo.vn/shop/vuongquocbalo/tui-xach/balo/balo-nu/';
$url = 'https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q=minion/';
$url = 'https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q=naruto/';
$url = 'https://www.sendo.vn/shop/vuongquocbalo/tim-kiem/?q=pikachu';
$url = 'https://www.sendo.vn/shop/vuongquocbalo/tui-xach/tui-xach-nu/tui-deo-cheo-nu/';
$url = 'https://www.sendo.vn/shop/shophocsinh/tui-xach/balo/balo-nam/';
$page = 1;
$productUrl = [];
$totalRecord = 0;
$totalPage = 0;
$limit = 40;
$count = 1;
$browser = isset($argv[1]) ? $argv[1] : 'chrome'; // chrome.exe,opera.exe,firefox.exe
$browser .= '.exe';
//$ps = exec("tasklist");
//p($ps, 1);
do {
	//$content = app_file_get_contents($url . '/?p=' . $page);	
	$content = app_file_get_contents($url);	
	$content = strip_tags_content($content, '<script><style>', true);
	if ($content == false) {
		batch_info($url . ' Failed');  
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
	foreach ($html->find('a[class=name_product shop_color_hover]') as $element) {
		if (!empty($element->href)) {
			$detailUrl = trim($element->href);
			//if (!in_array($detailUrl, $productUrl)) {
				batch_info('[' . $count . '] ' . $detailUrl);
				//$productUrl[] = $detailUrl;								
				//shell_exec("start {$browser} --user-data-dir=E:\asp {$detailUrl}");							
				shell_exec("start {$browser} {$detailUrl}");							
				sleep(rand(10, 20));	
				$ps = shell_exec("TASKLIST /FI \"IMAGENAME eq {$browser}\"");	
				preg_match("/(\d+)/", $ps, $match);
				if (isset($match[0])) {
					shell_exec("TASKKILL /F /PID {$match[0]}");						
				}
				$count++;
			//} 
			/*
			if ($count%5 == 0) {
				sleep(20);
				exec("taskkill /F /IM {$browser}");				
			}*/
		}
	}
	$page++; 
} while($page < $totalPage || 1==1);
batch_info('Done');
exit;