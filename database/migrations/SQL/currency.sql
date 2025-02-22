-- First ALTER TABLE statement
ALTER TABLE `purchasing_orders` 
    ADD COLUMN `currency` VARCHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'IDR',
    ADD COLUMN `currency_timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ADD COLUMN `exchange_rate` DECIMAL(20,6) NULL COMMENT 'Exchange rate to IDR',
    ADD INDEX `idx_po_currency` (`currency`);

-- Second ALTER TABLE statement (run this separately)
ALTER TABLE `purchasing_order_items` 
    ADD COLUMN `currency` VARCHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'IDR',
    ADD COLUMN `currency_amount` DECIMAL(20,6) NULL COMMENT 'Amount in selected currency',
    ADD COLUMN `currency_timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ADD INDEX `idx_poi_currency` (`currency`);