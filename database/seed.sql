-- =========================================================================
-- DỮ LIỆU MẪU (SEED DATA) CHO WEBSITE THƯƠNG MẠI ĐIỆN TỬ WEBBANDIENTHOAIMOI
-- =========================================================================

-- USE webbandienthoaimoi;
SET FOREIGN_KEY_CHECKS = 0;

-- Xóa dữ liệu cũ để tránh trùng lặp
DELETE FROM product_specs;
DELETE FROM product_images;
DELETE FROM inventory_logs;
DELETE FROM inventory;
DELETE FROM reviews;
DELETE FROM coupons;
DELETE FROM banners;
DELETE FROM news;
DELETE FROM products;

-- 1. Thêm sản phẩm (products)
INSERT INTO products (id, product_code, name, slug, category_id, brand_id, description, short_description, price_cost, price_sale, price_discount, discount_percent, main_image, status, stock_quantity, rating_avg, rating_count, is_featured, is_hot_deal, created_at) VALUES
(1, 'IP15PM256', 'iPhone 15 Pro Max 256GB', 'iphone-15-pro-max-256gb', 1, 1, 
'<h3>Đặc điểm nổi bật của iPhone 15 Pro Max</h3><p>iPhone 15 Pro Max sở hữu khung vỏ Titan siêu bền nhẹ, nút Action mới và chip xử lý A17 Pro mạnh mẽ nhất hiện nay cùng khả năng zoom quang học 5x vượt trội.</p>', 
'Khung vỏ Titan siêu bền nhẹ, chip A17 Pro cực mạnh, camera zoom quang 5x chuyên nghiệp.', 
24000000.00, 34990000.00, 29990000.00, 14, 'uploads/products/iphone-15-pro-max.png', 'active', 50, 5.00, 1, 1, 1, NOW()),

(2, 'S24U256', 'Samsung Galaxy S24 Ultra 256GB', 'samsung-galaxy-s24-ultra-256gb', 1, 2, 
'<h3>Kỷ nguyên quyền năng AI trên Galaxy S24 Ultra</h3><p>Mẫu flagship đỉnh cao từ Samsung tích hợp công nghệ Galaxy AI tiên tiến hỗ trợ dịch thuật trực tiếp, khoanh vùng tìm kiếm thông minh cùng bút S-Pen chuyên nghiệp.</p>', 
'Galaxy AI đột phá, khung viền Titan siêu bền, chip Snapdragon 8 Gen 3 cực đỉnh.', 
22000000.00, 31990000.00, 26990000.00, 16, 'uploads/products/samsung-s24-ultra.png', 'active', 40, 5.00, 1, 1, 0, NOW()),

(3, 'XI14256', 'Xiaomi 14 256GB', 'xiaomi-14-256gb', 1, 3, 
'<h3>Thiết kế nhỏ gọn, ống kính Leica chuyên nghiệp</h3><p>Xiaomi 14 là sự kết hợp hoàn hảo giữa kích thước màn hình 6.36 inch nhỏ gọn cầm nắm thoải mái và hệ thống camera Leica cao cấp bắt trọn khoảnh khắc.</p>', 
'Màn hình 6.36 inch nhỏ gọn, ống kính Leica thế hệ mới, chip Snapdragon 8 Gen 3.', 
14000000.00, 19990000.00, 17990000.00, 10, 'uploads/products/xiaomi-14.png', 'active', 35, 4.50, 2, 1, 0, NOW()),

(4, 'MBPM38512', 'MacBook Pro 14 inch M3 8GB/512GB', 'macbook-pro-14-inch-m3', 2, 1, 
'<h3>MacBook Pro 14 M3 - Đỉnh cao hiệu năng làm việc</h3><p>Trang bị con chip Apple M3 tiên tiến trên tiến trình 3nm, MacBook Pro mang lại hiệu năng mạnh mẽ vượt trội cùng thời lượng pin ấn tượng lên đến 22 giờ liên tục.</p>', 
'Chip Apple M3 đột phá hiệu năng, màn hình Liquid Retina XDR sắc nét, pin dùng đến 22 giờ.', 
32000000.00, 44990000.00, 39990000.00, 11, 'uploads/products/macbook-pro-m3.png', 'active', 20, 5.00, 1, 1, 1, NOW()),

