<?php
// send_email.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
require_once 'email_config.php';


function mailk($logData){

$config = require_once 'mail_config.php';
 

if ($config['send_email']) {

$mail = createMailer();
// 将 JSON 数据解码为数组
$logArray = json_decode($logData, true);

// 生成 HTML 表格
$htmlTable = '<table border="1" cellpadding="5" style="border-collapse: collapse;">';
$htmlTable .= '<tr><th>Field</th><th>Value</th></tr>';
foreach ($logArray['cont'] as $key => $value) {
    $htmlTable .= '<tr><td>' . htmlspecialchars($key) . '</td><td>' . htmlspecialchars($value) . '</td></tr>';
}
$htmlTable .= '</table>';


try {
    // 发件人
 $mail->setFrom('your_email@example.com', '沐雨酆臻防火墙');
    $mail->addAddress('your_email@example.com', '沐雨酆臻');  // 收件人
    $mail->Subject = '沐雨酆臻Waf防火墙';
    $mail->Body = '拦截到以下非法的数据:<br><br>' . $htmlTable;
    $mail->isHTML(true);  // 启用 HTML 内容

    // 发送邮件
    $mail->send();
} catch (PHPMailer\PHPMailer\Exception $e) {
    echo "邮件发送失败 Error: {$mail->ErrorInfo}";
}
}

}

/****/

?>