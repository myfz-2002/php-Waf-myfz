<?php

header('Content-type: text/json;charset=utf-8');
$dir = "./pvIP/".date('Y-m-d');
    $file = $dir."/ip.txt";
if(is_file($file)){

$pos=file_get_contents($file); 
$jsons=json_encode($pos,480);
$jsons=json_decode($jsons,true);
$ddx=sj($jsons,'"ip":"','","time":"');
echo $ddx;
}




//截取 原文 头部 尾部

function sj($str,$front,$back){
    if($front == null){
        return ($ret = substr($str, 0, 0-(slg($str)-siof($str, $back))) ) === "" ? 0 : $ret;
    }elseif($back == null){
        if(siof($str, $front) === false) return 0;
        return substr($str, slg($front)+siof($str, $front));
    }else{
        return sj(sj($str, $front, null), null, $back);
    }
}
//获取位置
function siof($str,$find,$pos=0){
    if(strpos($str,$find,$pos)===false){
        return 0;
    }else{
        return strpos($str,$find,$pos);
    }
}
//获取长度
function slg($str){
    return strlen($str);
}

//获取总数量
function sgszl($str,$strsa){
return substr_count($str, $strsa);  
}

//
function sgsz($str,$strs,$pos=1){
$firstSlashPos = strpos($str,$strs);  
if ($firstSlashPos !== false) {  
    $secondSlashPos = strpos($str,$strs, $firstSlashPos + $pos);  
    if ($secondSlashPos !== false) {  
        return $secondSlashPos;  
    } 
}
}


?>