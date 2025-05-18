<?php
set_time_limit(0);
ignore_user_abort(true);//设置与客户机断开是否会终止执行
fastcgi_finish_request();//提高请求的处理速度

$ip= $_SERVER['PHP_SELF'];
$url=$_SERVER["REQUEST_URI"];


$dir = "./pvIP/".date('Y-m-d');
    $file = $dir."/ip.txt";
    $ip33 = $_SERVER["REMOTE_ADDR"];
    $userAT = strtolower($_SERVER['HTTP_USER_AGENT']);
    $time=date("Y-m-d H:i:s");
    
foreach($_GET as $key=>$value) {
      
      echo $key;
      echo $value;
      $content = "ip地址:\r\n getvalue:".$value." \r\n时间:".$time."\r\ngetkey:".$key."\r\n ↑《".$url."]\r\n".$webscan_referer." \r\n\r\n";
    
    }
    
    
    foreach($_POST as $key=>$value) {
      echo $key;
      echo $value;
      
$pos=file_get_contents("php://input"); 
      $content = "\r\n".$pos;
    
    }


if (!file_exists($dir)) {
    mkdir($dir, 0755, true);
} 
$fp=fopen($file,"a+");
 	fputs($fp,$content);//写入新的计数
 	

    fclose($fp);


?>