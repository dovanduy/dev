<?php

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
$limit = 500;
do {
    $sqlCount = " 
        SELECT COUNT(*) AS cnt
        FROM users_1_2000000 JOIN social_users ON users_1_2000000.user_id =  social_users.user_id
        WHERE social_users.type = 'FB'
    ";
    $result = mysql_query($sqlCount);  
    $cnt = 0;
    if ($result !== false) {        
        $row = mysql_fetch_assoc($result);
        $cnt = $row['cnt'];         
    }
    $sql = " 
        SELECT full_name AS 'First Name', '' AS 'Middle Name', '' AS 'Last Name', '' AS 'Title', '' AS 'Suffix', '' AS 'Initials', '' AS 'Web Page', '' AS 'Gender', '' AS 'Birthday', '' AS 'Anniversary', '' AS 'Location', '' AS 'Language', '' AS 'Internet Free Busy', '' AS 'Notes', email AS 'E-mail Address', '' AS 'E-mail 2 Address', '' AS 'E-mail 3 Address', '' AS 'Primary Phone', '' AS 'Home Phone', '' AS 'Home Phone 2', '' AS 'Mobile Phone', '' AS 'Pager', '' AS 'Home Fax', '' AS 'Home Address', '' AS 'Home Street', '' AS 'Home Street 2', '' AS 'Home Street 3', '' AS 'Home Address PO Box', '' AS 'Home City', '' AS 'Home State', '' AS 'Home Postal Code', '' AS 'Home Country', '' AS 'Spouse', '' AS 'Children', '' AS 'Manager\'s Name', '' AS 'Assistant\'s Name', '' AS 'Referred By', '' AS 'Company Main Phone', '' AS 'Business Phone', '' AS 'Business Phone 2', '' AS 'Business Fax', '' AS 'Assistant\'s Phone', '' AS 'Company', '' AS 'Job Title', '' AS 'Department', '' AS 'Office Location', '' AS 'Organizational ID Number', '' AS 'Profession', '' AS 'Account', '' AS 'Business Address', '' AS 'Business Street', '' AS 'Business Street 2', '' AS 'Business Street 3', '' AS 'Business Address PO Box', '' AS 'Business City', '' AS 'Business State', '' AS 'Business Postal Code', '' AS 'Business Country', '' AS 'Other Phone', '' AS 'Other Fax', '' AS 'Other Address', '' AS 'Other Street', '' AS 'Other Street 2', '' AS 'Other Street 3', '' AS 'Other Address PO Box', '' AS 'Other City', '' AS 'Other State', '' AS 'Other Postal Code', '' AS 'Other Country', '' AS 'Callback', '' AS 'Car Phone', '' AS 'ISDN', '' AS 'Radio Phone', '' AS 'TTY/TDD Phone', '' AS 'Telex', '' AS 'User 1', '' AS 'User 2', '' AS 'User 3', '' AS 'User 4', '' AS 'Keywords', '' AS 'Mileage', '' AS 'Hobby', '' AS 'Billing Information', '' AS 'Directory Server', '' AS 'Sensitivity', 'Normal' AS 'Priority', '' AS 'Private', '' AS 'Categories'
        FROM users_1_2000000 JOIN social_users ON users_1_2000000.user_id =  social_users.user_id
        WHERE social_users.type = 'FB'
        ORDER BY email
        LIMIT {$limit}
        OFFSET {$offset}
    ";
    $result = mysql_query($sql);    
    if ($result !== false) {
        $csv = array();    
        while ($row = mysql_fetch_assoc($result)) {
            $row['First Name'] = explode('@', $row['E-mail Address'])[0];
            if (empty($csv)) {
                $csv[] = implode(',', array_keys($row)); 
            }       
            $csv[] = implode(',', array_values($row)) . ',';       
        }   
        $fileName = "contact_" . $offset . '_' . ($offset + $limit) . '.csv';
        file_put_contents(__DIR__ . DS . 'outlook_csv' . DS . $fileName, implode(PHP_EOL, $csv));    
    } else {
        break;
    }
    $offset += $limit;
}
while ($offset < $cnt);
mysql_close($link);
echo PHP_EOL . 'Done';
