<?php
namespace Lib\TaxType;
use Lib\MyQuery;

class Simple extends Tax{

    public function __construct()
    {
        $this->tax_type = 1;
    }

    public function generateTax($amount)
    {
        $query = new MyQuery();
        $query->query("SELECT rate FROM tax WHERE type = ? ");
        $query->bind = array('i', &$this->tax_type);
        $run = $query->run();

        $this->tax_array = array();
        while ($result = $run->fetch_assoc()){
            $rate = $result['rate'];
            $this->due_tax = $rate / 100 * $amount;
            array_push($this->tax_array,round($this->due_tax,2));
            $this->due_amount += round($this->due_tax,2);
        }

        return $this->due_amount;

    }


}

?>