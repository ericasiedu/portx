<?php 
namespace Api;

use Api\SuppImportInvoice;

class SuppTransitInvoice extends SuppImportInvoice{
    private $request,$response;
    public $trade_title = "Supplementary Transit Invoice";

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