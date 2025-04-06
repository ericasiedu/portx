<?php
namespace Lib;

class Excel{

    function getVesselId($query,$name){
        $query->query("SELECT id FROM vessel WHERE name = ?");
        $query->bind = array('s',&$name);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['id'];
    }

    function getShipId($query,$code){
        $query->query("SELECT id FROM `shipping_line` WHERE `code` = ?");
        $query->bind = array('s',&$code);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['id'];
    }

    function getPortId($query,$name){
        $query->query("SELECT id FROM port WHERE name = ?");
        $query->bind = array('s',&$name);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['id'];
    }

    function getAgentId($query,$code){
        $query->query("select id from agency where code = ?");
        $query->bind = array('s',&$code);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['id'];
    }
}

?>