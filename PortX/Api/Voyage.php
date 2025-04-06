<?php
namespace Api;
session_start();
use
    Lib\ACL,
    Lib\MyQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\MyTransactionQuery;

class Voyage{

    private $request,$response;

    function __construct($request,$response){
        $this->request = $request;
        $this->response = $response;
    }

    public function table(){
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'voyage')
            ->fields(
                Field::inst('voyage.reference')
                    ->setFormatter(function($val) {
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::maxLen( 20,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('voyage.rotation_number')
                    ->setFormatter(function($val) {
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::maxLen( 20,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('voyage.vessel_id')
                    ->setFormatter(function ($val) {
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from vessel where name = ?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if(!$query->num_rows()){
                            $val = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("select id from vessel where name = ?");
                            $query->bind = array('s', &$val);
                            $query->run();
                        }
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val){
                        $query = new MyQuery();
                        $query->query("select name from vessel where id = ?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) {
                        $val = html_entity_decode($val);
                        $query=new MyQuery();
                        $query->query("select name from vessel where id != 1 AND name  = ?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if(!$query->num_rows()){
                            $val = htmlspecialchars($val);
                            $query=new MyQuery();
                            $query->query("select name from vessel where id != 1 AND name  = ?");
                            $query->bind = array('s', &$val);
                            $query->run();
                        }
                        return ($query->fetch_num()[0]) ? true: 'Vessel Does Not Exist';
                    }),
                Field::inst('voyage.shipping_line_id')
                    ->setFormatter(function ($val) {
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from shipping_line where name = ?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if (!$query->num_rows()){
                            $val = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("select id from shipping_line where name = ?");
                            $query->bind = array('s', &$val);
                            $query->run();
                        }
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val){
                        $query = new MyQuery();
                        $query->query("select name from shipping_line where id = ?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) {
                        $val = html_entity_decode($val);
                        $query=new MyQuery();
                        $query->query("select name from shipping_line where name  = ?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if(!$query->num_rows()){
                            $val = htmlspecialchars($val);
                            $query=new MyQuery();
                            $query->query("select name from shipping_line where name  = ?");
                            $query->bind = array('s', &$val);
                            $query->run();
                        }
                        return ($query->fetch_num()[0]) ? true: 'Shipping Line Does Not Exist';
                    }),
                Field::inst('voyage.arrival_draft')
                    ->validator(Validate::numeric()),
                Field::inst('voyage.gross_tonnage')
                    ->validator(Validate::numeric()),
                Field::inst('voyage.voyage_status_id'),
                Field::inst('voyage.estimated_arrival')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('voyage.actual_arrival')
                    ->validator(function ( $val, $data, $field, $host ){
                        $actual_departure = $data['voyage']['actual_departure'];
                        if ($val > $actual_departure){
                            return "Actual Arrival cannot be more than Actual departure";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('voyage.estimated_departure')
                    ->validator(function ($val, $data, $field, $host ){
                        $extimated_arrival = $data['voyage']['estimated_arrival'];
                        if ($val < $extimated_arrival){
                            return "Estimated departure cannot be less than Estimated arrival arrival";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('voyage.actual_departure')
                    ->validator( function ( $val, $data, $field, $host ) {
                        $date = Date('Y-m-d');
                        $actual_arrival = $data['voyage']['actual_arrival'];
                        if ($val <= $date){
                            return 'Past date not allowed';
                        }
                        if ($val < $actual_arrival){
                            return "Actual departure cannot be less than Actual arrival";
                        }
                        else{
                            return true;
                        }

                    }),
                Field::inst('voyage.prev_port_id')
                    ->setFormatter(function ($val) {
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from port where name = ?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if(!$query->num_rows()){
                            $val = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("select id from port where name = ?");
                            $query->bind = array('s', &$val);
                            $query->run();
                        }
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val){
                        $query = new MyQuery();
                        $query->query("select name from port where id = ?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->validator(function ( $val, $data, $field, $host ) {
                        if($val == '') {
                            return true;
                        }
                        $val = html_entity_decode($val);
                        $query=new MyQuery();
                        $query->query("select name from port where name  = ?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if(!$query->num_rows()){
                            $val = htmlspecialchars($val);
                            $query=new MyQuery();
                            $query->query("select name from port where name  = ?");
                            $query->bind = array('s', &$val);
                            $query->run();
                        }
                        return ($query->fetch_num()[0]) ? true: 'Port Does Not Exist';
                    }),
                Field::inst('voyage.next_port_id')
                    ->setFormatter(function ($val) {
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from port where name = ?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if(!$query->num_rows()){
                            $val = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("select id from port where name = ?");
                            $query->bind = array('s', &$val);
                            $query->run();
                        }
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select name from port where id = ?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->validator(function ( $val, $data, $field, $host ) {
                        if($val == '') {
                            return true;
                        }
                        $val = html_entity_decode($val);
                        $query=new MyQuery();
                        $query->query("select name from port where name  = ?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if(!$query->num_rows()){
                            $val = htmlspecialchars($val);
                            $query=new MyQuery();
                            $query->query("select name from port where name  = ?");
                            $query->bind = array('s', &$val);
                            $query->run();
                        }
                        return ($query->fetch_num()[0]) ? true: 'Port Does Not Exist';
                    }),
                Field::inst('voyage.entry_status'),
                Field::inst('voyage.entry_date'),
                Field::inst('voyage.gcnet_job_number')
                    ->validator(Validate::maxLen( 20,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    )),
                Field::inst('voyage.gate_open'),
                Field::inst('voyage.gate_close'),
                Field::inst('voyage.discharge_from')
                    ->validator(Validate::maxLen( 100,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    )),
                Field::inst('voyage.discharge_to')
                    ->validator(Validate::maxLen( 100,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    )),
                Field::inst('vessel.name as vnam'),
                Field::inst('shipping_line.name as ship'),
                Field::inst('pport.name as ppor'),
                Field::inst('nport.name as npor')
            )
            ->on('preCreate', function ($editor,$values,$system_object='voyage-records'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='voyage-records'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='voyage-records'){
                if($id == 1)
                    return false;
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='voyage-records'){
                if($id == 1) {
                    return false;
                }
                ACl::verifyDelete($system_object);

                $query = new MyTransactionQuery();
                $query->query("select id from container where voyage = ? and (gate_status = 'GATED IN'  OR status = 1)");
                $query->bind = array('i', &$id);
                $query->run();
                if($query->num_rows() > 0) {
                    return false;
                }

                $query->query("DELETE from container_depot_info where container_id in ( select container.id from container where container.voyage = ?)");
                $query->bind = array('i', &$id);
                $query->run();

                $query->query("DELETE from container where voyage = ?");
                $query->bind = array('i', &$id);
                $query->run();
                $query->commit();

            })
            ->where("voyage.id", 1, "!=")
            ->leftJoin('vessel', 'voyage.vessel_id', '=', 'vessel.id')
            ->leftJoin('shipping_line', 'voyage.shipping_line_id', '=', 'shipping_line.id')
            ->leftJoin('port as pport', 'voyage.prev_port_id', '=', 'pport.id')
            ->leftJoin('port as nport', 'voyage.next_port_id', '=', 'nport.id')
            ->process($_POST)
            ->json();
    }


}
?>
