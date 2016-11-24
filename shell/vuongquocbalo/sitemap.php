<?php
error_reporting(1);
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

include ('../../functions.php');
$env = 'development'; // development, production
$config = [
    'development' => [
        'username' => 'root',
        'password' => '',    
        'dbname' => 'ken_live'
    ],
    'production' => [
        'username' => 'vuong761_ken',
        'password' => 'balo@2016', 
        'dbname' => 'vuong761_vqbalo2016'
    ]
];
$config = $config[$env];

$link = mysql_connect('localhost', $config['username'], $config['password']);
if (!$link) {
    die('Not connected : ' . mysql_error());
} else {
    echo PHP_EOL . 'connected db ok';
}

// make foo the current db
$db_selected = mysql_select_db($config['dbname'], $link);
if (!$db_selected) {
    die ('Can\'t use foo : ' . mysql_error());
} else {
    echo PHP_EOL . 'selected db ok';
}

echo PHP_EOL . 'BEGIN';

$websiteId = 1;
$domainUrl = 'http://vuongquocbalo.com';

mysql_query("SET NAMES 'UTF8'");
        
$sql = " 
    SELECT product_category_locales.category_id, product_category_locales.name
    FROM product_categories JOIN product_category_locales ON product_categories.category_id = product_category_locales.category_id
    WHERE product_categories.website_id = {$websiteId}
        AND product_categories.active = 1
	ORDER BY product_categories.sort;
";
$resultCategories = mysql_query($sql);
$categories = [];
if ($resultCategories !== false) {    
    while ($row = mysql_fetch_assoc($resultCategories)) {
		$row['category_url'] = $domainUrl . '/' . name_2_url($row['name']);	
		$categories[] = $row;    
    }       
}
$subCategores = [
	1 => [2, 3, 20], 
	4 => [5, 6, 21], 
	7 => [8, 9, 10], 
	11 => [12, 13], 
	14 => [15, 16, 17], 	
];
$sitemap = array();
$sql = " 
    SELECT * FROM url_ids 
    WHERE product_id IS NOT NULL 
    OR page_id IS NOT NULL
    OR category_id IS NOT NULL
    OR brand_id IS NOT NULL
";
$result1 = mysql_query($sql);
if ($result1 !== false) {    
    while ($row = mysql_fetch_assoc($result1)) {    
        $sitemap[] = "<url><loc>http://vuongquocbalo.com/{$row['url']}</loc></url>";       
    }       
}
echo PHP_EOL . 'R1:' . count($sitemap);

$sql = " 
    SELECT input_fields.field_id, input_option_locales.name 
	FROM input_fields 
		JOIN input_options ON input_fields.field_id = input_options.field_id
		JOIN input_option_locales ON input_options.option_id = input_option_locales.option_id
	WHERE input_fields.website_id = {$websiteId}
";
$result2 = mysql_query($sql);
if ($result2 !== false) {  	
    while ($row = mysql_fetch_assoc($result2)) {
		foreach ($categories as $category) {
			if (isset($subCategores[$category['category_id']])) {
				$categoryId = implode(',', $subCategores[$category['category_id']]);
			} else {
				$categoryId = $category['category_id'];
			}
            $sql = "
                SELECT COUNT(*) AS cnt 
                FROM product_has_fields JOIN product_has_categories ON product_has_fields.product_id = product_has_categories.product_id
                WHERE product_has_categories.category_id IN ({$categoryId})
                AND product_has_fields.field_id = {$row['field_id']}
            ";
            $checkResult = mysql_query($sql);
            if ($checkResult !== false && $find = mysql_fetch_assoc($checkResult)) {  
                if (!empty($find['cnt']) && $find['cnt'] > 0) {
					$value = name_2_url($row['name']);					
					$loc = "<url><loc>{$category['category_url']}/{$value}</loc></url>";
					if (!in_array($loc, $sitemap)) { //echo PHP_EOL . $loc;
						$sitemap[] = $loc;
					}
                }
            }
        }
    }   
}
echo PHP_EOL . 'R2:' . count($sitemap);

$sql = " 
    SELECT DISTINCT field_id, value_search  
    FROM product_has_fields 
    WHERE (value_id IS NULL OR value_id = '0')
";
$result3 = mysql_query($sql);
if ($result3 !== false) {    
	$fields = [];
	while ($row = mysql_fetch_assoc($result3)) {
		$values = explode(',', $row['value_search']);
		foreach ($values as $value) {
			if (!isset($fields[$row['field_id']])) {
				$fields[$row['field_id']] = [];
			}
			if (!in_array($value, $fields[$row['field_id']])) {
				$fields[$row['field_id']][] = $value;
			}
		}
	}	
    foreach ($fields as $fieldId => $values) {
		foreach ($categories as $category) {
			if (isset($subCategores[$category['category_id']])) {
				$categoryId = implode(',', $subCategores[$category['category_id']]);
			} else {
				$categoryId = $category['category_id'];
			}
            $sql = "
                SELECT COUNT(*) AS cnt 
                FROM product_has_fields JOIN product_has_categories ON product_has_fields.product_id = product_has_categories.product_id
                WHERE product_has_categories.category_id IN ({$categoryId})
                AND product_has_fields.field_id = {$fieldId}
            "; 
            $checkResult = mysql_query($sql);
            if ($checkResult !== false && $find = mysql_fetch_assoc($checkResult)) {  
                if (!empty($find['cnt']) && $find['cnt'] > 0) {					
					foreach ($values as $value) {
						$value = str_replace(['[', ']'], '', $value);
						$loc = "<url><loc>{$category['category_url']}/{$value}</loc></url>";
						if (!in_array($loc, $sitemap)) {
							$sitemap[] = $loc;
						}
					}
                }
            }
        }
    }     
}
echo PHP_EOL . 'R3:' . count($sitemap);

$sql = "
    SELECT brand_locales.brand_id, brand_locales.name
	FROM brands JOIN brand_locales ON brands.brand_id = brand_locales.brand_id 
	WHERE brands.website_id = {$websiteId}
";
$result4 = mysql_query($sql);
if ($result4 !== false) {    
    while ($row = mysql_fetch_assoc($result4)) {
        $value = name_2_url($row['name']);
        foreach ($categories as $category) {
            $sql = "
                SELECT COUNT(*) AS cnt 
                FROM products JOIN product_has_categories ON products.product_id = product_has_categories.product_id
                WHERE product_has_categories.category_id = {$category['category_id']}
                AND products.brand_id = {$row['brand_id']}
            "; 
            $checkResult = mysql_query($sql);
            if ($checkResult !== false && $find = mysql_fetch_assoc($checkResult)) {  
                if (!empty($find['cnt']) && $find['cnt'] > 0) {					
					$loc = "<url><loc>{$category['category_url']}/{$value}</loc></url>";
					if (!in_array($loc, $sitemap)) {
						$sitemap[] = $loc;
					}
                }
            }
        }
    }    
}
echo PHP_EOL . 'R4:' . count($sitemap);

$content = '<?xml version="1.0" encoding="UTF-8"?>
<urlset
  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . implode(PHP_EOL, $sitemap) . '</urlset>';
app_file_put_contents('sitemap.xml', $content);
mysql_close($link);
echo PHP_EOL . 'END';