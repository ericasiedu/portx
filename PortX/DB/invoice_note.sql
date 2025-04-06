CREATE TABLE `invoice_note` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `invoice_id` INT(11) NOT NULL , `note` TEXT NOT NULL , `note_type` ENUM("CANCELLED","RECALLED") NOT NULL , `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)); 
ALTER TABLE `invoice_note` ADD `user_id` INT(11) NOT NULL AFTER `note_type`; 
ALTER TABLE `invoice_note` ADD INDEX(`user_id`); 