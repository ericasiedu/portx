<?php

namespace Api;
session_start();
use
    Lib\ACL,
    Lib\Container,
    Lib\MyQuery,
    Lib\Respond,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\MyTransactionQuery;

$system_object='proforma-depot-overview';

class ProformaDepotOverview {
    private $request;

    function __construct($request) {
        $this->request = $request;
    }

    function table() {
        $db = new Bootstrap();
        $db = $db->database();

        $trade_type = $this->request->param('trade_type') ?? "ALL";

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
                        return isset($_SESSION['id']) ? $_SESSION['id'] : 1;
                    })->getFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("SELECT first_name, last_name FROM user WHERE id = ?");
                        $query->bind = array('i', &$val);
                        $run = $query->run();
                        $result = $run->fetch_assoc();
                        $first_name = $result['first_name'];
                        $last_name = $result['last_name'];
                        $full_name = "$first_name" . " " . "$last_name";
                        return $full_name ?? '';
                    }),
                Field::inst('container.number as cnum'),
                Field::inst('container.bl_number as bln'),
                Field::inst('container.book_number as bkn'),
                Field::inst('container_isotype_code.code as type'),
                Field::inst('depot.name as depot'),
                Field::inst('depot_gate.name as gate'),
                Field::inst('vehicle.number as veh'),
                Field::inst('vehicle_driver.name as drv'),
                Field::inst('trucking_company.name as tknam'),
                Field::inst('gate_record.id as gid'),
                Field::inst('container.id as cid')
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'proforma-depot-overview') {
                return false;
            })
            ->on('preGet', function ($editor, $id, $system_object = 'proforma-depot-overview') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'proforma-depot-overview') {
                return false;
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'proforma-depot-overview') {
                return false;
            })
            ->leftJoin('container', 'gate_record.container_id', '=', 'container.id')
            ->leftJoin('container_isotype_code', 'container.iso_type_code', '=', 'container_isotype_code.id')
            ->leftJoin('depot', 'gate_record.depot_id', '=', 'depot.id')
            ->leftJoin('depot_gate', 'gate_record.gate_id', '=', 'depot_gate.id')
            ->leftJoin('vehicle', 'gate_record.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('vehicle_driver', 'gate_record.driver_id', '=', 'vehicle_driver.id')
            ->leftJoin('trucking_company', 'gate_record.trucking_company_id', '=', 'trucking_company.id')
            ->where('container.gate_status', 'GATED IN')
            ->where('gate_record.type', 'GATE IN')
            ->where('gate_record.ucl_status', 0, '=')
            ->where(function ($q) use ($trade_type) {
                if ($trade_type == "ALL") {
                    $q->where('gate_record.id', '(SELECT id FROM gate_record)', 'IN', false);
                } elseif ($_POST['trade_type'] == '1') {
                    $q->where('gate_record.id', '(select gate_record.id from gate_record inner join container on gate_record.container_id = container.id where container.trade_type_code = 11)', 'IN', false);
                } elseif ($_POST['trade_type'] == '4') {
                    $q->where('gate_record.id', '(select gate_record.id from gate_record inner join container on gate_record.container_id = container.id where container.trade_type_code = 21)', 'IN', false);
                }
            })
            ->process($_POST)
            ->json();
    }

    public function activity_table() {
        $db = new Bootstrap();
        $db = $db->database();

        $query = new MyTransactionQuery();

        $container = $this->request->param('ctid') ?? 0;

        Editor::inst($db, 'proforma_container_log')
            ->fields(
                Field::inst('proforma_container_log.id')
                    ->setFormatter(function ($val) use ($query) {
                        $query->query("select id from container where number = ?");
                        $query->bind = array('s', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) use ($query) {
                        $query->query("select number from container where id = ?");
                        $query->bind = array('i', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('proforma_container_log.activity_id as act'),
                Field::inst('proforma_container_log.activity_id')
                    ->setFormatter(function ($val) use ($query) {
                        $val = html_entity_decode($val);
                        $query->query("select id from depot_activity where name = ?");
                        $query->bind = array('s', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) use ($query) {
                        $query->query("select name from depot_activity where id = ?");
                        $query->bind = array('i', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->validator(function ($val, $data, $field, $host) use ($query) {
                        $container_id = $data['proforma_container_log']['container_id'];
                        $check = new Container();
                        $str = $check->checkContainer($container_id, $query);
                        $info_id = $check->checkContainerInfo($container_id, true, $query);
                        $query->query("select id from depot_activity where name  = ?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        $result = $query->fetch_assoc();

                        if(!$result['id']){
                            return 'Activity Does Not Exist';
                        }

                        if ($str == 1) {
                            return "Container is flagged";
                        }
                        if (!$info_id) {
                            return "Container information  not added";
                        }

                        $query->query("select id from proforma_container_log where container_id=? and activity_id=? and invoiced=1");
                        $query->bind = array('ii',&$container_id,&$result['id']);
                        $query->run();
                        $query_result = $query->fetch_assoc();
                        if($query_result['id']){
                            return "Activity has been invoiced";
                        }

                        return true;
                        
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('proforma_container_log.note'),
                Field::inst('proforma_container_log.user_id')
                    ->setFormatter(function ($val) {
                        return isset($_SESSION['id']) ? $_SESSION['id'] : 1;
                    })
                    ->getFormatter(function ($val) use ($query) {
                        $query->query("select first_name, last_name from user where id  = ?");
                        $query->bind = array('i', &$val);
                        $run = $query->run();
                        $name = $run->fetch_assoc();
                        return $name['first_name'] . ' ' . $name['last_name'];
                    }),
                Field::inst('proforma_container_log.date'),
                Field::inst('proforma_container_log.container_id'),
                Field::inst('proforma_container_log.id as loged'),
                Field::inst('depot_activity.name as name')
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'proforma-depot-overview') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'proforma-depot-overview') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'proforma-depot-overview') {
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'proforma-depot-overview') use ($query) {
                ACl::verifyDelete($system_object);
                $query->query("select invoiced from proforma_container_log where id = ?");
                $query->bind = array('i', &$id);
                $run = $query->run();
                $result = $run->fetch_assoc();
                if ($result['invoiced'] == '1') {
                    return false;
                }
                return true;
            })
            ->on('postCreate', function ($editor, $id, $values, $system_object = 'depot-overview'){
                $container_id = $values["proforma_container_log"]["container_id"];
                $activity = $values["proforma_container_log"]["activity_id"];
                $note = $values["proforma_container_log"]["note"];
                $user_id = $_SESSION['id'];

                $query = new MyTransactionQuery();
                $query->query("select id from depot_activity where name=?");
                $query->bind = array('s',&$activity);
                $query->run();
                $result = $query->fetch_assoc();
         
                $query->query("insert into proforma_container_log_history(container_id,activity_id,note,user_id,status)values(?,?,?,?,'CREATED')");
                $query->bind = array('iisi',&$container_id,&$result['id'],&$note,&$user_id);
                $query->run();
                $query->commit();
            })
            ->on('postEdit', function ($editor, $id, $values, $system_object = 'depot-overview'){
                $container_id = $values["proforma_container_log"]["container_id"];
                $activity = $values["proforma_container_log"]["activity_id"];
                $note = $values["proforma_container_log"]["note"];
                $user_id = $_SESSION['id'];

                $query = new MyTransactionQuery();
                $query->query("select id from depot_activity where name=?");
                $query->bind = array('s',&$activity);
                $query->run();
                $result = $query->fetch_assoc();
         
                $query->query("insert into proforma_container_log_history(container_id,activity_id,note,user_id,status)values(?,?,?,?,'UPDATED')");
                $query->bind = array('iisi',&$container_id,&$result['id'],&$note,&$user_id);
                $query->run();
                $query->commit();
            })
            ->on('postRemove', function ($editor, $id, $values, $system_object = 'depot-overview'){
                $container_id = $values["proforma_container_log"]["container_id"];
                $activity = $values["proforma_container_log"]["activity_id"];
                $note = $values["proforma_container_log"]["note"];
                $user_id = $_SESSION['id'];

                $query = new MyTransactionQuery();
                $query->query("select id from depot_activity where name=?");
                $query->bind = array('s',&$activity);
                $query->run();
                $result = $query->fetch_assoc();
         
                $query->query("insert into proforma_container_log_history(container_id,activity_id,note,user_id,status)values(?,?,?,?,'DELETED')");
                $query->bind = array('iisi',&$container_id,&$result['id'],&$note,&$user_id);
                $query->run();
                $query->commit();
            })
            ->leftJoin('depot_activity', 'depot_activity.id', '=', 'proforma_container_log.activity_id')
            ->leftJoin('container', 'proforma_container_log.container_id', '=', 'container.id')
            ->where('proforma_container_log.container_id', $container, '=')
            ->where('proforma_container_log.activity_id', 3, '!=')
            ->where('proforma_container_log.activity_id', 4, '!=')
            ->process($_POST)
            ->json();
    }

    function info_table() {
        $db = new Bootstrap();
        $db = $db->database();
        $query = new MyTransactionQuery();
        Editor::inst($db, 'proforma_container_depot_info')
            ->fields(
                Field::inst('container_id')
                    ->setFormatter(function ($val) use ($query){
                        $query->query("select id from container where number = ? and status = 'GATED IN' order by id desc");
                        $query->bind = array('s', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) use ($query){
                        $query->query("select number from container where id = ?");
                        $query->bind = array('i', &$val);
                        $res = $query->run();
                        return $res->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ($val, $data, $field, $host) {
                        $check = new Container();
                        $str = $check->checkContainer($val);
                        if ($str == 1) {
                            return "Container is flagged";
                        } else {
                            return true;
                        }
                    }),
                Field::inst('load_status')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('goods'),
                Field::inst('user_id')
                    ->setFormatter(function ($val) {
                        return isset($_SESSION['id']) ? $_SESSION['id'] : 1;
                    }),
                Field::inst('date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
            )
            ->process($_POST)
            ->json();
    }

    public function get_container_info() {
        $container_number = $this->request->param('cntr');

        $result = array();
        $query = new MyQuery();
        $query->query("select load_status, goods from proforma_container_depot_info 
              inner join container on container.id = proforma_container_depot_info.container_id 
              where container.number = ?");
        $query->bind = array('s', &$container_number);
        $run = $query->run();
        $info = $run->fetch_assoc();
        $result['ldst'] = $info['load_status'];
        $result['good'] = $info['goods'];
        $result['ctnum'] = $container_number;
        new Respond(273, $result);
    }

    public function check_activity_usage() {
        $id = $this->request->param('acid');

        $ids = json_decode($id);
        $bind_types = "";
        $bind_mask = "";

        foreach ($ids as $id) {
            $bind_types = $bind_types . 'i';
            $bind_mask = $bind_mask . '?,';
        }

        $bind_mask = rtrim($bind_mask, ',');

        $query = new MyQuery();
        $query->query("select * from proforma_container_log where activity_id IN ($bind_mask)");
        $bind_data = array($bind_types);

        foreach ($ids as $id) {
            array_push($bind_data, $id);
        }

        $query->bind = $bind_data;
        $run = $query->run();

        if ($run->num_rows() > 0) {
            new Respond(174);
        } else {
            new Respond(274);
        }
    }

    public function check_info() {
        $id = $this->request->param('data');
        $query = new MyQuery();
        $query->query("SELECT id FROM proforma_container_depot_info WHERE container_id = ?");
        $query->bind = array('i', &$id);
        $run = $query->run();
        $count = $run->num_rows();
        new Respond($count > 0 ? 271 : 272);
    }

    public function update_container_info() {
        $container_number = $this->request->param('ctnr');
        $load_status = $this->request->param('ldst');
        $goods = $this->request->param('good');

        $query = new MyTransactionQuery();
        $query->query("select id from container where number = ?");
        $query->bind = array('s', &$container_number);
        $run = $query->run();
        $container_id = $run->fetch_assoc()['id'];

        $query->query("update proforma_container_depot_info set load_status= ?, goods=? where container_id=?");
        $query->bind = array('ssi', &$load_status, &$goods, &$container_id);
        $run = $query->run();
        $query->commit();
    }
}
?>
