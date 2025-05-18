### php-Waf-myfz

## 方法一:
直接使用waf--- 在网站的所有php文件或者程序的index.php头部放入 
```php
require_once("你的文件目录/common.php");
//然后 接着下一行放入
if (isBlacklisted($_SERVER['REMOTE_ADDR'])||isBlacklisted(getRealIp())) {
    header('HTTP/1.1 403 Forbidden');
    http_response_code(444);
    exit();
}
```
当黑名单ip再次访问你的网站时会使无法访问


## 方法二:

在当前使用php的php.ini 文件末尾加上
auto_prepend_file = 你的文件目录/common.php
保存并重启php
然后跟方法一 一样在文件头部放入
```php
if (isBlacklisted($_SERVER['REMOTE_ADDR'])||isBlacklisted(getRealIp())) {
    header('HTTP/1.1 403 Forbidden');
    http_response_code(444);
    exit();
}
```

推荐使用方法二
------------------------------------------------

根据你自己的情况配置邮箱
```php
$emailConfig = [
    'host' => 'smtp.example.com', 你的邮箱smtp
    'port' => 587,默认587 
    'username' => 'your_email@example.com',你的邮箱用户名
    'password' => 'your_password',你创建的邮箱授权码
    'encryption' => PHPMailer::ENCRYPTION_STARTTLS, 是一种用于加密 SMTP 通信的协议
    'send_email' => false, 发信开关，true 表示开启，false 表示关闭 默认是关闭的
];
```

删除黑名单ip方法
http://你的域名/black/remove.php?ip=需要删除的ip

也可以手动增加ip 不过需要去删除注释


主要文件360safe
邮箱文件:https://github.com/PHPMailer/PHPMailer
其它的都内置文件内
