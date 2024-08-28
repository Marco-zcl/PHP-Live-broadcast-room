<?php
if (isset($_POST['username']) && isset($_POST['danmu'])) {
    $username = $_POST['username'];
    $danmu = $_POST['danmu'];
    $logEntry = date('Y-m-d H:i:s') . " - $username: $danmu\n";
    file_put_contents('1.log', $logEntry, FILE_APPEND);
    echo "success";
} else {
    echo "error";
}

// 1.log文件路径