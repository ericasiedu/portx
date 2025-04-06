<?php
namespace Api;

session_start();
use
    Lib\ACL,
    Lib\Container,
    Lib\MyQuery,
    Lib\MyTransactionQuery,
    Lib\Respond,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    Lib\YardTime;

    date_default_timezone_set('UTC');

class BareChasis{
    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $date = date('Y-m-d');

        $start_date = $this->request->param('stdt');
        $end_date = $this->request->param('eddt');
        $db = new Bootstrap();
        $db = $db->database();
        $query = new MyTransactionQuery();
        Editor::inst($db, 'gate_truck_record')
            ->fields(
                Field::inst('id'),
                Field::inst('container_id')
                    ->setFormatter(function($val) use($query){
                        $query->query("select id from container where number=?");
                        $query->bind = array('s',&$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        return $result['id'];
                    })
                    ->getFormatter(function($val) use($query){
                        $query->query("select number from container where id=?");
                        $query->bind = array('i',&$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        return $result['number'];
                    })
                    ->validator(function($val,$data,$field,$host) use($query){
                        $query->query("select id from container where number=? and gate_status='GATED IN'");
                        $query->bind = array('s',&$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        if (!$result['id']) {
                            return "Container does not exist";
                        }
                        $query->query("select id from yard_log where container_id=? and yard_activity='IN STACK'");
                        $query->bind = array('i',&$result['id']);
                        $query->run();
                        $result1 = $query->fetch_assoc();
                        if (!$result1['id']) {
                            return "Container is not in stack";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('vehicle_number')
                    ->validator(function($val,$data,$field,$host) use($query){
                    $val = html_entity_decode($val);
                    $letpass_number = $data['letpass_no'];
                    $query->query("select id from gate_truck_record where  vehicle_number =? and letpass_no=?");
                    $query->bind = array('ss',&$val,&$letpass_number);
                    $run=$query->run();
                    if ($run->num_rows()){
                        $id = $run->fetch_num()[0];
                        if($id == $host['id']){
                            return true;
                        }
                        else {
                            return "Vehicle  $val with letpass number $letpass_number already exists";
                        }
                    }
                    else{
                        $val = htmlspecialchars($val);
                        // $query=new MyQuery();
                        $query->query("select id from gate_truck_record where  vehicle_number =? and letpass_no=?");
                        $query->bind = array('ss',&$val,&$letpass_number);
                        $run=$query->run();
                        if ($run->num_rows()){
                            $id = $run->fetch_num()[0];
                            if($id == $host['id']){
                                return true;
                            }
                            else {
                                return "Vehicle  $val with letpass number $letpass_number already exists";
                            }
                        }
                        else{
                            return true;
                        }
                    }
                    }),
                Field::inst('vehicle_driver')
                ->validator(function($val,$data,$field,$host) use($query){

                    $val = html_entity_decode($val);
                    $letpass_number = $data['letpass_no'];
                    $query->query("select id from gate_truck_record where vehicle_driver=? and letpass_no=?");
                    $query->bind = array('ss',&$val,&$letpass_number);
                    $run=$query->run();
                    if ($run->num_rows()){
                        $id = $run->fetch_num()[0];
                        if($id == $host['id']){
                            return true;
                        }
                        else {
                            return "Driver  $val with letpass number $letpass_number already exists";
                        }
                    }
                    else{
                        $val = htmlspecialchars($val);
                        $query->query("select id from gate_truck_record where vehicle_driver=? and letpass_no=?");
                        $query->bind = array('ss',&$val,&$letpass_number);
                        $run=$query->run();
                        if ($run->num_rows()){
                            $id = $run->fetch_num()[0];
                            if($id == $host['id']){
                                return true;
                            }
                            else {
                                return "Driver  $val with letpass number $letpass_number already exists";
                            }
                        }
                        else{
                            return true;
                        }
                    }
                    }),
                Field::inst('letpass_no'),
                Field::inst('letpass_id')
                    ->setFormatter(function($val,$data,$field){
                        return (int)$data['letpass_no'];
                    }),
                Field::inst('offload_time')
                    ->getFormatter(function($val,$data) use($query){
                        if ($data['gate_status'] =="") {
                           return "00:00:00";
                        }
                        elseif ($data['gate_status'] =="GATED IN" && $val !="") {
                            return $val;
                        }
                        else{
                            $current_date =date("Y-m-d H:i:s");
                            return YardTime::getTimeSpent($data['date'],$current_date);
                        }
                        
                    }),
                Field::inst('onload_time')
                    ->getFormatter(function($val,$data) use($query){
                        $query->query("select load_status from truck_log where vehicle_number=?");
                        $query->bind = array('s',&$data['vehicle_number']);
                        $query->run();
                        $result1 = $query->fetch_assoc();
                        if ($data['gate_status']=='GATED IN' && $result1['load_status'] ==1 ) {
                            $current_date =date("Y-m-d H:i:s");
                            return YardTime::getTimeSpent($data['date'],$current_date);
                        }
                        if ($data['gate_status']=='GATED OUT' && $result1['load_status'] ==1) {
                           return $val;
                        }
                        else{
                            return "00:00:00";
                        }
                    }),
                Field::inst('gate_status as gstat'),
                Field::inst('gate_status')
                    ->getFormatter(function($val){
                        return $val == "" ? "NOT GATED IN":$val;
                    }),
                Field::inst('date'),    
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'truck-record') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'truck-record') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'truck-record') {
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'truck-record') use($query) {
                ACl::verifyDelete($system_object);

                ACl::verifyDelete($system_object);
                $query->query("select id from gate_truck_record where id = ? and gate_status='GATED IN'");
                $query->bind=array('i',&$id);
                $query->run();
                if($query->num_rows() > 0){
                    return false;
                }
            })
            ->where(function ($q) use ($start_date,$end_date){
                $q->where( 'id', '(SELECT id FROM gate_truck_record WHERE cast(date as date) between "'.$start_date.'" and "'.$end_date.'")', 'IN', false );
            })
            ->process($_POST)
            ->json();
            $query->commit();
    }

    function gatein_truck(){
        $id = $this->request->param('id');
        $query = new MyQuery();
        $query->query("update gate_truck_record set gate_status='GATED IN' where id=?");
        $query->bind = array('i',&$id);
        $query->run();
        new Respond(273);
    }

    function get_trucks(){
        $letpass_number = $this->request->param('lno');
        $query = new MyQuery();
        $query->query("select license from letpass_driver where letpass_id=?");
        $query->bind = array('i',&$letpass_number);
        $query->run();
        $result = $query->fetch_all();
        echo json_encode($result);
    }

    function get_drivers(){
        $letpass_number = $this->request->param('lno');
        $query = new MyQuery();
        $query->query("select name from letpass_driver where letpass_id=?");
        $query->bind = array('i',&$letpass_number);
        $query->run();
        $result = $query->fetch_all();
        echo json_encode($result);
    }

    function get_containers(){
        $letpass_number = $this->request->param('lno');
        $query = new MyQuery();
        $query->query("select container_id from letpass_container where letpass_id=?");
        $query->bind = array('i',&$letpass_number);
        $query->run();
        $container_array = array();
        while ($result = $query->fetch_assoc()) {
            $query1 = new MyQuery();
            $query1->query("select number from container where id=? and gate_status='GATED IN'");
            $query1->bind = array('i',&$result['container_id']);
            $query1->run();
            $container_result = $query1->fetch_assoc();
            array_push($container_array,$container_result['number']);
        }
        echo json_encode($container_array);
    }
}

?>