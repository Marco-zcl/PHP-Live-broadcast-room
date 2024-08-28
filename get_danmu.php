<?php
$logFile = '1.log';
$danmuList = [];

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_slice($lines, -50); // 只获取最后50条弹幕

    foreach ($lines as $line) {
        if (preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) - (.+?): (.+)/', $line, $matches)) {
            $danmuList[] = [
                'time' => $matches[1],
                'username' => $matches[2],
                'danmu' => $matches[3]
            ];
        }
    }
}

echo json_encode($danmuList);