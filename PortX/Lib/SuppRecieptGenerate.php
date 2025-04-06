<?php
namespace Lib;
use Lib\MyQuery;
use Lib\MyTransactionQuery;

class SuppRecieptGenerate {

    public function supp_invoice_no($number,$trade_id){
        $qu=new MyTransactionQuery();
        $qu->query("UPDATE supplementary_payment_config SET number = '$number'  WHERE trade_type = '$trade_id'");
        $qu->run();
        $qu->commit();
    }

    public function generate_supp_re_no($trade_id){

        $qu=new MyTransactionQuery();
        $qu->query("SELECT trade_type, prefix, number FROM supplementary_payment_config WHERE trade_type = '$trade_id' ");
        $res=$qu->run();
        $result = $res->fetch_assoc();
        $prefix = $result['prefix'];
        $initial = $result['number'];
        $qu->query("SELECT idn_separator FROM system WHERE id = 1");
        $res=$qu->run();
        $result = $res->fetch_assoc();
        $qu->commit();
        $separator = $result['idn_separator'];

        $number = str_pad($initial,8,'0',STR_PAD_LEFT);
        $receipt = $prefix . $separator . "$number";
        $this->supp_invoice_no(++$number,$trade_id);
        return $receipt;
    }


}


?>
