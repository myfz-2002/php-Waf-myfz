<?php
error_reporting(0);
define('IN_CRONLITE', true);
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');
define('SYS_KEY', 'zmtd');
define('CC_Defender', 1); //防CC攻击开关(1为session模式)

date_default_timezone_set("PRC");

$date = date("Y-m-d H:i:s");

session_start();

if(CC_Defender!=0)

if(is_file(SYSTEM_ROOT.'360safe/360webscan.php')){//360网站卫士
require_once(SYSTEM_ROOT.'360safe/360webscan.php');
}

include_once(SYSTEM_ROOT."360safe/360_safe3.php");
include_once(SYSTEM_ROOT."360safe/waf.php");


include_once(SYSTEM_ROOT."function.php");
include_once(SYSTEM_ROOT."black/blacklist.php");
include_once(SYSTEM_ROOT."zwaf/zzwaf.php");





if (isBlacklisted($_SERVER['REMOTE_ADDR'])||isBlacklisted(getRealIp())) {
    header('HTTP/1.1 403 Forbidden');
    http_response_code(444);
    exit();
}
/*****/
?>