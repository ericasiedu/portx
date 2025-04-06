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
    DataTables\Editor\Field;


class Ucl{
    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db = new Bootstrap();
        $db = $db->database();


        Editor::inst($db, 'gate_record')
            ->fields(
                Field::inst('gate_record.special_seal as spsl'),
                Field::inst('gate_record.consignee as cons'),
                Field::inst('gate_record.external_reference as ref'),
                Field::inst('gate_record.cond as cond'),
                Field::inst('gate_record.note as note'),
                Field::inst('gate_record.waybill as eir'),
                Field::inst('gate_record.date as date'),
                Field::inst('gate_record.pdate as pdate'),
                Field::inst('gate_record.user_id as user')
                    ->setFormatter(function ($val) {
                        return isset($_SESSION['id']) ? $_SESSION['id'] : exit();
                    })->getFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("SELECT first_name, last_name FROM user WHERE id = ?");
                        $query->bind =  array('i', &$val);
                        $run = $query->run();
                        $result = $run->fetch_assoc();
                        $first_name = $result['first_name'];
                        $last_name = $result['last_name'];
                        $full_name = "$first_name" . " " . "$last_name";
                        return $full_name ?? '';
                    }),
                Field::inst('container.number as cnum'),
                Field::inst('container.iso_type_code as code')
                    ->getFormatter(function ($val){
                        $query = new MyQuery();
                        $query->query("select code from container_isotype_code where id=?");
                        $query->bind =  array('i', &$val);
                        $run = $query->run();
                        $result = $run->fetch_assoc();
                        return $result['code'] ?? "";
                    }),
                Field::inst('container.bl_number as blnum'),
                Field::inst('container.book_number as bknum'),
                Field::inst('depot.name as depot'),
                Field::inst('depot_gate.name as gate'),
                Field::inst('vehicle.number as veh'),
                Field::inst('vehicle_driver.name as drv'),
                Field::inst('trucking_company.name as tknam'),
                Field::inst('gate_record.id as gid'),
                Field::inst('container.id as cid')
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'ucl-depot') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'ucl-depot') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'ucl-depot') {
                return false;
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'ucl-depot') {
                return false;
            })
            ->leftJoin('container', 'gate_record.container_id', '=', 'container.id')
            ->leftJoin('depot', 'gate_record.depot_id', '=', 'depot.id')
            ->leftJoin('depot_gate', 'gate_record.gate_id', '=', 'depot_gate.id')
            ->leftJoin('vehicle', 'gate_record.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('vehicle_driver', 'gate_record.driver_id', '=', 'vehicle_driver.id')
            ->leftJoin('trucking_company', 'gate_record.trucking_company_id', '=', 'trucking_company.id')
            ->where('container.gate_status', 'GATED IN')
            ->where('gate_record.type', 'GATE IN')
            ->where('gate_record.ucl_status', 1,'=')
            ->process($_POST)
            ->json();
    }

    public function update_ucl_days(){

        ACL::verifyUpdate("udm-ucl-settings");

        $days = $this->request->param('days');
        $charge_20_ft = $this->request->param('ch20');
        $charge_40_ft = $this->request->param('ch40');
        $charge_45_ft = $this->request->param('ch45');
        $user_id = $_SESSION['id'];
        $current_date =date('Y-m-d H:m:s');

        if (!is_numeric($days) || $days <= 0){
            new Respond(1500);
        }

        $query = new MyTransactionQuery();
        $query->query("select id from ucl");
        $query->run();
        if ($query->num_rows() == 0){
            $query->query("insert into ucl(days,20ft_charge,40ft_charge,45ft_charge,user_id,date)values(?,?,?,?,?,?)");
            $query->bind = array('idddis',&$days,&$charge_20_ft,&$charge_40_ft,&$charge_45_ft,&$user_id,&$current_date);
            $query->run();
        }
        else{
            $id = $query->fetch_assoc();
            $query->query("update ucl set days = ?,20ft_charge =?,40ft_charge=?,45ft_charge=?, user_id = ? where id=?");
            $query->bind = array('idddii',&$days,&$charge_20_ft,&$charge_40_ft,&$charge_45_ft,&$user_id,&$id);
            $query->run();
        }
        $query->commit();
        new Respond(2300);
    }

    public function load_days(){
        $result_array = array();
        $query = new MyQuery();
        $query->query("select days,20ft_charge,40ft_charge,45ft_charge from ucl");
        $query->run();


        $result = $query->fetch_assoc();
        $days =  $result['days'] != "" || $result['days'] != null ? $result['days'] : 0;
        $twenty_ft_charge = $result['20ft_charge'] != "" || $result['20ft_charge'] != null ? $result['20ft_charge'] : 0.00;
        $fourty_ft_charge = $result['40ft_charge'] != "" || $result['40ft_charge'] != null ? $result['40ft_charge'] : 0.00;
        $fourty_five_ft_charge = $result['45ft_charge'] != "" || $result['45ft_charge'] != null ? $result['45ft_charge'] : 0.00;


        $result_array['days'] = $days;
        $result_array['ch20'] = $twenty_ft_charge;
        $result_array['ch40'] = $fourty_ft_charge;
        $result_array['ch45'] = $fourty_five_ft_charge;
        new Respond(2300,$result_array);
    }

    public function move_to_ucl(){
        if (!ACl::canUpdate("depot-overview")){
            new Respond(1510);
        }
        $container_id = $this->request->param('cnum');

        $check = new Container();
        $check_info = $check->checkContainerInfo($container_id);

        if ($check_info == ""){
            new Respond(1520);
        }

        $query = new MyTransactionQuery();
        $query->query("select voyage.actual_arrival,gate_record.date, trade_type.id as trade_id from container 
                left join voyage on voyage.id = container.voyage left join gate_record on gate_record.container_id = container.id 
                left join trade_type on trade_type.code = container.trade_type_code where container.id = ? 
                and container.gate_status = 'GATED IN'");
        $query->bind = array('i',&$container_id);
        $query->run();
        $result = $query->fetch_assoc();

        $actual_arrival = $result['actual_arrival'];
        $export_date = $result['date'];
        $ucl_date1 = ($result['trade_id'] == 1) ? $actual_arrival : $export_date;

        $query->query("select days from ucl");
        $query->bind = array();
        $query->run();
        $result1 = $query->fetch_assoc();
        $ucl_days = $result1['days'];

        $current_date =date('Y-m-d');
        $u_date = date_create($ucl_date1);
        $ucl_date = date_format($u_date,'Y-m-d');
        $date1 = strtotime($current_date);
        $date2 = strtotime($ucl_date);
        $diff = $date2 - $date1;
        $diffs = abs(round($diff / 86400));
        

        if ($diffs >= $ucl_days){
            $query->query("update gate_record set ucl_status = 1 where container_id =? 
                and container_id = (select id from container where id=? and gate_status='GATED IN')");
            $query->bind = array('ii',&$container_id,&$container_id);
            $query->run();

            $query->query("insert into container_ucl_history(container_id, status) VALUES (?,'CREATED')");
             $query->bind = array("i",&$container_id);
             $query->run();
             $query->commit();

            new Respond(2100);
        }
        else{
            new Respond(1521);
        }

        $query->commit();

    }

    public function move_to_depot(){
        if (!ACl::canUpdate("depot-overview")){
            new Respond(1510);
        }
        $container_id = $this->request->param('cnum');
        $query = new MyTransactionQuery();
        $query->query("update gate_record set ucl_status = 0 where container_id =? 
                and container_id = (select id from container where id=? and gate_status='GATED IN')");
        $query->bind = array('ii',&$container_id,&$container_id);
        $query->run();

        $query->query("insert into container_depot_history(container_id)values(?)");
        $query->bind = array('i',&$container_id);
        $query->run();
        $query->commit();

        new Respond(2200);
    }
}

?>