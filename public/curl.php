<?php


$headers = array("Content-Type:multipart/form-data");

$url = 'http://api.vuongquocbalo.com/admins/login';
$posts = array(
	'email' => 'root@gmail.com',
	'password' => '123456'
);

$url = 'http://chothoitrang.com/home/index/ajaxcathome';
$posts = array(
	'p' => isset($_GET['p']) ? $_GET['p'] : 1,
	'id' => '166',
	'sortlist' => 'product_new_desc',
);
$ch = curl_init();
$options = array(
	CURLOPT_URL => $url,
	CURLOPT_HEADER => false,
	CURLOPT_POST => true,
	CURLOPT_HTTPHEADER => $headers,
	CURLOPT_POSTFIELDS => $posts,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SAFE_UPLOAD => false,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_TIMEOUT => 5*60*60,
);
curl_setopt_array($ch, $options);
$response = curl_exec($ch);
$errno = curl_errno($ch);
if (empty($errno)) {
	curl_close($ch);
}
print_r($response); exit;
