ALTER TABLE `container` CHANGE `bl_number` `bl_number` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `container` CHANGE `book_number` `book_number` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `invoice` CHANGE `bl_number` `bl_number` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `invoice` CHANGE `book_number` `book_number` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;