(5, 'DELLXPS139340', 'Dell XPS 13 9340 Intel Ultra 7', 'dell-xps-13-9340', 2, 6, 
'<h3>Thiết kế tương lai siêu mỏng nhẹ từ Dell</h3><p>Dòng laptop cao cấp nhất của Dell sở hữu màn hình vô cực OLED sắc nét, bàn phím tràn viền phẳng hiện đại và bộ vi xử lý Intel Core Ultra 7 tích hợp AI.</p>', 
'Thiết kế nhôm nguyên khối siêu mỏng nhẹ, chip Core Ultra 7 tích hợp AI, màn hình vô cực FHD+.', 
30000000.00, 41990000.00, 38990000.00, 7, 'uploads/products/dell-xps-13.png', 'active', 15, 4.00, 1, 1, 0, NOW()),

(6, 'ASUSTUFA15', 'Asus TUF Gaming A15', 'asus-tuf-gaming-a15', 2, 5, 
'<h3>Laptop Gaming chuẩn quân đội bền bỉ</h3><p>Được thiết kế cho game thủ chuyên nghiệp, Asus TUF A15 sở hữu CPU AMD Ryzen 7 mạnh mẽ đi kèm card đồ họa rời RTX 4050 vượt trội cân mọi tựa game Esport.</p>', 
'CPU AMD Ryzen 7 mạnh mẽ, card RTX 4050, màn hình 144Hz mượt mà, độ bền chuẩn quân đội.', 
16000000.00, 23990000.00, 21990000.00, 8, 'uploads/products/asus-tuf-gaming.png', 'active', 25, 4.50, 1, 0, 1, NOW()),

(7, 'IPADAIR6M2', 'iPad Air 6 M2 11 inch 128GB Wi-Fi', 'ipad-air-6-m2-11-inch', 3, 1, 
'<h3>iPad Air thế hệ mới nâng tầm sức mạnh với chip M2</h3><p>iPad Air 6 với vi xử lý Apple M2 cực mạnh mang đến trải nghiệm đồ họa mượt mà, tương thích hoàn toàn bút Apple Pencil Pro mới cho công việc vẽ sáng tạo.</p>', 
'Vi xử lý Apple M2 siêu mạnh, màn hình Liquid Retina sắc nét, tương thích Apple Pencil Pro.', 
12000000.00, 16990000.00, 15490000.00, 9, 'uploads/products/ipad-air-m2.png', 'active', 30, 5.00, 1, 1, 0, NOW()),

(8, 'TABS9FE', 'Samsung Galaxy Tab S9 FE', 'samsung-galaxy-tab-s9-fe', 3, 2, 
'<h3>Máy tính bảng thông minh đi kèm bút S-Pen kháng nước</h3><p>Galaxy Tab S9 FE hỗ trợ công việc và học tập đắc lực với màn hình 90Hz mượt mà, tặng kèm bút S-Pen cao cấp và khả năng kháng nước kháng bụi chuẩn IP68.</p>', 
'Bút S-Pen đi kèm hỗ trợ IP68 kháng nước kháng bụi, màn hình 90Hz mượt mà.', 
7500000.00, 9990000.00, 8990000.00, 10, 'uploads/products/samsung-tab-s9fe.png', 'active', 20, 4.00, 1, 0, 0, NOW()),

