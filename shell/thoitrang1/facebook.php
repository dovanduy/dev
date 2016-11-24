<?php

/*
  php /home/vuong761/public_html/shell/thoitrang1/facebook.php
  facebook_group_id
  '794951187227341', 	// Adm: fb.ngocai@gmail.com, https://www.facebook.com/groups/chosaletonghopbmt/
  '487699031377171', 	// Adm: fb.huean@outlook.com, https://www.facebook.com/groups/muabansaigoncholon/
  '952553334783243', 	// Chợ online Khang Điền Q.9 https://www.facebook.com/groups/928701673904347/
  '928701673904347', 	// Adm: fb.huean@outlook.com, Chợ sinh viên giá rẻ https://www.facebook.com/groups/928701673904347/
  '1648395082048459', // Hội mua bán của các mẹ ở Gò vấp https://www.facebook.com/groups/1648395082048459/
  '297906577042130', 	// Hội những người mê kinh doanh online
  '519581824789114', 	// Adm: kenstore2016@gmail.com, https://www.facebook.com/groups/choraovatvaquangcao/, CHỢ RAO VẶT & QUẢNG CÁO ONLINE
  '209799659176359', 	// Rao vặt linh tinh
  '190283764491669', 	// Adm: fb.ngocai@gmail.com, https://www.facebook.com/groups/Hoicacbamemuahangthongminh/
  '113462365452492', 	// Adm: mail.vuongquocbalo.com@gmail.com, https://www.facebook.com/groups/24hmuabanraovat/
  '795251457184853', 	// Adm: fb.huean@outlook.com, https://www.facebook.com/groups/795251457184853/ HỘI MUA BÁN-RAO VẶT-GIAO LƯU KẾT BẠN TOÀN QUỐC
 * 
 */

include ('base.php');
if (file_exists('facebook_login.serialize')) {
    sleep(rand(1*60, 30*60));
    $limit = 1;
    $AppUI = unserialize(app_file_get_contents('facebook_login.serialize'));    
    $groups = app_facebook_groups();
    foreach ($groups as $groupId) {        
        $products = call('/products/allforfacebook', ['group_id' => $groupId, 'limit' => $limit]);
        $result = array();
        foreach ($products as $product) {            
            $data = app_get_fb_share_content($product);
            $postId = postToGroup($groupId, $data, $AppUI->fb_access_token, $errorMessage);
            if (!empty($postId)) {
                call('/productshares/add', [
                    'user_id' => $AppUI->id,
                    'facebook_id' => $AppUI->facebook_id,
                    'product_id' => $product['product_id'],
                    'group_id' => $groupId,
                    'social_id' => $postId
                ]);
                batch_info("Post OK {$groupId}: {$postId}");
            } else {
                batch_info("---Post FAIL {$groupId}: {$errorMessage}");
            }
        }
        sleep(rand(3*60, 8*60));     
    }
    batch_info('Done');
} else {
    batch_info('Token does not exists');
}
exit;