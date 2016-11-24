<?php
// php menus.php
include_once 'base.php'; 
$menus = [
	['name' => 'Chính sách bảo hành', 'type' => 'footer', 'url' => 'http://thoitrang1.net/chinh-sach-bao-hanh.html'],
	['name' => 'Chính sách bán hàng', 'type' => 'footer', 'url' => 'http://thoitrang1.net/chinh-sach-ban-hang.html'],
	['name' => 'Hướng dẫn mua hàng', 'type' => 'footer', 'url' => 'http://thoitrang1.net/huong-dan-mua-hang.html'],
	['name' => 'Phương thức thanh toán', 'type' => 'footer', 'url' => 'http://thoitrang1.net/phuong-thuc-thanh-toan.html'],	
];
$result = [];
foreach ($menus as $menu) {
	$param = [
		'website_id' => $websiteId,
		'name' => $menu['name'],		
		'url' => $menu['url'],		
		'type' => $menu['type'],		
	];        
	$id = call('/menus/add', $param);
	if (!empty($id)) {
		$result[$param['name']] = $id;
	}
}
p($result, 1);