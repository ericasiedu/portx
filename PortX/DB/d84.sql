UPDATE `tax_type` SET `name` = 'Total Exempt' WHERE `tax_type`.`id` = 3;
INSERT INTO `tax_type` (`id`, `name`, `locked`) VALUES (NULL, 'VAT Exempt', '1');
