<?php
// php categories.php

include_once 'base.php'; 
$websiteId = 2;
$categories = [
	['name' => 'Quần Áo Nữ', 'parent' => ''],
	['name' => 'Áo Khoác Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Áo Sơ Mi Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Áo Thun Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Áo Kiểu Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Quần Short Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Quần Lửng Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Quần Dài Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Đồ Thể Thao Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Đồ Mặc Nhà, Đồ Ngủ Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Jumpsuit Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Set Đồ Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Đồ Lót Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Đồ Bơi, Đồ Tắm Nữ', 'parent' => 'Quần Áo Nữ'],
	['name' => 'Đồ Chống Nắng Nữ', 'parent' => 'Quần Áo Nữ'],
	
	['name' => 'Váy, Đầm Nữ', 'parent' => ''],	
	['name' => 'Váy Nữ', 'parent' => 'Váy, Đầm Nữ'],
	['name' => 'Đầm Nữ', 'parent' => 'Váy, Đầm Nữ'],
	
	['name' => 'Ba lô, Túi xách Nữ', 'parent' => ''],
	
	['name' => 'Quần Áo Nam', 'parent' => ''],
	['name' => 'Áo Khoác Nam', 'parent' => 'Quần Áo Nam'],
	['name' => 'Áo Sơ Mi Nam', 'parent' => 'Quần Áo Nam'],
	['name' => 'Áo Thun Nam', 'parent' => 'Quần Áo Nam'],
	['name' => 'Áo Thun Nam', 'parent' => 'Quần Áo Nam'],
	['name' => 'Quần Short, Quần Lửng Nam', 'parent' => 'Quần Áo Nam'],
	['name' => 'Quần Tây, Quần Jeans Nam', 'parent' => 'Quần Áo Nam'],
	['name' => 'Quần Kiểu Nam', 'parent' => 'Quần Áo Nam'],
	['name' => 'Bộ Đồ Nam', 'parent' => 'Quần Áo Nam'],
	['name' => 'Đồ Thể Thao Nam', 'parent' => 'Quần Áo Nam'],
	['name' => 'Đồ Lót Nam', 'parent' => 'Quần Áo Nam'],
	['name' => 'Đồ Bơi, Đồ Tắm Nam', 'parent' => 'Quần Áo Nam'],
	
	['name' => 'Ba lô, Túi xách Nam', 'parent' => ''],
	
	['name' => 'Quần Áo Trẻ Em', 'parent' => ''],
	['name' => 'Quần Áo Bé Gái', 'parent' => 'Quần Áo Trẻ Em'],
	['name' => 'Quần Áo Bé Trai', 'parent' => 'Quần Áo Trẻ Em'],	
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