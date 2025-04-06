ALTER TABLE `invoice` CHANGE `status` `status` ENUM('UNPAID','PAID','CANCELLED','EXPIRED','DEFERRED','RECALLED') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL; 
ALTER TABLE `supplementary_invoice` CHANGE `status` `status` ENUM('UNPAID','PAID','CANCELLED','EXPIRED','DEFERRED','RECALLED') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL; 
ALTER TABLE `invoice` ADD `recalled_by` INT(11) NOT NULL AFTER `cancelled_by`; 
ALTER TABLE `supplementary_invoice` ADD `recalled_by` INT(11) NOT NULL AFTER `cancelled_by`; 

-- <<<<<<< HEAD
CREATE TABLE `portx`. ( `id` INT(11) NOT NULL AUTO_INCREMENT , `invoice_id` INT(11) NOT NULL , `container_id` INT(11) NOT NULL , `cancelled` TINYINT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB; 
-- =======
-- >>>>>>> LIBRARY

CREATE TABLE `invoice_status` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `invoice_id` INT(11) NOT NULL , `container_id` INT(11) NOT NULL , `cancelled` TINYINT NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB; 
ALTER TABLE `invoice_status` ADD INDEX(`invoice_id`); 
ALTER TABLE `invoice_status` ADD INDEX(`container_id`); 
ALTER TABLE `invoice_note` ADD INDEX(`invoice_id`); 

CREATE TABLE `supplementary_note` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `invoice_id` INT(11) NOT NULL , `note` TEXT NOT NULL , `user_id` INT(11) NOT NULL , `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)); 
ALTER TABLE `supplementary_note` ADD `note_type` ENUM("CANCELLED","RECALLED") NOT NULL AFTER `note`; 
ALTER TABLE `supplementary_note` ADD INDEX(`invoice_id`); 
ALTER TABLE `supplementary_note` ADD INDEX(`user_id`); 

CREATE TABLE `supplementary_status` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `invoice_id` INT(11) NOT NULL , `container_id` INT(11) NOT NULL , `cancelled` TINYINT NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB; 
ALTER TABLE `supplementary_status` ADD INDEX(`invoice_id`); 
ALTER TABLE `supplementary_status` ADD INDEX(`container_id`); 