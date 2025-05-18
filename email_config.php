<?php
// email_config.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
require_once 'mail/src/Exception.php';
require_once 'mail/src/PHPMailer.php';
require_once 'mail/src/SMTP.php';
//推荐使用绝对路径
function createMailer() {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
$config = require_once 'mail_config.php';
 

if ($config['send_email']) {
    
        // SMTP 配置
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port = $config['port'];
        return $mail;
    }
}

?>