<?php
require_once __DIR__ . '/core/Config.php';
require_once __DIR__ . '/core/Database.php';

$db = new Database();
echo "<pre>=== DATABASE DIAGNOSTICS ===\n";

try {
    $products = $db->query("SELECT id, name, main_image FROM products LIMIT 10")->resultSet();
    foreach ($products as $p) {
        $img = $p['main_image'];
        $exists = file_exists(__DIR__ . '/public/uploads/products/' . $img) ? "OK" : "MISSING";
        echo "Product ID {$p['id']}: {$p['name']}\n";
        echo "  - Database image: '$img'\n";
        echo "  - Physical path check: " . __DIR__ . '/public/uploads/products/' . $img . " -> $exists\n";
    }
} catch (Exception $e) {
    echo "LỖI PRODUCTS: " . $e->getMessage() . "\n";
}

try {
    $users = $db->query("SELECT id, username, email, role, status FROM users")->resultSet();
    echo "\n=== USERS LIST ===\n";
    foreach ($users as $u) {
        echo "ID {$u['id']}: Username: {$u['username']}, Email: {$u['email']}, Role: {$u['role']}, Status: {$u['status']}\n";
    }
} catch (Exception $e) {
    echo "LỖI USERS: " . $e->getMessage() . "\n";
}
echo "</pre>";
?>
