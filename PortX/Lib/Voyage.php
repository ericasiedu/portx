<?php
namespace Lib;
use Lib\MyQuery;

class Voyage {
    function getVessels(){
        $qu=new MyQuery();
        $qu->query("select name from vessel where id != 1");
        $res=$qu->run();
        return $res->fetch_all();
    }

    function getShipping(){
        $qu=new MyQuery();
        $qu->query("select name from shipping_line");
        $res=$qu->run();
        return $res->fetch_all();
    }

    function getPort(){
        $qu = new MyQuery();
        $qu->query("SELECT name FROM port");
        $res=$qu->run();
        return $res->fetch_all();
    }

}