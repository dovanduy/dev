<?php
file_put_contents('/home/vuong761/public_html/data/cache/test1.txt', 'Hello');
$ok = chmod('/home/vuong761/public_html/data/cache/test1.txt', 777);
var_dump($ok);