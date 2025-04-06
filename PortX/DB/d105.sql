ALTER TABLE `invoice` ADD `waiver_by` INT(11) UNSIGNED NOT NULL AFTER `waiver_note`;
ALTER TABLE `invoice` ADD `deferral_by` INT(11) UNSIGNED NOT NULL AFTER `deferral_note`;
ALTER TABLE `invoice` ADD `cancelled_by` INT(11) UNSIGNED NOT NULL AFTER `deferral_by`;
ALTER TABLE `supplementary_invoice` ADD `waiver_by` INT(11) UNSIGNED NOT NULL AFTER `waiver_note`;
ALTER TABLE `supplementary_invoice` ADD `deferral_by` INT(11) UNSIGNED NOT NULL AFTER `deferral_note`;
ALTER TABLE `supplementary_invoice` ADD `cancelled_by` INT(11) UNSIGNED NOT NULL AFTER `deferral_by`;