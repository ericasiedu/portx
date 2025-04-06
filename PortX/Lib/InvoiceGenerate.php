<?php
namespace Lib;
use Lib\MyQuery;
use Lib\MyTransactionQuery;

class InvoiceGenerate{

    public function invoice_no($number,$trade_id){
        $qu=new MyTransactionQuery();
        $qu->query("UPDATE invoice_config SET number = '$number'  WHERE trade_type = '$trade_id'");
        $qu->run();
        $qu->commit();
    }

    public function generate_no($trade_id){
        $qu=new MyQuery();
        $qu->query("SELECT trade_type, prefix, number FROM invoice_config WHERE trade_type = '$trade_id' ");
        $res=$qu->run();
        $result = $res->fetch_assoc();
        $initial = $result['number'];
        $invioce = str_pad(++$initial,8,'0',STR_PAD_LEFT);
        $number = "$invioce";
        $this->invoice_no($number,$trade_id);
    }

}


?>