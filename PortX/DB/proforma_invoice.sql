CREATE TABLE `portx`.`proforma_invoice_note` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `invoice_id` INT(11) NOT NULL , `note` TEXT NOT NULL , `note_type` ENUM('CANCELLED','RECALLED') NOT NULL , `user_id` INT(11) NOT NULL , `date` DATETIME NOT NULL , PRIMARY KEY (`id`));
CREATE TABLE `portx`.`supplementary_proforma_invoice_note` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `invoice_id` INT(11) NOT NULL , `note` TEXT NOT NULL , `note_type` ENUM('CANCELLED','RECALLED') NOT NULL , `user_id` INT(11) NOT NULL , `date` DATETIME NOT NULL , PRIMARY KEY (`id`));
CREATE TABLE `portx`.`proforma_invoice_status` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `invoice_id` INT(11) NOT NULL , `container_id` INT(11) NOT NULL , `cancelled` TINYINT(1) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`));
CREATE TABLE `portx`.`supplementary_proforma_invoice_status` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `invoice_id` INT(11) NOT NULL , `container_id` INT(11) NOT NULL , `cancelled` TINYINT(1) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`));

ALTER TABLE `proforma_invoice_note` ADD INDEX(`invoice_id`), ADD INDEX(`user_id`); 
ALTER TABLE `supplementary_proforma_invoice_note` ADD INDEX(`invoice_id`), ADD INDEX(`user_id`); 
ALTER TABLE `proforma_invoice_status` ADD INDEX(`invoice_id`), ADD INDEX(`container_id`); 
ALTER TABLE `supplementary_proforma_invoice_status` ADD INDEX(`invoice_id`), ADD INDEX(`container_id`); 
ALTER TABLE `invoice_status` CHANGE `cancelled` `cancelled` TINYINT(1) NOT NULL DEFAULT '0'; 
ALTER TABLE `proforma_invoice` ADD `recalled_by` INT(11) NOT NULL AFTER `cancelled_by`; 
ALTER TABLE `proforma_invoice` CHANGE `status` `status` ENUM('UNPAID','PAID','CANCELLED','EXPIRED','DEFERRED','RECALLED') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL; 
ALTER TABLE `proforma_supplementary_invoice` ADD `status` ENUM('UNPAID','PAID','CANCELLED','EXPIRED','DEFERRED','RECALLED') NOT NULL AFTER `date`; 
ALTER TABLE `proforma_supplementary_invoice` ADD `cancelled_by` INT(11) NOT NULL AFTER `waiver_by`, ADD `recalled_by` INT(11) NOT NULL AFTER `cancelled_by`; 
ALTER TABLE `proforma_supplementary_invoice` ADD INDEX(`invoice_id`); 