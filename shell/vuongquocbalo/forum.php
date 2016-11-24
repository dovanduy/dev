<?php
// php blogger.php
include ('base.php');
$products = call('/products/all', ['blog_id' => $blogId, 'limit' => $limit]);

if (file_exists('google_login.serialize')) {    
    $siteUrl = 'http://vuongquocbalo.com';
    $limit = 5;
    $AppUI = unserialize(app_file_get_contents('google_login.serialize'));   
    if (empty($AppUI->google_access_token) || strtotime($AppUI->access_token_expires_at) < time()) {
        batch_info('Invalid user or Token have been expired');
        exit;
    }
    $blogs = app_bloggers();
    $result = array();
    foreach ($blogs as $blogId => $info) {      
        $result[$blogId] = [];
        $products = call('/products/allforblogger', ['blog_id' => $blogId, 'limit' => $limit]);
        if (empty($products)) {
            batch_info('Product list is empty');
            continue;
        }
        foreach ($products as $row) {             
            $product = call('/products/detail', ['product_id' => $row['product_id'], 'get_images' => 1]);            
            if (empty($product)) {
                batch_info('Invalid product');
                continue;
            }
            if (!array_intersect($info['categories'], $product['category_id'])) {
                continue;
            }
            batch_info($row['code']);            
            $data = [
                'name' => $product['name']
            ];
            $product['url'] = $siteUrl . '/' . name_2_url($product['name']) . '?utm_source=blogger&utm_medium=social&utm_campaign=product';
            $product['content'] = strip_tags($product['content'], '<p><div><span><ul><li><strong><b><br><center>');
            if (!empty($product['images'])) {
                foreach ($product['images'] as $image) { 
                    $image['url_image'] = str_replace('.dev', '.com', $image['url_image']);
                    $product['content'] .= "<center><p><img style=\"width:80%\" src=\"{$image['url_image']}\"/></p></center>";
                } 
            }          
            $labels = [];
            if (!empty($product['categories'])) {
                foreach ($product['categories'] as $category) {
                    $labels[] = $category['name'];                    
                }
            }            
            if (array_intersect([15, 16], $product['category_id'])) {
                $data['content'] = implode('<br/>', [                
                        $product['content'],
                        "<center style=\"width:100%;color:#D4232B;font-size:30px;padding:5px;\"> Giá: " . app_money_format($product['price']) . '</center>',               
                        "<center><p><a href=\"{$product['url']}\"><img src=\"{$siteUrl}/web/images/buy_now.gif\"/></a></p></center>",		
                    ]
                );
                if (!empty($product['attributes'])) {
                    foreach ($product['attributes'] as $attribute) {
                        if (!empty($attribute['value'])) {
                            $labels[] = $attribute['value'];
                        }
                    } 
                }
            } else {
                $data['content'] = implode('<br/>', [                
                        $product['short'],
                        $product['content'],
                        "<center style=\"width:100%;color:#D4232B;font-size:30px;padding:5px;\"> Giá: " . app_money_format($product['price']) . '</center>',               
                        "<center><p><a href=\"{$product['url']}\"><img src=\"http://vuongquocbalo.com/web/images/buy_now.gif\"/></a></p></center>",		
                    ]
                );
            }
            $data['labels'] = $labels;
            $postId = postToBlog($blogId, $data, $AppUI->google_access_token, $errorMessage);
            if (!empty($postId)) {
                $result[$blogId][] = "{$product['product_id']}-{$postId}";
                call('/bloggerpostids/add', [
                    'product_id' => $product['product_id'],
                    'blog_id' => $blogId,
                    'post_id' => $postId,                
                ]);
            } else {
                $result[$blogId][] = "{$product['product_id']}-{$errorMessage}";
            }
        }
        if (!empty($result[$blogId])) {
            batch_info($blogId . ': ' . implode(',', $result[$blogId]));
        }        
    }
    batch_info('Done');
} else {
    batch_info('Token does not exists');
}
exit;