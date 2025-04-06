INSERT INTO `trade_type` (`id`, `code`, `name`, `handling_code_id`, `status`, `rent_free_days`, `deleted`) VALUES (NULL, '70', 'EMPTY', '3', '0', '0', '0');
INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'bookings');
INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'empty-bookings'); 

CREATE TABLE `portx`.`booking` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `shipping_line_id` INT UNSIGNED NOT NULL , `size` VARCHAR(30) NOT NULL , `quantity` SMALLINT UNSIGNED NOT NULL , `booking_number` VARCHAR(30) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `portx`.`booking_container` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `booking_id` INT NOT NULL , `container_id` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
-- ALTER TABLE `container` ADD INDEX(`number`); 
ALTER TABLE `booking` ADD `customer_id` VARCHAR(255) NOT NULL AFTER `shipping_line_id`; 
ALTER TABLE `booking_container` ADD INDEX(`container_id`);
ALTER TABLE `booking_container` ADD INDEX(`booking_id`); 

-- TEST CHANGES
ALTER TABLE `booking` CHANGE `quantity` `quantity` INT UNSIGNED NOT NULL; 

-- NEW ACTIVITIES FOR EMPTY MODULE
INSERT INTO `depot_activity` (`id`, `name`, `billable`, `is_default`) VALUES (NULL, 'Positioning', '1', '0'), (NULL, 'Drop-off', '1', '0');
UPDATE `depot_activity` SET `billable` = '0' WHERE `depot_activity`.`id` = 88;
UPDATE `depot_activity` SET `billable` = '0' WHERE `depot_activity`.`id` = 89 ;
INSERT INTO `invoice_config` (`id`, `trade_type`, `prefix`, `number`) VALUES (NULL, '8', 'EMP', '0');
INSERT INTO `depot_activity` (`id`, `name`, `billable`, `is_default`) VALUES (NULL, 'Sweeping', '1', '0'), (NULL, 'Upgrading to CMC (includes LOLO)', '1', '0'), (NULL, 'Stuffing (includes machine handling)', '1', '0'), (NULL, 'Trucking to Port', '1', '0'), (NULL, 'Trucking to CMC (Kejebii)', '1', '0');
INSERT INTO `proforma_invoice_config` (`id`, `trade_type`, `prefix`, `number`) VALUES (NULL, '8', 'EMP', '1');
INSERT INTO `supplementary_invoice_config` (`id`, `trade_type`, `prefix`, `number`) VALUES (NULL, '8', 'SMT', '1');
-- ALTER TABLE `proforma_supplementary_invoice_config` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT; 
-- ALTER TABLE `proforma_supplementary_invoice_config` ADD PRIMARY KEY(`id`);
-- INSERT INTO `proforma_supplementary_invoice_config` (`id`, `trade_type`, `prefix`, `number`) VALUES ('', '8', 'SMT', '1');
INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'move-to-export');
ALTER TABLE `container` ADD `moved_to` INT(11) NULL DEFAULT NULL AFTER `status`;
