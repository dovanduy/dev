<?php
// php truyencuoi.vn.php

include_once 'base.php'; 
include_once '../include/simple_html_dom.php';   
$urls = array(
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-gia-dinh',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-dan-gian',	
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-hoc-duong',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-con-gai',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-con-trai',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-cong-nghe',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-nghe-nghiep',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-y-hoc',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-tinh-yeu',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-giao-thong',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-say-xin',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-the-thao',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-phap-luat',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-nha-hang',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-nha-binh',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-khoa-hoc',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-ton-giao',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-danh-nhan',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-trang-quynh',
	'http://www.truyencuoi.vn/the-loai-truyen/truyen-cuoi-vova',
	'http://www.truyencuoi.vn/the-loai-truyen/tho-ca-cuoi',
);
$topics = array();
foreach ($urls as $url) {
	$lastPage = 0;
    $page = 1;
    $stop = false;
    do {
		batch_info('Url ' . $url . '?page=' . $page);
        $content = app_file_get_contents($url . '?page=' . $page);
        if ($content == false) {
            batch_info($url . ' Failed');
            continue;
        }
        $html = str_get_html($content);
		if ($page == 1) {
			foreach($html->find('li[class=pager-last last]') as $element) {				
				$subHtml = str_get_html($element->innertext);		
				foreach($subHtml->find('a') as $element1) { 				
					$lastPageUrl = $element1->href;		
					$querystring = parse_url($lastPageUrl, PHP_URL_QUERY);
					parse_str($querystring, $vars);				
					if (!empty($vars['page'])) {
						$lastPage = $vars['page'];
						break;
					}				
                }				
				if ($lastPage > 0) {
					break;
				}
			}
		}
        foreach($html->find('div[class=node]') as $element) { 
			$topic = [];
			if (!empty($element->innertext)) { 
                $subHtml = str_get_html($element->innertext); 
                foreach($subHtml->find('a') as $element1) {                
                    if (!empty($element1->innertext)) {
						$topic['title'] = '★★★ 😂😭😚😁 🚺 ' . trim($element1->innertext) . ' 🚹 😊😉😍😘 ★★★';						
						break;
                    }
                }
				foreach($subHtml->find('div[class=field-item]') as $element1) {                
                    if (!empty($element1->innertext)) {
						$topic['content'] = str_replace(array('<b>','</b>','<strong>','</strong>','<p>','</p>','<br/>','"'), array('','','','','','',PHP_EOL,'\"'), $element1->innertext);
						$topic['content'] = implode(PHP_EOL, array_map('trim', explode(PHP_EOL, trim($topic['content']))));
						break;
                    }
                }
				if (!empty($topic['title']) && !empty($topic['content'])) {					
					$topics[] = '"' . implode(PHP_EOL, $topic) . '",';
				}
            }			
        }
        $page++;
    } while ($lastPage >= $page);
	$fileName = end(explode('/', $url)) . '.php';
	app_file_put_contents(
		$fileName,
		'<?php ' . PHP_EOL . 'return [' . PHP_EOL . implode(PHP_EOL . PHP_EOL, $topics) . PHP_EOL . '];'
	);
}
batch_info('Done');
exit;