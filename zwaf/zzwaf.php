<?php
// WAF防火墙，作者QQ：2352164397

$webscan = true;// 防火墙开关
$white_ip = [];// IP白名单
$white_directory = '';// 后台白名单，多个用|
$white_url = '';// URL白名单，多个用|
$visits = 10;// 同一IP并发数，为0不限制
$max_visits = 50;// 同一网站每分钟最大访问数，为0不限制，超过设定数就会进行用户验证
$shield_time = 0;// 屏蔽时间，单位：秒，为0不限制
$shield_ua = [];// 过滤UA关键词
$webscan_post = true;// POST提交过滤
$webscan_get = true;// GET提交过滤
$webscan_cookie = true;// COOKIE提交过滤
$webscan_referrer = true;// REFERRER提交过滤
$rules = [
    '\.\./', //禁用包含 ../ 的参数
    '\<\?', //禁止php脚本出现
    '\s*or\s+.*=.*', //匹配' or 1=1 ,防止sql注入
    'select([\s\S]*?)(from|limit)', //防止sql注入
    '(?:(union([\s\S]*?)select))', //防止sql注入
    'having|updatexml|extractvalue', //防止sql注入
    'sleep\((\s*)(\d*)(\s*)\)', //防止sql盲注
    'benchmark\((.*)\,(.*)\)', //防止sql盲注
    'base64_decode\(', //防止sql变种注入
    '(?:from\W+information_schema\W)', //防止sql注入
    '(?:(?:current_)user|database|schema|connection_id)\s*\(', //防止sql注入
    '(?:etc\/\W*passwd)', //防止窥探linux用户信息
    'into(\s+)+(?:dump|out)file\s*', //禁用mysql导出函数
    'group\s+by.+\(', //防止sql注入
    '(?:define|eval|file_get_contents|include|require|require_once|shell_exec|phpinfo|system|passthru|preg_\w+|execute|echo|print|print_r|var_dump|(fp)open|alert|showmodaldialog)\(', //禁用webshell相关某些函数
    '(gopher|doc|php|glob|file|phar|zlib|ftp|ldap|dict|ogg|data)\:\/', //防止一些协议攻击
    '\$_(GET|post|cookie|files|session|env|phplib|GLOBALS|SERVER)\[', //禁用一些内置变量,建议自行修改
    '\<(iframe|script|body|img|layer|div|meta|style|base|object|input)', //防止xss标签植入
    '(onmouseover|onerror|onload|onclick)\=', //防止xss事件植入
    '\|\|.*(?:ls|pwd|whoami|ll|ifconfog|ipconfig|&&|chmod|cd|mkdir|rmdir|cp|mv)', //防止执行shell
    '\s*and\s+.*=.*' //匹配 and 1=1
];// 提交过滤拦截规则
$error = false;
$verify = false;
$time_second = date('Y-m-d H:i:s');
$time_minute = date('Y-m-d H:i:00');
$realip = get_real_ip();
// echo $realip;
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$requestUri = $_SERVER['REQUEST_URI'];
$currentUrl = $protocol . $host . $requestUri;
header('Access-Control-Allow-Origin: *');
include('Cache.class.php');
$cache = new Cache($_SERVER['DOCUMENT_ROOT'].'/cache');

// 获取验证码
if(!empty($_GET['act']) && $_GET['act'] === 'getCaptcha'){
ob_clean(); // 清除缓冲区
// 设置验证码字符的集合
$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$captcha_text = '';

// 生成随机的验证码字符串
for ($i = 0; $i < 4; $i++) {
    $index = rand(0, strlen($characters) - 1);
    $captcha_text .= $characters[$index];
}

// 将验证码字符串保存到缓存中
$cache->set($realip.'_captcha',$captcha_text);

// 创建图像
$image_width = 100;
$image_height = 50;
$image = imagecreatetruecolor($image_width, $image_height);

// 设置背景颜色
$background_color = imagecolorallocate($image, 255, 255, 255);
imagefilledrectangle($image, 0, 0, $image_width, $image_height, $background_color);

// 设置文本颜色和字体大小
$text_color = imagecolorallocate($image, 0, 0, 0);
$font_size = 20;

// 如果有自定义字体文件，可以指定路径，否则使用默认样式
$font_path = 'Arial.ttf'; // 确保路径正确，你可以下载字体文件或使用系统字体 请使用绝对路径

if (file_exists($font_path)) {
    imagettftext($image, $font_size, 0, 15, 35, $text_color, $font_path, $captcha_text);
} else {
    // 使用默认字体
    imagestring($image, 5, 35, 15, $captcha_text, $text_color);
}

// 添加一些干扰元素，如线条和点
for ($i = 0; $i < 10; $i++) {
    $line_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    imageline($image, rand(0, $image_width), rand(0, $image_height), rand(0, $image_width), rand(0, $image_height), $line_color);
}

for ($i = 0; $i < 100; $i++) {
    $dot_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    imagesetpixel($image, rand(0, $image_width), rand(0, $image_height), $dot_color);
}

// 输出图像
header('Content-Type: image/png');
imagepng($image);

// 销毁图像资源
imagedestroy($image);
exit;
}else if(!empty($_GET['act']) && $_GET['act'] === 'checkCaptcha'){
    $arr = ['code' => 0,'msg' => '失败'];
    if(empty($_POST['captchaInput'])){
        $arr['msg'] = '验证码不能为空，请输入验证码';
    }else if(strtolower($_POST['captchaInput']) !== strtolower($cache->get($realip.'_captcha'))){
        $arr['msg'] = '验证码错误，请重新输入';
    }else{
        $cache->set($realip.'_captcha','ok');
        $arr['code'] = 1;
        $arr['msg'] = '验证码正确';
    }
    exit(json_encode($arr));
}

