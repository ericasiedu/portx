<?php
namespace Api;

use Api\ImportReceipt;

class TransitReceipt extends ImportReceipt 
{
    private $request,$response;
    public $trade_title = "Transit Receipt";

    public function __construct($request,$response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function show_receipt($receipt_no){
        $this->showReceipt($receipt_no);
    }
    
}


?>