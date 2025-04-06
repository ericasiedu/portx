ALTER TABLE `charges_storage_rent_teu` ADD `goods` ENUM('General Goods','Engines/Spares Parts','Vehicle','DG I','DG II') NOT NULL AFTER `trade_type`; 
