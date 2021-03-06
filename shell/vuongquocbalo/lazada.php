<?php

// php lazada.php

include_once 'base.php';
include_once '../../include/simple_html_dom.php';

$fileUrls = 'lazada_urls.serialize';
$url = 'http://www.lazada.vn/vuong-quoc-balo';
$page = 1;
$limit = 36;
$totalPage = 8;
$urls = [];

if (file_exists($fileUrls)) {
    $urls = unserialize(app_file_get_contents($fileUrls));
} else {
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
					if (!empty($detailUrl)) {
						$urls[] = $detailUrl;					
						break;
					}				
				}
				/*
				if ($count%5 == 0) {
					sleep(20);
					exec("taskkill /F /IM {$browser}");				
				} 
				*/	
			}
		}
		$page++;
	} while($page <= $totalPage);
	app_file_put_contents($fileUrls, serialize($urls));
}

$browser = isset($argv[1]) ? $argv[1] : 'chrome'; // chrome.exe,opera.exe,firefox.exe
$browser .= '.exe';
$count = 1;
do {
	shuffle($urls);
	foreach ($urls as $detailUrl) {
		batch_info('[' . $count . '] ' . $detailUrl);
		shell_exec("start {$browser} {$detailUrl}");							
		sleep(rand(20, 30));	
		$ps = shell_exec("TASKLIST /FI \"IMAGENAME eq {$browser}\"");	
		preg_match("/(\d+)/", $ps, $match);
		if (isset($match[0])) {
			shell_exec("TASKKILL /F /PID {$match[0]}");						
		}
		$count++;	
	}
	batch_info('Done');
} while(1);
exit;