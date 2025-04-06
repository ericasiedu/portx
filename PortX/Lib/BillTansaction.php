<?php

namespace Lib;
use Lib\MyQuery;

class BillTansaction{

    public function getTaxList(){
        $qu=new MyQuery();
        $qu->query("SELECT id, name FROM tax_type");
        $res = $qu->run();
        return $res->fetch_all();
    }

    public function getCurrency(){
        $qu=new MyQuery();
        $qu->query("SELECT id, code FROM currency");
        $res = $qu->run();
        return $res->fetch_all();
    }

    public function get_customers(){
        $qu=new MyQuery();
        $qu->query("SELECT id,name from customer");
        $res = $qu->run();
        return $res->fetch_all();
    }

    public function getVoyage(){
        $qu=new MyQuery();
        $qu->query("select id,reference from voyage where id != 1");
        $res=$qu->run();
        return $res->fetch_all();
    }

    function getBankList(){
        $qu=new MyQuery();
        $qu->query("select name from bank");
        $res=$qu->run();
        return $res->fetch_all();
    }

    function getExchange(){
        $qu = new MyQuery();
        $qu->query("select code from currency");
        $res=$qu->run();
        return $res->fetch_all();
    }

}

?>