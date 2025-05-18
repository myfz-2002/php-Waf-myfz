<?php
//测试发信 可以删除
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
$logData = json_encode(array(
    'cont' => array(
        'ip' => "",
        'time' => strftime("%Y-%m-%d %H:%M:%S"),
        'page' => "",
        'method' => "",
        'rkey' => 'example_key',
        'rdata' => 'example_value',
        'user_agent' => "",
        'request_url' =>""
    )
));

require_once("send_email.php");


echo  mailk($logData);



?>