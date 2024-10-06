-- For the 'orders' table
ALTER TABLE `orders`
ADD COLUMN `is_backdate` INT DEFAULT 0;

-- For the 'order_items' table
ALTER TABLE `order_items`
ADD COLUMN `is_backdate` INT DEFAULT 0,
ADD COLUMN `product_id` INT UNSIGNED,
ADD INDEX (`product_id`);

ALTER TABLE `order_items`
ADD CONSTRAINT `order_items_product_id_foreign`
FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
ON DELETE CASCADE;