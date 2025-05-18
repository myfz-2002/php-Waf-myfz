<?php
// blacklist.php

// 数据库文件路径 请使用绝对路径
$dbFile = 'blacklist.db';

// 创建数据库连接（如果数据库不存在，则会自动创建）
try {
    $pdo = new PDO("sqlite:$dbFile");
    // 设置错误模式为异常
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 创建表（如果不存在）
    $pdo->exec("CREATE TABLE IF NOT EXISTS blacklist (
        ip VARCHAR(45) PRIMARY KEY
    )");
} catch (PDOException $e) {
    die("Could not connect to the database $dbFile :" . $e->getMessage());
}

/**
 * 将IP地址添加到黑名单中
 *
 * @param string $ip IP地址
 * @return bool 是否成功添加
 */
 
 
 /**
 * 将IP地址添加到黑名单中（如果尚不存在）
 *
 * @param string $ip IP地址
 * @return bool 是否成功添加（true表示新添加，false表示IP已存在或添加失败）
 */
function addToBlacklist($ip) {
    global $pdo;
    try {
        // 检查IP地址是否已存在
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM blacklist WHERE ip = :ip");
        $stmtCheck->execute([':ip' => $ip]);
        $exists = $stmtCheck->fetchColumn() > 0;
        
        if (!$exists) {
            // 如果IP不存在，则插入新记录
            $stmtInsert = $pdo->prepare("INSERT INTO blacklist (ip) VALUES (:ip)");
            $stmtInsert->execute([':ip' => $ip]);
            return $stmtInsert->rowCount() > 0; // 如果返回true，表示IP是新添加的
        } else {
            // 如果IP已存在，则不执行插入操作
            return false; // 表示IP已存在
        }
    } catch (PDOException $e) {
        // 记录错误日志或处理异常
        // 可以在这里添加日志记录代码，例如：error_log($e->getMessage());
        return false; // 添加失败
    }
}
 
 
 /**
function addToBlacklist($ip) {
    global $pdo;
    try {
        // 插入IP地址到黑名单表中
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO blacklist (ip) VALUES (:ip)");
        $stmt->execute([':ip' => $ip]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        // 记录错误日志或处理异常
        return false;
    }
}

/**
 * 检查IP地址是否在黑名单中
 *
 * @param string $ip IP地址
 * @return bool 是否在黑名单中
 */
function isBlacklisted($ip) {
    global $pdo;
    try {
        // 查询黑名单表
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM blacklist WHERE ip = :ip");
        $stmt->execute([':ip' => $ip]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        // 记录错误日志或处理异常
        return false;
    }
}




/**
 * 从黑名单中删除IP地址
 *
 * @param string $ip IP地址
 * @return bool 是否成功删除（true表示删除成功，false表示IP不存在或删除失败）
 */
function removeFromBlacklist($ip) {
    global $pdo;
    try {
        // 准备并执行删除语句
        $stmt = $pdo->prepare("DELETE FROM blacklist WHERE ip = :ip");
        $stmt->execute([':ip' => $ip]);
        
        // 检查是否实际删除了行
        $deleted = $stmt->rowCount() > 0;
        
        return $deleted; // 如果返回true，表示IP被成功删除；如果返回false，表示IP不存在或删除失败
    } catch (PDOException $e) {
        // 记录错误日志或处理异常
        // 可以在这里添加日志记录代码，例如：error_log($e->getMessage());
        return false; // 删除失败
    }
}




// 在其他文件中包含此文件并使用函数
// include 'blacklist.php';
// if (isBlacklisted($_SERVER['REMOTE_ADDR'])) {
//     // 处理黑名单用户访问，例如重定向到127.0.0.1
//     header('Location: http://127.0.0.1');
//     exit();
// }
?>