(9, 'AW941', 'Apple Watch Series 9 GPS 41mm Viền Nhôm', 'apple-watch-series-9-41mm', 4, 1, 
'<h3>Đồng hồ thông minh theo dõi sức khỏe chuyên sâu</h3><p>Trang bị chip S9 SiP tiên tiến mang lại màn hình siêu sáng, hỗ trợ tính năng Double Tap chạm hai lần độc đáo và theo dõi nhịp tim, giấc ngủ cực chính xác.</p>', 
'Chip S9 SiP mạnh mẽ, màn hình siêu sáng, tính năng chạm hai lần tiện dụng.', 
7500000.00, 10490000.00, 9290000.00, 11, 'uploads/products/apple-watch-s9.png', 'active', 40, 4.50, 1, 1, 0, NOW()),

(10, 'GW640', 'Samsung Galaxy Watch 6 40mm', 'samsung-galaxy-watch-6-40mm', 4, 2, 
'<h3>Smartwatch cá nhân hóa huấn luyện viên giấc ngủ</h3><p>Galaxy Watch 6 sở hữu viền màn hình mỏng hơn, cung cấp các tính năng theo dõi giấc ngủ chuyên sâu, phân tích thành phần cơ thể BIA và đo huyết áp tiện lợi.</p>', 
'Theo dõi sức khỏe chuyên sâu, phân tích thành phần cơ thể BIA, đo điện tâm đồ ECG.', 
4500000.00, 6990000.00, 5490000.00, 21, 'uploads/products/galaxy-watch-6.png', 'active', 30, 4.00, 1, 0, 0, NOW()),

(11, 'AIRPODSPRO2', 'AirPods Pro 2 USB-C', 'airpods-pro-2-usb-c', 5, 1, 
'<h3>Tai nghe chống ồn chủ động đỉnh cao thế giới</h3><p>AirPods Pro 2 mang đến chất lượng âm thanh vượt trội nhờ chip H2 nâng cấp, khả năng chống ồn chủ động tăng gấp 2 lần và cổng sạc USB-C hiện đại.</p>', 
'Chip âm thanh H2 thế hệ mới, chống ồn chủ động gấp 2 lần, hộp sạc USB-C.', 
4000000.00, 6190000.00, 5490000.00, 11, 'uploads/products/airpods-pro-2.png', 'active', 100, 5.00, 2, 1, 1, NOW()),

(12, 'APPLE20W', 'Sạc Nhanh Apple 20W USB-C Chính Hãng', 'sac-nhanh-apple-20w-usb-c', 5, 1, 
'<h3>Phụ kiện sạc không thể thiếu cho người dùng iPhone</h3><p>Cốc sạc nhanh Apple 20W với cổng USB-C giúp rút ngắn tối đa thời gian sạc cho điện thoại iPhone và máy tính bảng iPad của bạn an toàn, hiệu quả.</p>', 
'Cốc sạc nhanh 20W chính hãng Apple, cổng USB-C tiện dụng, độ bền cực cao.', 
300000.00, 590000.00, 490000.00, 17, 'uploads/products/apple-charger-20w.png', 'active', 150, 4.50, 1, 0, 0, NOW());

-- 2. Thêm thông số kỹ thuật (product_specs)
INSERT INTO product_specs (product_id, spec_name, spec_value, display_order) VALUES
-- iPhone 15 Pro Max
(1, 'Màn hình', 'Super Retina XDR OLED, 6.7 inch, 120Hz', 1),
(1, 'Hệ điều hành', 'iOS 17', 2),
(1, 'Chip xử lý', 'Apple A17 Pro 3nm', 3),
(1, 'RAM', '8 GB', 4),
(1, 'Bộ nhớ trong', '256 GB', 5),
(1, 'Camera sau', '48 MP (Chính) + 12 MP (Góc siêu rộng) + 12 MP (Tele zoom 5x)', 6),
(1, 'Camera trước', '12 MP', 7),
(1, 'Dung lượng pin', '4441 mAh, Sạc nhanh 20W', 8),

