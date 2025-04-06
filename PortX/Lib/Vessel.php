<?php

namespace Lib;
use Lib\MyQuery;

class Vessel{
    function getCountry(){
        $qu = new MyQuery();
        $qu->query("SELECT name FROM country");
        $res=$qu->run();
        return $res->fetch_all();
    }

    function getPort(){
        $qu = new MyQuery();
        $qu->query("SELECT name FROM port");
        $res=$qu->run();
        return $res->fetch_all();
    }

    function vesselTyoe(){
        $qu = new MyQuery();
        $qu->query("SELECT name FROM vessel_type");
        $res=$qu->run();
        return $res->fetch_all();
    }
}


?>