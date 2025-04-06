<?php
namespace Api;

use Api\SuppImportReciept;

class SuppTransitReceipt extends SuppImportReciept
{
    private $request,$response;
    public $receipt_title = "Supplementary Transit Receipt";

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