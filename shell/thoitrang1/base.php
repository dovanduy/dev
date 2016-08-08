<?php
set_time_limit(24*60*60);
date_default_timezone_set('Asia/Saigon');
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    define('OUTPUT_TO_NULL', '');
} else {
    define('OUTPUT_TO_NULL', '> /dev/null &');
}
//include ('../../vendor/facebook/php-sdk-v4/src/Facebook/autoload.php');
include ('../../vendor/autoload.php');
include ('../../functions.php');

$env = 'development'; // development, production
$config = [
    'development' => [
        'timeout' => 30*60,       
        'base_uri' => 'http://api.thoitrang1.vn',    
        
        'facebook_app_id' => '291514527862025',
        'facebook_app_secret' => 'fd711d2381d4a75dffc8027bc76841b8',

        'google_app_id' => '160316380666-8o4egcn76afejg56hg86r56mnhdthjn1.apps.googleusercontent.com',
        'google_app_secret' => 'BmbD_-n0wM-VY7pQHkd4pN60',
        'google_app_redirect_uri' => 'http://thoitrang1.vn/glogin',
        
        'img_domain' => 'http://img.thoitrang1.vn',
    ],
    'production' => [
        'timeout' => 30*60,
        'base_uri' => 'http://api.thoitrang1.net',   
        
        'facebook_app_id' => '1017869161653955',
        'facebook_app_secret' => 'e9f6b56a1a0de0210e3266625b327743',
        
        'google_app_id' => '57520396243-61uormtrqgdjpa42nt98vb6de1q6nqar.apps.googleusercontent.com',
        'google_app_secret' => 'sWYrv92ElITxEL8rmC9AVApe',
        'google_app_redirect_uri' => 'http://thoitrang1.net/glogin2',
        
        'img_domain' => 'http://img.thoitrang1.net',
    ]
];
$config = $config[$env];
$imgDomain = $config['img_domain'];
$docRoot = dirname(dirname(getcwd()));
$imgDir = implode(DS, [$docRoot, 'data', 'thoitrang1', 'img']);
$websiteId = 2;

function call($url, $param = array(), &$errors = null) {
	global $config, $websiteId;
	$method = 'post';
	if (isset($config[$url])) {
		if (is_array($config[$url])) {
			list($url, $method) = $config[$url];        
		} else {
			$url = $config[$url];
		}
	}
	try {		
        $param['website_id'] = $websiteId;
		$headers = array("Content-Type:multipart/form-data");
		$url = $config['base_uri'] . $url;
		$ch = curl_init();
		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => false,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => $param,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SAFE_UPLOAD => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => $config['timeout'],
		);
		curl_setopt_array($ch, $options);
		$jsonResponse = curl_exec($ch);
		$errno = curl_errno($ch);
		if (empty($errno)) {
			curl_close($ch);
			$result = json_decode($jsonResponse, true);
            switch ($result['status']) {
                case 'OK';
                    return $result['results'];                   
                case 'ERROR_VALIDATION':                                         
                case 'ERROR':
                    $errors = $result['results'];    
                    p($errors);
                    return false;
            }
            return $result;
		}
		if (!empty($ch)) {
			@curl_close($ch);
		}
	} catch (\Exception $e) {         
		print_r($e->getMessage());
	}
	return false;
}

function get_all_files($path, &$result = array()) {    
    if (is_dir($path)) {
        $files = glob($path . DS . '*');
        foreach ($files as $file) {         
            $file = get_all_files($file, $result);
            if ($file) {
                $result[] = $file;
            }
        }      
    } elseif (is_file($path) && file_exists($path) && basename($path) != 'empty') {
        return $path;
    }
    return null;
}

function rand_post_time() {
    return rand(3*60, 6*60);
}

function batch_info($message = '') {
    echo PHP_EOL . '[' . date('Y-m-d H:i:s') . '] ' . $message;
}

$userIds = [
    '10206637393356602', // Thai Lai
    '129881887426347', // thoitrang1.net
    '835521976592060', // Ngoc Nguyen My
    '1723524741251993', // Duc Tin
    '126728971080640', // kenstore2016@gmail.com https://www.facebook.com/kinhdothoitrang.vn
    '107846242974801', // fb.huean@outlook.com
    '127283041026900', // fb.ngocai@gmail.com
    '116860312071059', // fb.hoaian@gmail.com
];
        
$groupIds = [
	'794951187227341', 	// Adm: fb.ngocai@gmail.com, https://www.facebook.com/groups/chosaletonghopbmt/	
	'487699031377171', 	// Adm: fb.huean@outlook.com, https://www.facebook.com/groups/muabansaigoncholon/
	'952553334783243', 	// Chợ online Khang Điền Q.9 https://www.facebook.com/groups/928701673904347/
	'928701673904347', 	// Adm: fb.huean@outlook.com, Chợ sinh viên giá rẻ https://www.facebook.com/groups/928701673904347/
	'1648395082048459', // Hội mua bán của các mẹ ở Gò vấp https://www.facebook.com/groups/1648395082048459/
	'297906577042130', 	// Hội những người mê kinh doanh online
	'519581824789114', 	// Adm: kenstore2016@gmail.com, https://www.facebook.com/groups/choraovatvaquangcao/, CHỢ RAO VẶT & QUẢNG CÁO ONLINE
	'209799659176359', 	// Rao vặt linh tinh
];

