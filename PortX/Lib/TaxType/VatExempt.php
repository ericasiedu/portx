<?php
namespace Lib\TaxType;
use Lib\MyTransactionQuery;

class VatExempt extends Tax{

    public $tax_amount;
    public $vat_tax;
    public $amount_due;

    public function __construct()
    {
        $this->tax_type = 2;
    }

    public  function  generateTax($amount)
    {
        $query = new MyTransactionQuery();
        $query->query("SELECT rate FROM tax WHERE type = ? AND label != 'VAT' ");
        $query->bind = array('i', &$this->tax_type);
        $run = $query->run();

        $this->tax_array = array();
        while ($result = $run->fetch_assoc()){
            $rate = $result['rate'];
            $this->due_tax = $rate / 100 * $amount;
            $this->amount_due += round($this->due_tax,2);
        }

        return $this->amount_due;

    }

}

?>