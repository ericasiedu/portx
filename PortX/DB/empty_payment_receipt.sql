INSERT INTO `payment_config` (`id`, `trade_type`, `prefix`, `number`) VALUES (NULL, '8', 'REM', '1');
ALTER TABLE `supplementary_payment_config` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT; 
INSERT INTO `supplementary_payment_config` (`id`, `trade_type`, `prefix`, `number`) VALUES (NULL, '8', 'SEMP', '1'); 