<?php
/**
 * Script tự động giải nén deploy.zip trên Hosting InfinityFree
 */
$zipFile = __DIR__ . '/deploy.zip';
$extractTo = __DIR__;

if (!file_exists($zipFile)) {
    die("<h1>Lỗi: Không tìm thấy file deploy.zip!</h1><p>Vui lòng upload tệp deploy.zip lên thư mục htdocs trước.</p>");
}

// Hàm xóa đệ quy file hoặc thư mục
function deleteResource($path) {
    if (is_dir($path) && !is_link($path)) {
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
            deleteResource($path . '/' . $file);
        }
        return @rmdir($path);
    }
    return @unlink($path);
}

// Tự động dọn dẹp các tệp bị lỗi tên chứa dấu gạch chéo ngược '\' từ lần giải nén trước
echo "<div style='font-family: sans-serif; margin: 20px auto; max-width: 600px; padding: 20px; border: 1px solid #ccc; border-radius: 5px;'>";
echo "<h3>1. Tiến trình dọn dẹp tệp tin lỗi (chứa dấu '\\')</h3>";
$dir = new DirectoryIterator(__DIR__);
$deletedCount = 0;
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
        $filename = $fileinfo->getFilename();
        if (strpos($filename, '\\') !== false) {
            if (deleteResource($fileinfo->getPathname())) {
                echo "<div style='color: #666; font-size: 13px;'>Đã xóa: " . htmlspecialchars($filename) . "</div>";
                $deletedCount++;
            }
        }
    }
}
echo "<p style='color: #2e7d32; font-weight: bold;'>Đã dọn dẹp xong $deletedCount tệp lỗi thành công!</p>";
echo "<h3>2. Tiến trình giải nén mã nguồn</h3>";

$zip = new ZipArchive;
$res = $zip->open($zipFile);

if ($res === TRUE) {
    // Tiến hành giải nén
    $zip->extractTo($extractTo);
    $zip->close();
    
    // Tự động xóa file zip sau khi giải nén thành công để giải phóng bộ nhớ
    @unlink($zipFile);
    
    echo "<p style='color: #2e7d32; font-weight: bold;'>🎉 GIẢI NÉN THÀNH CÔNG!</p>";
    echo "<p>Toàn bộ mã nguồn đã được giải nén ra thư mục htdocs.</p>";
    echo "<p style='color: #d32f2f;'><b>Quan trọng:</b> Hãy xóa file <u>unzip.php</u> trên host đi để bảo mật website.</p>";
} else {
    echo "<p style='color: #d32f2f; font-weight: bold;'>❌ LỖI GIẢI NÉN!</p>";
    echo "<p>Mã lỗi: " . $res . ". Hãy chắc chắn rằng tệp deploy.zip không bị hỏng khi upload.</p>";
}
echo "</div>";
?>
