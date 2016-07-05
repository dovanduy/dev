<?php
// php categories.php

include_once 'base.php'; 
$websiteId = 1;
$categories = [
	['name' => 'Ba Lô, Túi Xách, Ví Nữ', 'parent' => ''],
	['name' => 'Ba Lô Nữ', 'parent' => 'Ba Lô, Túi Xách, Ví Nữ'],
	['name' => 'Túi Xách Nữ', 'parent' => 'Ba Lô, Túi Xách, Ví Nữ'],
	['name' => 'Bóp Ví Nữ', 'parent' => 'Ba Lô, Túi Xách, Ví Nữ'],
    
    ['name' => 'Ba Lô, Túi Xách, Ví Nam', 'parent' => ''],
	['name' => 'Ba Lô Nam', 'parent' => 'Ba Lô, Túi Xách, Ví Nam'],
	['name' => 'Túi Xách, Cặp Táp Nam', 'parent' => 'Ba Lô, Túi Xách, Ví Nam'],
	['name' => 'Bóp Ví Nam', 'parent' => 'Ba Lô, Túi Xách, Ví Nam'],
	    
    ['name' => 'Ba Lô, Cặp Học Sinh', 'parent' => ''],
	['name' => 'Ba Lô Mẫu Giáo', 'parent' => 'Ba Lô, Cặp Học Sinh'],
	['name' => 'Ba Lô Học Sinh Cấp 1', 'parent' => 'Ba Lô, Cặp Học Sinh'],
	['name' => 'Ba Lô Học Sinh Cấp 2,3', 'parent' => 'Ba Lô, Cặp Học Sinh'],	
	
    ['name' => 'Ba Lô, Túi Chéo Sinh Viên', 'parent' => ''],
    ['name' => 'Ba Lô Sinh Viên', 'parent' => 'Ba Lô, Túi Chéo Sinh Viên'],
	['name' => 'Túi Chéo Sinh Viên', 'parent' => 'Ba Lô, Túi Chéo Sinh Viên'],
	
	['name' => 'Ba Lô In Hình Độc, Đẹp', 'parent' => ''],	
	['name' => 'Ba Lô In　Giả Da', 'parent' => 'Ba Lô In Hình Độc, Đẹp'],
	['name' => 'Ba Lô In　Giây Rút', 'parent' => 'Ba Lô In Hình Độc, Đẹp'],
	['name' => 'Ba Lô Teen', 'parent' => 'Ba Lô In Hình Độc, Đẹp'],
	
	['name' => 'Ba Lô Laptop', 'parent' => ''],
	['name' => 'Ba Lô, Túi Xách Du Lịch', 'parent' => ''],	
];
$result = [];
foreach ($categories as $category) {		
	$parentId = 0;
	if (!empty($category['parent'])) {
		if (isset($result[$category['parent']])) {
			$parentId = $result[$category['parent']];
		} else {
			$param = [
				'website_id' => $websiteId,
				'name' => $category['parent'],
				'return_id' => 1
			];
			$parentId = call('/productcategories/add', $category);
			$result[$param['name']] = $parentId;
		}		
	}
	$param = [
		'website_id' => $websiteId,
		'name' => $category['name'],
		'parent_id' => $parentId,
		'return_id' => 1
	];
	$id = call('/productcategories/add', $param);
	if (!empty($id)) {
		$result[$param['name']] = $id;
	}
}
p($result, 1);