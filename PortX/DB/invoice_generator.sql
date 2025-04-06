ALTER TABLE `invoice` CHANGE `number` `number` VARCHAR(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL; 
ALTER TABLE `proforma_invoice` CHANGE `number` `number` VARCHAR(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL; 
ALTER TABLE `proforma_supplementary_invoice` CHANGE `number` `number` VARCHAR(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL; 
ALTER TABLE `supplementary_invoice` CHANGE `number` `number` VARCHAR(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL; 