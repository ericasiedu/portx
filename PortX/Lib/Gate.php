<?php
namespace Lib;
use Lib\MyQuery;

class Gate {

    function getContainers(){
        $query=new MyQuery();
        $query->query("select number from container where status = 0 AND gate_status = ''");
        $query->run();
        return $query->fetch_all();
    }

    function getGatedContainers(){
        $query=new MyQuery();
        $query->query("select number from container inner join gate_record on container.id = gate_record.container_id where gate_record.status = 0 and gate_record.cond = 'NOT SOUND'");
        $query->run();
        return $query->fetch_all();
    }

    function gateContainerOut(){
        $query=new MyQuery();
        $query->query("select number from container inner join gate_record on container.id = gate_record.container_id where gate_record.status = 0 and gate_record.cond = 'NOT SOUND'");
        $query->run();
        return $query->fetch_all();
    }

    function getContainerOut(){
        $query=new MyQuery();
        $query->query("SELECT number FROM container WHERE gate_status = 'GATED IN' AND status = 0 ");
        $query->run();
        return $query->fetch_all();
    }

    function vehicleOut(){
        $query=new MyQuery();
        $query->query("select license from letpass_driver LEFT JOIN letpass on letpass.id = letpass_driver.letpass_id where letpass.status = 0");
        $query->run();
        return $query->fetch_all();
    }

    function getGates(){
        $query=new MyQuery();
        $query->query("select name from depot_gate");
        $query->run();
        return $query->fetch_all();
    }

    function getVehicles(){
        $query=new MyQuery();
        $query->query("select number from vehicle");
        $query->run();
        return $query->fetch_all();
    }

    function checkVehicle($val,$query=null){
        if($query == null) {
            $query = new MyQuery();
        }
        $query->query("select id from vehicle where number  =?");
        $query->bind = array('s',&$val);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['id'];
    }

    function getDrivers(){
        $query=new MyQuery();
        $query->query("select name from vehicle_driver");
        $query->run();
        return $query->fetch_all();
    }

    function gateOutDrivers(){
        $qu=new MyQuery();
        $qu->query("select name from letpass_driver LEFT JOIN letpass on letpass.id = letpass_driver.letpass_id where letpass.status = 0");
        $res=$qu->run();
        return $res->fetch_all();
    }
    function getCompanies(){
        $query=new MyQuery();
        $query->query("select name from trucking_company");
        $query->run();
        return $query->fetch_all();
    }

    function getLines(){
        $query=new MyQuery();
        $query->query("select name from shipping_line where id != 1");
        $query->run();
        return $query->fetch_all();
    }

    function getVoyages(){
        $query=new MyQuery();
        $query->query("select reference from voyage");
        $query->run();
        return $query->fetch_all();
    }

    function getSections(){
        $query=new MyQuery();
        $query->query("select name from container_section");
        $query->run();
        return $query->fetch_all();
    }

    function getContainerType(){
        $query=new MyQuery();
        $query->query("select code from container_isotype_code");
        $query->run();
        return $query->fetch_all();
    }

    function getAgents(){
        $query=new MyQuery();
        $query->query("select name from agency");
        $query->run();
        return $query->fetch_all();
    }

    function getId($container){
        $query=new MyQuery();
        $query->query("select id from gate_record where container_id = (select id from container where number =?)");
        $query->bind = array('s',&$container);
        $query->run();
        return $query->fetch_all()[0][0];
    }

    function getImdg(){
        $query=new MyQuery();
        $query->query("select name from imdg");
        $query->run();
        return $query->fetch_all();
    }

    function insertWaybill($waybill, $id){
        $query=new MyQuery();
        $query->query("update gate_record set sys_waybill =? where id =?");
        $query->bind = array('si',&$waybill,&$id);
        $query->run();
    }

    function genWaybill($data) {
        $waybill = 'S';
        $id = $data;
        $gid = $id;
        if (strlen($gid) < 7) {
            $empty = 7 - strlen($gid);
            do {
                $gid = '0' . $gid;
                --$empty;
            } while ($empty > 0);
        }

        $this->insertWaybill($waybill . $gid, $id);
    }

    static function checkPaidQuery($invoice,$container_id){
        $query = new MyQuery();
        $query->query("select ".$invoice."invoice.status from ".$invoice."invoice left join ".$invoice."invoice_container on ".$invoice."invoice_container.invoice_id = ".$invoice."invoice.id 
        where ".$invoice."invoice_container.container_id=?");
        $query->bind = array('i',&$container_id);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['status'];
    }

    static function checkLetpass($container_id){
        $query = new MyQuery();
        $query->query("select letpass_id from letpass_container where container_id=?");
        $query->bind = array('i',&$container_id);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['letpass_id'] ?? "";
    }

}