<?php
namespace Lib;
use Lib\MyQuery;

class Yard{
    function getEquipment(){
        $query=new MyQuery();
        $query->query("select equipment_no from reach_stacker");
        $query->run();
        return $query->fetch_all();
    }
    function getStackList(){
        $query=new MyQuery();
        $query->query("select name from stack");
        $query->run();
        return $query->fetch_all();
    }
}

?>