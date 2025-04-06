<?php
namespace Lib;
use Lib\MyQuery,
    Lib\Container;

class Stack{
    public $stack;

    function getFortyFeetBayId($query,$bay){
        $query->query("select id from forty_feet_log where stack=? and bay=?");
        $query->bind = array('si',&$this->stack,&$bay);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['id'];
    }

    function getFortyContainerId($query,$bay){
        $query->query("select container_id from forty_feet_log where stack=? and bay=?");
        $query->bind = array('si',&$this->stack,&$bay);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['id'];
    }

    function getYardBayId($query,$bay){
        $query->query("select id from yard_log where stack=? and bay=?");
        $query->bind = array('si',&$this->stack,&$bay);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['id'];
    }


}

?>