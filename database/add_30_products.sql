-- Additive, rerunnable catalog import. Does not delete or modify existing products.
-- Retail reference prices and model listings checked on FPT Shop on 2026-10-14.
-- price_cost is 0 because the shop's wholesale cost was not supplied; enter the
-- actual cost before relying on profit reports. price_sale uses the observed
-- retailer listing price as a starting reference, not a claim about wholesale cost.
-- Product images are left NULL so the storefront uses its existing placeholder;
-- no retailer/manufacturer images are copied without verified reuse rights.

DROP TABLE IF EXISTS seed30_specs;
DROP TABLE IF EXISTS seed30_products;

INSERT IGNORE INTO brands (name, slug, country, status) VALUES
('HONOR', 'honor', 'Trung Quốc', 'active'),
('Huawei', 'huawei', 'Trung Quốc', 'active'),
('Bose', 'bose', 'Mỹ', 'active'),
('Marshall', 'marshall', 'Anh', 'active'),
('Sony', 'sony', 'Nhật Bản', 'active'),
('Havit', 'havit', 'Trung Quốc', 'active'),
('Trusmi', 'trusmi', 'Trung Quốc', 'active');

CREATE TABLE seed30_products (
    product_code VARCHAR(100) NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    category_slug VARCHAR(255) NOT NULL,
    brand_slug VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    short_description TEXT NOT NULL,
    price_sale DECIMAL(12,2) NOT NULL,
    source_url VARCHAR(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO seed30_products
(product_code, name, slug, category_slug, brand_slug, description, short_description, price_sale, source_url)
VALUES
('ADD30-PH-001', 'Xiaomi POCO C81 Pro 4GB/128GB', 'xiaomi-poco-c81-pro-4gb-128gb', 'dien-thoai', 'xiaomi',
 'Điện thoại POCO C81 Pro phiên bản RAM 4GB, bộ nhớ 128GB. Phù hợp nhu cầu liên lạc, học tập, giải trí và sử dụng ứng dụng hằng ngày.',
 'RAM 4GB, bộ nhớ trong 128GB; giá tham khảo FPT Shop.', 3890000,
 'https://fptshop.com.vn/dien-thoai/xiaomi-poco-c81-pro'),
('ADD30-PH-002', 'Xiaomi POCO M8 Pro 5G 8GB/256GB', 'xiaomi-poco-m8-pro-5g-8gb-256gb', 'dien-thoai', 'xiaomi',
 'Điện thoại POCO M8 Pro 5G với RAM 8GB, bộ nhớ 256GB và kết nối 5G; dành cho nhu cầu đa nhiệm và giải trí.',
 'RAM 8GB, bộ nhớ trong 256GB, hỗ trợ 5G.', 8990000,
 'https://fptshop.com.vn/dien-thoai/xiaomi-poco-m8-pro-8gb'),
('ADD30-PH-003', 'HONOR X9d 5G 8GB/256GB', 'honor-x9d-5g-8gb-256gb', 'dien-thoai', 'honor',
 'Điện thoại HONOR X9d 5G phiên bản RAM 8GB và bộ nhớ trong 256GB, đáp ứng nhu cầu lưu trữ và kết nối di động tốc độ cao.',
 'RAM 8GB, bộ nhớ trong 256GB, hỗ trợ 5G.', 11990000,
 'https://fptshop.com.vn/dien-thoai/honor-x9d'),
('ADD30-PH-004', 'Xiaomi Redmi Note 17 4GB/128GB', 'xiaomi-redmi-note-17-4gb-128gb', 'dien-thoai', 'xiaomi',
 'Điện thoại Redmi Note 17 phiên bản RAM 4GB và bộ nhớ trong 128GB, phù hợp nhu cầu sử dụng phổ thông.',
 'RAM 4GB, bộ nhớ trong 128GB.', 6490000,
 'https://fptshop.com.vn/dien-thoai/xiaomi-redmi-note-17'),
('ADD30-PH-005', 'OPPO Reno16 F 5G 8GB/256GB', 'oppo-reno16-f-5g-8gb-256gb', 'dien-thoai', 'oppo',
 'Điện thoại OPPO Reno16 F 5G phiên bản RAM 8GB và bộ nhớ trong 256GB, phục vụ chụp ảnh, giải trí và đa nhiệm.',
 'RAM 8GB, bộ nhớ trong 256GB, hỗ trợ 5G.', 14990000,
 'https://fptshop.com.vn/dien-thoai/oppo-reno16-f'),
('ADD30-PH-006', 'Samsung Galaxy A17 8GB/128GB', 'samsung-galaxy-a17-8gb-128gb', 'dien-thoai', 'samsung',
 'Điện thoại Samsung Galaxy A17 phiên bản RAM 8GB và bộ nhớ trong 128GB, phù hợp nhu cầu sử dụng mỗi ngày.',
 'RAM 8GB, bộ nhớ trong 128GB.', 5490000,
 'https://fptshop.com.vn/dien-thoai/samsung-galaxy-a17'),

('ADD30-LT-001', 'Acer Aspire Lite 14 AL14-44P-R0SP Ryzen 7 7730U', 'acer-aspire-lite-14-al14-44p-r0sp-ryzen-7-7730u', 'laptop', 'acer',
 'Laptop Acer Aspire Lite 14 mã AL14-44P-R0SP sử dụng AMD Ryzen 7 7730U, thiết kế gọn cho học tập và công việc.',
 'AMD Ryzen 7 7730U, màn hình 14 inch; cấu hình theo mã sản phẩm.', 19990000,
 'https://fptshop.com.vn/may-tinh-xach-tay/acer-aspire-lite-14-al14-44p-r0sp-r7-7730u'),
('ADD30-LT-002', 'HP 250R G10 Core 5-120U C3SH7AT', 'hp-250r-g10-core-5-120u-c3sh7at', 'laptop', 'hp',
 'Laptop HP 250R G10 mã C3SH7AT sử dụng Intel Core 5-120U, phù hợp học tập, làm việc văn phòng và hội họp trực tuyến.',
 'Intel Core 5-120U; cấu hình theo mã C3SH7AT.', 22990000,
 'https://fptshop.com.vn/may-tinh-xach-tay/hp-250r-g10-core-5-120u-c3sh7at'),
('ADD30-LT-003', 'ASUS Vivobook X1404VA-EB355W Core 7 150U', 'asus-vivobook-x1404va-eb355w-core-7-150u', 'laptop', 'asus',
 'Laptop ASUS Vivobook X1404VA-EB355W sử dụng Intel Core 7 150U trong thiết kế 14 inch.',
 'Intel Core 7 150U, dòng Vivobook 14.', 22990000,
 'https://fptshop.com.vn/may-tinh-xach-tay/asus-vivobook-x1404va-eb355w-core-7-150u'),
('ADD30-LT-004', 'Dell 14 DC14250 DC4C5386W Core 5 120U', 'dell-14-dc14250-dc4c5386w-core-5-120u', 'laptop', 'dell',
 'Laptop Dell 14 DC14250 mã DC4C5386W trang bị Intel Core 5 120U trong thiết kế 14 inch.',
 'Intel Core 5 120U, màn hình 14 inch, mã DC4C5386W.', 25990000,
 'https://fptshop.com.vn/may-tinh-xach-tay/dell-14-dc14250-core-5-120u-dc4c5386w'),
('ADD30-LT-005', 'Lenovo IdeaPad Slim 3 16IWC11 83RS002KVN Core 5 320', 'lenovo-ideapad-slim-3-16iwc11-83rs002kvn-core-5-320', 'laptop', 'lenovo',
 'Laptop Lenovo IdeaPad Slim 3 16IWC11 mã 83RS002KVN sử dụng Intel Core 5 320 và màn hình 16 inch.',
 'Intel Core 5 320, màn hình 16 inch, mã 83RS002KVN.', 19990000,
 'https://fptshop.com.vn/may-tinh-xach-tay/lenovo-ideapad-slim-3-16iwc11-83rs002kvn'),
('ADD30-LT-006', 'Acer Aspire Lite 15 AL15-36P-30TN Celeron N350', 'acer-aspire-lite-15-al15-36p-30tn-celeron-n350', 'laptop', 'acer',
 'Laptop Acer Aspire Lite 15 mã AL15-36P-30TN sử dụng Intel Celeron N350, phù hợp học tập và các tác vụ văn phòng cơ bản.',
 'Intel Celeron N350, màn hình 15 inch.', 16990000,
 'https://fptshop.com.vn/may-tinh-xach-tay/acer-aspire-lite-15-al15-36p-30tn-c3-n350'),
('ADD30-TB-001', 'iPad A16 WiFi 128GB', 'ipad-a16-wifi-128gb', 'may-tinh-bang', 'apple',
 'Máy tính bảng iPad A16 phiên bản WiFi 128GB, phù hợp học tập, làm việc và giải trí trên màn hình lớn.',
 'Chip Apple A16, kết nối WiFi, bộ nhớ 128GB.', 12090000,
 'https://fptshop.com.vn/may-tinh-bang/ipad-a16-wifi'),
('ADD30-TB-002', 'HONOR Pad X7 WiFi 4GB/128GB', 'honor-pad-x7-wifi-4gb-128gb', 'may-tinh-bang', 'honor',
 'Máy tính bảng HONOR Pad X7 phiên bản WiFi, RAM 4GB và bộ nhớ trong 128GB; thiết kế nhỏ gọn cho học tập và giải trí.',
 'RAM 4GB, bộ nhớ 128GB, WiFi.', 3590000,
 'https://fptshop.com.vn/may-tinh-bang/honor-pad-x7-wifi'),
('ADD30-TB-003', 'Samsung Galaxy Tab S10 Lite WiFi 6GB/128GB', 'samsung-galaxy-tab-s10-lite-wifi-6gb-128gb', 'may-tinh-bang', 'samsung',
 'Máy tính bảng Samsung Galaxy Tab S10 Lite phiên bản WiFi, RAM 6GB và bộ nhớ trong 128GB.',
 'RAM 6GB, bộ nhớ 128GB, kết nối WiFi.', 8390000,
 'https://fptshop.com.vn/may-tinh-bang/samsung-galaxy-tab-s10-lite'),
('ADD30-TB-004', 'Lenovo Idea Tab WiFi 4GB/128GB', 'lenovo-idea-tab-wifi-4gb-128gb', 'may-tinh-bang', 'lenovo',
 'Máy tính bảng Lenovo Idea Tab WiFi RAM 4GB, bộ nhớ 128GB; listing tham khảo kèm bao da và bút.',
 'RAM 4GB, bộ nhớ 128GB, WiFi; kèm bao da và bút theo listing.', 6190000,
 'https://fptshop.com.vn/may-tinh-bang/lenovo-idea-tab-wifi-4gb'),
('ADD30-TB-005', 'HONOR Pad X9a WiFi 6GB/128GB', 'honor-pad-x9a-wifi-6gb-128gb', 'may-tinh-bang', 'honor',
 'Máy tính bảng HONOR Pad X9a phiên bản WiFi RAM 6GB, bộ nhớ 128GB; listing tham khảo kèm bao da bàn phím.',
 'RAM 6GB, bộ nhớ 128GB, WiFi; listing kèm bao da bàn phím.', 7290000,
 'https://fptshop.com.vn/may-tinh-bang/honor-pad-x9a'),
('ADD30-TB-006', 'Samsung Galaxy Tab A11 WiFi 4GB/64GB', 'samsung-galaxy-tab-a11-wifi-4gb-64gb', 'may-tinh-bang', 'samsung',
 'Máy tính bảng Samsung Galaxy Tab A11 phiên bản WiFi với RAM 4GB và bộ nhớ trong 64GB.',
 'RAM 4GB, bộ nhớ 64GB, WiFi.', 3590000,
 'https://fptshop.com.vn/may-tinh-bang/samsung-galaxy-tab-a11-wifi'),

('ADD30-W-001', 'Apple Watch SE 3 GPS 40mm', 'apple-watch-se-3-gps-40mm', 'dong-ho-thong-minh', 'apple',
 'Đồng hồ thông minh Apple Watch SE 3 phiên bản GPS, kích thước 40mm; listing tham khảo với dây cao su.',
 'GPS, kích thước 40mm.', 5990000,
 'https://fptshop.com.vn/smartwatch/apple-watch-se-3-gps-40mm-vien-nhom-day-cao-su'),
('ADD30-W-002', 'Huawei Watch GT 7 46mm Silicone', 'huawei-watch-gt-7-46mm-silicone', 'dong-ho-thong-minh', 'huawei',
 'Đồng hồ thông minh Huawei Watch GT 7 kích thước 46mm, dây silicone.',
 'Kích thước 46mm, dây silicone.', 5490000,
 'https://fptshop.com.vn/smartwatch/huawei-watch-gt-7-46mm-silicone'),
('ADD30-W-003', 'Huawei Watch GT 7 41mm Silicone', 'huawei-watch-gt-7-41mm-silicone', 'dong-ho-thong-minh', 'huawei',
 'Đồng hồ thông minh Huawei Watch GT 7 kích thước 41mm, dây silicone.',
 'Kích thước 41mm, dây silicone.', 5490000,
 'https://fptshop.com.vn/smartwatch/huawei-watch-gt-7-41mm-silicone'),
('ADD30-W-004', 'Apple Watch Ultra 3 GPS + Cellular 49mm', 'apple-watch-ultra-3-gps-cellular-49mm', 'dong-ho-thong-minh', 'apple',
 'Đồng hồ Apple Watch Ultra 3 phiên bản GPS + Cellular, vỏ titan kích thước 49mm.',
 'GPS + Cellular, vỏ titan, kích thước 49mm.', 22990000,
 'https://fptshop.com.vn/smartwatch/apple-watch-ultra-3-gps-cellular-49mm-vien-titan-day-ocean'),
('ADD30-W-005', 'Xiaomi Redmi Watch 6 Active', 'xiaomi-redmi-watch-6-active', 'dong-ho-thong-minh', 'xiaomi',
 'Đồng hồ thông minh Xiaomi Redmi Watch 6 Active, hỗ trợ theo dõi hoạt động và kết nối với điện thoại tương thích.',
 'Đồng hồ thông minh Redmi Watch 6 Active.', 1190000,
 'https://fptshop.com.vn/smartwatch/xiaomi-redmi-watch-6-active'),
('ADD30-W-006', 'Samsung Galaxy Watch9 40mm Bluetooth', 'samsung-galaxy-watch9-40mm-bluetooth', 'dong-ho-thong-minh', 'samsung',
 'Đồng hồ thông minh Samsung Galaxy Watch9 phiên bản Bluetooth, kích thước 40mm.',
 'Bluetooth, kích thước 40mm.', 8490000,
 'https://fptshop.com.vn/smartwatch/samsung-galaxy-watch9-40mm'),

('ADD30-PK-001', 'Bose SoundLink Max', 'bose-soundlink-max', 'phu-kien', 'bose',
 'Loa Bluetooth Bose SoundLink Max, thiết kế di động cho nhu cầu nghe nhạc trong nhà và ngoài trời.',
 'Loa Bluetooth di động Bose.', 8470000,
 'https://fptshop.com.vn/phu-kien/loa-bluetooth-bose-soundlink-max'),
('ADD30-PK-002', 'Marshall Emberton III', 'marshall-emberton-iii', 'phu-kien', 'marshall',
 'Loa Bluetooth di động Marshall Emberton III với thiết kế nhỏ gọn, chuẩn kháng bụi nước IP67 và thời lượng pin được công bố trên 32 giờ.',
 'Loa di động, IP67, Bluetooth 5.3 LE.', 4700000,
 'https://fptshop.com.vn/phu-kien/loa-bluetooth-marshall-emberton-iii'),
('ADD30-PK-003', 'Bose SoundLink Micro 2', 'bose-soundlink-micro-2', 'phu-kien', 'bose',
 'Loa Bluetooth Bose SoundLink Micro 2 kích thước nhỏ gọn, chuẩn kháng bụi nước IP67 và thời lượng pin được công bố đến 12 giờ.',
 'Loa Bluetooth Bose, IP67.', 2570000,
 'https://fptshop.com.vn/phu-kien/loa-bluetooth-bose-soundlink-micro-2'),
('ADD30-PK-004', 'Sony SRS-XB100', 'sony-srs-xb100', 'phu-kien', 'sony',
 'Loa Bluetooth di động Sony SRS-XB100 với bộ tản âm thụ động, phù hợp nhu cầu nghe nhạc cá nhân.',
 'Loa Bluetooth di động Sony, có bộ tản âm thụ động.', 990000,
 'https://fptshop.com.vn/phu-kien/loa-bluetooth-sony-srs-xb100-extra-bass'),
('ADD30-PK-005', 'Tai nghe Bluetooth Havit H618BT', 'havit-h618bt', 'phu-kien', 'havit',
 'Tai nghe chụp tai Bluetooth Havit H618BT với driver 40mm và thời lượng pin được công bố đến 55 giờ.',
 'Tai nghe chụp tai Bluetooth, driver 40mm.', 269000,
 'https://fptshop.com.vn/phu-kien/tai-nghe-bluetooth-choang-dau-co-mic-havit-h618bt'),
('ADD30-PK-006', 'Tai nghe Trusmi WS18-011 Open Ear', 'trusmi-ws18-011-open-ear', 'phu-kien', 'trusmi',
 'Tai nghe Trusmi WS18-011 thiết kế Open Ear dạng kẹp, driver 11mm, giúp người dùng nghe nội dung trong khi vẫn nhận biết âm thanh xung quanh.',
 'Tai nghe Open Ear dạng kẹp, driver 11mm.', 299000,
 'https://fptshop.com.vn/phu-kien/tai-nghe-bluetooth-open-ear-trusmi-ws18-011');

INSERT INTO products
(product_code, name, slug, category_id, brand_id, description, short_description,
 price_cost, price_sale, price_discount, discount_percent, main_image, status,
 stock_quantity, is_featured, is_hot_deal, is_best_selling, is_newest)
SELECT
    s.product_code, s.name, s.slug, c.id, b.id, s.description, s.short_description,
    0, s.price_sale, NULL, 0, NULL, 'active', 15, 0, 0, 0, 1
FROM seed30_products s
JOIN categories c ON c.slug = s.category_slug AND c.status = 'active'
JOIN brands b ON b.slug = s.brand_slug AND b.status = 'active'
LEFT JOIN products existing ON existing.product_code = s.product_code
WHERE existing.id IS NULL;

INSERT INTO inventory (product_id, quantity, reorder_level)
SELECT p.id, 15, 5
FROM products p
JOIN seed30_products s ON s.product_code = p.product_code
LEFT JOIN inventory i ON i.product_id = p.id
WHERE i.product_id IS NULL;

CREATE TABLE seed30_specs (
    product_code VARCHAR(100) NOT NULL,
    spec_name VARCHAR(255) NOT NULL,
    spec_value TEXT NOT NULL,
    display_order INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO seed30_specs (product_code, spec_name, spec_value, display_order) VALUES
('ADD30-PH-001', 'RAM', '4GB', 1), ('ADD30-PH-001', 'Bộ nhớ trong', '128GB', 2), ('ADD30-PH-001', 'Kết nối', 'Theo phiên bản POCO C81 Pro', 3),
('ADD30-PH-002', 'RAM', '8GB', 1), ('ADD30-PH-002', 'Bộ nhớ trong', '256GB', 2), ('ADD30-PH-002', 'Kết nối di động', '5G', 3),
('ADD30-PH-003', 'RAM', '8GB', 1), ('ADD30-PH-003', 'Bộ nhớ trong', '256GB', 2), ('ADD30-PH-003', 'Kết nối di động', '5G', 3),
('ADD30-PH-004', 'RAM', '4GB', 1), ('ADD30-PH-004', 'Bộ nhớ trong', '128GB', 2), ('ADD30-PH-004', 'Dòng sản phẩm', 'Redmi Note 17', 3),
('ADD30-PH-005', 'RAM', '8GB', 1), ('ADD30-PH-005', 'Bộ nhớ trong', '256GB', 2), ('ADD30-PH-005', 'Kết nối di động', '5G', 3),
('ADD30-PH-006', 'RAM', '8GB', 1), ('ADD30-PH-006', 'Bộ nhớ trong', '128GB', 2), ('ADD30-PH-006', 'Dòng sản phẩm', 'Samsung Galaxy A17', 3),
('ADD30-LT-001', 'Bộ xử lý', 'AMD Ryzen 7 7730U', 1), ('ADD30-LT-001', 'Kích thước màn hình', '14 inch', 2), ('ADD30-LT-001', 'Mã sản phẩm', 'AL14-44P-R0SP', 3),
('ADD30-LT-002', 'Bộ xử lý', 'Intel Core 5-120U', 1), ('ADD30-LT-002', 'Dòng máy', 'HP 250R G10', 2), ('ADD30-LT-002', 'Mã sản phẩm', 'C3SH7AT', 3),
('ADD30-LT-003', 'Bộ xử lý', 'Intel Core 7 150U', 1), ('ADD30-LT-003', 'Dòng máy', 'ASUS Vivobook X1404VA', 2), ('ADD30-LT-003', 'Mã sản phẩm', 'EB355W', 3),
('ADD30-LT-004', 'Bộ xử lý', 'Intel Core 5 120U', 1), ('ADD30-LT-004', 'Kích thước màn hình', '14 inch', 2), ('ADD30-LT-004', 'Mã sản phẩm', 'DC4C5386W', 3),
('ADD30-LT-005', 'Bộ xử lý', 'Intel Core 5 320', 1), ('ADD30-LT-005', 'Kích thước màn hình', '16 inch', 2), ('ADD30-LT-005', 'Mã sản phẩm', '83RS002KVN', 3),
('ADD30-LT-006', 'Bộ xử lý', 'Intel Celeron N350', 1), ('ADD30-LT-006', 'Kích thước màn hình', '15 inch', 2), ('ADD30-LT-006', 'Mã sản phẩm', 'AL15-36P-30TN', 3),
('ADD30-TB-001', 'Bộ xử lý', 'Apple A16', 1), ('ADD30-TB-001', 'Bộ nhớ trong', '128GB', 2), ('ADD30-TB-001', 'Kết nối', 'WiFi', 3),
('ADD30-TB-002', 'RAM', '4GB', 1), ('ADD30-TB-002', 'Bộ nhớ trong', '128GB', 2), ('ADD30-TB-002', 'Kết nối', 'WiFi', 3),
('ADD30-TB-003', 'RAM', '6GB', 1), ('ADD30-TB-003', 'Bộ nhớ trong', '128GB', 2), ('ADD30-TB-003', 'Kết nối', 'WiFi', 3),
('ADD30-TB-004', 'RAM', '4GB', 1), ('ADD30-TB-004', 'Bộ nhớ trong', '128GB', 2), ('ADD30-TB-004', 'Kết nối', 'WiFi; listing kèm bao da và bút', 3),
('ADD30-TB-005', 'RAM', '6GB', 1), ('ADD30-TB-005', 'Bộ nhớ trong', '128GB', 2), ('ADD30-TB-005', 'Kết nối', 'WiFi; listing kèm bao da bàn phím', 3),
('ADD30-TB-006', 'RAM', '4GB', 1), ('ADD30-TB-006', 'Bộ nhớ trong', '64GB', 2), ('ADD30-TB-006', 'Kết nối', 'WiFi', 3),
('ADD30-W-001', 'Kết nối', 'GPS', 1), ('ADD30-W-001', 'Kích thước', '40mm', 2), ('ADD30-W-001', 'Loại dây', 'Dây cao su', 3),
('ADD30-W-002', 'Kích thước', '46mm', 1), ('ADD30-W-002', 'Loại dây', 'Silicone', 2), ('ADD30-W-002', 'Dòng sản phẩm', 'Huawei Watch GT 7', 3),
('ADD30-W-003', 'Kích thước', '41mm', 1), ('ADD30-W-003', 'Loại dây', 'Silicone', 2), ('ADD30-W-003', 'Dòng sản phẩm', 'Huawei Watch GT 7', 3),
('ADD30-W-004', 'Kết nối', 'GPS + Cellular', 1), ('ADD30-W-004', 'Kích thước', '49mm', 2), ('ADD30-W-004', 'Vật liệu vỏ', 'Titan', 3),
('ADD30-W-005', 'Dòng sản phẩm', 'Redmi Watch 6 Active', 1), ('ADD30-W-005', 'Kết nối', 'Bluetooth với điện thoại tương thích', 2), ('ADD30-W-005', 'Loại thiết bị', 'Đồng hồ thông minh', 3),
('ADD30-W-006', 'Kết nối', 'Bluetooth', 1), ('ADD30-W-006', 'Kích thước', '40mm', 2), ('ADD30-W-006', 'Dòng sản phẩm', 'Samsung Galaxy Watch9', 3),
('ADD30-PK-001', 'Loại thiết bị', 'Loa Bluetooth di động', 1), ('ADD30-PK-001', 'Thương hiệu', 'Bose', 2), ('ADD30-PK-001', 'Model', 'SoundLink Max', 3),
('ADD30-PK-002', 'Loại thiết bị', 'Loa Bluetooth di động', 1), ('ADD30-PK-002', 'Chuẩn kháng bụi nước', 'IP67', 2), ('ADD30-PK-002', 'Kết nối và pin', 'Bluetooth 5.3 LE; pin công bố trên 32 giờ', 3),
('ADD30-PK-003', 'Loại thiết bị', 'Loa Bluetooth di động', 1), ('ADD30-PK-003', 'Chuẩn kháng bụi nước', 'IP67', 2), ('ADD30-PK-003', 'Thời lượng pin công bố', 'Đến 12 giờ', 3),
('ADD30-PK-004', 'Loại thiết bị', 'Loa Bluetooth di động', 1), ('ADD30-PK-004', 'Model', 'SRS-XB100', 2), ('ADD30-PK-004', 'Đặc điểm', 'Bộ tản âm thụ động', 3),
('ADD30-PK-005', 'Loại thiết bị', 'Tai nghe chụp tai Bluetooth', 1), ('ADD30-PK-005', 'Kích thước driver', '40mm', 2), ('ADD30-PK-005', 'Thời lượng pin công bố', 'Đến 55 giờ', 3),
('ADD30-PK-006', 'Loại thiết bị', 'Tai nghe Open Ear dạng kẹp', 1), ('ADD30-PK-006', 'Kích thước driver', '11mm', 2), ('ADD30-PK-006', 'Model', 'WS18-011', 3);

INSERT INTO product_specs (product_id, spec_name, spec_value, display_order)
SELECT p.id, s.spec_name, s.spec_value, s.display_order
FROM seed30_specs s
JOIN products p ON p.product_code = s.product_code
WHERE NOT EXISTS (
    SELECT 1 FROM product_specs existing
    WHERE existing.product_id = p.id AND existing.spec_name = s.spec_name
);

DROP TABLE seed30_specs;
DROP TABLE seed30_products;
