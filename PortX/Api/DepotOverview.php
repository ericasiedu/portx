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

$system_object='depot-overview';

class DepotOverview{
    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
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
                        $query->bind =  array('i', &$val);
                        $run = $query->run();
                        $result = $run->fetch_assoc();
                        $first_name = $result['first_name'];
                        $last_name = $result['last_name'];
                        $full_name = "$first_name" . " " . "$last_name";
                        return $full_name ?? '';
                    }),
                Field::inst('container.iso_type_code as iso')
                ->getFormatter(function ($val){
                    $query = new MyQuery();
                    $query->query("select code from container_isotype_code where id = ?");
                    $query->bind =  array('i', &$val);
                    $run = $query->run();
                    $result = $run->fetch_assoc();
                    return $result['code'] ?? '';
                }),
                Field::inst('container.bl_number as blnum'),
                Field::inst('container.book_number as bknum'),
                Field::inst('container.number as cnum'),
                Field::inst('depot.name as depot'),
                Field::inst('depot_gate.name as gate'),
                Field::inst('vehicle.number as veh'),
                Field::inst('vehicle_driver.name as drv'),
                Field::inst('trucking_company.name as tknam'),
                Field::inst('gate_record.id as gid'),
                Field::inst('container.id as cid')
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'depot-overview') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'depot-overview') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'depot-overview') {
//                ACl::verifyUpdate($system_object);
                return false;
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'depot-overview') {
//                ACl::verifyDelete($system_object);
                return false;
            })
            ->leftJoin('container', 'gate_record.container_id', '=', 'container.id')
            ->leftJoin('depot', 'gate_record.depot_id', '=', 'depot.id')
            ->leftJoin('depot_gate', 'gate_record.gate_id', '=', 'depot_gate.id')
            ->leftJoin('vehicle', 'gate_record.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('vehicle_driver', 'gate_record.driver_id', '=', 'vehicle_driver.id')
            ->leftJoin('trucking_company', 'gate_record.trucking_company_id', '=', 'trucking_company.id')
            ->where('gate_record.ucl_status', 0,'=')
            ->where(function ($q) use ($trade_type) {
                $q->where('container.gate_status', 'GATED OUT', '<>');
                // $q->or_where('container.gate_status', 'MOVED');
                $q->and_where('gate_record.type', 'GATE IN');
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

    public function activity_table(){
        $db = new Bootstrap();
        $db = $db->database();

        $container = $this->request->param('ctid') ?? 0;
        $query = new MyTransactionQuery();

        Editor::inst($db, 'container_log')
            ->fields(
                Field::inst('container_log.id')
                    ->setFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select id from container where number = ?");
                        $query->bind =  array('s', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select number from container where id = ?");
                        $query->bind =  array('i', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('container_log.activity_id as act'),
                Field::inst('container_log.activity_id')
                    ->setFormatter(function ($val) {
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from depot_activity where name = ?");
                        $query->bind = array('s', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select name from depot_activity where id = ?");
                        $query->bind = array('i', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->validator(function ($val, $data, $field, $host) use ($query) {
                        $container_id = $data['container_log']['container_id'];
                        $check = new Container();
                        $str = $check->checkContainer($container_id);
                        $info_id = $check->checkContainerInfo($container_id);

                        $query->query("select id from depot_activity where name  = ?");
                        $query->bind =  array('s', &$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        if(!$result['id']){
                            return 'Activity Does Not Exist';
                        }
                        if ($str == 1) {
                            return "Container is flagged";
                        }
                        if ($info_id == "") {
                            return "Container information  not added";
                        } 


                        $query->query("select id from container_log where container_id=? and activity_id=? and invoiced=1");
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
                Field::inst('container_log.note'),
                Field::inst('container_log.user_id')
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
                Field::inst('container_log.date'),
                Field::inst('container_log.container_id'),
                Field::inst('depot_activity.name as name'),
                Field::inst('container_log.id as loged')
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'depot-overview') {
                ACl::verifyCreate($system_object);
                // var_dump($editor);
                $activity = $values['container_log']['activity_id'];
                $containerId = $values['container_log']['container_id'];
                // var_dump($containerId);die;


                if ($activity == 'Unstuffing') {
                    $movedId = $this->hasMoved($containerId);
                    
                    if (is_null($movedId)) 
                        $this->moveToEmpty($containerId);
                }
            })
            ->on('preGet', function ($editor, $id, $system_object = 'depot-overview') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, &$values, $system_object = 'depot-overview') {
                ACl::verifyUpdate($system_object);
                $rowActivity = $this->getRowActivity($id);
                // var_dump($id);die;
                $formActivity = $values['container_log']['activity_id'];
                $containerId = $values['container_log']['container_id'];
                $movedId = $this->hasMoved($containerId);

                if ($formActivity == 'Unstuffing') {
                    // $rowActivity = $this->getRowActivity($id);

                    if ($rowActivity != "Unstuffing") {

                        
                        if (is_null($movedId))
                            $this->moveToEmpty($containerId);
                    }
                } else {
                    if ($rowActivity == "Unstuffing") {

                        if (!is_null($movedId))
                            $this->removeFromEmpty($movedId);
                    }
                }
            })
            ->on('preRemove', function ($editor, $id, &$values, $system_object = 'depot-overview') {
                ACl::verifyDelete($system_object);

                $activity = $values['container_log']['activity_id'];
                $containerId = $values['container_log']['container_id'];

                $query = new MyQuery();
                $query->query("select invoiced from container_log where id = ?");
                $query->bind = array('i', &$id);
                $run = $query->run();
                $result = $run->fetch_assoc();
                if ($result['invoiced'] == '1') {
                    return false;
                }

                if ($activity == 'Unstuffing') {
                    $movedId = $this->hasMoved($containerId);
                    
                    if (!is_null($movedId)) 
                        $this->removeFromEmpty($movedId);
                }

                return true;
            })
            ->on('postCreate', function ($editor, $id, $values, $system_object = 'depot-overview'){
                $container_id = $values["container_log"]["container_id"];
                $activity = $values["container_log"]["activity_id"];
                $note = $values["container_log"]["note"];
                $user_id = $_SESSION['id'];

                $query = new MyTransactionQuery();
                $query->query("select id from depot_activity where name=?");
                $query->bind = array('s',&$activity);
                $query->run();
                $result = $query->fetch_assoc();
         
                $query->query("insert into container_log_history(container_id,activity_id,note,user_id,status)values(?,?,?,?,'CREATED')");
                $query->bind = array('iisi',&$container_id,&$result['id'],&$note,&$user_id);
                $query->run();
                $query->commit();
            })
            ->on('postEdit', function ($editor, $id, $values, $system_object = 'depot-overview'){
                $container_id = $values["container_log"]["container_id"];
                $activity = $values["container_log"]["activity_id"];
                $note = $values["container_log"]["note"];
                $user_id = $_SESSION['id'];

                $query = new MyTransactionQuery();
                $query->query("select id from depot_activity where name=?");
                $query->bind = array('s',&$activity);
                $query->run();
                $result = $query->fetch_assoc();
         
                $query->query("insert into container_log_history(container_id,activity_id,note,user_id,status)values(?,?,?,?,'UPDATED')");
                $query->bind = array('iisi',&$container_id,&$result['id'],&$note,&$user_id);
                $query->run();
                $query->commit();
            })
            ->on('postRemove', function ($editor, $id, $values, $system_object = 'depot-overview'){
                $container_id = $values["container_log"]["container_id"];
                $activity = $values["container_log"]["activity_id"];
                $note = $values["container_log"]["note"];
                $user_id = $_SESSION['id'];

                $query = new MyTransactionQuery();
                $query->query("select id from depot_activity where name=?");
                $query->bind = array('s',&$activity);
                $query->run();
                $result = $query->fetch_assoc();
         
                $query->query("insert into container_log_history(container_id,activity_id,note,user_id,status)values(?,?,?,?,'DELETED')");
                $query->bind = array('iisi',&$container_id,&$result['id'],&$note,&$user_id);
                $query->run();
                $query->commit();
            })
            ->leftJoin('depot_activity', 'depot_activity.id', '=', 'container_log.activity_id')
            ->leftJoin('container', 'container_log.container_id', '=', 'container.id')
            ->where('container_log.container_id', $container, '=')
            ->where('container_log.activity_id', 3, '!=')
            ->where('container_log.activity_id', 4, '!=')
            ->process($_POST)
            ->json();
    }

    function info_table(){
        $db=new Bootstrap();
        $db=$db->database();
        Editor::inst( $db, 'container_depot_info' )
            ->fields(
                Field::inst('container_id')
                    ->setFormatter(function($val) {
                        $query=new MyQuery();
                        $query->query("select id from container where number = ? and status = 'GATED IN' order by id desc");
                        $query->bind =  array('s', &$val);
                        $run=$query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function($val) {
                        $query=new MyQuery();
                        $query->query("select number from container where id = ?");
                        $query->bind =  array('i', &$val);
                        $res=$query->run();
                        return $res->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ($val, $data, $field, $host){
                        $check = new Container();
                        $str = $check->checkContainer($val);
                        if ($str == 1){
                            return "Container is flagged";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('load_status')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('goods'),
                Field::inst('user_id')
                    ->setFormatter(function ($val){
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

    private function moveToEmpty($id) {
        // var_dump("$id got here");die;
        $tranQuery = new MyTransactionQuery();
        $tranQuery->query("SELECT number, iso_type_code, shipping_line_id, 
            agency_id, /* trade_type_code, */ gate_record.cond FROM container INNER JOIN gate_record 
            ON container.id = gate_record.container_id WHERE container.id = ?");
        $tranQuery->bind = array('i', &$id);
        $run = $tranQuery->run();
        $result = $run->fetch_assoc();

        $number = $result['number'];
        $isoTypeCode = $result['iso_type_code'];
        $shippingLineId = $result['shipping_line_id'];
        $agencyId = $result['agency_id'];
        // $tradeTypeCode = $result['trade_type_code'];
        $condition = $result['cond'];

        $tranQuery->query("SELECT id FROM voyage WHERE reference = 'EMP'");
        $res3 = $tranQuery->run();
        $result1 = $res3->fetch_assoc();
        $voyageId = $result1['id'];

        // var_dump($isoTypeCode);die;
        $tranQuery->query("INSERT INTO container (number, iso_type_code, voyage,
            shipping_line_id, agency_id, trade_type_code, gate_status, full_status) VALUES 
            (?, ?, ?, ?, ?, '70', 'GATED IN', 0)");
        $tranQuery->bind = array('siiii', &$number, &$isoTypeCode, &$voyageId, 
            &$shippingLineId, &$agencyId);
        $run = $tranQuery->run();
        // $result = $run->fetch_all();

        $tranQuery->query("SELECT id FROM container WHERE number = ? 
            AND trade_type_code = '70' AND gate_status = 'GATED IN' ORDER BY id DESC");
        $tranQuery->bind = array('s', &$number);
        $run = $tranQuery->run();
        $result = $run->fetch_assoc();

        $emptyId = $result['id'];
        $userId = $_SESSION['id'];
        $date = date('Y-m-d H:i:s');
        $info_category = 'General Goods';

        $tranQuery->query("INSERT INTO gate_record (container_id, type, depot_id, 
            gate_id, user_id, cond) VALUES (?, 'GATE IN', 2, 4, 
            ?, ?)");
        $tranQuery->bind = array('iii', &$emptyId, &$userId, &$condition);
        $run = $tranQuery->run();

        $tranQuery->query("UPDATE container SET moved_to = ?, gate_status = 'MOVED' 
                        WHERE id = ?");
        $tranQuery->bind = array('ii', &$emptyId, &$id);
        $run = $tranQuery->run();

        /* $tranQuery->query("select id from depot_activity where name = 'Lift on-lift off charges' OR 
        name='Handling'");
        $tranQuery->bind = array();
        $tranQuery->run();
        $emptyDefaults = $tranQuery->fetch_all(MYSQLI_ASSOC);
        // $empty_default = $query_res['id'];


        foreach ($emptyDefaults as $activity) {
            $empty_default = $activity['id'];

            $tranQuery->query("INSERT INTO container_log(container_id, activity_id, user_id, date)VALUES(?,?,?, now())");
            $tranQuery->bind = array('iii', &$emptyId, &$empty_default, &$userId);
            $tranQuery->run();

            $tranQuery->query("insert into container_log_history(container_id,activity_id,user_id,status)values(?,?,?,'CREATED');
            ");
            $tranQuery->bind = array('iii', &$emptyId, &$empty_default, &$userId);
            $tranQuery->run();

            $tranQuery->query("INSERT INTO proforma_container_log(container_id, activity_id, user_id, date)VALUES(?,?,?, now())");
            $tranQuery->bind = array('iii', &$emptyId, &$empty_default, &$userId);
            $tranQuery->run();

            $tranQuery->query("insert into proforma_container_log_history(container_id,activity_id,user_id,status)values(?,?,?,'CREATED');
            ");
            $tranQuery->bind = array('iii', &$emptyId, &$empty_default, &$userId);
            $tranQuery->run();
        } */

        $tranQuery->query("insert into container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL',?,?)");
        $tranQuery->bind = array('isi', &$emptyId, &$info_category, &$userId);
        $tranQuery->run();
        $tranQuery->query("insert into proforma_container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL',?,?)");
        $tranQuery->bind = array('isi', &$emptyId, &$info_category, &$userId);
        $tranQuery->run();

        $tranQuery->query("SELECT id FROM depot_activity WHERE name = 'Lift on-lift off charges'");
        $run = $tranQuery->run();
        $result = $run->fetch_assoc();

        $liftOnLiftOffId = $result['id'];

        $tranQuery->query("INSERT INTO container_log (container_id, activity_id, 
            user_id, date) VALUES (?, ?, ?, ?)");
        $tranQuery->bind = array('iiis', &$emptyId, &$liftOnLiftOffId, &$userId, &$date);
        $run = $tranQuery->run();

        $tranQuery->query("INSERT INTO proforma_container_log (container_id, activity_id, 
            user_id, date) VALUES (?, ?, ?, ?)");
        $tranQuery->bind = array('iiis', &$emptyId, &$liftOnLiftOffId, &$userId, &$date);
        $run = $tranQuery->run();

        $tranQuery->commit();
    }

    private function removeFromEmpty($id) {

        $tranQuery = new MyTransactionQuery();

        $tranQuery = new MyTransactionQuery();
        $tranQuery->query("DELETE FROM container_log WHERE EXISTS (SELECT 1 FROM depot_activity 
            WHERE container_log.activity_id = depot_activity.id) 
            AND container_log.container_id = ?");
        $tranQuery->bind = array('i', &$id);
        $run = $tranQuery->run();

        $tranQuery->query("DELETE FROM proforma_container_log WHERE EXISTS (SELECT 1 FROM depot_activity 
            WHERE proforma_container_log.activity_id = depot_activity.id) 
            AND proforma_container_log.container_id = ?");
        $tranQuery->bind = array('i', &$id);
        $run = $tranQuery->run();

        $tranQuery->query("DELETE FROM gate_record WHERE container_id = ?");
        $tranQuery->bind = array('i', &$id);
        $run = $tranQuery->run();

        $tranQuery->query("DELETE FROM container_depot_info WHERE container_id = ?");
        $tranQuery->bind = array('i', &$id);
        $run = $tranQuery->run();

        $tranQuery->query("DELETE FROM proforma_container_depot_info WHERE container_id = ?");
        $tranQuery->bind = array('i', &$id);
        $run = $tranQuery->run();

        $tranQuery->query("UPDATE container SET moved_to = NULL, gate_status = 'GATED IN' 
                        WHERE moved_to = ?");
        $tranQuery->bind = array('i', &$id);
        $run = $tranQuery->run();

        $tranQuery->query("DELETE FROM container WHERE id = ?");
        $tranQuery->bind = array('i', &$id);
        $run = $tranQuery->run();

        $tranQuery->commit();
    }

    private function hasMoved($id) {
        $query = new MyQuery();
        $query->query("SELECT moved_to FROM container WHERE id = ?");
        $query->bind =  array('s', &$id);
        $run = $query->run();
        $result1 = $run->fetch_assoc();
        $movedId = $result1['moved_to'];
        
        return $movedId;
    }

    private function getRowActivity($id) {
        $query = new MyQuery();
        $query->query("SELECT depot_activity.name FROM container_log 
                        INNER JOIN depot_activity 
                        ON depot_activity.id = container_log.activity_id 
                        WHERE container_log.id = ?");
        $query->bind = array('i', &$id);
        $run = $query->run();
        $result = $run->fetch_assoc();

        return $result['name'];

    }

    public function get_invoice_cost(){
        $container_number = $this->request->param('cntr');

        $result = array();
        $query = new MyQuery();
        $query->query("select (cost+tax) as amount from invoice where number = ?");
        $query->bind =  array('s', &$container_number);
        $run = $query->run();
        $result1 = $run->fetch_assoc();
        $result['amt'] = $result1['amount'];
        $result['cntr'] = $container_number;
        new Respond( 273, $result);
    }

    public function get_container_info(){
        $container_number = $this->request->param('cntr');

        $result = array();
        $query = new MyQuery();
        $query->query("select load_status, goods from container_depot_info 
              inner join container on container.id = container_depot_info.container_id 
              where container.number = ?  and gate_status = 'GATED IN'");
        $query->bind =  array('s', &$container_number);
        $run = $query->run();
        $info = $run->fetch_assoc();
        $result['ldst'] = $info['load_status'];
        $result['good'] = $info['goods'];
        $result['ctnum'] = $container_number;
        new Respond(273, $result);
    }

    public function get_sup_invoice_cost(){
        $container_number = $this->request->param('cntr');

        $result = array();
        $query = new MyQuery();
        $query->query("select (cost+tax) as amount from supplementary_invoice where number = ?");
        $query->bind =  array('s', &$container_number);
        $run = $query->run();
        $result1 = $run->fetch_assoc();
        $result['amt'] = $result1['amount'];
        $result['cntr'] = $container_number;
        new Respond( 273, $result);
    }

    public  function  check_activity_usage() {
        $id = $this->request->param('acid');

        $ids = json_decode($id);
        $bind_types = "";
        $bind_mask = "";

        foreach($ids as $id) {
            $bind_types = $bind_types.'i';
            $bind_mask = $bind_mask . '?,';
        }

        $bind_mask = rtrim($bind_mask, ',');

        $query=new MyQuery();
        $query->query("select * from container_log where activity_id IN ($bind_mask)");
        $bind_data =array($bind_types);

        foreach ($ids as $id) {
            array_push($bind_data, $id);
        }

        $query->bind = $bind_data;
        $run = $query->run();

        if($run->num_rows() > 0){
            new Respond(174);
        }
        else {
            new Respond(274);
        }
    }

    public function check_info(){
        $id = $this->request->param('data');
        $query=new MyQuery();
        $query->query("SELECT id FROM container_depot_info WHERE container_id = ?");
        $query->bind =  array('i', &$id);
        $run=$query->run();
        $count = $run->num_rows();
        new Respond($count > 0 ? 271 : 272);
    }

    public function update_container_info(){
        $container_number = $this->request->param('ctnr');
        $load_status = $this->request->param('ldst');
        $goods = $this->request->param('good');

        $query = new MyTransactionQuery();
        $query->query("select id from container where number = ?  and gate_status = 'GATED IN'");
        $query->bind =  array('s', &$container_number);
        $run = $query->run();
        $container_id = $run->fetch_assoc()['id'];

        $query->query("select id from container_log where invoiced = 1 and container_id = ?");
        $query->bind = array('i',&$container_id);
        $query->run();
        if ($query->num_rows() > 0){
            new Respond(112);
        }
        $query->query("update container_depot_info set load_status= ?, goods=? where container_id=?");
        $query->bind =  array('ssi', &$load_status, &$goods, &$container_id);
        $query->run();

        $query->commit();
    }

    public function acitivty_invoiced(){
        $container_log = $this->request->param('logg');
        $is_proforma = $this->request->param('prof') == 1;
        $table_prefix = $is_proforma ? "proforma_" : "";
        $query = new MyQuery();
        $query->query("select id from ".$table_prefix."container_log where id=? and invoiced=1");
        $query->bind = array('i',&$container_log);
        $query->run();
        $result = $query->fetch_assoc();
        $error = array();
        if($result['id']){
            $error['err'] = 1;
        }
        echo json_encode($error);
    }

}


?>
