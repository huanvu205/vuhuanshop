-- ===================================
-- Database: webbandienthoaimoi (MS SQL Server)
-- Mục đích: Website bán điện thoại, laptop, tablet, đồng hồ thông minh
-- ===================================

-- Xóa database nếu tồn tại (chạy riêng nếu cần thiết)
-- IF DB_ID('webbandienthoaimoi') IS NOT NULL
-- BEGIN
--     ALTER DATABASE webbandienthoaimoi SET SINGLE_USER WITH ROLLBACK IMMEDIATE;
--     DROP DATABASE webbandienthoaimoi;
-- END;
-- GO

-- CREATE DATABASE webbandienthoaimoi;
-- GO

-- USE webbandienthoaimoi;
-- GO

-- ===================================
-- 1. Bảng ROLES - Phân quyền
-- ===================================
CREATE TABLE roles (
    id INT PRIMARY KEY IDENTITY(1,1),
    name NVARCHAR(50) NOT NULL UNIQUE,
    description NVARCHAR(MAX),
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP
);

-- ===================================
-- 2. Bảng USERS - Người dùng hệ thống
-- ===================================
CREATE TABLE users (
    id INT PRIMARY KEY IDENTITY(1,1),
    username NVARCHAR(100) NOT NULL UNIQUE,
    email NVARCHAR(100) NOT NULL UNIQUE,
    password NVARCHAR(255) NOT NULL,
    phone NVARCHAR(20),
    full_name NVARCHAR(255) NOT NULL,
    avatar NVARCHAR(255),
    address NVARCHAR(MAX),
    city NVARCHAR(100),
    district NVARCHAR(100),
    ward NVARCHAR(100),
    zip_code NVARCHAR(20),
    role_id INT DEFAULT 3,
    status NVARCHAR(50) DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'banned')),
    last_login DATETIME2,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_status ON users(status);

CREATE TABLE dbo.password_reset_tokens (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME2 NOT NULL,
    created_at DATETIME2 NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT FK_password_reset_tokens_users FOREIGN KEY (user_id)
        REFERENCES dbo.users(id) ON DELETE CASCADE
);

-- ===================================
-- 3. Bảng PERMISSIONS - Quyền hạn
-- ===================================
CREATE TABLE permissions (
    id INT PRIMARY KEY IDENTITY(1,1),
    name NVARCHAR(100) NOT NULL UNIQUE,
    description NVARCHAR(MAX),
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP
);

-- ===================================
-- 4. Bảng ROLE_PERMISSIONS - Quyền của từng role
-- ===================================
CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- ===================================
-- 5. Bảng CATEGORIES - Danh mục sản phẩm
-- ===================================
CREATE TABLE categories (
    id INT PRIMARY KEY IDENTITY(1,1),
    name NVARCHAR(255) NOT NULL UNIQUE,
    slug NVARCHAR(255) NOT NULL UNIQUE,
    description NVARCHAR(MAX),
    image NVARCHAR(255),
    parent_id INT,
    display_order INT DEFAULT 0,
    status NVARCHAR(50) DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE NO ACTION
);

CREATE INDEX idx_categories_slug ON categories(slug);
CREATE INDEX idx_categories_status ON categories(status);
CREATE INDEX idx_categories_parent_id ON categories(parent_id);

-- ===================================
-- 6. Bảng BRANDS - Thương hiệu
-- ===================================
CREATE TABLE brands (
    id INT PRIMARY KEY IDENTITY(1,1),
    name NVARCHAR(255) NOT NULL UNIQUE,
    slug NVARCHAR(255) NOT NULL UNIQUE,
    logo NVARCHAR(255),
    description NVARCHAR(MAX),
    website NVARCHAR(255),
    country NVARCHAR(100),
    display_order INT DEFAULT 0,
    status NVARCHAR(50) DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_brands_slug ON brands(slug);
CREATE INDEX idx_brands_status ON brands(status);

-- ===================================
-- 7. Bảng PRODUCTS - Sản phẩm
-- ===================================
CREATE TABLE products (
    id INT PRIMARY KEY IDENTITY(1,1),
    product_code NVARCHAR(100) NOT NULL UNIQUE,
    name NVARCHAR(255) NOT NULL,
    slug NVARCHAR(255) NOT NULL UNIQUE,
    category_id INT NOT NULL,
    brand_id INT NOT NULL,
    description NVARCHAR(MAX),
    short_description NVARCHAR(MAX),
    price_cost DECIMAL(12,2) NOT NULL,
    price_sale DECIMAL(12,2) NOT NULL,
    price_discount DECIMAL(12,2),
    discount_percent INT DEFAULT 0,
    main_image NVARCHAR(255),
    status NVARCHAR(50) DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'discontinued')),
    stock_quantity INT DEFAULT 0,
    view_count INT DEFAULT 0,
    rating_avg DECIMAL(3,2) DEFAULT 0,
    rating_count INT DEFAULT 0,
    is_featured INT DEFAULT 0,
    is_hot_deal INT DEFAULT 0,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (brand_id) REFERENCES brands(id)
);

