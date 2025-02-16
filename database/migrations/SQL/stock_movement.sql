DROP TABLE `stock_movement`;

CREATE TABLE `stock_movement` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `movement_timestamp` timestamp NULL DEFAULT NULL,
  `inventory_id` int DEFAULT NULL,
  `product_id` int NULL DEFAULT NULL,
  `shop_id` int NULL DEFAULT NULL,
  `qty` int NULL DEFAULT NULL,
  `initial_qty` int NULL DEFAULT NULL,
  `status` varchar NULL DEFAULT NULL,
  `source` varchar NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `admin_note` text COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;