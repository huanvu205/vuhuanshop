-- =========================================================================
-- DỮ LIỆU MẪU (SEED DATA) CHO WEBSITE THƯƠNG MẠI ĐIỆN TỬ VŨ HUÂN SHOP (MS SQL)
-- =========================================================================

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
GO

-- 1. Thêm sản phẩm (products)
SET IDENTITY_INSERT products ON;
GO

INSERT INTO products (id, product_code, name, slug, category_id, brand_id, description, short_description, price_cost, price_sale, price_discount, discount_percent, main_image, status, stock_quantity, rating_avg, rating_count, is_featured, is_hot_deal, created_at) VALUES
(1, N'IP15PM256', N'iPhone 15 Pro Max 256GB', N'iphone-15-pro-max-256gb', 1, 1, 
N'<h3>Đặc điểm nổi bật của iPhone 15 Pro Max</h3><p>iPhone 15 Pro Max sở hữu khung vỏ Titan siêu bền nhẹ, nút Action mới và chip xử lý A17 Pro mạnh mẽ nhất hiện nay cùng khả năng zoom quang học 5x vượt trội.</p>', 
N'Khung vỏ Titan siêu bền nhẹ, chip A17 Pro cực mạnh, camera zoom quang 5x chuyên nghiệp.', 
24000000.00, 34990000.00, 29990000.00, 14, N'iphone-15-pro-max.png', N'active', 50, 5.00, 1, 1, 1, GETDATE()),

(2, N'S24U256', N'Samsung Galaxy S24 Ultra 256GB', N'samsung-galaxy-s24-ultra-256gb', 1, 2, 
N'<h3>Kỷ nguyên quyền năng AI trên Galaxy S24 Ultra</h3><p>Mẫu flagship đỉnh cao từ Samsung tích hợp công nghệ Galaxy AI tiên tiến hỗ trợ dịch thuật trực tiếp, khoanh vùng tìm kiếm thông minh cùng bút S-Pen chuyên nghiệp.</p>', 
N'Galaxy AI đột phá, khung viền Titan siêu bền, chip Snapdragon 8 Gen 3 cực đỉnh.', 
22000000.00, 31990000.00, 26990000.00, 16, N'samsung-s24-ultra.png', N'active', 40, 5.00, 1, 1, 0, GETDATE()),

(3, N'XI14256', N'Xiaomi 14 256GB', N'xiaomi-14-256gb', 1, 3, 
N'<h3>Thiết kế nhỏ gọn, ống kính Leica chuyên nghiệp</h3><p>Xiaomi 14 là sự kết hợp hoàn hảo giữa kích thước màn hình 6.36 inch nhỏ gọn cầm nắm thoải mái và hệ thống camera Leica cao cấp bắt trọn khoảnh khắc.</p>', 
N'Màn hình 6.36 inch nhỏ gọn, ống kính Leica thế hệ mới, chip Snapdragon 8 Gen 3.', 
14000000.00, 19990000.00, 17990000.00, 10, N'xiaomi-14.png', N'active', 35, 4.50, 2, 1, 0, GETDATE()),

(4, N'MBPM38512', N'MacBook Pro 14 inch M3 8GB/512GB', N'macbook-pro-14-inch-m3', 2, 1, 
N'<h3>MacBook Pro 14 M3 - Đỉnh cao hiệu năng làm việc</h3><p>Trang bị con chip Apple M3 tiên tiến trên tiến trình 3nm, MacBook Pro mang lại hiệu năng mạnh mẽ vượt trội cùng thời lượng pin ấn tượng lên đến 22 giờ liên tục.</p>', 
N'Chip Apple M3 đột phá hiệu năng, màn hình Liquid Retina XDR sắc nét, pin dùng đến 22 giờ.', 
32000000.00, 44990000.00, 39990000.00, 11, N'macbook-pro-m3.png', N'active', 20, 5.00, 1, 1, 1, GETDATE()),

