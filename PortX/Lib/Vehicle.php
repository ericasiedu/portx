<?php
namespace Lib;
use Lib\MyQuery;

class Vehicle {
    function getTypes(){
        $qu=new MyQuery();
        $qu->query("select name from vehicle_type");
        $res=$qu->run();
        return $res->fetch_all();
    }

    function getCompanies(){
        $qu=new MyQuery();
        $qu->query("select name from trucking_company");
        $res=$qu->run();
        return $res->fetch_all();
    }
}