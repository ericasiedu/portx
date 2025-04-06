<?php

namespace Lib;
use Lib\MyQuery;

class Ships{

    function getShipAgent(){
        $qu=new MyQuery();
        $qu->query("SELECT name FROM shipping_line");
        $res=$qu->run();
        return $res->fetch_all();
    }

}

?>