<?php

namespace Lib;


class Container {

    function getLines(){
        $query=new MyQuery();
        $query->query("select name from shipping_line where id != 1");
        $run=$query->run();
        return $run->fetch_all();
    }


    function getAgents(){
        $query=new MyQuery();
        $query->query("select name from agency");
        $run=$query->run();
        return $run->fetch_all();
    }

    function getPorts(){
        $query=new MyQuery();
        $query->query("select code from port");
        $run=$query->run();
        return $run->fetch_all();
    }

    function getTypes(){
        $query=new MyQuery();
        $query->query("select code from container_isotype_code");
        $run=$query->run();
        return $run->fetch_all();
    }

    function getSizes(){
        $query=new MyQuery();
        $query->query("select code from container_size_code");
        $run=$query->run();
        return $run->fetch_all();
    }

    function getImdg(){
        $query=new MyQuery();
        $query->query("select name from imdg");
        $run=$query->run();
        return $run->fetch_all();
    }

    function getVoyage(){
        $query=new MyQuery();
        $query->query("select reference from voyage where id != 1");
        $run=$query->run();
        return $run->fetch_all();
    }

    public function checkContainer($container, $db_link = null){
        if($db_link == null) {
            $db_link = new MyQuery();
        }
        $db_link->query("SELECT status FROM container WHERE gate_status != 'GATED OUT' AND id = ?");
        $db_link->bind =  array('i', &$container);
        $run=$db_link->run();
        $result = $run->fetch_assoc();
        $status = $result['status'];
        if ($status){
            return $status;
        }
    }

    public function checkContainerInfo($container_id, $is_proforma = false, $db_link = null){
        if($db_link == null) {
            $db_link = new MyQuery();
        }

        $table_prefix = $is_proforma ? "proforma_" : "";
        $db_link->query("select ".$table_prefix."container_depot_info.id from ".$table_prefix."container_depot_info 
              inner join container on container.id = ".$table_prefix."container_depot_info.container_id 
              where container.id = ?");
        $db_link->bind =  array('i', &$container_id);
        $run=$db_link->run();
        $result = $run->fetch_assoc();
        $depot_id = $result['id'];
        if ($depot_id){
            return $depot_id;
        }
    }

    static public function getTradeType($query,$container){
        $query->query("select trade_type.name as trade from container left join trade_type on trade_type.code = container.trade_type_code where container.id=? and container.gate_status='GATED IN'");
        $query->bind = array('i',&$container);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['trade'];
    }

    static public function getContainerSize($query,$container){
        $query->query("select container_isotype_code.length,container_isotype_code.height from container left join container_isotype_code on container_isotype_code.id = container.iso_type_code where container.id=? and container.gate_status = 'GATED IN'");
        $query->bind = array('i',&$container);
        $query->run();
        $result = $query->fetch_all();
        return $result;
    }   

    static public function getContainerID($query,$container){
        $query->query("select id from container where number=? and gate_status='GATED IN'");
        $query->bind = array('s',&$container);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['id'];
    }

}

?>