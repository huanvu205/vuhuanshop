<?php
/**
 * Script tự động sửa quyền file (permissions) trên Hosting
 */
header('Content-Type: text/html; charset=utf-8');

function chmod_r($path) {
    if (!file_exists($path)) {
        return;
    }
    if (is_dir($path)) {
        @chmod($path, 0755);
        echo "Thư mục: " . htmlspecialchars($path) . " -> 0755<br>";
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            chmod_r($path . '/' . $item);
        }
    } else {
        @chmod($path, 0644);
        echo "Tệp tin: " . htmlspecialchars($path) . " -> 0644<br>";
    }
}

echo "<h3>Đang sửa quyền truy cập cho thư mục public...</h3><div style='font-family: monospace; font-size: 12px; max-height: 400px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px;'>";
chmod_r(__DIR__ . '/public');
echo "</div><h3>Đã hoàn thành!</h3>";
?>
