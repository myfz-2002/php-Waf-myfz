<?php
// email_config.php

// 邮箱配置
$emailConfig = [
    'host' => 'smtp.example.com',
    'port' => 587,
    'username' => 'your_email@example.com',
    'password' => 'your_password',
    'encryption' => PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS,
    'send_email' => false, // 发信开关，true 表示开启，false 表示关闭  默认关闭
];

// 587端口和25端口通用 PHPMailer::ENCRYPTION_STARTTLS
//465端口 PHPMailer::ENCRYPTION_SMTPS

// 返回配置数组
return $emailConfig;