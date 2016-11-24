<?php
/*
   php /home/vuong761/public_html/shell/vuongquocbalo/khanh.php
 * 
 */
 
include ('base.php');
$auth = ['facebook_login5.serialize'];
$authFilename = $auth[array_rand($auth, 1)];
if (!file_exists($authFilename)) {
    batch_info('Token does not exists');
    exit;
}
$AppUI = unserialize(app_file_get_contents($authFilename));
$groups = app_array_rand(app_facebook_groups($AppUI->user_id), 2);
$link = "http://www.lazada.vn/vuong-quoc-balo/";
foreach ($groups as $groupId) {	
	$data = [
		'message' => implode(PHP_EOL, [
			"Khánh (TP.HCM) 0906.209.227 - 0982.180.502",         
            "- Nhận dạy lái xe B2, C, năng dấu các loại, bổ túc tay lái!",         
            "- Dạy uy tín, chất lượng, giáo viên dạy nhiệt tình dễ hiểu, có nhiều kinh nghiệm.",         
            "- Giờ học linh hoạt theo thời gian học viên.",         
            "- Học phí được đóng nhiều lần, giúp học viên tự chủ trong tài chính.",         
            "- Nhận chạy đưa rước dịch vụ theo yêu cầu.",            
            "- Xe đời mới, máy lạnh.",            
        ]),
		'picture' => 'http://img.vuongquocbalo.com/khanh_xe.jpg',		
		'link' => $link, 
		'caption' => 'vuongquocbalo.com',		
	];
	$postId = postToGroup($groupId, $data, $AppUI->fb_access_token, $errorMessage);
	if (!empty($postId)) {                
		batch_info("Post OK {$groupId}: {$postId}");
	} else {
		batch_info("Post FAIL {$groupId}: {$errorMessage}");
	}        
	//sleep(rand(3*60, 10*60));	
}
batch_info('Done');
exit;