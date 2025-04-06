<?php
namespace Api;

use Api\ProformaImportInvoice;

class ProformaTransitInvoice extends ProformaImportInvoice{
    private $request,$response;
    public $trade_title = 'Proforma Transit Invoice';

    public function __construct($request,$response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function show_transit($invoice_number){
        $this->show_import($invoice_number);
    }
}


?>