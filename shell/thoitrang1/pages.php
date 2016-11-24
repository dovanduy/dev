<?php
// php pages.php
include_once 'base.php'; 
$pages = [
	['title' => 'Chính sách bảo hành', 'sort' => '1'],
	['title' => 'Chính sách bán hàng', 'sort' => '2'],
	['title' => 'Hướng dẫn mua hàng', 'sort' => '3'],
	['title' => 'Phương thức thanh toán', 'sort' => '4'],	
];
$result = [];
foreach ($pages as $page) {
	$param = [
		'website_id' => $websiteId,
		'title' => $page['title'],		
	];    
    if (isset($page['url']))  {
        $param['url'] = $page['url'];
    }
	$id = call('/pages/add', $param);
	if (!empty($id)) {
		$result[$param['title']] = $id;
	}
}
p($result, 1);