(5, N'DELLXPS139340', N'Dell XPS 13 9340 Intel Ultra 7', N'dell-xps-13-9340', 2, 6, 
N'<h3>Thiết kế tương lai siêu mỏng nhẹ từ Dell</h3><p>Dòng laptop cao cấp nhất của Dell sở hữu màn hình vô cực OLED sắc nét, bàn phím tràn viền phẳng hiện đại và bộ vi xử lý Intel Core Ultra 7 tích hợp AI.</p>', 
N'Thiết kế nhôm nguyên khối siêu mỏng nhẹ, chip Core Ultra 7 tích hợp AI, màn hình vô cực FHD+.', 
30000000.00, 41990000.00, 38990000.00, 7, N'dell-xps-13.png', N'active', 15, 4.00, 1, 1, 0, GETDATE()),

(6, N'ASUSTUFA15', N'Asus TUF Gaming A15', N'asus-tuf-gaming-a15', 2, 5, 
N'<h3>Laptop Gaming chuẩn quân đội bền bỉ</h3><p>Được thiết kế cho game thủ chuyên nghiệp, Asus TUF A15 sở hữu CPU AMD Ryzen 7 mạnh mẽ đi kèm card đồ họa rời RTX 4050 vượt trội cân mọi tựa game Esport.</p>', 
N'CPU AMD Ryzen 7 mạnh mẽ, card RTX 4050, màn hình 144Hz mượt mà, độ bền chuẩn quân đội.', 
16000000.00, 23990000.00, 21990000.00, 8, N'asus-tuf-gaming.png', N'active', 25, 4.50, 1, 0, 1, GETDATE()),

(7, N'IPADAIR6M2', N'iPad Air 6 M2 11 inch 128GB Wi-Fi', N'ipad-air-6-m2-11-inch', 3, 1, 
N'<h3>iPad Air thế hệ mới nâng tầm sức mạnh với chip M2</h3><p>iPad Air 6 với vi xử lý Apple M2 cực mạnh mang đến trải nghiệm đồ họa mượt mà, tương thích hoàn toàn bút Apple Pencil Pro mới cho công việc vẽ sáng tạo.</p>', 
N'Vi xử lý Apple M2 siêu mạnh, màn hình Liquid Retina sắc nét, tương thích Apple Pencil Pro.', 
12000000.00, 16990000.00, 15490000.00, 9, N'ipad-air-m2.png', N'active', 30, 5.00, 1, 1, 0, GETDATE()),

(8, N'TABS9FE', N'Samsung Galaxy Tab S9 FE', N'samsung-galaxy-tab-s9-fe', 3, 2, 
N'<h3>Máy tính bảng thông minh đi kèm bút S-Pen kháng nước</h3><p>Galaxy Tab S9 FE hỗ trợ công việc và học tập đắc lực với màn hình 90Hz mượt mà, tặng kèm bút S-Pen cao cấp và khả năng kháng nước kháng bụi chuẩn IP68.</p>', 
N'Bút S-Pen đi kèm hỗ trợ IP68 kháng nước kháng bụi, màn hình 90Hz mượt mà.', 
7500000.00, 9990000.00, 8990000.00, 10, N'samsung-tab-s9fe.png', N'active', 20, 4.00, 1, 0, 0, GETDATE()),

(9, N'AW941', N'Apple Watch Series 9 GPS 41mm Viền Nhôm', N'apple-watch-series-9-41mm', 4, 1, 
N'<h3>Đồng hồ thông minh theo dõi sức khỏe chuyên sâu</h3><p>Trang bị chip S9 SiP tiên tiến mang lại màn hình siêu sáng, hỗ trợ tính năng Double Tap chạm hai lần độc đáo và theo dõi nhịp tim, giấc ngủ cực chính xác.</p>', 
N'Chip S9 SiP mạnh mẽ, màn hình siêu sáng, tính năng chạm hai lần tiện dụng.', 
7500000.00, 10490000.00, 9290000.00, 11, N'apple-watch-s9.png', N'active', 40, 4.50, 1, 1, 0, GETDATE()),

