<?php
/*
   php /home/vuong761/public_html/shell/vuongquocbalo/lazada_facebook_share.php
 * 
 */
include ('base.php');
$auth = ['facebook_login.serialize', 'facebook_login2.serialize', 'facebook_login3.serialize', 'facebook_login4.serialize', 'facebook_login5.serialize'];
$authFilename = $auth[array_rand($auth, 1)];
$userId = 49;
if (!file_exists($authFilename)) {
    batch_info('Token does not exists');
    exit;
}
$AppUI = unserialize(app_file_get_contents($authFilename));
batch_info($AppUI->facebook_id . ' : ' . $AppUI->email);
$shareds = call('/facebookwallshares/all', [
        'user_id' => $userId,                           
        'site' => 'lazada.vn',                           
    ]
);
$groups = app_array_rand(app_facebook_groups($AppUI->user_id), 8);
foreach ($groups as $groupId) {    
    foreach (app_array_rand($shareds, 1) as $shared) {   
        sleep(rand(3*60, 10*60));
        $socialId = explode('_', $shared['social_id']);
        $id = end($socialId);
        if ($userId == 49) {
            $link = "https://www.facebook.com/vuongquocbalochamcom/posts/{$id}";
        } else {
            $link = "https://www.facebook.com/thoitrang1.net/posts/{$id}";
        }
        $sharedGroupIds = !empty($shared['group_id']) ? explode(',', $shared['group_id']) : [];
        $sharedGroupSocialIds = !empty($shared['group_social_id']) ? explode(',', $shared['group_social_id']) : [];        
        if (in_array($groupId, $sharedGroupIds)) { // shared
            foreach ($sharedGroupIds as $i => $sharedGroupId) {
                if ($sharedGroupId == $groupId && !empty($sharedGroupSocialIds[$i])) {
                    $sharedGroupSocialId = $sharedGroupSocialIds[$i];
                    $commentId = commentToPost($sharedGroupSocialId, app_get_fb_share_comment(), $AppUI->fb_access_token, $errorMessage, $errorCode);
                    if (!empty($commentId)) {
                        batch_info("Comment OK {$sharedGroupSocialId}: {$commentId}");                    
                    } else {
                        if ($errorCode == 100) {
                            call('/facebookgroupshares/add', 
                                [
                                    'delete_group_post' => 1,           
                                    'user_id' => $AppUI->user_id,           
                                    'facebook_id' => $AppUI->facebook_id,           
                                    'wall_social_id' => $shared['social_id'],
                                    'social_id' => $sharedGroupSocialId,
                                    'group_id' => $groupId,                                    
                                ]
                            );
                        }
                        batch_info("Comment FAIL {$sharedGroupSocialId}: {$errorMessage}");                    
                    }
                    break;
                }
            }
        } elseif (!empty($link)) {
            $data = [
                'link' => $link,                          
            ];
            $postId = postToGroup($groupId, $data, $AppUI->fb_access_token, $errorMessage);
            if (!empty($postId)) {                
                batch_info("Post OK {$groupId} - {$shared['social_id']}: {$postId}");
                call('/facebookgroupshares/add', 
                    [
                        'user_id' => $AppUI->user_id,           
                        'facebook_id' => $AppUI->facebook_id,           
                        'wall_social_id' => $shared['social_id'],
                        'social_id' => $postId,
                        'group_id' => $groupId,
                    ]
                );               
            } else {
                batch_info("Post FAIL {$groupId} - {$shared['social_id']}: {$errorMessage}");
            }
        }
    }        
}
batch_info('Done');
exit;