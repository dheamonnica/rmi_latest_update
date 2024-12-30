ALTER TABLE rmi.stock_transfers MODIFY COLUMN delivered_time timestamp DEFAULT NULL NULL;
ALTER TABLE rmi.stock_transfers MODIFY COLUMN received_time timestamp DEFAULT NULL NULL;
ALTER TABLE rmi.stock_transfers MODIFY COLUMN send_by_warehouse_time timestamp DEFAULT NULL NULL;
ALTER TABLE rmi.stock_transfers MODIFY COLUMN on_delivery_time timestamp NULL;
ALTER TABLE rmi.stock_transfers MODIFY COLUMN approved_by_time timestamp NULL;
ALTER TABLE rmi.stock_transfers MODIFY COLUMN on_delivery_time timestamp DEFAULT NULL NULL;
ALTER TABLE rmi.stock_transfers MODIFY COLUMN approved_by_time timestamp DEFAULT NULL NULL;

ALTER TABLE rmi.stock_transfers MODIFY COLUMN transfer_date DATETIME NOT NULL;

ALTER TABLE rmi.order_items ADD request_quantity INT NULL; // Add new column