(10, N'GW640', N'Samsung Galaxy Watch 6 40mm', N'samsung-galaxy-watch-6-40mm', 4, 2, 
N'<h3>Smartwatch cá nhân hóa huấn luyện viên giấc ngủ</h3><p>Galaxy Watch 6 sở hữu viền màn hình mỏng hơn, cung cấp các tính năng theo dõi giấc ngủ chuyên sâu, phân tích thành phần cơ thể BIA và đo huyết áp tiện lợi.</p>', 
N'Theo dõi sức khỏe chuyên sâu, phân tích thành phần cơ thể BIA, đo điện tâm đồ ECG.', 
4500000.00, 6990000.00, 5490000.00, 21, N'galaxy-watch-6.png', N'active', 30, 4.00, 1, 0, 0, GETDATE()),

(11, N'AIRPODSPRO2', N'AirPods Pro 2 USB-C', N'airpods-pro-2-usb-c', 5, 1, 
N'<h3>Tai nghe chống ồn chủ động đỉnh cao thế giới</h3><p>AirPods Pro 2 mang đến chất lượng âm thanh vượt trội nhờ chip H2 nâng cấp, khả năng chống ồn chủ động tăng gấp 2 lần và cổng sạc USB-C hiện đại.</p>', 
N'Chip âm thanh H2 thế hệ mới, chống ồn chủ động gấp 2 lần, hộp sạc USB-C.', 
4000000.00, 6190000.00, 5490000.00, 11, N'airpods-pro-2.png', N'active', 100, 5.00, 2, 1, 1, GETDATE()),

(12, N'APPLE20W', N'Sạc Nhanh Apple 20W USB-C Chính Hãng', N'sac-nhanh-apple-20w-usb-c', 5, 1, 
N'<h3>Phụ kiện sạc không thể thiếu cho người dùng iPhone</h3><p>Cốc sạc nhanh Apple 20W với cổng USB-C giúp rút ngắn tối đa thời gian sạc cho điện thoại iPhone và máy tính bảng iPad của bạn an toàn, hiệu quả.</p>', 
N'Cốc sạc nhanh 20W chính hãng Apple, cổng USB-C tiện dụng, độ bền cực cao.', 
300000.00, 590000.00, 490000.00, 17, N'apple-charger-20w.png', N'active', 150, 4.50, 1, 0, 0, GETDATE());
GO

SET IDENTITY_INSERT products OFF;
GO

-- 2. Thêm ảnh phụ vào bảng product_images
INSERT INTO product_images (product_id, image_path, display_order, is_main) VALUES
(1, N'iphone-15-pro-max.png', 1, 1),
(2, N'samsung-s24-ultra.png', 1, 1),
(3, N'xiaomi-14.png', 1, 1),
(4, N'macbook-pro-m3.png', 1, 1),
(5, N'dell-xps-13.png', 1, 1),
(6, N'asus-tuf-gaming.png', 1, 1),
(7, N'ipad-air-m2.png', 1, 1),
(8, N'samsung-tab-s9fe.png', 1, 1),
(9, N'apple-watch-s9.png', 1, 1),
(10, N'galaxy-watch-6.png', 1, 1),
(11, N'airpods-pro-2.png', 1, 1),
(12, N'apple-charger-20w.png', 1, 1);
GO

-- 3. Thêm thông số kỹ thuật (product_specs)
INSERT INTO product_specs (product_id, spec_name, spec_value, display_order) VALUES
-- iPhone 15 Pro Max
(1, N'Màn hình', N'Super Retina XDR OLED, 6.7 inch, 120Hz', 1),
(1, N'Hệ điều hành', N'iOS 17', 2),
(1, N'Chip xử lý', N'Apple A17 Pro 3nm', 3),
(1, N'RAM', N'8 GB', 4),
(1, N'Bộ nhớ trong', N'256 GB', 5),
(1, N'Camera sau', N'48 MP (Chính) + 12 MP (Góc siêu rộng) + 12 MP (Tele zoom 5x)', 6),
(1, N'Camera trước', N'12 MP', 7),
(1, N'Dung lượng pin', N'4441 mAh, Sạc nhanh 20W', 8),

