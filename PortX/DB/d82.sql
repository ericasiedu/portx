ALTER TABLE `invoice` ADD `boe_number` VARCHAR(20) NOT NULL AFTER `book_number`; 
ALTER TABLE `invoice` ADD `release_instructions` ENUM('','H/H','Unstuffing') NOT NULL AFTER `do_number`; 
