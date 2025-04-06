<?php

namespace Api;

use Lib\DocumentInfo;

class SuppEmptyInvoice extends SuppExportInvoice {
    function show_empty($invoice_number) {
        $this->show_export($invoice_number);
    }

    function getTradeInfo($container_id,$book_number) {
        // var_dump("called");die;
        $info = new DocumentInfo();
        return $info->getEmptyInvoiceInfo($book_number);
    }

    function setTitle($pdf) {
        $pdf->SetTitle('Supplementary Empty Invoice');
    }
}