-- Galaxy S24 Ultra
(2, N'Màn hình', N'Dynamic AMOLED 2X, 6.8 inch, QHD+, 120Hz', 1),
(2, N'Hệ điều hành', N'Android 14', 2),
(2, N'Chip xử lý', N'Snapdragon 8 Gen 3 for Galaxy', 3),
(2, N'RAM', N'12 GB', 4),
(2, N'Bộ nhớ trong', N'256 GB', 5),
(2, N'Camera sau', N'200 MP (Chính) + 50 MP + 12 MP + 10 MP', 6),
(2, N'Camera trước', N'12 MP', 7),
(2, N'Dung lượng pin', N'5000 mAh, Sạc nhanh 45W', 8),

-- Xiaomi 14
(3, N'Màn hình', N'LTPO OLED, 6.36 inch, 1.5K, 120Hz', 1),
(3, N'Hệ điều hành', N'Xiaomi HyperOS (Android 14)', 2),
(3, N'Chip xử lý', N'Snapdragon 8 Gen 3', 3),
(3, N'RAM', N'12 GB', 4),
(3, N'Bộ nhớ trong', N'256 GB', 5),
(3, N'Camera sau', N'Ba camera Leica 50 MP', 6),
(3, N'Camera trước', N'32 MP', 7),
(3, N'Dung lượng pin', N'4610 mAh, Sạc nhanh 90W', 8),

-- MacBook Pro M3
(4, N'Màn hình', N'Liquid Retina XDR, 14.2 inch, 120Hz', 1),
(4, N'CPU', N'Apple M3 8-Core', 2),
(4, N'RAM', N'8 GB', 3),
(4, N'Bộ nhớ SSD', N'512 GB', 4),
(4, N'Card đồ họa', N'Apple GPU 10-Core', 5),
(4, N'Trọng lượng', N'1.55 kg', 6),
(4, N'Hệ điều hành', N'macOS Sonoma', 7),

-- Dell XPS 13
(5, N'Màn hình', N'FHD+ IPS, 13.4 inch, Anti-Glare', 1),
(5, N'CPU', N'Intel Core Ultra 7 155H (up to 4.8 GHz)', 2),
(5, N'RAM', N'16 GB LPDDR5X', 3),
(5, N'Bộ nhớ SSD', N'512 GB PCIe NVMe', 4),
(5, N'Card đồ họa', N'Intel Arc Graphics', 5),
(5, N'Trọng lượng', N'1.19 kg', 6),
(5, N'Hệ điều hành', N'Windows 11 Home', 7),

-- Asus TUF
(6, N'Màn hình', N'FHD IPS, 15.6 inch, 144Hz, G-Sync', 1),
(6, N'CPU', N'AMD Ryzen 7 7735HS', 2),
(6, N'RAM', N'16 GB DDR5', 3),
(6, N'Bộ nhớ SSD', N'512 GB PCIe NVMe', 4),
(6, N'Card đồ họa', N'NVIDIA GeForce RTX 4050 6GB GDDR6', 5),
(6, N'Trọng lượng', N'2.20 kg', 6),
(6, N'Hệ điều hành', N'Windows 11 Home', 7),

-- iPad Air 6
(7, N'Màn hình', N'Liquid Retina IPS, 11 inch, True Tone', 1),
(7, N'Chip xử lý', N'Apple M2 8-Core', 2),
(7, N'RAM', N'8 GB', 3),
(7, N'Bộ nhớ trong', N'128 GB', 4),
(7, N'Camera', N'Sau 12 MP, Trước 12 MP siêu rộng', 5),
(7, N'Hệ điều hành', N'iPadOS 17', 6),

-- AirPods Pro 2
(11, N'Kết nối', N'Bluetooth 5.3', 1),
(11, N'Chip xử lý', N'Apple H2 chip, Apple U1 chip in case', 2),
(11, N'Chuẩn kháng nước', N'IPX4 kháng mồ hôi và nước', 3),
(11, N'Thời lượng pin', N'Lên đến 6 giờ nghe nhạc (30 giờ kèm hộp sạc)', 4),
(11, N'Cổng sạc', N'USB-C', 5);
GO

