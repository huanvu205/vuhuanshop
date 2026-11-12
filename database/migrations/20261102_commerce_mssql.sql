/*
  VuHuanShop additive SQL Server migration.
  Review and back up the database before applying. No existing rows are deleted.
  This migration intentionally does not change the database engine or secrets.
*/
SET XACT_ABORT ON;
BEGIN TRY
    BEGIN TRANSACTION;

    IF OBJECT_ID(N'dbo.product_variants', N'U') IS NULL
    BEGIN
        CREATE TABLE dbo.product_variants (
            id INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_product_variants PRIMARY KEY,
            product_id INT NOT NULL,
            sku NVARCHAR(80) NOT NULL,
            variant_name NVARCHAR(200) NOT NULL,
            attributes_json NVARCHAR(MAX) NULL,
            price_adjustment DECIMAL(12,2) NOT NULL CONSTRAINT DF_product_variants_price DEFAULT 0,
            stock_quantity INT NOT NULL CONSTRAINT DF_product_variants_stock DEFAULT 0,
            is_active BIT NOT NULL CONSTRAINT DF_product_variants_active DEFAULT 1,
            created_at DATETIME2 NOT NULL CONSTRAINT DF_product_variants_created DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME2 NOT NULL CONSTRAINT DF_product_variants_updated DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT FK_product_variants_product FOREIGN KEY(product_id) REFERENCES dbo.products(id),
            CONSTRAINT CK_product_variants_stock CHECK(stock_quantity >= 0)
        );
        CREATE UNIQUE INDEX UX_product_variants_sku ON dbo.product_variants(sku);
        CREATE INDEX IX_product_variants_product ON dbo.product_variants(product_id, is_active);
    END;

    IF COL_LENGTH(N'dbo.order_details', N'variant_id') IS NULL
        ALTER TABLE dbo.order_details ADD variant_id INT NULL;
    IF COL_LENGTH(N'dbo.order_details', N'variant') IS NULL
        ALTER TABLE dbo.order_details ADD variant NVARCHAR(200) NULL;
    IF COL_LENGTH(N'dbo.order_details', N'variant_sku') IS NULL
        ALTER TABLE dbo.order_details ADD variant_sku NVARCHAR(80) NULL;
    IF COL_LENGTH(N'dbo.order_details', N'variant_name') IS NULL
        ALTER TABLE dbo.order_details ADD variant_name NVARCHAR(200) NULL;
    IF COL_LENGTH(N'dbo.cart_items', N'variant_id') IS NULL
        ALTER TABLE dbo.cart_items ADD variant_id INT NULL;
    IF NOT EXISTS (SELECT 1 FROM sys.foreign_keys WHERE name = N'FK_order_details_variant')
        ALTER TABLE dbo.order_details ADD CONSTRAINT FK_order_details_variant
            FOREIGN KEY(variant_id) REFERENCES dbo.product_variants(id);
    IF NOT EXISTS (SELECT 1 FROM sys.foreign_keys WHERE name = N'FK_cart_items_variant')
        ALTER TABLE dbo.cart_items ADD CONSTRAINT FK_cart_items_variant
            FOREIGN KEY(variant_id) REFERENCES dbo.product_variants(id);

    IF OBJECT_ID(N'dbo.vnpay_refunds', N'U') IS NULL
    BEGIN
        CREATE TABLE dbo.vnpay_refunds (
            id BIGINT IDENTITY(1,1) NOT NULL CONSTRAINT PK_vnpay_refunds PRIMARY KEY,
            order_id INT NOT NULL,
            request_id NVARCHAR(50) NOT NULL,
            transaction_ref NVARCHAR(100) NOT NULL,
            transaction_no NVARCHAR(30) NOT NULL,
            transaction_date CHAR(14) NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            transaction_type CHAR(2) NOT NULL,
            state NVARCHAR(20) NOT NULL CONSTRAINT DF_vnpay_refunds_state DEFAULT N'requested',
            response_code NVARCHAR(10) NULL,
            transaction_status NVARCHAR(10) NULL,
            response_json NVARCHAR(MAX) NULL,
            requested_by INT NULL,
            reason NVARCHAR(500) NULL,
            created_at DATETIME2 NOT NULL CONSTRAINT DF_vnpay_refunds_created DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME2 NOT NULL CONSTRAINT DF_vnpay_refunds_updated DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT FK_vnpay_refunds_order FOREIGN KEY(order_id) REFERENCES dbo.orders(id),
            CONSTRAINT FK_vnpay_refunds_user FOREIGN KEY(requested_by) REFERENCES dbo.users(id),
            CONSTRAINT CK_vnpay_refunds_amount CHECK(amount > 0),
            CONSTRAINT CK_vnpay_refunds_type CHECK(transaction_type IN ('02','03')),
            CONSTRAINT CK_vnpay_refunds_state CHECK(state IN (N'requested',N'processing',N'succeeded',N'rejected',N'unknown'))
        );
        CREATE UNIQUE INDEX UX_vnpay_refunds_request ON dbo.vnpay_refunds(request_id);
        CREATE INDEX IX_vnpay_refunds_order ON dbo.vnpay_refunds(order_id, state);
        CREATE UNIQUE INDEX UX_vnpay_refunds_one_open_per_order ON dbo.vnpay_refunds(order_id)
            WHERE state IN (N'requested',N'processing',N'unknown');
    END;

    IF COL_LENGTH(N'dbo.payments', N'provider_txn_ref') IS NULL
        ALTER TABLE dbo.payments ADD provider_txn_ref NVARCHAR(100) NULL;
    IF COL_LENGTH(N'dbo.payments', N'provider_transaction_no') IS NULL
        ALTER TABLE dbo.payments ADD provider_transaction_no NVARCHAR(30) NULL;
    IF COL_LENGTH(N'dbo.payments', N'provider_transaction_date') IS NULL
        ALTER TABLE dbo.payments ADD provider_transaction_date CHAR(14) NULL;
    IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id=OBJECT_ID(N'dbo.payments') AND name=N'UX_payments_provider_txn_ref')
        CREATE UNIQUE INDEX UX_payments_provider_txn_ref ON dbo.payments(provider_txn_ref) WHERE provider_txn_ref IS NOT NULL;

    DECLARE @oldStatusCheck SYSNAME, @dropStatusSql NVARCHAR(MAX);
    WHILE EXISTS (SELECT 1 FROM sys.check_constraints WHERE parent_object_id=OBJECT_ID(N'dbo.orders') AND definition LIKE N'%pending%' AND definition LIKE N'%cancelled%')
    BEGIN
        SELECT TOP 1 @oldStatusCheck=name FROM sys.check_constraints
         WHERE parent_object_id=OBJECT_ID(N'dbo.orders') AND definition LIKE N'%pending%' AND definition LIKE N'%cancelled%';
        SET @dropStatusSql=N'ALTER TABLE dbo.orders DROP CONSTRAINT '+QUOTENAME(@oldStatusCheck);
        EXEC sp_executesql @dropStatusSql;
        SET @oldStatusCheck=NULL;
    END;
    IF NOT EXISTS (SELECT 1 FROM sys.check_constraints WHERE parent_object_id=OBJECT_ID(N'dbo.orders') AND name=N'CK_orders_status_commerce')
        ALTER TABLE dbo.orders ADD CONSTRAINT CK_orders_status_commerce CHECK(status IN ('pending','confirmed','processing','shipping','delivered','cancelled','return_requested','return_received','refunded'));

    /* Replaces the legacy trigger which blindly decremented stock and allowed negatives. */
    EXEC(N'CREATE OR ALTER TRIGGER dbo.trg_update_product_stock_after_order ON dbo.order_details AFTER INSERT AS
    BEGIN
      SET NOCOUNT ON;
      SET XACT_ABORT ON;
      IF EXISTS (
        SELECT 1 FROM inserted i LEFT JOIN dbo.product_variants v ON v.id=i.variant_id
        WHERE (i.variant_id IS NOT NULL AND (v.id IS NULL OR v.product_id<>i.product_id OR v.is_active=0))
      ) BEGIN; THROW 51001, ''Invalid or inactive product variant.'', 1; END;
      DECLARE @productGroups INT, @productUpdated INT, @variantGroups INT, @variantUpdated INT;
      SELECT @productGroups=COUNT(*) FROM (SELECT product_id FROM inserted GROUP BY product_id) g;
      UPDATE p SET p.stock_quantity=p.stock_quantity-i.qty
        FROM dbo.products p WITH (UPDLOCK,ROWLOCK)
        JOIN (SELECT product_id,SUM(quantity) qty FROM inserted GROUP BY product_id) i ON i.product_id=p.id
        WHERE p.stock_quantity>=i.qty;
      SET @productUpdated=@@ROWCOUNT;
      IF @productUpdated<>@productGroups BEGIN; THROW 51002, ''Insufficient product stock.'', 1; END;
      SELECT @variantGroups=COUNT(*) FROM (SELECT variant_id FROM inserted WHERE variant_id IS NOT NULL GROUP BY variant_id) g;
      UPDATE v SET v.stock_quantity=v.stock_quantity-i.qty, v.updated_at=SYSUTCDATETIME()
        FROM dbo.product_variants v WITH (UPDLOCK,ROWLOCK)
        JOIN (SELECT variant_id,SUM(quantity) qty FROM inserted WHERE variant_id IS NOT NULL GROUP BY variant_id) i ON i.variant_id=v.id
        WHERE v.stock_quantity>=i.qty;
      SET @variantUpdated=@@ROWCOUNT;
      IF @variantUpdated<>@variantGroups BEGIN; THROW 51003, ''Insufficient variant stock.'', 1; END;
      UPDATE inv SET inv.quantity=inv.quantity-i.qty
        FROM dbo.inventory inv JOIN (SELECT product_id,SUM(quantity) qty FROM inserted GROUP BY product_id) i ON i.product_id=inv.product_id;
    END');

    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    IF XACT_STATE() <> 0 ROLLBACK TRANSACTION;
    THROW;
END CATCH;
GO
