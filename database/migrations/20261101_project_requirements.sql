ALTER TABLE orders MODIFY status ENUM('pending','confirmed','processing','shipping','delivered','cancelled','return_requested','refunded') NOT NULL DEFAULT 'pending';

CREATE TABLE IF NOT EXISTS order_status_history (
 id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, from_status VARCHAR(40) NULL,
 to_status VARCHAR(40) NOT NULL, actor_id INT NULL, note TEXT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_order_history_order(order_id),
 CONSTRAINT fk_order_history_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
 CONSTRAINT fk_order_history_actor FOREIGN KEY(actor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_addresses (
 id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL,
 label VARCHAR(80) NOT NULL DEFAULT 'Nhà riêng', recipient_name VARCHAR(150) NOT NULL,
 phone VARCHAR(30) NOT NULL, address_line VARCHAR(255) NOT NULL,
 city VARCHAR(100) NOT NULL, district VARCHAR(100) NULL, ward VARCHAR(100) NULL,
 is_default TINYINT(1) NOT NULL DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 KEY idx_user_addresses_user(user_id),
 CONSTRAINT fk_user_addresses_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
