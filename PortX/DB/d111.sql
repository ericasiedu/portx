CREATE TABLE `proforma_container_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `container_id` int(11) UNSIGNED NOT NULL,
  `activity_id` int(11) UNSIGNED NOT NULL,
  `note` tinytext NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `invoiced` tinyint(1) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `pdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'proforma-depot-overview');
CREATE TABLE `proforma_container_depot_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `container_id` int(11) UNSIGNED NOT NULL,
  `load_status` enum('FCL','LCL') NOT NULL,
  `goods` enum('General Goods','Engines/Spares Parts','Vehicle','DG I','DG II') NOT NULL,
  `full_status` int(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `proforma_invoice`  ADD `status` ENUM('UNPAID','PAID','CANCELLED','EXPIRED','DEFERRED') CHARACTER SET latin1 COLLATE latin1_swedish_ci AFTER `approved`;
ALTER TABLE `proforma_invoice` ADD `cancelled_by` INT(11) UNSIGNED NOT NULL AFTER `user_id`;