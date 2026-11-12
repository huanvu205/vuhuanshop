DECLARE @statusCheck SYSNAME, @dropCheck NVARCHAR(MAX);
SELECT TOP 1 @statusCheck = cc.name FROM sys.check_constraints cc
 WHERE cc.parent_object_id = OBJECT_ID('orders')
 AND cc.definition LIKE '%pending%' AND cc.definition LIKE '%cancelled%';
IF @statusCheck IS NOT NULL BEGIN
 SET @dropCheck = N'ALTER TABLE orders DROP CONSTRAINT ' + QUOTENAME(@statusCheck);
 EXEC sp_executesql @dropCheck;
END;
ALTER TABLE orders ADD CONSTRAINT CK_orders_status_project CHECK
 (status IN ('pending','confirmed','processing','shipping','delivered','cancelled','return_requested','refunded'));
GO
IF OBJECT_ID('order_status_history','U') IS NULL
BEGIN
 CREATE TABLE order_status_history (
  id INT IDENTITY(1,1) PRIMARY KEY, order_id INT NOT NULL,
  from_status NVARCHAR(40) NULL, to_status NVARCHAR(40) NOT NULL,
  actor_id INT NULL, note NVARCHAR(MAX) NULL, created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY(actor_id) REFERENCES users(id) ON DELETE SET NULL
 );
 CREATE INDEX idx_order_history_order ON order_status_history(order_id);
END;
GO
IF OBJECT_ID('user_addresses','U') IS NULL
BEGIN
 CREATE TABLE user_addresses (
  id INT IDENTITY(1,1) PRIMARY KEY, user_id INT NOT NULL,
  label NVARCHAR(80) NOT NULL DEFAULT N'Nhà riêng', recipient_name NVARCHAR(150) NOT NULL,
  phone NVARCHAR(30) NOT NULL, address_line NVARCHAR(255) NOT NULL,
  city NVARCHAR(100) NOT NULL, district NVARCHAR(100) NULL, ward NVARCHAR(100) NULL,
  is_default BIT NOT NULL DEFAULT 0, created_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME2 DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
 );
 CREATE INDEX idx_user_addresses_user ON user_addresses(user_id);
END;
GO
