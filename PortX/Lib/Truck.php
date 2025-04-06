<?php

namespace Lib;
use Lib\MyQuery;

class Truck{

    function getTruckCompany(){
        $qu=new MyQuery();
        $qu->query("SELECT name FROM trucking_company");
        $res=$qu->run();
        return $res->fetch_all();
    }

}

?>