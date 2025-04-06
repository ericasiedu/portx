ALTER TABLE `depot_activity` ADD `is_default` TINYINT(1) NOT NULL DEFAULT '0' AFTER `billable`;
UPDATE `depot_activity` SET `is_default` = '1' WHERE `depot_activity`.`id` = 1;
UPDATE `depot_activity` SET `is_default` = '1' WHERE `depot_activity`.`id` = 2;