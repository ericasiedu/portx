<?php

namespace Api;

use Lib\DocumentInfo;
use Lib\ExportPdf;

class EmptyInvoice extends ExportInvoice {
    function show_empty($invoice_number) {
        $pdf = new ExportPdf();
        $this->setTitle($pdf);
        $this->show_export($invoice_number);
    }

    function getTradeInfo($container_id,$book_number) {
        // var_dump("called");die;
        $info = new DocumentInfo();
        return $info->getEmptyInfo($container_id,$book_number);
    }

    function setTitle($pdf) {
        $pdf->SetTitle('Empty Invoice');
    }
}