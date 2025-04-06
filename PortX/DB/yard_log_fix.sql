ALTER TABLE `yard_log` CHANGE `yard_activity` `yard_activity` ENUM('','MOVE','ASSIGN','IN STACK','OUT STACK','REMOVE','EXAMINATION','ON TRUCK');
ALTER TABLE `yard_log_history` CHANGE `yard_activity` `yard_activity` ENUM('ASSIGN','ASSIGN EXAMINATION','APPROVE EXAMINATION','CHANGE POSITION','MOVE','POSITION','APPROVE','REMOVE','MOVE OUT','APPROVE REMOVAL','EXAMINATION MOVE','ASSIGN MOVE TO TRUCK','APPROVE MOVE TO TRUCK') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

ALTER TABLE `gate_record` ADD `yard_status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `ucl_status`; 

CREATE TABLE `proport_test`.`gate_truck_record` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,`container_id` INT(11) NOT NULL, `vehicle_number` VARCHAR(20) NOT NULL , `vehicle_driver` VARCHAR(255) NOT NULL , `letpass_id` INT(11) NOT NULL, `letpass_no` VARCHAR(20) NOT NULL , `gate_status` ENUM('','GATED IN','GATED OUT') NOT NULL , `offload_time` VARCHAR(20) NOT NULL , `onload_time` VARCHAR(20) NOT NULL , `date` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB; 
ALTER TABLE `gate_truck_record` ADD INDEX(`letpass_id`),ADD INDEX(`container_id`); 
INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'truck-record');
CREATE TABLE `proport_test`.`truck_log` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `yard_id` INT(11) NOT NULL,`vehicle_number` VARCHAR(255) NOT NULL , `container_id` INT NOT NULL ,`user_id` INT(11) NOT NULL,`load_status` TINYINT(1) NOT NULL DEFAULT '0', `date` TIMESTAMP NOT NULL , PRIMARY KEY (`id`));
ALTER TABLE `truck_log` ADD INDEX(`container_id`),ADD INDEX(`yard_id`),ADD INDEX(`user_id`); 