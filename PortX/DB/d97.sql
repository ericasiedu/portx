INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'supplementary-invoice-approvals');
ALTER TABLE `supplementary_invoice` ADD `approved` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `date`;