if($webscan && !in_array($realip,$white_ip) && ($white_directory && !preg_match('/\/('.str_replace('/','\/',$white_directory).')\//i',$requestUri) || !$white_directory) && ($white_url && !preg_match('/('.str_replace('/','\/',$white_url).')/i',$currentUrl) || !$white_url)){
    // 限制IP并发
    if($visits){
        $arr = $cache->get($realip);
        if(!empty($arr['shield_time'])){
            if(time() >= $arr['shield_time']){
                $cache->delete($realip);
            }else{
                $error = true;
            }
        }else{
            if(empty($arr[$time_second])){
                $arr[$time_second] = 0;
            }
            $arr[$time_second] = $arr[$time_second]+1;
            if($arr[$time_second] >= $visits){
                $error = true;
                $arr = ['shield_time' => time()+$shield_time];
            }
            $cache->set($realip,$arr);
        }
    }

    // 限制同一网站访问
    if($max_visits){
        $arr = $cache->get($_SERVER['HTTP_HOST']);
        if(!empty($arr['shield_time'])){
            if(time() >= $arr['shield_time']){
                $cache->delete($_SERVER['HTTP_HOST']);
            }else{
                $verify = true;
            }
        }else{
            if(empty($arr[$time_minute])){
                $arr[$time_minute] = 0;
            }
            $arr[$time_minute] = $arr[$time_minute]+1;
            if($arr[$time_minute] >= $max_visits){
                $verify = true;
                $arr = ['shield_time' => time()+$shield_time];
            }
            $cache->set($_SERVER['HTTP_HOST'],$arr);
        }
    }

    // 过滤UA
    if($shield_ua){
        foreach ($shield_ua as $v){
            if(stripos($_SERVER['HTTP_USER_AGENT'],$v) !== false){
                $error = true;break;
            }
        }
    }
    
    // 提交过滤
    foreach ($rules as $v){
        if($webscan_post){
            $post = !empty($_POST) ? implode('&',$_POST) : file_get_contents("php://input");
            if($post && preg_match('^'.$v.'^i',$post)){
                $error = true;break;
            }
        }
        if($webscan_get){
            $get = !empty($_GET) ? implode('&',$_GET) : file_get_contents("php://input");
            if($get && preg_match('^'.$v.'^i',$get)){
                $error = true;break;
            }
        }
        if($webscan_cookie){
            $cookie = !empty($_COOKIE) ? implode('&',$_COOKIE) : '';
            if($cookie && preg_match('^'.$v.'^i',$cookie)){
                $error = true;break;
            }
        }
        if($webscan_referrer){
            $referrer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            if($referrer && preg_match('^'.$v.'^i',$referrer)){
                $error = true;break;
            }
        }
    }
    
    // 缓存文件每天清理一次
    if($cache->get('ip_data_add_time') !== date('Y-m-d')){
        $cache->clear();
        $cache->set('ip_data_add_time',date('Y-m-d'));
    }
}

// 访问量过大用户验证
if($verify){
    if($cache->get($realip.'_captcha') !== 'ok'){
        include 'verify.html';exit;
    }
}

// 屏蔽403页面
if($error){
    http_response_code(403);
    echo '<html>
<head><title>403 Not Found</title></head>
<body>
<center><h1>403 Not Found</h1></center>
<hr><center>nginx</center>
</body>
</html>';
    exit;
}

function get_real_ip()
{
    static $realip = NULL;
    if ($realip !== NULL) {
        return $realip;
    }

    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($arr as $ip) {
                $ip = trim($ip);
                if ($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $realip = $_SERVER['REMOTE_ADDR'];
        } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('REMOTE_ADDR')) {
            $realip = getenv('REMOTE_ADDR');
        } else if (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        }
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
    return $realip;
}

function doRmdir($dirname, $self = true)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    if ($dir) {
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            doRmdir($dirname . '/' . $entry);
        }
    }
    $dir->close();
    $self && rmdir($dirname);
}