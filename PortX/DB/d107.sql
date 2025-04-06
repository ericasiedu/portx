

CREATE TABLE `proforma_invoice` (
  `id` int(11) UNSIGNED NOT NULL,
  `trade_type` int(10) UNSIGNED NOT NULL,
  `number` varchar(18) NOT NULL,
  `bl_number` varchar(20) NOT NULL,
  `book_number` varchar(20) NOT NULL,
  `boe_number` varchar(20) NOT NULL,
  `do_number` varchar(20) NOT NULL,
  `release_instructions` enum('','H/H','Unstuffing') NOT NULL,
  `bill_date` date NOT NULL,
  `due_date` date NOT NULL,
  `cost` decimal(18,2) NOT NULL,
  `waiver_pct` decimal(6,2) NOT NULL,
  `waiver_amount` decimal(18,2) NOT NULL,
  `waiver_note` tinytext NOT NULL,
  `waiver_by` int(11) UNSIGNED NOT NULL,
  `currency` int(10) UNSIGNED NOT NULL,
  `tax` decimal(18,2) NOT NULL,
  `tax_type` int(10) UNSIGNED NOT NULL,
  `note` tinytext NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `billing_group` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `proforma_invoice_config` (
  `id` int(11) UNSIGNED NOT NULL,
  `trade_type` int(11) UNSIGNED NOT NULL,
  `prefix` varchar(4) NOT NULL,
  `number` int(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `proforma_invoice_container` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `container_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `proforma_invoice_details` (
  `id` int(11) UNSIGNED NOT NULL,
  `invoice_id` int(11) UNSIGNED NOT NULL,
  `description` tinytext NOT NULL,
  `product_key` varchar(5) NOT NULL,
  `container_id` int(10) UNSIGNED NOT NULL,
  `cost` decimal(18,2) NOT NULL,
  `exchange_rate` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `qty` int(10) UNSIGNED NOT NULL,
  `total_cost` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `proforma_invoice_details_tax` (
  `id` int(11) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `description` tinytext NOT NULL,
  `rate` decimal(6,2) NOT NULL,
  `cost` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `proforma_invoice`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `proforma_invoice_config`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `proforma_invoice_container`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `proforma_invoice_details`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `proforma_invoice_details_tax`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `proforma_invoice`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `proforma_invoice_config`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `proforma_invoice_container`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `proforma_invoice_details`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `proforma_invoice_details_tax`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


INSERT INTO `proforma_invoice_config` (`id`, `trade_type`, `prefix`, `number`) VALUES
(1, 1, 'IMP', 0),
(2, 4, 'EXP', 0);


INSERT INTO `system_object` (`id`, `name`) VALUES (NULL, 'proforma-invoicing'), (NULL, 'proforma-invoice-records');
