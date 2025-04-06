ALTER TABLE `container` CHANGE `gate_status` `gate_status` ENUM('','GATED IN','GATED OUT','MOVED') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `booking` ADD `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `booking_number`;
