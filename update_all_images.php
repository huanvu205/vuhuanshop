<?php
/**
 * Script tự động cập nhật lại toàn bộ đường dẫn ảnh bị thiếu trên CSDL Hosting
 */
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/core/Config.php';
require_once __DIR__ . '/core/Database.php';

$db = new Database();
echo "<pre>=== BẮT ĐẦU CẬP NHẬT ẢNH TRÊN HOST ===\n";

$data = array (
  'products' => 
  array (
    0 => 
    array (
      'id' => 1,
      'image' => 'uploads/products/6a361facde216_1781931948.jpg',
    ),
    1 => 
    array (
      'id' => 2,
      'image' => 'samsung-s24-ultra.png',
    ),
    2 => 
    array (
      'id' => 3,
      'image' => 'xiaomi-14.png',
    ),
    3 => 
    array (
      'id' => 4,
      'image' => 'macbook-pro-m3.png',
    ),
    4 => 
    array (
      'id' => 5,
      'image' => 'dell-xps-13.png',
    ),
    5 => 
    array (
      'id' => 6,
      'image' => 'asus-tuf-gaming.png',
    ),
    6 => 
    array (
      'id' => 7,
      'image' => 'ipad-air-m2.png',
    ),
    7 => 
    array (
      'id' => 8,
      'image' => 'uploads/products/6a361f4c59ae8_1781931852.jpg',
    ),
    8 => 
    array (
      'id' => 9,
      'image' => 'apple-watch-s9.png',
    ),
    9 => 
    array (
      'id' => 10,
      'image' => 'galaxy-watch-6.png',
    ),
    10 => 
    array (
      'id' => 11,
      'image' => 'airpods-pro-2.png',
    ),
    11 => 
    array (
      'id' => 12,
      'image' => 'apple-charger-20w.png',
    ),
    12 => 
    array (
      'id' => 15,
      'image' => 'uploads/products/6a36c7904efff_1781974928.jpg',
    ),
  ),
);

try {
    // Cập nhật sản phẩm
    if (isset($data['products'])) {
        echo "Đang cập nhật ảnh sản phẩm...\n";
        foreach ($data['products'] as $p) {
            $db->query("UPDATE products SET main_image = :img WHERE id = :id")
               ->bind(':img', $p['image'])
               ->bind(':id', $p['id'])
               ->execute();
            echo "  - Cập nhật sản phẩm ID {$p['id']} -> {$p['image']}\n";
        }
    }

    // Cập nhật danh mục
    if (isset($data['categories'])) {
        echo "\nĐang cập nhật ảnh danh mục...\n";
        foreach ($data['categories'] as $c) {
            $db->query("UPDATE categories SET image = :img WHERE id = :id")
               ->bind(':img', $c['image'])
               ->bind(':id', $c['id'])
               ->execute();
            echo "  - Cập nhật danh mục ID {$c['id']} -> {$c['image']}\n";
        }
    }

    // Cập nhật thương hiệu
    if (isset($data['brands'])) {
        echo "\nĐang cập nhật logo thương hiệu...\n";
        foreach ($data['brands'] as $b) {
            $db->query("UPDATE brands SET logo = :img WHERE id = :id")
               ->bind(':img', $b['image'])
               ->bind(':id', $b['id'])
               ->execute();
            echo "  - Cập nhật thương hiệu ID {$b['id']} -> {$b['image']}\n";
        }
    }

    // Cập nhật banners
    if (isset($data['banners'])) {
        echo "\nĐang cập nhật ảnh banners...\n";
        foreach ($data['banners'] as $ba) {
            $db->query("UPDATE banners SET image = :img WHERE id = :id")
               ->bind(':img', $ba['image'])
               ->bind(':id', $ba['id'])
               ->execute();
            echo "  - Cập nhật banner ID {$ba['id']} -> {$ba['image']}\n";
        }
    }

    // Cập nhật tin tức
    if (isset($data['news'])) {
        echo "\nĐang cập nhật ảnh tin tức...\n";
        foreach ($data['news'] as $n) {
            $db->query("UPDATE news SET featured_image = :img WHERE id = :id")
               ->bind(':img', $n['image'])
               ->bind(':id', $n['id'])
               ->execute();
            echo "  - Cập nhật tin tức ID {$n['id']} -> {$n['image']}\n";
        }
    }

    echo "\n=== HOÀN TẤT CẬP NHẬT THÀNH CÔNG! ===\n";
} catch (Exception $e) {
    echo "LỖI CẬP NHẬT: " . $e->getMessage() . "\n";
}
echo "</pre>";
?>
