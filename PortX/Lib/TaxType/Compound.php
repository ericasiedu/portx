<?php
namespace Lib\TaxType;
use Lib\MyTransactionQuery;


class Compound extends Tax{

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
        $this->due_amount = round($amount + $this->amount_due,2);


        $query->query("SELECT rate FROM tax WHERE type = ? AND label = 'VAT' ");
        $query->bind = array('i', &$this->tax_type);
        $run = $query->run();
        $query->commit();
        $result = $run->fetch_assoc();
        $rate = $result['rate'];
        $this->vat_tax = $rate / 100 * $this->due_amount;
        $this->tax_amount = round($this->vat_tax + $this->amount_due,2);
        return $this->tax_amount;
    }


}
?>