-- Galaxy S24 Ultra
(2, 'Màn hình', 'Dynamic AMOLED 2X, 6.8 inch, QHD+, 120Hz', 1),
(2, 'Hệ điều hành', 'Android 14', 2),
(2, 'Chip xử lý', 'Snapdragon 8 Gen 3 for Galaxy', 3),
(2, 'RAM', '12 GB', 4),
(2, 'Bộ nhớ trong', '256 GB', 5),
(2, 'Camera sau', '200 MP (Chính) + 50 MP + 12 MP + 10 MP', 6),
(2, 'Camera trước', '12 MP', 7),
(2, 'Dung lượng pin', '5000 mAh, Sạc nhanh 45W', 8),

-- Xiaomi 14
(3, 'Màn hình', 'LTPO OLED, 6.36 inch, 1.5K, 120Hz', 1),
(3, 'Hệ điều hành', 'Xiaomi HyperOS (Android 14)', 2),
(3, 'Chip xử lý', 'Snapdragon 8 Gen 3', 3),
(3, 'RAM', '12 GB', 4),
(3, 'Bộ nhớ trong', '256 GB', 5),
(3, 'Camera sau', 'Ba camera Leica 50 MP', 6),
(3, 'Camera trước', '32 MP', 7),
(3, 'Dung lượng pin', '4610 mAh, Sạc nhanh 90W', 8),

-- MacBook Pro M3
(4, 'Màn hình', 'Liquid Retina XDR, 14.2 inch, 120Hz', 1),
(4, 'CPU', 'Apple M3 8-Core', 2),
(4, 'RAM', '8 GB', 3),
(4, 'Bộ nhớ SSD', '512 GB', 4),
(4, 'Card đồ họa', 'Apple GPU 10-Core', 5),
(4, 'Trọng lượng', '1.55 kg', 6),
(4, 'Hệ điều hành', 'macOS Sonoma', 7),

-- Dell XPS 13
(5, 'Màn hình', 'FHD+ IPS, 13.4 inch, Anti-Glare', 1),
(5, 'CPU', 'Intel Core Ultra 7 155H (up to 4.8 GHz)', 2),
(5, 'RAM', '16 GB LPDDR5X', 3),
(5, 'Bộ nhớ SSD', '512 GB PCIe NVMe', 4),
(5, 'Card đồ họa', 'Intel Arc Graphics', 5),
(5, 'Trọng lượng', '1.19 kg', 6),
(5, 'Hệ điều hành', 'Windows 11 Home', 7),

-- Asus TUF
(6, 'Màn hình', 'FHD IPS, 15.6 inch, 144Hz, G-Sync', 1),
(6, 'CPU', 'AMD Ryzen 7 7735HS', 2),
(6, 'RAM', '16 GB DDR5', 3),
(6, 'Bộ nhớ SSD', '512 GB PCIe NVMe', 4),
(6, 'Card đồ họa', 'NVIDIA GeForce RTX 4050 6GB GDDR6', 5),
(6, 'Trọng lượng', '2.20 kg', 6),
(6, 'Hệ điều hành', 'Windows 11 Home', 7),

-- iPad Air 6
(7, 'Màn hình', 'Liquid Retina IPS, 11 inch, True Tone', 1),
(7, 'Chip xử lý', 'Apple M2 8-Core', 2),
(7, 'RAM', '8 GB', 3),
(7, 'Bộ nhớ trong', '128 GB', 4),
(7, 'Camera', 'Sau 12 MP, Trước 12 MP siêu rộng', 5),
(7, 'Hệ điều hành', 'iPadOS 17', 6),

-- AirPods Pro 2
(11, 'Kết nối', 'Bluetooth 5.3', 1),
(11, 'Chip xử lý', 'Apple H2 chip, Apple U1 chip in case', 2),
(11, 'Chuẩn kháng nước', 'IPX4 kháng mồ hôi và nước', 3),
(11, 'Thời lượng pin', 'Lên đến 6 giờ nghe nhạc (30 giờ kèm hộp sạc)', 4),
(11, 'Cổng sạc', 'USB-C', 5);

