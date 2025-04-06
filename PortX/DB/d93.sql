DROP EVENT IF EXISTS `INVOICE_UPDATE`;
DELIMITER $$
CREATE EVENT `INVOICE_UPDATE` ON SCHEDULE EVERY 1 DAY STARTS '2020-07-01 00:00:00.000000' ON COMPLETION PRESERVE ENABLE DO BEGIN UPDATE invoice set status = 'EXPIRED' where invoice.due_date <= CURRENT_DATE && status = 'UNPAID'; UPDATE supplementary_invoice set status = 'EXPIRED' where supplementary_invoice.due_date <= CURRENT_DATE && status = 'UNPAID';
END $$