<?php

namespace Lib\TaxType;
use Lib\MyQuery;
use Lib\MyTransactionQuery;

class Exempt extends Tax{
    public function __construct()
    {
        $this->tax_type = 3;
    }

    public  function  generateTax($amount)
    {
       return 0;
    }
}

?>