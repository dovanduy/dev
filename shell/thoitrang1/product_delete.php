<?php

// php product_delete.php
// php /home/vuong761/public_html/shell/thoitrang1/product_available.php

include_once 'base.php';
$categoryId = '51,52,53,54, 56,57,58, 60,61,62, 63, 65,66, 69,70, 76,77,78, 80,81,82, 85, 86,87';
$categoryId = '51';
$i = 1;
$products = call('/products/all', [
    'category_id' => $categoryId,     
]);
if (!empty($products)) {
    batch_info('Total: ' . count($products));
    foreach ($products as $product) {           
        $ok = call('/products/delete', [
            'product_id' => $product['product_id'], 
        ]);     
        batch_info('[' . $i . ']' . $product['code'] . ($ok ? ' OK' : ' ERR'));
        $i++;                      
    }
}
batch_info('Done');
exit;