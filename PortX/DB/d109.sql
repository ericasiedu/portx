CREATE TABLE `ucl` ( `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT , `days` TINYINT UNSIGNED NOT NULL , `user_id` INT(11) UNSIGNED NOT NULL , `date` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL , PRIMARY KEY (`id`));
ALTER TABLE `gate_record` ADD `ucl_status` TINYINT NOT NULL DEFAULT '0' AFTER `sys_waybill`;

INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'ucl-depot'), (NULL, 'udm-ucl-settings');
