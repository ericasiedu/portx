<?php 
namespace Api;

use Api\ImportInvoice;

class TransitInvoice extends ImportInvoice{
    private $request,$response;
    public $trade_title = "Transit Invoice";

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