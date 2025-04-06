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

    $system_object='examination-area';

class ExaminationArea{
    public $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'gate_record')
            ->fields(
                Field::inst('gate_record.consignee as cons'),
                Field::inst('gate_record.date as date'),
                Field::inst('gate_record.examination_by as user')
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
                Field::inst('vehicle.number as veh'),
                Field::inst('vehicle_driver.name as drv'),
                Field::inst('trucking_company.name as tknam'),
                Field::inst('gate_record.id as gid'),
                Field::inst('container.id as cid')
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'examination-area') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'examination-area') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'examination-area') {
                return false;
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'examination-area') {
                return false;
            })
            ->leftJoin('container', 'gate_record.container_id', '=', 'container.id')
            ->leftJoin('vehicle', 'gate_record.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('vehicle_driver', 'gate_record.driver_id', '=', 'vehicle_driver.id')
            ->leftJoin('trucking_company', 'gate_record.trucking_company_id', '=', 'trucking_company.id')
            ->where('container.gate_status', 'GATED IN')
            ->where('gate_record.type', 'GATE IN')
            ->where('gate_record.examination_status', 1,'=')
            ->process($_POST)
            ->json();
    }

    function depot_move(){
        $id = $this->request->param('id');
        $query = new MyQuery();
        $query->query("update gate_record set examination_status=0 where id=?");
        $query->bind = array('i',&$id);
        $query->run();
        new Respond(280);
    }

}

?>