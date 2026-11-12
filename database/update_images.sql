-- Cập nhật ảnh sản phẩm cho Vũ Huân Shop
USE webbandienthoaimoi;

-- Cập nhật main_image cho từng sản phẩm
UPDATE products SET main_image = 'iphone-15-pro-max.png' WHERE id = 1;
UPDATE products SET main_image = 'samsung-s24-ultra.png' WHERE id = 2;
UPDATE products SET main_image = 'xiaomi-14.png' WHERE id = 3;
UPDATE products SET main_image = 'macbook-pro-m3.png' WHERE id = 4;
UPDATE products SET main_image = 'dell-xps-13.png' WHERE id = 5;
UPDATE products SET main_image = 'asus-tuf-gaming.png' WHERE id = 6;
UPDATE products SET main_image = 'ipad-air-m2.png' WHERE id = 7;
UPDATE products SET main_image = 'samsung-tab-s9fe.png' WHERE id = 8;
UPDATE products SET main_image = 'apple-watch-s9.png' WHERE id = 9;
UPDATE products SET main_image = 'galaxy-watch-6.png' WHERE id = 10;
UPDATE products SET main_image = 'airpods-pro-2.png' WHERE id = 11;
UPDATE products SET main_image = 'apple-charger-20w.png' WHERE id = 12;

-- Thêm ảnh vào bảng product_images
INSERT INTO product_images (product_id, image, sort_order) VALUES
(1, 'iphone-15-pro-max.png', 1),
(2, 'samsung-s24-ultra.png', 1),
(3, 'xiaomi-14.png', 1),
(4, 'macbook-pro-m3.png', 1),
(5, 'dell-xps-13.png', 1),
(6, 'asus-tuf-gaming.png', 1),
(7, 'ipad-air-m2.png', 1),
(8, 'samsung-tab-s9fe.png', 1),
(9, 'apple-watch-s9.png', 1),
(10, 'galaxy-watch-6.png', 1),
(11, 'airpods-pro-2.png', 1),
(12, 'apple-charger-20w.png', 1);

SELECT 'Images updated successfully!' AS result;
