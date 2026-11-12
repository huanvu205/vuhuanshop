<?php
echo "<h3>Danh sách file trong htdocs:</h3><pre>";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    $isDir = is_dir(__DIR__ . '/' . $file) ? '[DIR]' : '[FILE]';
    $size = !is_dir(__DIR__ . '/' . $file) ? number_format(filesize(__DIR__ . '/' . $file) / 1024, 2) . ' KB' : '';
    echo str_pad($isDir, 8) . str_pad($file, 40) . $size . "\n";
}
echo "</pre>";

echo "<h3>Danh sách thư mục con public/uploads/products/ và quyền file:</h3><pre>";
$prodDir = __DIR__ . '/public/uploads/products';
if (is_dir($prodDir)) {
    $pFiles = scandir($prodDir);
    foreach ($pFiles as $pf) {
        if ($pf === '.' || $pf === '..') continue;
        $path = $prodDir . '/' . $pf;
        $perms = decoct(fileperms($path) & 0777);
        $size = number_format(filesize($path) / 1024, 2) . ' KB';
        echo str_pad($pf, 40) . str_pad($size, 15) . "Quyền: " . $perms . "\n";
    }
} else {
    echo "Thư mục $prodDir không tồn tại!\n";
}
echo "</pre>";

echo "<h3>Kiểm tra file .htaccess ẩn:</h3><pre>";
foreach (['', '/public', '/public/uploads', '/public/uploads/products'] as $sub) {
    $htaccess = __DIR__ . $sub . '/.htaccess';
    if (file_exists($htaccess)) {
        echo "Tìm thấy .htaccess tại $sub/.htaccess:\n";
        echo htmlspecialchars(file_get_contents($htaccess)) . "\n---\n";
    } else {
        echo "Không có .htaccess tại $sub/.htaccess\n";
    }
}
echo "</pre>";
?>
