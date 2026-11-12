/*
  OPTIONAL additive migration for deployments that ALREADY use MySQL.
  This does not convert SQL Server to MySQL. Do not run on SQL Server.
  Take a backup and test on a staging copy first.
*/
CREATE TABLE IF NOT EXISTS product_variants (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    sku VARCHAR(80) NOT NULL,
    variant_name VARCHAR(200) NOT NULL,
    attributes_json JSON NULL,
    price_adjustment DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock_quantity INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY ux_product_variants_sku (sku),
    KEY ix_product_variants_product (product_id,is_active),
    CONSTRAINT fk_product_variants_product FOREIGN KEY(product_id) REFERENCES products(id),
    CONSTRAINT ck_product_variants_stock CHECK(stock_quantity >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @ddl = IF(NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='cart_items' AND column_name='variant_id'),'ALTER TABLE cart_items ADD COLUMN variant_id INT NULL','SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @ddl = IF(NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='order_details' AND column_name='variant'),'ALTER TABLE order_details ADD COLUMN variant VARCHAR(200) NULL','SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @ddl = IF(NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='order_details' AND column_name='variant_id'),'ALTER TABLE order_details ADD COLUMN variant_id INT NULL','SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @ddl = IF(NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='order_details' AND column_name='variant_sku'),'ALTER TABLE order_details ADD COLUMN variant_sku VARCHAR(80) NULL','SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @ddl = IF(NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='order_details' AND column_name='variant_name'),'ALTER TABLE order_details ADD COLUMN variant_name VARCHAR(200) NULL','SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @ddl = IF(NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='payments' AND column_name='provider_txn_ref'),'ALTER TABLE payments ADD COLUMN provider_txn_ref VARCHAR(100) NULL','SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @ddl = IF(NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='payments' AND column_name='provider_transaction_no'),'ALTER TABLE payments ADD COLUMN provider_transaction_no VARCHAR(30) NULL','SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @ddl = IF(NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='payments' AND column_name='provider_transaction_date'),'ALTER TABLE payments ADD COLUMN provider_transaction_date CHAR(14) NULL','SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS vnpay_refunds (
    id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    request_id VARCHAR(50) NOT NULL,
    transaction_ref VARCHAR(100) NOT NULL,
    transaction_no VARCHAR(30) NOT NULL,
    transaction_date CHAR(14) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    transaction_type CHAR(2) NOT NULL,
    state ENUM('requested','processing','succeeded','rejected','unknown') NOT NULL DEFAULT 'requested',
    response_code VARCHAR(10) NULL,
    transaction_status VARCHAR(10) NULL,
    response_json TEXT NULL,
    requested_by INT NULL,
    reason VARCHAR(500) NULL,
    active_order_id INT GENERATED ALWAYS AS (CASE WHEN state IN ('requested','processing','unknown') THEN order_id ELSE NULL END) STORED,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY ux_vnpay_refunds_request (request_id),
    UNIQUE KEY ux_vnpay_refunds_one_open_order (active_order_id),
    KEY ix_vnpay_refunds_order (order_id,state),
    CONSTRAINT fk_vnpay_refunds_order FOREIGN KEY(order_id) REFERENCES orders(id),
    CONSTRAINT fk_vnpay_refunds_user FOREIGN KEY(requested_by) REFERENCES users(id),
    CONSTRAINT ck_vnpay_refunds_amount CHECK(amount > 0),
    CONSTRAINT ck_vnpay_refunds_type CHECK(transaction_type IN ('02','03'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @ddl = IF(NOT EXISTS (SELECT 1 FROM information_schema.key_column_usage WHERE constraint_schema=DATABASE() AND table_name='cart_items' AND column_name='variant_id' AND referenced_table_name='product_variants'),
    'ALTER TABLE cart_items ADD CONSTRAINT fk_cart_items_variant FOREIGN KEY(variant_id) REFERENCES product_variants(id)', 'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @ddl = IF(NOT EXISTS (SELECT 1 FROM information_schema.key_column_usage WHERE constraint_schema=DATABASE() AND table_name='order_details' AND column_name='variant_id' AND referenced_table_name='product_variants'),
    'ALTER TABLE order_details ADD CONSTRAINT fk_order_details_variant FOREIGN KEY(variant_id) REFERENCES product_variants(id)', 'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @ddl = IF(NOT EXISTS (SELECT 1 FROM information_schema.statistics WHERE table_schema=DATABASE() AND table_name='payments' AND index_name='ux_payments_provider_txn_ref'),
    'CREATE UNIQUE INDEX ux_payments_provider_txn_ref ON payments(provider_txn_ref)', 'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','processing','shipping','delivered','cancelled','return_requested','return_received','refunded') DEFAULT 'pending';
