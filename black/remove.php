<?php
//这里是删除黑名单ip;
include "blacklist.php";

$ugw=$_GET['ip'];

if($ugw!==""||$ugw!==null) {
    if(removeFromBlacklist($ugw)){
    echo "ip删除成功 已踢出黑名单";
    }else{
    echo "删除失败 ip不存在";
    }
}
?>