-- ===================================
-- Database: webbandienthoaimoi
-- Mục đích: Website bán điện thoại, laptop, tablet, đồng hồ thông minh
-- ===================================

-- Xóa database nếu tồn tại (Đã tắt để tương thích hosting free)
-- DROP DATABASE IF EXISTS webbandienthoaimoi;

-- Tạo database (Đã tắt để tương thích hosting free)
-- CREATE DATABASE IF NOT EXISTS webbandienthoaimoi 
-- CHARACTER SET utf8mb4 
-- COLLATE utf8mb4_unicode_ci;

-- USE webbandienthoaimoi;

-- Dọn dẹp sạch sẽ các bảng cũ theo thứ tự quan hệ khóa ngoại (từ con đến cha)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS promo_details;
DROP TABLE IF EXISTS promo_campaigns;
DROP TABLE IF EXISTS inventory_logs;
DROP TABLE IF EXISTS inventory;
DROP TABLE IF EXISTS order_status_history;
DROP TABLE IF EXISTS user_addresses;
DROP TABLE IF EXISTS order_details;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS shipping;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS coupon_uses;
DROP TABLE IF EXISTS coupons;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS wishlists;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS product_specs;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS brands;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS newsletter_subscribers;
DROP TABLE IF EXISTS banners;
DROP TABLE IF EXISTS news;
SET FOREIGN_KEY_CHECKS = 1;


-- ===================================
-- 1. Bảng ROLES - Phân quyền
-- ===================================
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu roles
INSERT INTO roles (name, description) VALUES 
('admin', 'Quản trị viên hệ thống'),
('staff', 'Nhân viên bán hàng'),
('customer', 'Khách hàng'),
('guest', 'Khách vãng lai');

-- ===================================
-- 2. Bảng USERS - Người dùng hệ thống
-- ===================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    full_name VARCHAR(255) NOT NULL,
    avatar VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    district VARCHAR(100),
    ward VARCHAR(100),
    zip_code VARCHAR(20),
    role_id INT DEFAULT 3,
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    KEY idx_username (username),
    KEY idx_email (email),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_reset_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_password_reset_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 3. Bảng PERMISSIONS - Quyền hạn