-- 4. Thêm kho hàng ban đầu (inventory)
INSERT INTO inventory (product_id, quantity, reorder_level, last_count_date) VALUES
(1, 50, 10, GETDATE()),
(2, 40, 10, GETDATE()),
(3, 35, 10, GETDATE()),
(4, 20, 5, GETDATE()),
(5, 15, 5, GETDATE()),
(6, 25, 5, GETDATE()),
(7, 30, 8, GETDATE()),
(8, 20, 8, GETDATE()),
(9, 40, 10, GETDATE()),
(10, 30, 10, GETDATE()),
(11, 100, 20, GETDATE()),
(12, 150, 25, GETDATE());
GO

-- 5. Thêm nhật ký kho ban đầu (inventory_logs)
INSERT INTO inventory_logs (product_id, transaction_type, quantity, reference_type, notes) VALUES
(1, N'in', 50, N'purchase_order', N'Nhập kho ban đầu'),
(2, N'in', 40, N'purchase_order', N'Nhập kho ban đầu'),
(3, N'in', 35, N'purchase_order', N'Nhập kho ban đầu'),
(4, N'in', 20, N'purchase_order', N'Nhập kho ban đầu'),
(5, N'in', 15, N'purchase_order', N'Nhập kho ban đầu'),
(6, N'in', 25, N'purchase_order', N'Nhập kho ban đầu'),
(7, N'in', 30, N'purchase_order', N'Nhập kho ban đầu'),
(8, N'in', 20, N'purchase_order', N'Nhập kho ban đầu'),
(9, N'in', 40, N'purchase_order', N'Nhập kho ban đầu'),
(10, N'in', 30, N'purchase_order', N'Nhập kho ban đầu'),
(11, N'in', 100, N'purchase_order', N'Nhập kho ban đầu'),
(12, N'in', 150, N'purchase_order', N'Nhập kho ban đầu');
GO

-- 6. Thêm mã giảm giá (coupons)
INSERT INTO coupons (coupon_code, description, discount_type, discount_value, max_discount, min_order_amount, max_uses, used_count, start_date, end_date, status) VALUES
(N'WELCOME100', N'Giảm trực tiếp 100.000₫ cho khách hàng mới với đơn hàng từ 1.000.000₫', N'fixed', 100000.00, 100000.00, 1000000.00, 1000, 0, GETDATE(), DATEADD(day, 90, GETDATE()), N'active'),
(N'TECHSUMMER', N'Giảm 10% tối đa 500.000₫ cho đơn hàng công nghệ từ 2.000.000₫', N'percent', 10.00, 500000.00, 2000000.00, 500, 0, GETDATE(), DATEADD(day, 30, GETDATE()), N'active');
GO

-- 7. Thêm banner (banners)
INSERT INTO banners (title, image, link, position, display_order, status, start_date, end_date) VALUES
(N'Siêu Phẩm iPhone 15 Pro Max', N'', N'products/iphone-15-pro-max-256gb', N'main_slider', 1, N'active', GETDATE(), DATEADD(day, 30, GETDATE())),
(N'Đồng Hồ Thông Minh Mới Nhất', N'', N'products/apple-watch-series-9-41mm', N'main_slider', 2, N'active', GETDATE(), DATEADD(day, 30, GETDATE())),
(N'Phụ Kiện Chính Hãng Apple', N'', N'products/airpods-pro-2-usb-c', N'main_slider', 3, N'active', GETDATE(), DATEADD(day, 30, GETDATE()));
GO

-- 8. Thêm tin tức công nghệ (news)
INSERT INTO news (title, slug, content, excerpt, featured_image, author_id, category, status, view_count, published_at) VALUES
(N'Đánh giá chi tiết iPhone 15 Pro Max sau 6 tháng sử dụng', N'danh-gia-chi-tiet-iphone-15-pro-max-sau-6-thang', 
N'<p>Sau 6 tháng có mặt trên thị trường, iPhone 15 Pro Max vẫn là mẫu flagship được săn lùng nhiều nhất. Trải nghiệm thực tế cho thấy khung Titan giữ được vẻ sang trọng, không trầy xước nhiều.</p><p>Chip A17 Pro chiến mượt mọi game nặng nhất như Genshin Impact. Thời lượng pin cực trâu, dễ dàng đạt 8-9 tiếng sử dụng màn hình liên tục.</p>', 
N'Sau 6 tháng trải nghiệm thực tế, iPhone 15 Pro Max chứng minh vị thế độc tôn với hiệu năng cực đỉnh và khung Titan bền bỉ.', 
N'', 1, N'Đánh giá', N'published', 245, GETDATE()),

