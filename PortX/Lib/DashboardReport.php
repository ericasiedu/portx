<?php

namespace Lib;
use Lib\MyQuery;

class DashboardReport{

    public function  getLaden(){
        $qu = new MyQuery();
        $qu->query("select sum(payment.paid) + sum(supplementary_payment.paid) 
              as total_amount from payment inner join invoice on invoice.id = payment.invoice_id 
              inner join supplementary_invoice on supplementary_invoice.invoice_id = invoice.id 
              inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id 
              where invoice.trade_type = '1'");
        $res = $qu->run();
        return $res->fetch_all();
    }

    public function  getExport(){
        $qu = new MyQuery();
        $qu->query("select sum(payment.paid) + sum(supplementary_payment.paid) 
              as total_amount from payment inner join invoice on invoice.id = payment.invoice_id 
              inner join supplementary_invoice on supplementary_invoice.invoice_id = invoice.id 
              inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id 
              where invoice.trade_type = '4'");
        $res = $qu->run();
        return $res->fetch_all();
    }

    public function getActivity($activity_id, $length){
        $qu = new MyQuery();
        $qu->query("select count(container_log.activity_id) as activity, count(container.trade_type_code) 
            as trade_count, trade_type.name from container_log inner join container on container.id = container_log.container_id 
            inner join container_isotype_code on container_isotype_code.id = container.iso_type_code inner join trade_type 
            on trade_type.code = container.trade_type_code where container_log.activity_id = '$activity_id' 
            and container_isotype_code.length = '$length'");
        $res = $qu->run();
        return $res->fetch_all();
    }

}

?>