<?php
/**
 * Database Migration - Cấu hình Flash Sale & Nhóm Sản phẩm
 */

require_once __DIR__ . '/../core/Config.php';
require_once __DIR__ . '/../core/Database.php';

echo "=== ĐANG CHẠY MIGRATION CẤU HÌNH & NHÓM SẢN PHẨM ===\n";

try {
    $db = new Database();
    
    // 1. Tạo bảng settings nếu chưa tồn tại
    echo "1. Kiểm tra/Tạo bảng settings...\n";
    $createSettingsTableSql = "
    IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[settings]') AND type in (N'U'))
    BEGIN
        CREATE TABLE settings (
            [key] NVARCHAR(100) PRIMARY KEY,
            [value] NVARCHAR(MAX) NOT NULL,
            created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP
        );
        INSERT INTO settings ([key], [value]) VALUES ('flash_sale_end', '2026-06-21 23:59:59');
        PRINT 'Đã tạo bảng settings và chèn giá trị mặc định flash_sale_end';
    END
    ELSE
    BEGIN
        PRINT 'Bảng settings đã tồn tại';
    END";
    
    $db->query($createSettingsTableSql)->execute();
    
    // 2. Thêm cột is_best_selling vào bảng products nếu chưa tồn tại
    echo "2. Kiểm tra/Thêm cột is_best_selling vào bảng products...\n";
    $addBestSellingSql = "
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'[dbo].[products]') AND name = 'is_best_selling')
    BEGIN
        ALTER TABLE products ADD is_best_selling INT DEFAULT 0;
        PRINT 'Đã thêm cột is_best_selling';
    END
    ELSE
    BEGIN
        PRINT 'Cột is_best_selling đã tồn tại';
    END";
    $db->query($addBestSellingSql)->execute();
    
    // 3. Thêm cột is_newest vào bảng products nếu chưa tồn tại
    echo "3. Kiểm tra/Thêm cột is_newest vào bảng products...\n";
    $addNewestSql = "
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'[dbo].[products]') AND name = 'is_newest')
    BEGIN
        ALTER TABLE products ADD is_newest INT DEFAULT 0;
        PRINT 'Đã thêm cột is_newest';
    END
    ELSE
    BEGIN
        PRINT 'Cột is_newest đã tồn tại';
    END";
    $db->query($addNewestSql)->execute();
    
    echo "\n[OK] Chạy migration thành công!\n";
} catch (Exception $e) {
    echo "\n[THẤT BẠI] Lỗi migration: " . $e->getMessage() . "\n";
}
?>
