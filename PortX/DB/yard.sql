INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'yard-planning'); 

-- CREATE TABLE `portx`.`yard_log` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `stack` VARCHAR(10) NOT NULL ,  `bay` INT(11) NOT NULL ,  `row` VARCHAR(10) NOT NULL ,  `tier` INT(11) NOT NULL ,  `reefer_status` TINYINT(1) NOT NULL DEFAULT '0' ,  `date` DATE NOT NULL DEFAULT CURRENT_TIMESTAMP ,    PRIMARY KEY  (`id`));
-- ALTER TABLE `yard_log` ADD `container_id` INT(11) NOT NULL AFTER `id`; 

-- INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'udm-reach-stacker') ;
-- CREATE TABLE `portx`.`reach_stacker` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `equipment_no` VARCHAR(200) NOT NULL , `type` ENUM('LADEN','EMPTY') NOT NULL , `date` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB; 
-- ALTER TABLE `yard_log` ADD `equipment_no` VARCHAR(200) NOT NULL AFTER `reefer_status`; 
-- INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'operator-view'); 
-- INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'examination-area'); 
-- ALTER TABLE `yard_log` ADD `assigned_by` INT(11) NOT NULL AFTER `stack_time`, ADD `approved_by` INT(11) NOT NULL AFTER `assigned_by`, ADD `position_by` INT(11) NOT NULL AFTER `approved_by`, ADD `positioned` TINYINT(1) NOT NULL DEFAULT '0' AFTER `position_by`; 
-- ALTER TABLE `yard_log` CHANGE `assigned_by` `assigned_by` INT(11) UNSIGNED NOT NULL; 
-- ALTER TABLE `yard_log` CHANGE `approved_by` `approved_by` INT(11) UNSIGNED NOT NULL; 
-- ALTER TABLE `yard_log` CHANGE `position_by` `position_by` INT(11) UNSIGNED NOT NULL; 

CREATE TABLE `yard_log` (
  `id` int(11) NOT NULL,
  `container_id` int(11) NOT NULL AUTO_INCREMENT,
  `stack` varchar(10) NOT NULL,
  `bay` int(11) NOT NULL,
  `row` varchar(10) NOT NULL,
  `tier` int(11) NOT NULL,
  `reefer_status` tinyint(1) NOT NULL DEFAULT 0,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `equipment_no` varchar(200) NOT NULL,
  `stack_time` varchar(200) NOT NULL,
  `assigned_by` int(11) UNSIGNED NOT NULL,
  `approved_by` int(11) UNSIGNED NOT NULL,
  `position_by` int(11) UNSIGNED NOT NULL,
  `positioned` tinyint(1) NOT NULL DEFAULT 0,
  `yard_activity` enum('','MOVE','ASSIGN','IN STACK','OUT STACK','REMOVE','EXAMINATION','ON TRUCK') NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
   PRIMARY KEY  (`id`)
);

INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'operator-view'); 
INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'examination-area'); 
ALTER TABLE `yard_log` CHANGE `assigned_by` `assigned_by` INT(11) UNSIGNED NOT NULL; 
ALTER TABLE `yard_log` CHANGE `approved_by` `approved_by` INT(11) UNSIGNED NOT NULL; 
ALTER TABLE `yard_log` CHANGE `position_by` `position_by` INT(11) UNSIGNED NOT NULL; 

CREATE TABLE `portx`.`yard_log_history` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `container_id` INT(11) NOT NULL , `yard_id` INT(11) NOT NULL , `position` VARCHAR(255) NOT NULL , `yard_activity` ENUM('ASSIGN','ASSIGN EXAMINATION','APPROVE EXAMINATION','CHANGE POSITION','MOVE','POSITION','APPROVE','REMOVE','MOVE OUT','APPROVE REMOVAL','EXAMINATION MOVE') NOT NULL , `user_id` INT(11) NOT NULL , `date` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB; 
ALTER TABLE `yard_log_history` CHANGE `yard_id` `yard_id` INT(11) NULL DEFAULT NULL; 
ALTER TABLE `yard_log_history` ADD INDEX(`container_id`), ADD INDEX(`yard_id`), ADD INDEX(`user_id`); 
ALTER TABLE `yard_log_history` ADD `stack` VARCHAR(20) NOT NULL AFTER `yard_id`; 

CREATE TABLE `portx`.`holding_area` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `container_id` INT(11) NOT NULL , `user_id` INT(11) NOT NULL , `date` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB; 

ALTER TABLE `gate_record` ADD `examination_status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `ucl_status`; 
ALTER TABLE `gate_record` ADD `examination_by` INT(11) NOT NULL AFTER `examination_status`; 
ALTER TABLE `gate_record` ADD INDEX(`examination_by`); 

CREATE TABLE `portx`.`stack` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(100) NOT NULL , `stack_type` ENUM('General Goods','DG') NOT NULL , `date` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB; 

INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'udm-stack'); 
