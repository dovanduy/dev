<?php
// php categories.php

include_once 'base.php'; 
$websiteId = 2;
$categories = [
	['name' => 'Áo Nữ', 'parent' => ''],
	['name' => 'Áo Khoác Nữ', 'parent' => 'Áo Nữ'],
	['name' => 'Áo Sơ Mi Nữ', 'parent' => 'Áo Nữ'],
	['name' => 'Áo Thun Nữ', 'parent' => 'Áo Nữ'],
	['name' => 'Áo Kiểu Nữ', 'parent' => 'Áo Nữ'],
    
    ['name' => 'Quần Nữ', 'parent' => ''],
	['name' => 'Quần Short Nữ', 'parent' => 'Quần Nữ'],
	['name' => 'Quần Lửng Nữ', 'parent' => 'Quần Nữ'],
	['name' => 'Quần Dài Nữ', 'parent' => 'Quần Nữ'],
    
    ['name' => 'Bộ Đồ Nữ', 'parent' => ''],	
	['name' => 'Đồ Mặc Nhà, Đồ Ngủ Nữ', 'parent' => 'Bộ Đồ Nữ'],
	['name' => 'Jumpsuit Nữ', 'parent' => 'Bộ Đồ Nữ'],
	['name' => 'Set Đồ Nữ', 'parent' => 'Bộ Đồ Nữ'],
	
    ['name' => 'Đồ Thể Thao Nữ', 'parent' => ''],
    
    ['name' => 'Đồ Lót, Đồ Tắm Nữ', 'parent' => ''],
    ['name' => 'Đồ Lót Nữ', 'parent' => 'Đồ Lót, Đồ Tắm Nữ'],
	['name' => 'Đồ Bơi, Đồ Tắm Nữ', 'parent' => 'Đồ Lót, Đồ Tắm Nữ'],
	['name' => 'Đồ Chống Nắng Nữ', 'parent' => 'Đồ Lót, Đồ Tắm Nữ'],
	
	['name' => 'Váy, Đầm Nữ', 'parent' => ''],	
	['name' => 'Váy Nữ', 'parent' => 'Váy, Đầm Nữ'],
	['name' => 'Đầm Nữ', 'parent' => 'Váy, Đầm Nữ'],
	
	['name' => 'Ba Lô, Túi Xách, Ví Nữ', 'parent' => ''],
	['name' => 'Ba Lô Nữ', 'parent' => 'Ba Lô, Túi Xách, Ví Nữ'],
	['name' => 'Túi Xách Nữ', 'parent' => 'Ba Lô, Túi Xách, Ví Nữ'],
	['name' => 'Bóp Ví Nữ', 'parent' => 'Ba Lô, Túi Xách, Ví Nữ'],
	
	['name' => 'Áo Nam', 'parent' => ''],
	['name' => 'Áo Khoác Nam', 'parent' => 'Áo Nam'],
	['name' => 'Áo Sơ Mi Nam', 'parent' => 'Áo Nam'],
	['name' => 'Áo Thun Nam', 'parent' => 'Áo Nam'],
	['name' => 'Áo Thun Nam', 'parent' => 'Áo Nam'],
    
    ['name' => 'Quần Nam', 'parent' => ''],
	['name' => 'Quần Short, Quần Lửng Nam', 'parent' => 'Quần Nam'],
	['name' => 'Quần Tây, Quần Jeans Nam', 'parent' => 'Quần Nam'],
	['name' => 'Quần Kiểu Nam', 'parent' => 'Quần Nam'],
    
	['name' => 'Đồ Lót, Đồ Tắm Nam', 'parent' => ''],	
	['name' => 'Đồ Lót Nam', 'parent' => 'Đồ Lót, Đồ Lót, Đồ Tắm Nam'],
	['name' => 'Đồ Bơi, Đồ Tắm Nam', 'parent' => 'Đồ Lót, Đồ Tắm Nam'],
    
    ['name' => 'Đồ Thể Thao Nam', 'parent' => ''],
	
	['name' => 'Ba Lô, Túi xách, Ví Nam', 'parent' => ''],
    ['name' => 'Ba Lô Nam', 'parent' => 'Ba lô, Túi xách, Ví Nam'],
	['name' => 'Túi Xách Nam', 'parent' => 'Ba lô, Túi xách, Ví Nam'],
	['name' => 'Bóp Ví Nam', 'parent' => 'Ba lô, Túi xách, Ví Nam'],
	
	['name' => 'Quần Áo Trẻ Em', 'parent' => ''],
	['name' => 'Quần Áo Bé Gái', 'parent' => 'Quần Áo Trẻ Em'],
	['name' => 'Quần Áo Bé Trai', 'parent' => 'Quần Áo Trẻ Em'],
	['name' => 'Quần Áo Trẻ Sơ Sinh', 'parent' => 'Quần Áo Trẻ Em'],
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