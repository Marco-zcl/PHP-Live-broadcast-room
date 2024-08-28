<?php
session_start();
$viewersFile = 'viewers.txt';

// 函数来安全地增加观看人数
function incrementViewers() {
    global $viewersFile;

    // 获取文件句柄并尝试锁定
    $handle = fopen($viewersFile, 'r+');
    if (flock($handle, LOCK_EX)) { // 获取独占锁
        try {
            // 读取当前观看人数
            $viewers = file_get_contents($viewersFile);
            $viewers = intval($viewers) + 1;

            // 清空文件内容并写入新的观看人数
            ftruncate($handle, 0);
            fwrite($handle, $viewers);
        } finally {
            // 释放锁
            flock($handle, LOCK_UN);
        }
    } else {
        // 如果无法获取锁，记录错误或抛出异常
        error_log('Unable to lock the viewers file for writing.');
        return false;
    }

    // 关闭文件句柄
    fclose($handle);

    return $viewers;
}

// 检查文件是否存在，如果不存在，则创建并初始化为0
if (!file_exists($viewersFile)) {
    file_put_contents($viewersFile, '0');
}

// 如果是新会话，增加观看人数
if (!isset($_SESSION['viewer'])) {
    $_SESSION['viewer'] = true;
    $viewers = incrementViewers();
}

// 读取当前观看人数
$viewers = file_get_contents($viewersFile);
$viewers = intval($viewers);

// 返回当前观看人数
echo $viewers;
