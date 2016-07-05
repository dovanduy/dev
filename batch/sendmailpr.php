<?php

// php sendmailpr.php
include_once 'base.php';

// Test
//$contact['smtp'] = 6;
//$contact['email'] = 'thailvn@gmail.com';
//$result = call('/contactlists/sendmail', $contact, $errors);
//if (!empty($errors)) {
//    p($errors, 1);
//} else {
//    batch_info('Sent to email: ' . $contact['email']);
//}
//exit;


$docRoot = dirname(getcwd());
$param = [
    'limit' => 30,
];
$contacts = call('/contactlists/all', $param);
batch_info('Total contact: ' . count($contacts));
foreach ($contacts as $i => $contact) {
    $contact['smtp'] = 6;
    $result = call('/contactlists/sendmail', $contact, $errors);
    if (!empty($errors)) {
        p($errors, 1);
    }
    batch_info('[' . str_pad($i+1, 2, '0', STR_PAD_LEFT) . '] Sent to email: ' . $contact['email']);
    sleep(3);
}
batch_info('Done');
exit;
