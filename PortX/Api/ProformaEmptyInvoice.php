<?php

namespace Api;

use Lib\DocumentInfo;

class ProformaEmptyInvoice extends ProformaExportInvoice {
    function show_empty($invoice_number) {
        $this->show_export($invoice_number);
    }

    function getTradeInfo($container_id,$book_number) {
        $info = new DocumentInfo();
        return $info->getEmptyInfo($container_id,$book_number);
    }

}