CREATE INDEX idx_products_slug ON products(slug);
CREATE INDEX idx_products_code ON products(product_code);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_brand ON products(brand_id);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_products_featured ON products(is_featured);
CREATE INDEX idx_products_rating ON products(rating_avg);
CREATE INDEX idx_products_created ON products(created_at);

-- ===================================
-- 8. Bảng PRODUCT_IMAGES - Hình ảnh sản phẩm
-- ===================================
CREATE TABLE product_images (
    id INT PRIMARY KEY IDENTITY(1,1),
    product_id INT NOT NULL,
    image_path NVARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    is_main INT DEFAULT 0,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE INDEX idx_product_images_pid ON product_images(product_id);

-- ===================================
-- 9. Bảng PRODUCT_SPECS - Thông số kỹ thuật
-- ===================================
CREATE TABLE product_specs (
    id INT PRIMARY KEY IDENTITY(1,1),
    product_id INT NOT NULL,
    spec_name NVARCHAR(255) NOT NULL,
    spec_value NVARCHAR(MAX) NOT NULL,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE INDEX idx_product_specs_pid ON product_specs(product_id);

-- ===================================
-- 10. Bảng CARTS - Giỏ hàng
-- ===================================
CREATE TABLE carts (
    id INT PRIMARY KEY IDENTITY(1,1),
    user_id INT,
    session_id NVARCHAR(255),
    total_items INT DEFAULT 0,
    total_price DECIMAL(12,2) DEFAULT 0,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_carts_uid ON carts(user_id);
CREATE INDEX idx_carts_session ON carts(session_id);

-- ===================================
-- 11. Bảng CART_ITEMS - Chi tiết giỏ hàng
-- ===================================
CREATE TABLE cart_items (
    id INT PRIMARY KEY IDENTITY(1,1),
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(12,2) NOT NULL,
    variant_id INT NULL,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE INDEX idx_cart_items_cid ON cart_items(cart_id);
CREATE INDEX idx_cart_items_pid ON cart_items(product_id);

-- ===================================
-- 12. Bảng ORDERS - Đơn hàng
-- ===================================
CREATE TABLE orders (
    id INT PRIMARY KEY IDENTITY(1,1),
    order_number NVARCHAR(100) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    order_date DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    ship_date DATETIME2,
    delivery_date DATETIME2,
    total_amount DECIMAL(12,2) NOT NULL,
    discount_amount DECIMAL(12,2) DEFAULT 0,
    shipping_fee DECIMAL(12,2) DEFAULT 0,
    final_amount DECIMAL(12,2) NOT NULL,
    
    -- Thông tin giao hàng
    ship_name NVARCHAR(255) NOT NULL,
    ship_phone NVARCHAR(20) NOT NULL,
    ship_email NVARCHAR(100),
    ship_address NVARCHAR(MAX) NOT NULL,
    ship_city NVARCHAR(100),
    ship_district NVARCHAR(100),
    ship_ward NVARCHAR(100),
    ship_zip NVARCHAR(20),
    
    -- Trạng thái
    status NVARCHAR(50) DEFAULT 'pending' CHECK (status IN ('pending', 'confirmed', 'processing', 'shipping', 'delivered', 'cancelled', 'return_requested', 'refunded')),
    payment_method NVARCHAR(50) DEFAULT 'cod' CHECK (payment_method IN ('cod', 'transfer', 'qr_banking', 'wallet')),
    payment_status NVARCHAR(50) DEFAULT 'unpaid' CHECK (payment_status IN ('unpaid', 'paid', 'refund')),
    
    -- Ghi chú
    customer_note NVARCHAR(MAX),
    admin_note NVARCHAR(MAX),
    
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE INDEX idx_orders_num ON orders(order_number);
CREATE INDEX idx_orders_uid ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_pstatus ON orders(payment_status);
CREATE INDEX idx_orders_date ON orders(order_date);

-- ===================================
-- 13. Bảng ORDER_DETAILS - Chi tiết đơn hàng
-- ===================================
CREATE TABLE order_details (
    id INT PRIMARY KEY IDENTITY(1,1),
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name NVARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    discount DECIMAL(12,2) DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE INDEX idx_order_details_oid ON order_details(order_id);
CREATE INDEX idx_order_details_pid ON order_details(product_id);

CREATE TABLE order_status_history (
    id INT PRIMARY KEY IDENTITY(1,1), order_id INT NOT NULL,
    from_status NVARCHAR(40) NULL, to_status NVARCHAR(40) NOT NULL,
    actor_id INT NULL, note NVARCHAR(MAX) NULL, created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL
);
CREATE INDEX idx_order_history_order ON order_status_history(order_id);

CREATE TABLE user_addresses (
    id INT PRIMARY KEY IDENTITY(1,1), user_id INT NOT NULL,
    label NVARCHAR(80) NOT NULL DEFAULT N'Nhà riêng', recipient_name NVARCHAR(150) NOT NULL,
    phone NVARCHAR(30) NOT NULL, address_line NVARCHAR(255) NOT NULL,
    city NVARCHAR(100) NOT NULL, district NVARCHAR(100), ward NVARCHAR(100),
    is_default BIT NOT NULL DEFAULT 0, created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX idx_user_addresses_user ON user_addresses(user_id);

-- ===================================
-- 14. Bảng PAYMENTS - Thanh toán
-- ===================================
CREATE TABLE payments (
    id INT PRIMARY KEY IDENTITY(1,1),
    order_id INT NOT NULL,
    payment_method NVARCHAR(50) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    transaction_code NVARCHAR(255),
    status NVARCHAR(50) DEFAULT 'pending' CHECK (status IN ('pending', 'completed', 'failed', 'refunded')),
    payment_date DATETIME2,
    bank_name NVARCHAR(255),
    account_number NVARCHAR(100),
    notes NVARCHAR(MAX),
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE INDEX idx_payments_oid ON payments(order_id);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_payments_date ON payments(payment_date);

-- ===================================
-- 15. Bảng SHIPPING - Vận chuyển
-- ===================================
CREATE TABLE shipping (
    id INT PRIMARY KEY IDENTITY(1,1),
    order_id INT NOT NULL,
    tracking_number NVARCHAR(100) NOT NULL UNIQUE,
    shipping_provider NVARCHAR(100),
    status NVARCHAR(50) DEFAULT 'pending' CHECK (status IN ('pending', 'picked_up', 'in_transit', 'delivered', 'failed')),
    ship_date DATETIME2,
    delivery_date DATETIME2,
    estimated_delivery DATETIME2,
    shipping_fee DECIMAL(12,2),
    notes NVARCHAR(MAX),
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE INDEX idx_shipping_oid ON shipping(order_id);
CREATE INDEX idx_shipping_track ON shipping(tracking_number);
CREATE INDEX idx_shipping_status ON shipping(status);

-- ===================================
-- 16. Bảng COUPONS - Mã giảm giá
-- ===================================
CREATE TABLE coupons (
    id INT PRIMARY KEY IDENTITY(1,1),
    coupon_code NVARCHAR(100) NOT NULL UNIQUE,
    description NVARCHAR(MAX),
    discount_type NVARCHAR(50) DEFAULT 'percent' CHECK (discount_type IN ('fixed', 'percent')),
    discount_value DECIMAL(12,2) NOT NULL,
    max_discount DECIMAL(12,2),
    min_order_amount DECIMAL(12,2),
    max_uses INT,
    used_count INT DEFAULT 0,
    start_date DATETIME2 NOT NULL,
    end_date DATETIME2 NOT NULL,
    status NVARCHAR(50) DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_coupons_code ON coupons(coupon_code);
CREATE INDEX idx_coupons_status ON coupons(status);
CREATE INDEX idx_coupons_end ON coupons(end_date);

-- ===================================
-- 17. Bảng COUPON_USES - Lịch sử sử dụng coupon
-- ===================================
CREATE TABLE coupon_uses (
    id INT PRIMARY KEY IDENTITY(1,1),
    coupon_id INT NOT NULL,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    discount_amount DECIMAL(12,2) NOT NULL,
    used_date DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE NO ACTION, -- Thay đổi ON DELETE CASCADE sang NO ACTION để tránh Cycle/Multiple paths
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE NO ACTION
);

CREATE INDEX idx_coupon_uses_cid ON coupon_uses(coupon_id);
CREATE INDEX idx_coupon_uses_uid ON coupon_uses(user_id);

-- ===================================
-- 18. Bảng REVIEWS - Đánh giá sản phẩm
-- ===================================
CREATE TABLE reviews (
    id INT PRIMARY KEY IDENTITY(1,1),
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL DEFAULT 5,
    title NVARCHAR(255),
    comment NVARCHAR(MAX),
    status NVARCHAR(50) DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
    helpful_count INT DEFAULT 0,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE NO ACTION
);

CREATE INDEX idx_reviews_pid ON reviews(product_id);
CREATE INDEX idx_reviews_uid ON reviews(user_id);
CREATE INDEX idx_reviews_status ON reviews(status);
CREATE INDEX idx_reviews_rating ON reviews(rating);

-- ===================================
-- 19. Bảng WISHLISTS - Sản phẩm yêu thích
-- ===================================
CREATE TABLE wishlists (
    id INT PRIMARY KEY IDENTITY(1,1),
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT unique_user_product UNIQUE (user_id, product_id)
);

CREATE INDEX idx_wishlists_uid ON wishlists(user_id);
CREATE INDEX idx_wishlists_pid ON wishlists(product_id);

-- ===================================
-- 20. Bảng BANNERS - Banner quảng cáo
-- ===================================
CREATE TABLE banners (
    id INT PRIMARY KEY IDENTITY(1,1),
    title NVARCHAR(255) NOT NULL,
    image NVARCHAR(255) NOT NULL,
    link NVARCHAR(255),
    position NVARCHAR(50),
    display_order INT DEFAULT 0,
    status NVARCHAR(50) DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
    start_date DATETIME2,
    end_date DATETIME2,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_banners_status ON banners(status);
CREATE INDEX idx_banners_pos ON banners(position);

-- ===================================
-- 21. Bảng NEWS - Tin tức
-- ===================================
CREATE TABLE news (
    id INT PRIMARY KEY IDENTITY(1,1),
    title NVARCHAR(255) NOT NULL,
    slug NVARCHAR(255) NOT NULL UNIQUE,
    content NVARCHAR(MAX) NOT NULL,
    excerpt NVARCHAR(MAX),
    featured_image NVARCHAR(255),
    author_id INT,
    category NVARCHAR(100),
    status NVARCHAR(50) DEFAULT 'draft' CHECK (status IN ('draft', 'published', 'archived')),
    view_count INT DEFAULT 0,
    published_at DATETIME2,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX idx_news_slug ON news(slug);
CREATE INDEX idx_news_status ON news(status);
CREATE INDEX idx_news_pub ON news(published_at);

-- ===================================
-- 22. Bảng INVENTORY - Quản lý kho
-- ===================================
CREATE TABLE inventory (
    id INT PRIMARY KEY IDENTITY(1,1),
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    reorder_level INT DEFAULT 10,
    last_count_date DATETIME2,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT unique_product UNIQUE (product_id)
);

CREATE INDEX idx_inventory_pid ON inventory(product_id);

-- ===================================
-- 23. Bảng INVENTORY_LOGS - Lịch sử kho
-- ===================================
CREATE TABLE inventory_logs (
    id INT PRIMARY KEY IDENTITY(1,1),
    product_id INT NOT NULL,
    transaction_type NVARCHAR(50) DEFAULT 'in' CHECK (transaction_type IN ('in', 'out', 'adjust', 'return')),
    quantity INT NOT NULL,
    reference_id INT,
    reference_type NVARCHAR(50),
    notes NVARCHAR(MAX),
    created_by INT,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX idx_inventory_logs_pid ON inventory_logs(product_id);
CREATE INDEX idx_inventory_logs_created ON inventory_logs(created_at);
CREATE INDEX idx_inventory_logs_ref ON inventory_logs(reference_type);

-- ===================================
-- 24. Bảng NOTIFICATIONS - Thông báo
-- ===================================
CREATE TABLE notifications (
    id INT PRIMARY KEY IDENTITY(1,1),
    user_id INT,
    type NVARCHAR(50) NOT NULL,
    title NVARCHAR(255) NOT NULL,
    message NVARCHAR(MAX) NOT NULL,
    data NVARCHAR(MAX), -- JSON stringified
    is_read INT DEFAULT 0,
    read_at DATETIME2,
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_notifications_uid ON notifications(user_id);
CREATE INDEX idx_notifications_read ON notifications(is_read);

-- ===================================
-- 25. Bảng PROMO_CAMPAIGNS - Chiến dịch khuyến mãi
-- ===================================
CREATE TABLE promo_campaigns (
    id INT PRIMARY KEY IDENTITY(1,1),
    name NVARCHAR(255) NOT NULL,
    description NVARCHAR(MAX),
    type NVARCHAR(50) DEFAULT 'flash_sale' CHECK (type IN ('flash_sale', 'category_discount', 'brand_discount', 'quantity_discount')),
    discount_type NVARCHAR(50) DEFAULT 'percent' CHECK (discount_type IN ('fixed', 'percent')),
    discount_value DECIMAL(12,2) NOT NULL,
    max_discount DECIMAL(12,2),
    start_date DATETIME2 NOT NULL,
    end_date DATETIME2 NOT NULL,
    status NVARCHAR(50) DEFAULT 'inactive' CHECK (status IN ('active', 'inactive', 'scheduled')),
    created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_promo_campaigns_status ON promo_campaigns(status);
CREATE INDEX idx_promo_campaigns_start ON promo_campaigns(start_date);
CREATE INDEX idx_promo_campaigns_end ON promo_campaigns(end_date);

-- ===================================
-- 26. Bảng PROMO_DETAILS - Chi tiết khuyến mãi
-- ===================================
CREATE TABLE promo_details (
    id INT PRIMARY KEY IDENTITY(1,1),
    campaign_id INT NOT NULL,
    category_id INT,
    brand_id INT,
    product_id INT,
    FOREIGN KEY (campaign_id) REFERENCES promo_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE NO ACTION,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE NO ACTION,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE NO ACTION
);

CREATE INDEX idx_promo_details_cid ON promo_details(campaign_id);

-- ===================================
-- TRIGGERS CẬP NHẬT updated_at TỰ ĐỘNG
-- ===================================
GO

CREATE TRIGGER trg_users_update ON users AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE users SET updated_at = GETDATE() FROM users INNER JOIN inserted i ON users.id = i.id;
END;
GO

CREATE TRIGGER trg_categories_update ON categories AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE categories SET updated_at = GETDATE() FROM categories INNER JOIN inserted i ON categories.id = i.id;
END;
GO

CREATE TRIGGER trg_brands_update ON brands AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE brands SET updated_at = GETDATE() FROM brands INNER JOIN inserted i ON brands.id = i.id;
END;
GO

CREATE TRIGGER trg_products_update ON products AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE products SET updated_at = GETDATE() FROM products INNER JOIN inserted i ON products.id = i.id;
END;
GO

CREATE TRIGGER trg_carts_update ON carts AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE carts SET updated_at = GETDATE() FROM carts INNER JOIN inserted i ON carts.id = i.id;
END;
GO

CREATE TRIGGER trg_cart_items_update ON cart_items AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE cart_items SET updated_at = GETDATE() FROM cart_items INNER JOIN inserted i ON cart_items.id = i.id;
END;
GO

CREATE TRIGGER trg_orders_update ON orders AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE orders SET updated_at = GETDATE() FROM orders INNER JOIN inserted i ON orders.id = i.id;
END;
GO

CREATE TRIGGER trg_payments_update ON payments AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE payments SET updated_at = GETDATE() FROM payments INNER JOIN inserted i ON payments.id = i.id;
END;
GO

CREATE TRIGGER trg_shipping_update ON shipping AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE shipping SET updated_at = GETDATE() FROM shipping INNER JOIN inserted i ON shipping.id = i.id;
END;
GO

CREATE TRIGGER trg_coupons_update ON coupons AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE coupons SET updated_at = GETDATE() FROM coupons INNER JOIN inserted i ON coupons.id = i.id;
END;
GO

CREATE TRIGGER trg_reviews_update ON reviews AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE reviews SET updated_at = GETDATE() FROM reviews INNER JOIN inserted i ON reviews.id = i.id;
END;
GO

CREATE TRIGGER trg_banners_update ON banners AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE banners SET updated_at = GETDATE() FROM banners INNER JOIN inserted i ON banners.id = i.id;
END;
GO

CREATE TRIGGER trg_news_update ON news AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE news SET updated_at = GETDATE() FROM news INNER JOIN inserted i ON news.id = i.id;
END;
GO

CREATE TRIGGER trg_inventory_update ON inventory AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE inventory SET updated_at = GETDATE() FROM inventory INNER JOIN inserted i ON inventory.id = i.id;
END;
GO

CREATE TRIGGER trg_promo_campaigns_update ON promo_campaigns AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    UPDATE promo_campaigns SET updated_at = GETDATE() FROM promo_campaigns INNER JOIN inserted i ON promo_campaigns.id = i.id;
END;
GO

-- ===================================
-- TRIGGERS HÀNH VI (STOCK, RATING, TOTALS)
-- ===================================

-- Trigger: Cập nhật số lượng sản phẩm sau khi order
CREATE TRIGGER trg_update_product_stock_after_order ON order_details AFTER INSERT AS
BEGIN
    SET NOCOUNT ON;
    UPDATE p
    SET p.stock_quantity = p.stock_quantity - i.quantity
    FROM products p
    INNER JOIN inserted i ON p.id = i.product_id;
    
    UPDATE inv
    SET inv.quantity = inv.quantity - i.quantity
    FROM inventory inv
    INNER JOIN inserted i ON inv.product_id = i.product_id;
END;
GO

-- Trigger: Ghi log thay đổi kho
CREATE TRIGGER trg_log_inventory_change ON inventory AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO inventory_logs (product_id, transaction_type, quantity, reference_type, notes)
    SELECT i.product_id, 'adjust', (i.quantity - d.quantity), 'system_adjust', N'Cập nhật kho'
    FROM inserted i
    INNER JOIN deleted d ON i.id = d.id
    WHERE i.quantity <> d.quantity;
END;
GO

-- Trigger: Cập nhật rating sản phẩm
CREATE TRIGGER trg_update_product_rating_after_review ON reviews AFTER INSERT AS
BEGIN
    SET NOCOUNT ON;
    UPDATE p
    SET p.rating_avg = COALESCE((SELECT AVG(CAST(rating AS DECIMAL(3,2))) FROM reviews WHERE product_id = i.product_id AND status = 'approved'), 0),
        p.rating_count = (SELECT COUNT(*) FROM reviews WHERE product_id = i.product_id AND status = 'approved')
    FROM products p
    INNER JOIN inserted i ON p.id = i.product_id;
END;
GO

-- Trigger: Cập nhật tổng tiền đơn hàng
CREATE TRIGGER trg_update_order_total_after_details ON order_details AFTER INSERT AS
BEGIN
    SET NOCOUNT ON;
    UPDATE o
    SET o.total_amount = COALESCE((SELECT SUM(subtotal) FROM order_details WHERE order_id = i.order_id), 0)
    FROM orders o
    INNER JOIN inserted i ON o.id = i.order_id;
END;
GO

-- ===================================
-- Tạo tài khoản mặc định
-- ===================================
-- Bật IDENTITY_INSERT cho bảng roles để chèn data chuẩn
SET IDENTITY_INSERT roles ON;
INSERT INTO roles (id, name, description) VALUES 
(1, N'admin', N'Quản trị viên hệ thống'),
(2, N'staff', N'Nhân viên bán hàng'),
(3, N'customer', N'Khách hàng'),
(4, N'guest', N'Khách vãng lai');
SET IDENTITY_INSERT roles OFF;
GO

SET IDENTITY_INSERT users ON;
INSERT INTO users (id, username, email, password, phone, full_name, role_id, status) VALUES 
(1, N'admin', N'admin@vuhuanshop.vn', N'$2y$10$eImiTXuWVxfaHNYY0iPffe.Y5f7WMwO6IHUw9owFh1lxB9XfXe2Sy', N'0123456789', N'Admin', 1, N'active'),
(2, N'employee', N'employee@vuhuanshop.vn', N'$2y$10$eImiTXuWVxfaHNYY0iPffe.Y5f7WMwO6IHUw9owFh1lxB9XfXe2Sy', N'0987654321', N'Nhân viên', 2, N'active'),
(3, N'customer_demo', N'customer@vuhuanshop.vn', N'$2y$10$eImiTXuWVxfaHNYY0iPffe.Y5f7WMwO6IHUw9owFh1lxB9XfXe2Sy', N'0888888888', N'Khách Hàng Demo', 3, N'active');
SET IDENTITY_INSERT users OFF;
GO
