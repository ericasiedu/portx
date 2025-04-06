DROP PROCEDURE IF EXISTS INVOICE_EXPIRE;
DELIMITER //
CREATE DEFINER = 'root'@'localhost' PROCEDURE INVOICE_EXPIRE()
SQL SECURITY DEFINER
BLOCK0:BEGIN
	DECLARE invoiceid INT(10);
    DECLARE product_id varchar(5);
    DECLARE container_id INT(10);
 	DECLARE inv_fetch_state BOOLEAN;
 	DECLARE info_fetch_state BOOLEAN;
    DECLARE activity INT(10);
    DECLARE expired CURSOR for SELECT invoice.id from invoice where invoice.due_date <= CURRENT_DATE && status = 'UNPAID';
    DECLARE CONTINUE HANDLER for NOT FOUND SET inv_fetch_state=FALSE;
	SET inv_fetch_state =true;
	SET info_fetch_state =true;
	OPEN expired;
invoice_fetch:LOOP
	FETCH expired into invoiceid;
    IF inv_fetch_state = FALSE THEN
  		LEAVE invoice_fetch;
 	END IF;
	BLOCK1: Begin
	DECLARE invoice_info CURSOR for select invoice_details.container_id, invoice_details.product_key
                  from invoice inner join invoice_details on invoice_details.invoice_id = invoice.id
                  where invoice.id = invoiceid and (invoice_details.product_key != 'M' && invoice_details.product_key != 'S') order by invoice_details.id desc;
    DECLARE CONTINUE HANDLER for NOT FOUND SET info_fetch_state=FALSE;
        OPEN invoice_info;
    details_fetch:LOOP
    	FETCH invoice_info into container_id, product_id;
        IF info_fetch_state = FALSE THEN
  			LEAVE details_fetch;
 	 	END IF;
    	SELECT id into activity FROM `container_log`
                      WHERE container_log.container_id=container_id and invoiced = 1 and activity_id = product_id
                      ORDER BY id DESC LIMIT 1;
    	UPDATE container_log set container_log.invoiced = 0 where id = activity;
    	DELETE from invoice_container WHERE invoice_container.invoice_id = invoiceid and invoice_container.container_id =  container_id;
	SET container_id = NULL;
	SET product_id = NULL;
	SET activity = NULL;
    END LOOP;
    CLOSE invoice_info;
	END BLOCK1;
    UPDATE invoice set invoice.status = "EXPIRED" where id = invoiceid;
    SET invoiceid = NULL;
END LOOP;
CLOSE expired;
END BLOCK0
//
DELIMITER ;

DROP PROCEDURE IF EXISTS SUPP_INVOICE_EXPIRE;
DELIMITER //
CREATE DEFINER = 'root'@'localhost' PROCEDURE SUPP_INVOICE_EXPIRE()
SQL SECURITY DEFINER
BLOCK0:BEGIN
	DECLARE invoiceid INT(10);
    DECLARE product_id varchar(5);
    DECLARE container_id INT(10);
 	DECLARE inv_fetch_state BOOLEAN;
 	DECLARE info_fetch_state BOOLEAN;
    DECLARE activity INT(10);
    DECLARE expired CURSOR for SELECT supplementary_invoice.id from supplementary_invoice where supplementary_invoice.due_date <= CURRENT_DATE && status = 'UNPAID';
    DECLARE CONTINUE HANDLER for NOT FOUND SET inv_fetch_state=FALSE;
	SET inv_fetch_state =true;
	SET info_fetch_state =true;
	OPEN expired;
invoice_fetch:LOOP
	FETCH expired into invoiceid;
    IF inv_fetch_state = FALSE THEN
  		LEAVE invoice_fetch;
 	END IF;
	BLOCK1: Begin
	DECLARE invoice_info CURSOR for select supplementary_invoice_details.container_id,supplementary_invoice_details.product_key
                  from supplementary_invoice inner join supplementary_invoice_details on supplementary_invoice_details.invoice_id = supplementary_invoice.id
                  where supplementary_invoice.id = invoiceid   and (supplementary_invoice_details.product_key != 'M' && supplementary_invoice_details.product_key != 'S') order by supplementary_invoice_details.id desc;
    DECLARE CONTINUE HANDLER for NOT FOUND SET info_fetch_state=FALSE;
        OPEN invoice_info;
    details_fetch:LOOP
    	FETCH invoice_info into container_id, product_id;
        IF info_fetch_state = FALSE THEN
  			LEAVE details_fetch;
 	 	END IF;
    	SELECT id into activity FROM `container_log`
                      WHERE container_log.container_id=container_id and invoiced = 1 and activity_id = product_id
                      ORDER BY id DESC LIMIT 1;
    	UPDATE container_log set container_log.invoiced = 0 where id = activity;
    	DELETE from supplementary_invoice_container WHERE supplementary_invoice_container.invoice_id = invoiceid and supplementary_invoice_container.container_id =  container_id;
	SET container_id = NULL;
	SET product_id = NULL;
	SET activity = NULL;
    END LOOP;
    CLOSE invoice_info;
	END BLOCK1;
    UPDATE supplementary_invoice set supplementary_invoice.status = "EXPIRED" where id = invoiceid;
    SET invoiceid = NULL;
END LOOP;
CLOSE expired;
END BLOCK0
//
DELIMITER ;
DROP EVENT IF EXISTS `INVOICE_UPDATE`;
DELIMITER $$
CREATE EVENT `INVOICE_UPDATE` ON SCHEDULE EVERY 1 DAY STARTS '2020-07-01 00:00:00.000000' ON COMPLETION PRESERVE ENABLE DO BEGIN CALL INVOICE_EXPIRE(); CALL SUPP_INVOICE_EXPIRE();
END $$
DELIMITER ;