CREATE TABLE invoice_history_log ( id INT(11) NOT NULL AUTO_INCREMENT , invoice_id INT(11) NOT NULL , status ENUM('CREATED','APPROVED','UNPAID','PAID','CANCELLED','EXPIRED','DEFERRED','RECALLED','WAIVED') NOT NULL , user_id INT(11) NOT NULL , date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (id)); 
ALTER TABLE invoice_history_log ADD INDEX(`invoice_id`), ADD INDEX(`user_id`);

CREATE TABLE proforma_invoice_history_log ( id INT(11) NOT NULL AUTO_INCREMENT , invoice_id INT(11) NOT NULL , status ENUM('CREATED','APPROVED','UNPAID','PAID','CANCELLED','EXPIRED','DEFERRED','RECALLED','WAIVED') NOT NULL , user_id INT(11) NOT NULL , date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (id)); 
ALTER TABLE proforma_invoice_history_log ADD INDEX(`invoice_id`), ADD INDEX(`user_id`);

CREATE TABLE supplementary_invoice_history_log ( id INT(11) NOT NULL AUTO_INCREMENT , invoice_id INT(11) NOT NULL , status ENUM('CREATED','APPROVED','UNPAID','PAID','CANCELLED','EXPIRED','DEFERRED','RECALLED','WAIVED') NOT NULL , user_id INT(11) NOT NULL , date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (id)); 
ALTER TABLE supplementary_invoice_history_log ADD INDEX(`invoice_id`), ADD INDEX(`user_id`);

CREATE TABLE proforma_supplementary_invoice_history_log ( id INT(11) NOT NULL AUTO_INCREMENT , invoice_id INT(11) NOT NULL , status ENUM('CREATED','APPROVED','UNPAID','PAID','CANCELLED','EXPIRED','DEFERRED','RECALLED','WAIVED') NOT NULL , user_id INT(11) NOT NULL , date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (id)); 
ALTER TABLE proforma_supplementary_invoice_history_log ADD INDEX(`invoice_id`), ADD INDEX(`user_id`);


