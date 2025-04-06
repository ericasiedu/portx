CREATE TABLE IF NOT EXISTS `proforma_supplementary_invoice` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `number` varchar(18) NOT NULL,
  `bill_date` date NOT NULL,
  `due_date` date NOT NULL,
  `cost` decimal(18,2) NOT NULL,
  `waiver_pct` decimal(6,2) NOT NULL,
  `waiver_amount` decimal(18,2) NOT NULL,
  `waiver_note` tinytext NOT NULL,
  `waiver_by` int(11) UNSIGNED NOT NULL,
  `tax` decimal(18,2) NOT NULL,
  `note` tinytext NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `proforma_supplementary_invoice_config` (
  `id` int(11) UNSIGNED NOT NULL,
  `trade_type` int(11) UNSIGNED NOT NULL,
  `prefix` varchar(4) NOT NULL,
  `number` int(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `proforma_supplementary_invoice_container` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `container_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `proforma_supplementary_invoice_details` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) UNSIGNED NOT NULL,
  `description` tinytext NOT NULL,
  `product_key` varchar(5) NOT NULL,
  `container_id` int(10) UNSIGNED NOT NULL,
  `cost` decimal(18,2) NOT NULL,
  `exchange_rate` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `qty` int(10) UNSIGNED NOT NULL,
  `total_cost` decimal(18,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `proforma_supplementary_invoice_details_tax` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `description` tinytext NOT NULL,
  `rate` decimal(6,2) NOT NULL,
  `cost` decimal(18,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `proforma_supplementary_invoice_config` (`id`, `trade_type`, `prefix`, `number`) VALUES
(1, 1, 'SMP', 0),
(2, 4, 'SXP', 0);

INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'proforma-supplementary-invoicing'), (NULL, 'proforma-supplementary-invoice-records');