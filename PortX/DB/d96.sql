ALTER TABLE `customer_billing_group` ADD `trade_type` INT(10) UNSIGNED NOT NULL AFTER `extra_free_rent_days`;
ALTER TABLE `customer` DROP `billing_group`;
CREATE TABLE `portx`.`customer_billing` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `customer_id` INT(10) UNSIGNED NOT NULL ,  `billing_group` INT(10) UNSIGNED NOT NULL ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;