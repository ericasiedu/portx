<?php
namespace Api;

use Api\ProformaSuppImportInvoice;

class ProformaSuppTransitInvoice extends ProformaSuppImportInvoice{
    private $request,$response;
    public $trade_title = 'Proforma Supplementary Import Invoice';

    public function __construct($request,$response) {
        $this->request = $request;
        $this->response = $response;
    }

    public function show_transit($invoice_number){
        $this->show_import($invoice_number);
    }
}

?>