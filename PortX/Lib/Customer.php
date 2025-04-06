<?php

namespace Lib;

class Customer {
    function getBillingGroups(){
        $query=new MyQuery();
        $query->query("select name from customer_billing_group");
        $run=$query->run();
        return $run->fetch_all();
    }

    static function getCustomerID($customer){
        $query = new MyQuery();
        $query->query("select id from customer where name = ?");
        $query->bind = array('s', &$customer);
        $run = $query->run();
        $result = $run->fetch_assoc();
        return $result['id'];
    }
}

?>