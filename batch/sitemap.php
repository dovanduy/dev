<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

include ('../functions.php');

$env = 'development'; // development, production
$config = [
    'development' => [
        'username' => 'root',
        'password' => '',    
        'dbname' => 'dev2'
    ],
    'production' => [
        'username' => 'vuong761_balo',
        'password' => 'balo@2016', 
        'dbname' => 'vuong761_vqbalo2016'
    ]
];
$config = $config[$env];

$link = mysql_connect('localhost', $config['username'], $config['password']);
if (!$link) {
    die('Not connected : ' . mysql_error());
}

// make foo the current db
$db_selected = mysql_select_db($config['dbname'], $link);
if (!$db_selected) {
    die ('Can\'t use foo : ' . mysql_error());
}
$sql = " 
    SELECT * FROM url_ids 
    WHERE product_id IS NOT NULL 
    OR page_id IS NOT NULL
    OR category_id IS NOT NULL
    OR brand_id IS NOT NULL
";
$result = mysql_query($sql);
$sitemap = array();
if ($result !== false) {    
    while ($row = mysql_fetch_assoc($result)) {         
        $sitemap[] = "<url><loc>http://vuongquocbalo.com/{$row['url']}</loc></url>";       
    }
    file_put_contents(__DIR__ . DS . 'sitemap.xml', implode(PHP_EOL, $sitemap));    
}
mysql_close($link);
echo PHP_EOL . 'Done';