-- ===================================
CREATE TABLE permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 4. Bảng ROLE_PERMISSIONS - Quyền của từng role
-- ===================================
CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 5. Bảng CATEGORIES - Danh mục sản phẩm
-- ===================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    parent_id INT,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    KEY idx_slug (slug),
    KEY idx_status (status),
    KEY idx_parent_id (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu categories
INSERT INTO categories (name, slug, description, display_order, status) VALUES 
('Điện Thoại', 'dien-thoai', 'Các loại điện thoại di động', 1, 'active'),
('Laptop', 'laptop', 'Máy tính xách tay', 2, 'active'),
('Máy Tính Bảng', 'may-tinh-bang', 'Tablet và iPad', 3, 'active'),
('Đồng Hồ Thông Minh', 'dong-ho-thong-minh', 'Smartwatch và wearables', 4, 'active'),
('Phụ Kiện', 'phu-kien', 'Phụ kiện công nghệ', 5, 'active');

-- ===================================
-- 6. Bảng BRANDS - Thương hiệu
-- ===================================
CREATE TABLE brands (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    slug VARCHAR(255) NOT NULL UNIQUE,
    logo VARCHAR(255),
    description TEXT,
    website VARCHAR(255),
    country VARCHAR(100),
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_slug (slug),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu brands
INSERT INTO brands (name, slug, country, display_order, status) VALUES 
('Apple', 'apple', 'Mỹ', 1, 'active'),
('Samsung', 'samsung', 'Hàn Quốc', 2, 'active'),
('Xiaomi', 'xiaomi', 'Trung Quốc', 3, 'active'),
('Oppo', 'oppo', 'Trung Quốc', 4, 'active'),
('Asus', 'asus', 'Đài Loan', 5, 'active'),
('Dell', 'dell', 'Mỹ', 6, 'active'),
('HP', 'hp', 'Mỹ', 7, 'active'),
('Lenovo', 'lenovo', 'Trung Quốc', 8, 'active'),
('MSI', 'msi', 'Đài Loan', 9, 'active'),
('Acer', 'acer', 'Đài Loan', 10, 'active');

-- ===================================
-- 7. Bảng PRODUCTS - Sản phẩm
-- ===================================
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_code VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    category_id INT NOT NULL,
    brand_id INT NOT NULL,
    description LONGTEXT,
    short_description TEXT,
    price_cost DECIMAL(12,2) NOT NULL,
    price_sale DECIMAL(12,2) NOT NULL,
    price_discount DECIMAL(12,2),
    discount_percent INT DEFAULT 0,
    main_image VARCHAR(255),
    status ENUM('active', 'inactive', 'discontinued') DEFAULT 'active',
    stock_quantity INT DEFAULT 0,
    view_count INT DEFAULT 0,
    rating_avg DECIMAL(3,2) DEFAULT 0,
    rating_count INT DEFAULT 0,
    is_featured INT DEFAULT 0,
    is_hot_deal INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (brand_id) REFERENCES brands(id),
    KEY idx_slug (slug),
    KEY idx_product_code (product_code),
    KEY idx_category_id (category_id),
    KEY idx_brand_id (brand_id),
    KEY idx_status (status),
    KEY idx_is_featured (is_featured),
    KEY idx_rating_avg (rating_avg),
    KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 8. Bảng PRODUCT_IMAGES - Hình ảnh sản phẩm
-- ===================================
CREATE TABLE product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    is_main INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    KEY idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 9. Bảng PRODUCT_SPECS - Thông số kỹ thuật
-- ===================================
CREATE TABLE product_specs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    spec_name VARCHAR(255) NOT NULL,
    spec_value TEXT NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    KEY idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 10. Bảng CARTS - Giỏ hàng
-- ===================================
CREATE TABLE carts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    session_id VARCHAR(255),
    total_items INT DEFAULT 0,
    total_price DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_user_id (user_id),
    KEY idx_session_id (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 11. Bảng CART_ITEMS - Chi tiết giỏ hàng
-- ===================================
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    KEY idx_cart_id (cart_id),
    KEY idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 12. Bảng ORDERS - Đơn hàng
-- ===================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(100) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    ship_date DATETIME,
    delivery_date DATETIME,
    total_amount DECIMAL(12,2) NOT NULL,
    discount_amount DECIMAL(12,2) DEFAULT 0,
    shipping_fee DECIMAL(12,2) DEFAULT 0,
    final_amount DECIMAL(12,2) NOT NULL,
    
    -- Thông tin giao hàng
    ship_name VARCHAR(255) NOT NULL,
    ship_phone VARCHAR(20) NOT NULL,
    ship_email VARCHAR(100),
    ship_address TEXT NOT NULL,
    ship_city VARCHAR(100),
    ship_district VARCHAR(100),
    ship_ward VARCHAR(100),
    ship_zip VARCHAR(20),
    
    -- Trạng thái
    status ENUM('pending', 'confirmed', 'processing', 'shipping', 'delivered', 'cancelled', 'return_requested', 'refunded') DEFAULT 'pending',
    payment_method ENUM('cod', 'transfer', 'qr_banking', 'wallet') DEFAULT 'cod',
    payment_status ENUM('unpaid', 'paid', 'refund') DEFAULT 'unpaid',
    
    -- Ghi chú
    customer_note TEXT,
    admin_note TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    KEY idx_order_number (order_number),
    KEY idx_user_id (user_id),
    KEY idx_status (status),
    KEY idx_payment_status (payment_status),
    KEY idx_order_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 13. Bảng ORDER_DETAILS - Chi tiết đơn hàng
-- ===================================
CREATE TABLE order_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    discount DECIMAL(12,2) DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    KEY idx_order_id (order_id),
    KEY idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, from_status VARCHAR(40) NULL,
    to_status VARCHAR(40) NOT NULL, actor_id INT NULL, note TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_order_history_order (order_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL,
    label VARCHAR(80) NOT NULL DEFAULT 'Nhà riêng', recipient_name VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL, address_line VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL, district VARCHAR(100) NULL, ward VARCHAR(100) NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_user_addresses_user (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 14. Bảng PAYMENTS - Thanh toán
-- ===================================
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    transaction_code VARCHAR(255),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_date DATETIME,
    bank_name VARCHAR(255),
    account_number VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    KEY idx_order_id (order_id),
    KEY idx_status (status),
    KEY idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 15. Bảng SHIPPING - Vận chuyển
-- ===================================
CREATE TABLE shipping (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    tracking_number VARCHAR(100) NOT NULL UNIQUE,
    shipping_provider VARCHAR(100),
    status ENUM('pending', 'picked_up', 'in_transit', 'delivered', 'failed') DEFAULT 'pending',
    ship_date DATETIME,
    delivery_date DATETIME,
    estimated_delivery DATETIME,
    shipping_fee DECIMAL(12,2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    KEY idx_order_id (order_id),
    KEY idx_tracking_number (tracking_number),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 16. Bảng COUPONS - Mã giảm giá
-- ===================================
CREATE TABLE coupons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coupon_code VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    discount_type ENUM('fixed', 'percent') DEFAULT 'percent',
    discount_value DECIMAL(12,2) NOT NULL,
    max_discount DECIMAL(12,2),
    min_order_amount DECIMAL(12,2),
    max_uses INT,
    used_count INT DEFAULT 0,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_coupon_code (coupon_code),
    KEY idx_status (status),
    KEY idx_end_date (end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 17. Bảng COUPON_USES - Lịch sử sử dụng coupon
-- ===================================
CREATE TABLE coupon_uses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coupon_id INT NOT NULL,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    discount_amount DECIMAL(12,2) NOT NULL,
    used_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_coupon_id (coupon_id),
    KEY idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 18. Bảng REVIEWS - Đánh giá sản phẩm
-- ===================================
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL DEFAULT 5,
    title VARCHAR(255),
    comment TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    helpful_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    KEY idx_product_id (product_id),
    KEY idx_user_id (user_id),
    KEY idx_status (status),
    KEY idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 19. Bảng WISHLISTS - Sản phẩm yêu thích
-- ===================================
CREATE TABLE wishlists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    KEY idx_user_id (user_id),
    KEY idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 20. Bảng BANNERS - Banner quảng cáo
-- ===================================
CREATE TABLE banners (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    position VARCHAR(50),
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    start_date DATETIME,
    end_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_status (status),
    KEY idx_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 21. Bảng NEWS - Tin tức
-- ===================================
CREATE TABLE news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    author_id INT,
    category VARCHAR(100),
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    view_count INT DEFAULT 0,
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    KEY idx_slug (slug),
    KEY idx_status (status),
    KEY idx_published_at (published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 22. Bảng INVENTORY - Quản lý kho
-- ===================================
CREATE TABLE inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    reorder_level INT DEFAULT 10,
    last_count_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 23. Bảng INVENTORY_LOGS - Lịch sử kho
-- ===================================
CREATE TABLE inventory_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    transaction_type ENUM('in', 'out', 'adjust', 'return') DEFAULT 'in',
    quantity INT NOT NULL,
    reference_id INT,
    reference_type VARCHAR(50),
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    KEY idx_product_id (product_id),
    KEY idx_created_at (created_at),
    KEY idx_reference_type (reference_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 24. Bảng NOTIFICATIONS - Thông báo
-- ===================================
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON,
    is_read INT DEFAULT 0,
    read_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_user_id (user_id),
    KEY idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 25. Bảng PROMO_CAMPAIGNS - Chiến dịch khuyến mãi
-- ===================================
CREATE TABLE promo_campaigns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('flash_sale', 'category_discount', 'brand_discount', 'quantity_discount') DEFAULT 'flash_sale',
    discount_type ENUM('fixed', 'percent') DEFAULT 'percent',
    discount_value DECIMAL(12,2) NOT NULL,
    max_discount DECIMAL(12,2),
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('active', 'inactive', 'scheduled') DEFAULT 'inactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_status (status),
    KEY idx_start_date (start_date),
    KEY idx_end_date (end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 26. Bảng PROMO_DETAILS - Chi tiết khuyến mãi
-- ===================================
CREATE TABLE promo_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    campaign_id INT NOT NULL,
    category_id INT,
    brand_id INT,
    product_id INT,
    FOREIGN KEY (campaign_id) REFERENCES promo_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    KEY idx_campaign_id (campaign_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TRIGGERS (Đã vô hiệu hóa để tương thích hosting free - Thay thế bằng logic PHP ở Backend)
-- ===================================

-- Trigger: Cập nhật số lượng sản phẩm sau khi order
-- DELIMITER //
-- CREATE TRIGGER update_product_stock_after_order
-- AFTER INSERT ON order_details
-- FOR EACH ROW
-- BEGIN
--     UPDATE products 
--     SET stock_quantity = stock_quantity - NEW.quantity
--     WHERE id = NEW.product_id;
--     
--     UPDATE inventory 
--     SET quantity = quantity - NEW.quantity
--     WHERE product_id = NEW.product_id;
-- END //
-- DELIMITER ;

-- Trigger: Ghi log thay đổi kho
-- DELIMITER //
-- CREATE TRIGGER log_inventory_change
-- AFTER UPDATE ON inventory
-- FOR EACH ROW
-- BEGIN
--     IF NEW.quantity != OLD.quantity THEN
--         INSERT INTO inventory_logs (product_id, transaction_type, quantity, reference_type, notes)
--         VALUES (NEW.product_id, 'adjust', (NEW.quantity - OLD.quantity), 'system_adjust', 'Cập nhật kho');
--     END IF;
-- END //
-- DELIMITER ;

-- Trigger: Cập nhật rating sản phẩm
-- DELIMITER //
-- CREATE TRIGGER update_product_rating_after_review
-- AFTER INSERT ON reviews
-- FOR EACH ROW
-- BEGIN
--     UPDATE products 
--     SET rating_avg = (
--         SELECT AVG(rating) FROM reviews WHERE product_id = NEW.product_id AND status = 'approved'
--     ),
--     rating_count = (
--         SELECT COUNT(*) FROM reviews WHERE product_id = NEW.product_id AND status = 'approved'
--     )
--     WHERE id = NEW.product_id;
-- END //
-- DELIMITER ;

-- Trigger: Cập nhật tổng tiền đơn hàng
-- DELIMITER //
-- CREATE TRIGGER update_order_total_after_details
-- AFTER INSERT ON order_details
-- FOR EACH ROW
-- BEGIN
--     UPDATE orders 
--     SET total_amount = (
--         SELECT SUM(subtotal) FROM order_details WHERE order_id = NEW.order_id
--     )
--     WHERE id = NEW.order_id;
-- END //
-- DELIMITER ;

-- ===================================
-- INDEXES
-- ===================================
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_brand ON products(brand_id);
CREATE INDEX idx_reviews_product ON reviews(product_id);
CREATE INDEX idx_reviews_user ON reviews(user_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_inventory_product ON inventory(product_id);

-- ===================================
-- Tạo tài khoản admin mặc định
-- ===================================
INSERT INTO users (username, email, password, phone, full_name, role_id, status) VALUES 
('admin', 'admin@bandienthoaimoi.com', '$2y$10$eImiTXuWVxfaHNYY0iPffe.Y5f7WMwO6IHUw9owFh1lxB9XfXe2Sy', '0123456789', 'Admin', 1, 'active'),
('employee', 'employee@bandienthoaimoi.com', '$2y$10$eImiTXuWVxfaHNYY0iPffe.Y5f7WMwO6IHUw9owFh1lxB9XfXe2Sy', '0987654321', 'Nhân viên', 2, 'active'),
('customer_demo', 'customer@bandienthoaimoi.com', '$2y$10$eImiTXuWVxfaHNYY0iPffe.Y5f7WMwO6IHUw9owFh1lxB9XfXe2Sy', '0888888888', 'Khách Hàng Demo', 3, 'active');

-- Mật khẩu mặc định là: password123
