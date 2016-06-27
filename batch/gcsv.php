<?php
// php gcsv.php
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

include ('../functions.php');

function csvquote($str){
    $str = preg_replace("/[\\n\\r]+/", "", $str);
    $str = preg_replace("/\"/", "", $str);
    return '"' . trim($str) . '"';       
}
    
$link = mysql_connect('localhost', 'root', '');
if (!$link) {
    die('Not connected : ' . mysql_error());
}

// make foo the current db
$db_selected = mysql_select_db('123m', $link);
if (!$db_selected) {
    die ('Can\'t use foo : ' . mysql_error());
}

$offset = 0;
$limit = 1000;
do {
	mysql_query("SET CHARSET 'utf8'");
    $sqlCount = " 
        SELECT COUNT(*) AS cnt
        FROM users_1_2000000 A JOIN social_users B ON A.user_id = B.user_id 
        WHERE (A.email LIKE '%@gmail.com%' OR  A.email LIKE '%@yahoo%') AND B.type='FB'
    ";
    $result = mysql_query($sqlCount);  
    $cnt = 0;
    if ($result !== false) {        
        $row = mysql_fetch_assoc($result);
        $cnt = $row['cnt'];        
    }
    $sql = " 
        SELECT A.full_name AS 'Name', '' AS 'Given Name', '' AS 'Additional Name', '' AS 'Family Name', '' AS 'Yomi Name', '' AS 'Given Name Yomi', '' AS 'Additional Name Yomi', '' AS 'Family Name Yomi', '' AS 'Name Prefix', '' AS 'Name Suffix', '' AS 'Initials', '' AS 'Nickname', '' AS 'Short Name', '' AS 'Maiden Name', '' AS 'Birthday', '' AS 'Gender', '' AS 'Location', '' AS 'Billing Information', '' AS 'Directory Server', '' AS 'Mileage', '' AS 'Occupation', '' AS 'Hobby', '' AS 'Sensitivity', '' AS 'Priority', '' AS 'Subject', '' AS 'Notes', '* My Contacts' AS 'Group Membership', email AS 'E-mail 1 - Type', '' AS 'E-mail 1 - Value', 'Mobile' AS 'Phone 1 - Type', mobile_number AS 'Phone 1 - Value', '' AS 'Address 1 - Type', '' AS 'Address 1 - Formatted', '' AS 'Address 1 - Street', '' AS 'Address 1 - City', '' AS 'Address 1 - PO Box', '' AS 'Address 1 - Region', '' AS 'Address 1 - Postal Code', '' AS 'Address 1 - Country', '' AS 'Address 1 - Extended Address'
		FROM users_1_2000000 A JOIN social_users B ON A.user_id = B.user_id 
		WHERE (A.email LIKE '%@gmail.com%' OR  A.email LIKE '%@yahoo%') AND B.type='FB'
		ORDER by A.email
        LIMIT {$limit}
        OFFSET {$offset}
    ";
    $result = mysql_query($sql);    
    if ($result !== false) {
        $csv = array();    
        while ($row = mysql_fetch_assoc($result)) {           
            if (empty($csv)) {
                $csv[] = implode(',', array_keys($row)); 
            }       
            $csv[] = implode(',', array_values($row)) . ',';       
        }   
        $fileName = "g_" . $offset . '_' . ($offset + $limit) . '.csv';
        file_put_contents(__DIR__ . DS . 'google_csv' . DS . $fileName, implode(PHP_EOL, $csv));    
		echo PHP_EOL . $fileName . ' -> Done';
    } else {
        break;
    }
    $offset += $limit;
}
while ($offset < $cnt);
mysql_close($link);
echo PHP_EOL . 'Done';