-- 3. Thêm kho hàng ban đầu (inventory)
INSERT INTO inventory (product_id, quantity, reorder_level, last_count_date) VALUES
(1, 50, 10, NOW()),
(2, 40, 10, NOW()),
(3, 35, 10, NOW()),
(4, 20, 5, NOW()),
(5, 15, 5, NOW()),
(6, 25, 5, NOW()),
(7, 30, 8, NOW()),
(8, 20, 8, NOW()),
(9, 40, 10, NOW()),
(10, 30, 10, NOW()),
(11, 100, 20, NOW()),
(12, 150, 25, NOW());

-- 4. Thêm nhật ký kho ban đầu (inventory_logs)
INSERT INTO inventory_logs (product_id, transaction_type, quantity, reference_type, notes) VALUES
(1, 'in', 50, 'purchase_order', 'Nhập kho ban đầu'),
(2, 'in', 40, 'purchase_order', 'Nhập kho ban đầu'),
(3, 'in', 35, 'purchase_order', 'Nhập kho ban đầu'),
(4, 'in', 20, 'purchase_order', 'Nhập kho ban đầu'),
(5, 'in', 15, 'purchase_order', 'Nhập kho ban đầu'),
(6, 'in', 25, 'purchase_order', 'Nhập kho ban đầu'),
(7, 'in', 30, 'purchase_order', 'Nhập kho ban đầu'),
(8, 'in', 20, 'purchase_order', 'Nhập kho ban đầu'),
(9, 'in', 40, 'purchase_order', 'Nhập kho ban đầu'),
(10, 'in', 30, 'purchase_order', 'Nhập kho ban đầu'),
(11, 'in', 100, 'purchase_order', 'Nhập kho ban đầu'),
(12, 'in', 150, 'purchase_order', 'Nhập kho ban đầu');

-- 5. Thêm mã giảm giá (coupons)
INSERT INTO coupons (coupon_code, description, discount_type, discount_value, max_discount, min_order_amount, max_uses, used_count, start_date, end_date, status) VALUES
('WELCOME100', 'Giảm trực tiếp 100.000₫ cho khách hàng mới với đơn hàng từ 1.000.000₫', 'fixed', 100000.00, 100000.00, 1000000.00, 1000, 0, NOW(), DATE_ADD(NOW(), INTERVAL 90 DAY), 'active'),
('TECHSUMMER', 'Giảm 10% tối đa 500.000₫ cho đơn hàng công nghệ từ 2.000.000₫', 'percent', 10.00, 500000.00, 2000000.00, 500, 0, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 'active');

-- 6. Thêm banner (banners)
INSERT INTO banners (title, image, link, position, display_order, status, start_date, end_date) VALUES
('Siêu Phẩm iPhone 15 Pro Max', '', 'products/iphone-15-pro-max-256gb', 'main_slider', 1, 'active', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY)),
('Đồng Hồ Thông Minh Mới Nhất', '', 'products/apple-watch-series-9-41mm', 'main_slider', 2, 'active', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY)),
('Phụ Kiện Chính Hãng Apple', '', 'products/airpods-pro-2-usb-c', 'main_slider', 3, 'active', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY));

-- 7. Thêm tin tức công nghệ (news)
INSERT INTO news (title, slug, content, excerpt, featured_image, author_id, category, status, view_count, published_at) VALUES
('Đánh giá chi tiết iPhone 15 Pro Max sau 6 tháng sử dụng', 'danh-gia-chi-tiet-iphone-15-pro-max-sau-6-thang', 
'<p>Sau 6 tháng có mặt trên thị trường, iPhone 15 Pro Max vẫn là mẫu flagship được săn lùng nhiều nhất. Trải nghiệm thực tế cho thấy khung Titan giữ được vẻ sang trọng, không trầy xước nhiều.</p><p>Chip A17 Pro chiến mượt mọi game nặng nhất như Genshin Impact. Thời lượng pin cực trâu, dễ dàng đạt 8-9 tiếng sử dụng màn hình liên tục.</p>', 
'Sau 6 tháng trải nghiệm thực tế, iPhone 15 Pro Max chứng minh vị thế độc tôn với hiệu năng cực đỉnh và khung Titan bền bỉ.', 
'', 1, 'Đánh giá', 'published', 245, NOW()),

