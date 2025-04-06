<?php
namespace Lib\TaxType;
abstract class Tax{

    public $tax_type;
    public $due_amount;
    public $due_tax;
    public $tax_array;

    abstract public function generateTax($amount);

}
?>