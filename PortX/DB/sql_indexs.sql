ALTER TABLE `container` ADD INDEX(`iso_type_code`), ADD INDEX(`voyage`), ADD INDEX(`shipping_line_id`), ADD INDEX(`agency_id`), ADD INDEX(`trade_type_code`), ADD INDEX(`imdg_code_id`);
ALTER TABLE `container_depot_info` ADD INDEX(`container_id`), ADD INDEX(`user_id`);  
ALTER TABLE `container_log` ADD INDEX(`container_id`), ADD INDEX(`activity_id`), ADD INDEX(`user_id`); 
ALTER TABLE `gate_record` ADD INDEX(`container_id`), ADD INDEX(`depot_id`), ADD INDEX(`gate_id`), ADD INDEX(`driver_id`), ADD INDEX(`trucking_company_id`), ADD INDEX(`user_id`);
ALTER TABLE `gate_record_container_condition` ADD INDEX(`user_id`);
ALTER TABLE `invoice` ADD INDEX(`trade_type`), ADD INDEX(`tax_type`), ADD INDEX(`customer_id`), ADD INDEX(`user_id`); 
ALTER TABLE `invoice_container` ADD INDEX(`invoice_id`), ADD INDEX(`container_id`);
ALTER TABLE `invoice_details` ADD INDEX(`invoice_id`), ADD INDEX(`container_id`);
ALTER TABLE `invoice_details_tax` ADD INDEX(`invoice_id`); 
ALTER TABLE `payment` ADD INDEX(`invoice_id`), ADD INDEX(`bank_name`), ADD INDEX(`user_id`); 
ALTER TABLE `supplementary_invoice` ADD INDEX(`invoice_id`), ADD INDEX(`user_id`); 
ALTER TABLE `supplementary_invoice_container` ADD INDEX(`invoice_id`), ADD INDEX(`container_id`); 
ALTER TABLE `supplementary_invoice_details` ADD INDEX(`invoice_id`), ADD INDEX(`container_id`); 
ALTER TABLE `supplementary_invoice_details_tax` ADD INDEX(`invoice_id`); 
ALTER TABLE `supplementary_payment` ADD INDEX(`invoice_id`), ADD INDEX(`bank_name`), ADD INDEX(`user_id`); 