$tagIds = [
    '10206637393356602', // Thai Lai
    '125965971158216', // Ken Ken https://www.facebook.com/thaibaodat
    '129881887426347', // Balo Đẹp
    '835521976592060', // Ngoc Nguyen My
    '1723524741251993', // Duc Tin
    '490650357797276', // Nguyễn Huỳnh Liên
    '277249729330270', // Thảo GD
    '1021398451274096', // Thuỷ Gumiho
    '126728971080640', // https://www.facebook.com/kinhdothoitrang.vn
];

$fb = new \Facebook\Facebook([
    'app_id' => $config['facebook_app_id'],
    'app_secret' => $config['facebook_app_secret'],
    'default_graph_version' => 'v2.6',
    //'default_access_token' => $user['access_token'], // optional
]);



function postToWall($data, $accessToken, &$errorMessage = '') {
    global $fb;
    try {
        $response = $fb->post("/me/feed", $data, $accessToken);
        $graphNode = $response->getGraphNode();
        if (!empty($graphNode['id'])) {                   
            return $graphNode['id'];
        }
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        $errorMessage = $e->getMessage(); 
		batch_info($errorMessage);	
		exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        $errorMessage = $e->getMessage();
		batch_info($errorMessage);	
		exit;
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
    }
    return false;
}

function postToGroup($groupId, $data, $accessToken, &$errorMessage = '') {
    global $fb;
    try {
        $response = $fb->post("/{$groupId}/feed", $data, $accessToken);
        $graphNode = $response->getGraphNode();
        if (!empty($graphNode['id'])) {                   
            return $graphNode['id'];
        }
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        $errorMessage = $e->getMessage(); 
		batch_info($errorMessage);	
		exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        $errorMessage = $e->getMessage();
		batch_info($errorMessage);	
		exit;
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
    }
    return false;
}

function commentToPost($postId, $data, $accessToken, &$errorMessage = '') {
    global $fb;
    try {
        if (empty($data['message'])) {
            $data['message'] = 'thoitrang1.net';
        }       
        $response = $fb->post("/{$postId}/comments", $data, $accessToken);
        $graphNode = $response->getGraphNode();
        if (!empty($graphNode['id'])) {                   
            return $graphNode['id'];
        }
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        $errorMessage = $e->getMessage(); 
		batch_info($errorMessage);	
		exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        $errorMessage = $e->getMessage();
		batch_info($errorMessage);	
		exit;
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
    }
    return false;
}

function meCreateAlbum($data, $accessToken, &$errorMessage = '') {
    global $fb;
    try {
        $response = $fb->post("/me/albums", $data, $accessToken);
        $graphNode = $response->getGraphNode();
        if (!empty($graphNode['id'])) {                   
            return $graphNode['id'];
        }
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        $errorMessage = $e->getMessage(); 
		batch_info($errorMessage);	
		exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        $errorMessage = $e->getMessage();
		batch_info($errorMessage);	
		exit;
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
    }
    return false;
}

function groupCreateAlbum($groupId, $data, $accessToken, &$errorMessage = '') {
    global $fb;
    try {
        $response = $fb->post("/{$groupId}/albums", $data, $accessToken);
        $graphNode = $response->getGraphNode();
        if (!empty($graphNode['id'])) {                   
            return $graphNode['id'];
        }
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        $errorMessage = $e->getMessage(); 
		batch_info($errorMessage);	
		exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        $errorMessage = $e->getMessage();
		batch_info($errorMessage);	
		exit;
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
    }
    return false;
}

function addPhotoToAlbum($albumId, $data, $accessToken, &$errorMessage = '') {
    global $fb;
    try {
        $response = $fb->post("/{$albumId}/photos", $data, $accessToken);
        $graphNode = $response->getGraphNode();
        if (!empty($graphNode['id'])) {                   
            return $graphNode['id'];
        }
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        $errorMessage = $e->getMessage(); 
		batch_info($errorMessage);	
		exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        $errorMessage = $e->getMessage();
		batch_info($errorMessage);	
		exit;
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
    }
    return false;
}

function postToBlog($blogId, $data, $accessToken, &$errorMessage = '') {     
    global $config;
    try {        
        $scopes = implode(' ' , [
            \Google_Service_Oauth2::USERINFO_EMAIL, 
            \Google_Service_Blogger::BLOGGER_READONLY,
            \Google_Service_Blogger::BLOGGER
        ]);
        $client = new \Google_Client();
        $client->setClientId($config['google_app_id']);
        $client->setClientSecret($config['google_app_secret']);
        $client->setRedirectUri($config['google_app_redirect_uri']);
        $client->addScope($scopes);    
        $client->setAccessToken($accessToken);
        $service = new \Google_Service_Blogger($client); 
        $bloggerPost = new \Google_Service_Blogger_Post();
        $bloggerPost->setTitle($data['name']);
        $bloggerPost->setContent($data['content']);            
        $bloggerPost->setLabels($data['labels']);                  
        $post = $service->posts->insert($blogId, $bloggerPost);
        if ($postId = $post->getId()) {
            return $postId;
        }            
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
    }
    return false;
}