(N'Top 5 máy tính bảng cho học sinh, sinh viên tốt nhất năm 2026', N'top-5-may-tinh-bang-cho-hoc-sinh-sinh-vien', 
N'<p>Năm học mới sắp bắt đầu, việc trang bị một chiếc máy tính bảng giúp học sinh, sinh viên ghi chép tài liệu dễ dàng hơn bao giờ hết.</p><p>Danh sách đề xuất bao gồm iPad Air 6, Galaxy Tab S9 FE và một số lựa chọn giá rẻ từ Xiaomi có tặng kèm bút vẽ stylus cao cấp.</p>', 
N'Tìm kiếm mẫu máy tính bảng hỗ trợ học tập, làm việc hiệu quả với giá thành phải chăng kèm bút stylus.', 
N'', 1, N'Tư vấn', N'published', 189, GETDATE());
GO

-- 9. Thêm đánh giá mẫu (reviews)
INSERT INTO reviews (product_id, user_id, rating, comment, status, created_at) VALUES
(1, 3, 5, N'Máy cực kỳ sang xịn mịn, khung titan cầm siêu nhẹ và đầm tay. Chụp ảnh zoom 5x nét căng.', N'approved', GETDATE()),
(3, 3, 4, N'Điện thoại dùng rất mượt và nhỏ gọn vừa túi quần. Camera Leica chụp ảnh có chiều sâu ấn tượng, chỉ hơi tiếc là pin dùng một ngày vừa đủ chứ không quá trâu.', N'approved', GETDATE()),
(3, 3, 5, N'Quá hài lòng với Xiaomi 14. Thiết kế viền siêu mỏng, sạc 90W cực kỳ nhanh.', N'approved', GETDATE()),
(4, 3, 5, N'Màn hình Liquid Retina XDR quá đẹp, màu đen sâu. Loa nghe hay đỉnh cao, làm việc văn phòng máy không hề nóng.', N'approved', GETDATE()),
(5, 3, 4, N'Bàn phím Dell XPS gõ rất êm, touchpad tàng hình nhìn hiện đại. Tuy nhiên các cổng kết nối hơi hạn chế nên phải mua đầu chuyển.', N'approved', GETDATE()),
(6, 3, 5, N'Chiến game siêu mượt, quạt tản nhiệt chạy êm không bị quá rú. Thiết kế chuẩn quân đội cứng cáp.', N'approved', GETDATE()),
(7, 3, 5, N'Màu xanh lục của iPad Air mới rất nhã nhặn. Dùng chung với Apple Pencil Pro phục vụ vẽ vời thiết kế quá tuyệt vời.', N'approved', GETDATE()),
(8, 3, 4, N'Bút S-Pen viết rất nhạy. Máy có kháng nước nên mang ra quán cafe dùng yên tâm hơn hẳn.', N'approved', GETDATE()),
(9, 3, 5, N'Tính năng chạm hai lần Double Tap rất tiện khi tay đang bận làm việc khác. Màn hình cực sáng khi ở ngoài nắng.', N'approved', GETDATE()),
(10, 3, 4, N'Đo giấc ngủ rất chi tiết, có phân tích thành phần cơ thể để tập gym. Pin hơi hẻo, ngày nào cũng phải sạc.', N'approved', GETDATE()),
(11, 3, 5, N'Chống ồn đỉnh cao, ngồi trên xe bus bật nhạc là không nghe thấy tiếng xung quanh nữa. Cổng USB-C sạc chung với điện thoại tiện lợi.', N'approved', GETDATE()),
(11, 3, 5, N'Tai đeo êm tai không bị đau, chất âm cải thiện rõ rệt so với bản cũ. Rất đáng nâng cấp.', N'approved', GETDATE()),
(12, 3, 5, N'Sạc rất nhanh và không bị nóng máy nhiều. Đúng chuẩn chính hãng Apple yên tâm sử dụng.', N'approved', GETDATE());
GO