('Top 5 máy tính bảng cho học sinh, sinh viên tốt nhất năm 2026', 'top-5-may-tinh-bang-cho-hoc-sinh-sinh-vien', 
'<p>Năm học mới sắp bắt đầu, việc trang bị một chiếc máy tính bảng giúp học sinh, sinh viên ghi chép tài liệu dễ dàng hơn bao giờ hết.</p><p>Danh sách đề xuất bao gồm iPad Air 6, Galaxy Tab S9 FE và một số lựa chọn giá rẻ từ Xiaomi có tặng kèm bút vẽ stylus cao cấp.</p>', 
'Tìm kiếm mẫu máy tính bảng hỗ trợ học tập, làm việc hiệu quả với giá thành phải chăng kèm bút stylus.', 
'', 1, 'Tư vấn', 'published', 189, NOW());

-- 8. Thêm đánh giá mẫu (reviews)
INSERT INTO reviews (product_id, user_id, rating, comment, status, created_at) VALUES
(1, 3, 5, 'Máy cực kỳ sang xịn mịn, khung titan cầm siêu nhẹ và đầm tay. Chụp ảnh zoom 5x nét căng.', 'approved', NOW()),
(3, 3, 4, 'Điện thoại dùng rất mượt và nhỏ gọn vừa túi quần. Camera Leica chụp ảnh có chiều sâu ấn tượng, chỉ hơi tiếc là pin dùng một ngày vừa đủ chứ không quá trâu.', 'approved', NOW()),
(3, 3, 5, 'Quá hài lòng với Xiaomi 14. Thiết kế viền siêu mỏng, sạc 90W cực kỳ nhanh.', 'approved', NOW()),
(4, 3, 5, 'Màn hình Liquid Retina XDR quá đẹp, màu đen sâu. Loa nghe hay đỉnh cao, làm việc văn phòng máy không hề nóng.', 'approved', NOW()),
(5, 3, 4, 'Bàn phím Dell XPS gõ rất êm, touchpad tàng hình nhìn hiện đại. Tuy nhiên các cổng kết nối hơi hạn chế nên phải mua đầu chuyển.', 'approved', NOW()),
(6, 3, 5, 'Chiến game siêu mượt, quạt tản nhiệt chạy êm không bị quá rú. Thiết kế chuẩn quân đội cứng cáp.', 'approved', NOW()),
(7, 3, 5, 'Màu xanh lục của iPad Air mới rất nhã nhặn. Dùng chung với Apple Pencil Pro phục vụ vẽ vời thiết kế quá tuyệt vời.', 'approved', NOW()),
(8, 3, 4, 'Bút S-Pen viết rất nhạy. Máy có kháng nước nên mang ra quán cafe dùng yên tâm hơn hẳn.', 'approved', NOW()),
(9, 3, 5, 'Tính năng chạm hai lần Double Tap rất tiện khi tay đang bận làm việc khác. Màn hình cực sáng khi ở ngoài nắng.', 'approved', NOW()),
(10, 3, 4, 'Đo giấc ngủ rất chi tiết, có phân tích thành phần cơ thể để tập gym. Pin hơi hẻo, ngày nào cũng phải sạc.', 'approved', NOW()),
(11, 3, 5, 'Chống ồn đỉnh cao, ngồi trên xe bus bật nhạc là không nghe thấy tiếng xung quanh nữa. Cổng USB-C sạc chung với điện thoại tiện lợi.', 'approved', NOW()),
(11, 3, 5, 'Tai đeo êm tai không bị đau, chất âm cải thiện rõ rệt so với bản cũ. Rất đáng nâng cấp.', 'approved', NOW()),
(12, 3, 5, 'Sạc rất nhanh và không bị nóng máy nhiều. Đúng chuẩn chính hãng Apple yên tâm sử dụng.', 'approved', NOW());

SET FOREIGN_KEY_CHECKS = 1;
