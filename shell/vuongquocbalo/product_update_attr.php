<?php
// php /home/vuong761/public_html/shell/vuongquocbalo/product_update_attr.php
// php product_update_attr.php 16

include_once 'base.php';
$categoryId = $argv[1];
$param = [
    'category_id' => $categoryId,
    //'code' => 'VBDKL104',
];
$products = call('/products/all', $param);
if (empty($products)) {
    batch_info('Product not found');
    exit;
}
batch_info(count($products)); 
foreach ($products as $product) {
    $attribute = [];
    switch ($categoryId) {
        case 8:
            $attribute = [
                3 => 'Simili',
                4 => 'Đeo chéo',
                5 => 'Công sở, Đi học, Đi chơi',
                6 => 'Thu Đông, Xuân Hè',
                7 => '34x9x25',
                14 => '46',
                15 => '32',
            ];
            break;
        case 15:
            $attribute = [
                3 => 'Simili',
                4 => 'Đeo vai, Đeo chéo',
                5 => 'Công sở, Đi học, Đi chơi',
                6 => 'Thu Đông, Xuân Hè',
                7 => '32x14.5x41.5',
                14 => '46',
                15 => '35',
            ];
            break;
        case 16:
            $attribute = [
                3 => 'Simili',
                4 => 'Đeo vai',
                5 => 'Đi học, Đi chơi',
                6 => 'Thu Đông, Xuân Hè',
                7 => '29x2x40',
                14 => '46',
                15 => '30',
            ];
            break;
        case 99:
            $attribute = [
                3 => 'Simili',
                4 => 'Đeo chéo',
                5 => 'Công sở, Đi học, Đi chơi',
                6 => 'Thu Đông, Xuân Hè',
                7 => '24x2x17',
                14 => '46',
                15 => '29',
            ];
            break;
    }
    if (!empty($attribute)) {
        $code = $product['code'];
        $ok = call('/products/saveattribute', [
                'only_update' => 1, 
                'product_id' => $product['product_id'], 
                'field' => $attribute
            ]
        );
        if ($ok) {
            batch_info($code . ' -> OK');
        } else {
            batch_info($code . ' -> FAIL');
        }
    }    
}
batch_info('Done');
exit;