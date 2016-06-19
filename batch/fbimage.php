<?php
include ('base.php');
$param = [
    'website_id' => 1,
    'limit' => 20,
];
$all = call('/products/all', $param);
$offset = 0;
$limit = 5;
$countUpdate = 0;
echo PHP_EOL . 'Start';
echo PHP_EOL . 'Total product: ' . count($all);
do {
    $products = array_slice($all, $offset, $limit);   
    $updateResult = call('/products/updatefbimage', ['website_id' => 1, 'products' => json_encode($products)]);
    if ($updateResult) {
        foreach ($updateResult as $productId => $value) {
            echo PHP_EOL . $productId . ': ' . $value;
        }
        $countUpdate += count($products);
    }
    $offset += $limit;
} while (!empty($products));
echo PHP_EOL . 'Total updated: ' . $countUpdate;
echo PHP_EOL . 'Done';
exit;