<?php
// php blocks.php

include_once 'base.php'; 
$websiteId = 2;
$blocks = [
	['name' => 'Áo Nữ', 'sort' => '1'],
	['name' => 'Quần Nữ', 'sort' => '2'],
	['name' => 'Đồ Thể Thao Nữ', 'sort' => '3'],
	['name' => 'Đồ Mặc Nhà, Đồ Ngủ Nữ', 'sort' => '4'],	
	['name' => 'Đồ Lót, Đồ Tắm Nữ', 'sort' => '5'],
	['name' => 'Đồ Thể Thao Nữ', 'sort' => '7'],
	['name' => 'Váy, Đầm Nữ', 'sort' => '7'],
	
	['name' => 'Áo Nam', 'sort' => '8'],	
	['name' => 'Quần Nam', 'sort' => '9'],		
	['name' => 'Đồ Thể Thao Nam', 'sort' => '10'],
	['name' => 'Đồ Lót, Đồ Tắm Nam', 'sort' => '11'],	
];
$result = [];
foreach ($blocks as $block) {
	$param = [
		'website_id' => $websiteId,
		'name' => $block['name'],		
	];
    if (isset($block['sort']))  {
        $param['sort'] = $block['sort'];
    }
    if (isset($block['url']))  {
        $param['url'] = $block['url'];
    }
	$id = call('/blocks/add', $param);
	if (!empty($id)) {
		$result[$param['name']] = $id;
	}
}
p($result, 1);