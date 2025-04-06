<?php
namespace Api;
session_start();

use DateTime;
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
    Lib\MyTransactionQuery,
    Lib\YardTime,
    Lib\Gate,
    Lib\Stack,
    PhpOffice\PhpSpreadsheet\Shared\Date;

    $system_object='yard-planning';

    date_default_timezone_set('UTC');

    class YardPlanning{
        private $request;
        public $stack;
        public $container;
        public $bay;
        public $row;
        public $tier;
        public $container_trade;
        public $container_id;
        public $container_size;
        public $equipment_number;
        public $trade_type;


        function __construct($request){
            $this->request = $request;
        }

        function table(){
            $db = new Bootstrap();
            $db = $db->database();
            $query = new MyTransactionQuery();

            Editor::inst($db, 'gate_record')
                ->fields(
                    Field::inst('container.trade_type_code as emx')
                        ->getFormatter(function($val){
                            $trade_type = "";
                            switch ($val) {
                                case '11':
                                    $trade_type = "IMP";
                                    break;
                                case '21':
                                    $trade_type = "EXP";
                                    break;
                                case '13':
                                    $trade_type = "TRA";
                                    break;
                                case '70':
                                    $trade_type = "EMP";
                                    break;
                                default:
                                    # co
                                    break;
                            }
                            return $trade_type;
                        }),
                    Field::inst('container.trade_type_code as trty')
                        ->getFormatter(function($val){
                            $trade_type = "";
                            switch ($val) {
                                case '11':
                                    $trade_type = "IMPORT";
                                    break;
                                case '21':
                                    $trade_type = "EXPORT";
                                    break;
                                case '13':
                                    $trade_type = "TRANSIT";
                                    break;
                                case '70':
                                    $trade_type = "EMPTY";
                                    break;
                                default:
                                    # co
                                    break;
                            }
                            return $trade_type;
                        }),
                    Field::inst('gate_record.date as date'),
                    Field::inst('yard_log.id as pos')
                        ->getFormatter(function($val,$data) use ($query){
                            $query->query("select stack,bay,row,tier,yard_activity from yard_log where id =?");
                            $query->bind = array('i',&$val);
                            $query->run();
                            if ($query->num_rows() == 0) {
                                return "Not Positioned";
                            }
                            $result = $query->fetch_assoc();
                            if (($result['yard_activity'] == 'EXAMINATION')) {
                                return "Not Positioned";
                            }
                            elseif (($result['yard_activity'] == 'OUT STACK')) {
                                return "Not Positioned";
                            }
                            else{
                                
                                return  $result['stack'].$result['bay'].$result['row'].$result['tier'];
                            }
                        }),
                    Field::inst('yard_log.id as rfs')
                        ->getFormatter(function($val) use ($query){
                            $query->query("select reefer_status from yard_log where id=?");
                            $query->bind = array('i',&$val);
                            $query->run();
                            if ($query->num_rows() == 0) {
                                return "Reefer Status not assigned";
                            }
                            else{
                                $result = $query->fetch_assoc();
                                return $result['reefer_status'] == 1 ? "YES" : "NO";
                            }
                        }),
                    Field::inst('container.id as yid')
                        ->getFormatter(function($val) use($query){
                            $query->query("select id from yard_log where container_id=?");
                            $query->bind = array('i',&$val);
                            $query->run();
                            $result = $query->fetch_assoc();
                            if ($result['id']) {
                                return "STACKED";
                            }
                            else{
                                return "NOT STACKED";
                            }
                        }),
                    Field::inst('container.id as stk')
                        ->getFormatter(function($val) use ($query){
                            $query->query("select id,yard_activity from yard_log where container_id=?");
                            $query->bind = array('i',&$val);
                            $query->run();
                            $result = $query->fetch_assoc();
                            if ((!$result['id'])) {
                                return "Not in Stack";
                            }
                            if ($result['yard_activity'] == 'EXAMINATION') {
                                return "Not in Stack";
                            }
                            elseif ($result['yard_activity'] == 'OUT STACK') {
                                return "Not in Stack";
                            }
                            else{
                                $query->query("select stack from yard_log where container_id=?");
                                $query->bind = array('i',&$val);
                                $query->run();
                                $result = $query->fetch_assoc();
                                return $result['stack'];
                            }
                        }),
                    Field::inst('container.number as cnum'),
                    Field::inst('yard_log.id as yard_id'),
                    Field::inst('yard_log.positioned as posi'),
                    Field::inst('container_isotype_code.code as size')
                        ->getFormatter(function($val){
                            return $val;
                        }),
                    Field::inst('container.shipping_line_id as opr')
                        ->getFormatter(function($val) use ($query) {
                            $query->query("select code from shipping_line where id  = ?");
                            $query->bind = array('i', &$val);
                            $query->run();
                            $result = $query->fetch_assoc();
                            return substr($result['code'],0,3);
                        }),
                    Field::inst('container.shipping_line_id as owr')
                        ->getFormatter(function($val) use ($query) {
                            $query->query("select code from shipping_line where id  = ?");
                            $query->bind = array('i', &$val);
                            $query->run();
                            $result = $query->fetch_assoc();
                            return substr($result['code'],0,3);
                        }),
                    Field::inst('container.id as mins')
                        ->getFormatter(function($val) use($query){
                            $query->query("select id, stack_time from yard_log where container_id=?");
                            $query->bind = array('i', &$val);
                            $query->run();

                            if (($query->num_rows() > 0) ) {
                                $result = $query->fetch_assoc();
                                $start_date = $this->get_gatein_date($query,$val);
                                $since_start = $result['stack_time'];
                                return YardTime::getTimeSpent($start_date,$since_start);
                            }
                            else{
                                $start_date = $this->get_gatein_date($query,$val);
                                $current_date =date("Y-m-d H:i:s");
                                return YardTime::getTimeSpent($start_date,$current_date);
                            }
                            
                        }),    
                        Field::inst('vehicle.number as veh'),
                    Field::inst('gate_record.container_id as ltps')
                        ->getFormatter(function($val) use($query){
                            $query->query("select letpass_id from letpass_container where container_id=?");
                            $query->bind = array('i',&$val);
                            $query->run();
                            $result = $query->fetch_assoc();
                            $let_pass = $result['letpass_id'];
                            return $let_pass ?? "";
                        }),
                    Field::inst('gate_record.yard_status as acty')
                        ->getFormatter(function($val){
                           return $val == 0 ? "IN" : "OUT";
                        }),
                    Field::inst('gate_record.yard_status as ytat'),    
                    Field::inst('gate_record.id as gid'),
                    Field::inst('yard_log.approved as appr'),
                    Field::inst('yard_log.yard_activity as actv'),
                    Field::inst('yard_log.equipment_no as eqno'),
                    Field::inst('container.id as cid'),
                    Field::inst('container.id as sts')
                        ->getFormatter(function($val) use($query){
                            $query->query("select id,approved,yard_activity from yard_log where container_id=?");
                            $query->bind = array('i',&$val);
                            $query->run();
                            $result = $query->fetch_assoc();

                            $query->query("select id from holding_area where container_id=?");
                            $query->bind = array('i',&$val);
                            $query->run();
                            $result2 = $query->fetch_assoc();

                            $query->query("select letpass_id from letpass_container where container_id=?");
                            $query->bind = array('i',&$val);
                            $query->run();
                            $result3 = $query->fetch_assoc();

                            if ($result['yard_activity'] == 'ASSIGN') {
                                return "ASSIGNED FOR STACKING";
                            }
                            if ($result['yard_activity'] == 'EXAMINATION') {
                                return "ASSIGNED TO EXAMINATION";
                            }
                            if ($result['yard_activity'] == 'REMOVE') {
                                return "ASSIGNED FOR REMOVAL";
                            }
                            if ($result['yard_activity'] == 'ON TRUCK') {
                                return "ASSIGNED FOR MOVING ONTO TRUCK";
                            }
                            elseif($result['yard_activity'] == 'IN STACK'){
                                return "IN STACK";
                            }
                            elseif(($result3['letpass_id'] != "") && (!$result['id'])){
                                return "ON TRUCK";
                            }
                            elseif($result2['id']){
                                return "OUT OF STAdCK";
                            }
                            else{
                                return "ON TRUCK";
                            }
                        })
                )
                ->on('preCreate', function ($editor, $values, $system_object = 'yard-planning') {
                    ACl::verifyCreate($system_object);
                })
                ->on('preGet', function ($editor, $id, $system_object = 'yard-planning') {
                    ACl::verifyRead($system_object);
                })
                ->on('preEdit', function ($editor, $id, $values, $system_object = 'yard-planning') {
                    return false;
                })
                ->on('preRemove', function ($editor, $id, $values, $system_object = 'yard-planning') {
                    return false;
                })
                ->leftJoin('container', 'gate_record.container_id', '=', 'container.id')
                ->leftJoin('yard_log', 'yard_log.container_id', '=', 'container.id')
                ->leftJoin('container_isotype_code', 'container_isotype_code.id', '=', 'container.iso_type_code')
                ->leftJoin('vehicle', 'gate_record.vehicle_id', '=', 'vehicle.id')
                ->leftJoin('trucking_company', 'gate_record.trucking_company_id', '=', 'trucking_company.id')
                ->where('gate_record.ucl_status', 0,'=')
                ->where('gate_record.examination_status', 0,'=')
                ->where('container.gate_status', 'GATED OUT','<>')
                ->where('gate_record.type', 'GATE IN', '=')
                ->process($_POST)
                ->json();
                $query->commit();
        }

        function yard_table(){
            $db = new Bootstrap();
            $db = $db->database();
            $query =new MyTransactionQuery();
            Editor::inst($db, 'yard_log')
                ->fields(
                    Field::inst('container_id')
                        ->setFormatter(function($val) use($query){
                            return Container::getContainerID($query,$val);
                        })
                        ->validator(function($val,$data) use($query){
                            $stack = $data['stack'];
                            $row = $data['row'];
                            $bay = $data['bay'];

                            $container_q = new MyQuery();
                            $container_q->query("select id from container where number=? and gate_status='GATED IN'");
                            $container_q->bind = array('s',&$val);
                            $container_q->run();
                            $container_query = $container_q->fetch_assoc();
                            $container_id = $container_query['id'];

                            $query->query("select tier,container_id from yard_log where stack=? and bay=? and row=?");
                            $query->bind = array('sis',&$stack,&$bay,&$row);
                            $query->run();
                            $result = $query->fetch_all();
                            if (count($result) == 1) {
                                $container_first_tier = Container::getTradeType($query,$result[0][1]);
                                $container_tier = Container::getTradeType($query,$container_id);
                                if ($container_first_tier != $container_tier) {
                                    return "Cannot place ".$container_tier." container on top of ".$container_first_tier." container";
                                }
                                $container_first_size = Container::getContainerSize($query,$result[0][1]);
                                $container_size = Container::getContainerSize($query,$container_id);
                                $container_first_height = $container_first_size[0][1];
                                $container_height = $container_size[0][1];
                             
                                if ($container_first_size[0][0] != $container_size[0][0]) {
                                    return "Cannot place ".$container_size[0][0]." feet container on top of ".$container_first_size[0][0]." feet container";
                                }
                                elseif ($container_first_height != $container_height) {
                                    return "Cannot place ".$container_height." container height on top of ".$container_first_height." container height";
                                }
                            }
                            elseif (count($result) == 2) {
                                $container_first_tier = Container::getTradeType($query,$result[0][1]);
                                $container_tier = Container::getTradeType($query,$container_id);
                                if ($container_first_tier != $container_tier) {
                                    return "Cannot place ".$container_tier." container on top of".$container_first_tier." container";
                                }
                                $container_first_size = Container::getContainerSize($query,$result[0][1]);
                                $container_size = Container::getContainerSize($query,$container_id);
                                $container_first_height = $container_first_size[0][1];
                                $container_height = $container_size[0][1];

                                if ($container_first_size[0][0] != $container_size[0][0]) {
                                    return "Cannot place ".$container_size[0][0]." feet on top of ".$container_first_size[0][0]." feet";
                                }
                                elseif ($container_first_height != $container_height) {
                                    return "Cannot place ".$container_height." container height on top of ".$container_first_height." container height";
                                }
                            }
                            return true;
                        }),
                    Field::inst('stack')
                        ->validator(function($val,$data) use ($query){
                            $container = $data['container_id'];
                            $stack = $val;
                            $row = $data['row'];
                            $bay = $data['bay'];

                            $query->query("select id from stack where name=?");
                            $query->bind = array('s',&$val);
                            $query->run();
                            $stack_result = $query->fetch_assoc();
                            if (!$stack_result['id']) {
                                return "Stack $val does not exist";
                            }
                            
                            $query->query("select container_depot_info.goods as goods from container left join container_depot_info on container.id = container_depot_info.container_id where container.number=? and container.gate_status='GATED IN'");
                            $query->bind = array('s',&$container);
                            $query->run();
                            $result = $query->fetch_assoc();
                            $goods = substr($result['goods'],0,2);
                
                            if (($goods == "DG") && ($stack != "DG")) {
                                return "Container is DG and must be place at Stack DG";
                            }
                            elseif (($goods != "DG") && ($stack == "DG")) {
                                return "Container cannot be place at Stack DG";
                            }
                            $query->query("select count(id) as qty from yard_log where stack=? and bay=? and row=?");
                            $query->bind = array('sis',&$stack,&$bay,&$row);
                            $query->run();
                            $tier_query = $query->fetch_assoc();
                            $tier_quantity = $tier_query['qty'];

                            if ($tier_quantity >= 3) {
                                return "Stack ".$stack.", bay ".$bay." of row ".$row." is full";
                            }
                            else{
                                return true;
                            }
                        }),
                    Field::inst('bay')
                        ->validator(function($val,$data) use($query){
                            if ($val > 1) {
                                $bay_value = $val%2;
                                if ($bay_value == 0) {
                                    return "Invalid bay";
                                }
                                else{
                                    $container_id = Container::getContainerID($query,$data['container_id']);
                                    $container_length = Container::getContainerSize($query,$container_id);
                                    $bay_check = new Stack();
                                    $bay_check->stack = $data['stack'];
                                    if ($container_length[0][0] <= 25) {
                                        if ($bay_check->getFortyFeetBayId($query,$data['bay'])) {
                                            return "bay $val already occupied";
                                        }
                                        else{
                                            return true;
                                        }
                                    }
                                    else{
                                        $this->validate_forty_bay($query,$data['bay'],$data['container_id']);
                                    }
                                }
                            }
                            else{
                                return true;
                            }
                        })
                        ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                    Field::inst('stack_time')
                        ->setFormatter(function($val){
                            return date('Y-m-d H:i:s');
                        }),
                    Field::inst('row'),
                    Field::inst('tier')
                        ->validator(function($val,$data) use($query){
                            $stack = $data['stack'];
                            $row = $data['row'];
                            $bay = $data['bay'];

                            $query->query("select tier from yard_log where stack=? and bay=? and row=?");
                            $query->bind = array('sis',&$stack,&$bay,&$row);
                            $query->run();

                            if ($query->num_rows() >= 3) {
                                return "Stack ".$stack.", bay ".$bay." of row ".$row." is full";
                            }
                            else{

                                $tier_query = $query->fetch_all();

                                if(empty($tier_query)){
                                    return $val != 1 ? "Tier $val is already occupied":true;
                                }
                                elseif (count($tier_query) == 1) {
                                    return $val != 2 ? "Tier $val is already occupied" : true;
                                }
                                elseif (count($tier_query) ==2) {
                                    return $val != 3 ? "Tier $val is already occupied" : true;
                                }
                                return true;
                            }
                        }),
                    Field::inst('reefer_status'),
                    Field::inst('yard_activity')
                        ->setFormatter(function($val){
                            return "ASSIGN";
                        }),
                    Field::inst('assigned_by')
                        ->setFormatter(function($val){
                            return $_SESSION['id'];
                        }),
                    Field::inst('equipment_no')
                        ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function($val,$data) use($query){
                        $query->query("select id,type from reach_stacker where equipment_no=?");
                        $query->bind = array('s',&$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        
                        if (!$result['id']) {
                            return "Reach Stacker equipment Number does not exist";
                        }
                        $query->query("select trade_type.name from container left join trade_type on trade_type.code = container.trade_type_code where container.number =? and container.gate_status='GATED IN'");
                        $query->bind = array('s',&$data['container_id']);
                        $query->run();
                        $result2 = $query->fetch_assoc();
                        if (($result2['name'] !="EMPTY")&&($result['type'] == "EMPTY")) {
                            return "Cannot use empty reach stacker";
                        }
                        return true;
                        
                    })
                )
                ->on('writeCreate', function ($editor, $id, $values) {
                    $container = $values['container_id'];
                    $user_id = $_SESSION['id'];
                    $query = new MyTransactionQuery();
                    $query->query("select id from container where gate_status='GATED IN' and number=?");
                    $query->bind = array('s',&$container);
                    $query->run();
                    $result = $query->fetch_assoc();

                    $query->query("update gate_record set examination_status=0 where container_id=?");
                    $query->bind = array('i',&$result['id']);
                    $query->run();

                    $position = $values['stack'].$values['bay'].$values['row'].$values['tier'];

                    $query->query("select container_id from forty_feet_log where stack=? and bay=?");
                    $query->bind = array('si',&$values['stack'],&$values['bay']);
                    $query->run();
                    $forty_result = $query->fetch_assoc();

                    $container_length = Container::getContainerSize($query,$result['id']);
                    if (($container_length[0][0] >= 40) && (!$forty_result['container_id'])) {
                        $forty_bay = $values['bay']+2;
                        $query->query("insert into forty_feet_log(yard_id,container_id,stack,bay)values(?,?,?,?)");
                        $query->bind = array('iisi',&$result['id'],&$id,&$values['stack'],&$forty_bay);
                        $query->run();
                    }

                    $query->query("insert into yard_log_history(container_id,stack,position,yard_activity,user_id)values(?,?,?,'ASSIGN',?)");
                    $query->bind = array('issi',&$result['id'],&$values['stack'],&$position,&$user_id);
                    $query->run();
                    $query->commit();
                })
                ->on('preCreate', function ($editor, $values, $system_object = 'yard-planning') {
                    ACl::verifyCreate($system_object);
                })
                ->on('preGet', function ($editor, $id, $system_object = 'yard-planning') {
                    ACl::verifyRead($system_object);
                })
                ->on('preEdit', function ($editor, $id, $values, $system_object = 'yard-planning') {
                    return false;
                })
                ->on('preRemove', function ($editor, $id, $values, $system_object = 'yard-planning') {
                    return false;
                })
                ->process($_POST)
                ->json();
                $query->commit();
        }

        public function check_stack(){
            $id = $this->request->param('data');
            $query=new MyQuery();
            $query->query("select id from yard_log where container_id = ?");
            $query->bind =  array('i', &$id);
            $run=$query->run();
            $count = $run->num_rows();
            new Respond($count > 0 ? 281 : 282);
        }

        function get_stack_info(){
            $container_id = $this->request->param('cid');

            $result = array();
            $query = new MyTransactionQuery();
            $query->query("select number from container where id=?");
            $query->bind = array('i',&$container_id);
            $container = $query->run();
            $container_number = $container->fetch_assoc();
            $result['cnum'] = $container_number['number']; 

            $query->query("select * from yard_log where container_id=?");
            $query->bind = array('i',&$container_id);
            $yard_query = $query->run();

            $stack_info = $yard_query->fetch_assoc();
            $result['stk'] = $stack_info['stack'];
            $result['row'] = $stack_info['row'];
            $result['reft'] = $stack_info['reefer_status'];
            $result['bay'] = $stack_info['bay'];
            $result['tier'] = $stack_info['tier'];
            $result['eqip'] = $stack_info['equipment_no'];

            $query->commit();
            new Respond(283, $result);
        }

        function update_stack(){
            $this->container_id = $this->request->param('cid');
            $this->stack = $this->request->param('stk');
            $this->row = $this->request->param('row');
            $this->bay = $this->request->param('bay');
            $this->tier = $this->request->param('tier');
            $reefer_status = $this->request->param('refs');
            $this->equipment_number = $this->request->param('eqip');
            $yard_stack = $this->request->param('isyd');
            $user_id = $_SESSION['id'];

            $error_array = array();
            if ($this->bay == "") {
                $error_array['berr'] = "empty";
            }
            if ($this->equipment_number == "") {
                $error_array['eqer'] = "empty";
            }

            if (!empty($error_array)) {
                new Respond(170,$error_array);
            }

            $query = new MyTransactionQuery();
            $this->check_reach_stacker($query);
            $this->check_bay($query);

            $query->query("update yard_log set stack=?,bay=?,row=?,tier=?,reefer_status=?,equipment_no =?,positioned=0,approved=0,assigned_by=?,yard_activity='ASSIGN' where container_id=?");
            $query->bind = array('sisiisii',&$this->stack,&$this->bay,&$this->row,&$this->tier,&$reefer_status,&$this->equipment_number,&$user_id,&$this->container_id);
            $query->run();

            $position = $this->stack.$this->bay.$this->row.$this->tier;
            $query->query("insert into yard_log_history(container_id,stack,position,yard_activity,user_id)values(?,?,?,'CHANGE POSITION',?)");
            $query->bind = array('issi',&$this->container_id,&$this->stack,&$position,&$user_id);
            $query->run();
            $query->commit();
            new Respond(298);
        }

        function get_container(){
            $container_id = $this->request->param('cid');
            $result = array();
            $query = new MyQuery();
            $query->query("select number from container where id=?");
            $query->bind = array('i',&$container_id);
            $query->run();
            $container = $query->fetch_assoc();
            $result['cnum'] = $container['number'];
            new Respond(283, $result);
        }

        function check_reach_stacker($query){
            $query->query("select id,type from reach_stacker where equipment_no=?");
            $query->bind = array('s',&$this->equipment_number);
            $query->run();
            $result = $query->fetch_assoc();
            $this->trade_type = $result['type'];
            if (!$result['id']) {
                new Respond(161);
            }
         
        }

        function check_bay($query){
            $query->query("select id from stack where name=?");
            $query->bind = array('s',&$this->stack);
            $query->run();
            $stack_result1 = $query->fetch_assoc();

            if (!$stack_result1['id']) {
                $query->commit();
                new Respond(176,array('stk'=>$this->stack));
            }

            $query->query("select id from yard_log where stack=? and bay=? and row=? and tier=?");
            $query->bind = array('sisi',&$this->stack,&$this->bay,&$this->row,&$this->tier);
            $query->run();
            $stack_result = $query->fetch_assoc();

            if($stack_result['id']){
                $query->commit();
                new Respond(177);
            }

            $query->query("select container_depot_info.goods,trade_type.name as trade from container left join trade_type on trade_type.code = container.trade_type_code left join container_depot_info on container.id = container_depot_info.container_id where container.id=? and container.gate_status='GATED IN'");
            $query->bind = array('s',&$this->container_id);
            $query->run();
            $result = $query->fetch_assoc();
            $goods = substr($result['goods'],0,2);

            if (($result['trade'] =="EMPTY")&&($this->trade_type != "EMPTY")) {
                $query->query("select equipment_no from reach_stacker where type='EMPTY'");
                $query->run();
                $result3 = $query->fetch_assoc();
                new Respond(163,$result3);
            }

            if (($goods == "DG") && ($this->stack != "H" || $this->stack != "I")) {
                $query->commit();
                new Respond(154);
            }
            elseif (($goods != "DG") && ($this->stack == "H" || $this->stack == "I")) {
                $query->commit();
                new Respond(155);
            }
            $query->query("select count(id) as qty from yard_log where stack=? and bay=? and row=?");
            $query->bind = array('sis',&$this->stack,&$this->bay,&$this->row);
            $query->run();
            $tier_query = $query->fetch_assoc();
            $tier_quantity = $tier_query['qty'];
         
            if ($tier_quantity >= 3) {
                $query->commit();
                new Respond(156);
            }
            else{
                $query->query("select tier,container_id from yard_log where stack=? and bay=? and row=?");
                $query->bind = array('sis',&$this->stack,&$this->bay,&$this->row);
                $query->run();
                $tier_query = $query->fetch_all();
                $this->container_size = $tier_query[0][1];

                $tier_row = array();

              

                if(empty($tier_query)){
                    if($this->tier != 1){
                        $tier_row['tier1'] = 1;
                        $query->commit();
                        new Respond(157,$tier_row);
                    }
                }
                elseif (count($tier_query) == 1) {
                    $this->validate_stacks($query);
                    if($this->tier != 2){
                        $tier_row['tier2'] = $tier_query[0][0];
                        new Respond(170,$tier_row);
                    }
                   
                }
                elseif (count($tier_query) ==2) {
                    $this->validate_stacks($query);
                }
                
            }
        }

        public function validate_stacks($query){
            $container_first_tier = Container::getTradeType($query,$this->container_size);
            $container_tier = Container::getTradeType($query,$this->container_id);
            if ($container_first_tier != $container_tier) { 
                $tier_row['ftyp'] = $container_first_tier;
                $tier_row['ttyp'] = $container_tier;
                $query->commit();
                new Respond(158,$tier_row);
            }

            $container_first_size = Container::getContainerSize($query,$this->container_size);
            $container_size = Container::getContainerSize($query,$this->container_id);
            $container_first_height = $container_first_size[0][1];
            $container_height = $container_size[0][1];
         
            if ($container_first_size[0][0] != $container_size[0][0]) {
                $tier_row['fsiz'] = $container_first_size[0][0];
                $tier_row['size'] = $container_size[0][0];
                $query->commit();
                new Respond(159,$tier_row);
            }
            elseif ($container_first_height != $container_height) {
                $tier_row['fhgt'] = $container_first_height;
                $tier_row['hgt'] = $container_height;
                $query->commit();
                new Respond(160,$tier_row);                   
             }     
        }

        function get_gatein_date($query,$container_id){
            $query->query("select date from container_log where container_id=?");
            $query->bind = array('i', &$container_id);
            $query->run();
            $result = $query->fetch_assoc();
            return $result['date'];
        }

        function approve_stack(){
            $id = $this->request->param('id');
            $approve_by = $_SESSION['id'];
    
            $query = new MyTransactionQuery();
            $query->query("update yard_log set yard_activity='IN STACK',approved=1,approved_by=? where id=?");
            $query->bind = array('ii',&$approve_by,&$id);
            $query->run();

            $query->query("select container_id,stack,concat(stack,bay,row,tier) as position from yard_log where id=?");
            $query->bind = array('i',&$id);
            $query->run();
            $result = $query->fetch_assoc();
    
            $query->query("insert into yard_log_history(container_id,stack,position,yard_activity,user_id)values(?,?,?,'APPROVE',?)");
            $query->bind = array('issi',&$result['container_id'],&$result['stack'],&$result['position'],&$approve_by);
            $query->run();
            $query->commit();

            new Respond(262);
        }

        function remove_stack_container(){
            $id = $this->request->param('id');
            $assigned_by = $_SESSION['id'];

            $query = new MyTransactionQuery();
            $query->query("select stack,bay,row,tier from yard_log where id=?");
            $query->bind = array('i',&$id);
            $query->run();
            $tier_result = $query->fetch_assoc();

            $query->query("select count(tier) as tier_count from yard_log where stack=? and bay=? and row=?");
            $query->bind = array('sis',&$tier_result['stack'],&$tier_result['bay'],&$tier_result['row']);
            $query->run();
            $count_query = $query->fetch_assoc();

            if ($tier_result['tier'] != $count_query['tier_count']) {
                $query->commit();
                new Respond(129, array('stk'=>$tier_result['stack'],'bay'=>$tier_result['bay'],'row'=>$tier_result['row'],'tier'=>$count_query['tier_count']));
            }


            $query->query("update yard_log set approved=0,positioned=0,yard_activity='REMOVE',assigned_by=? where id=?");
            $query->bind = array('ii',&$assigned_by,&$id);
            $query->run();

            $query->query("select container_id,stack,concat(stack,bay,row,tier) as position from yard_log where id=?");
            $query->bind = array('i',&$id);
            $query->run();
            $result = $query->fetch_assoc();
    
            $query->query("insert into yard_log_history(container_id,stack,position,yard_activity,user_id)values(?,?,?,'MOVE OUT',?)");
            $query->bind = array('issi',&$result['container_id'],&$result['stack'],&$result['position'],&$assigned_by);
            $query->run();
            $query->commit();
            new Respond(263);
        }

        function approve_removal(){
            $id = $this->request->param('id');
            $remove_by = $_SESSION['id'];

            $query = new MyTransactionQuery();
            $query->query("select container_id,stack,concat(stack,bay,row,tier) as position from yard_log where id=?");
            $query->bind = array('i',&$id);
            $query->run();
            $result = $query->fetch_assoc();

            $query->query("insert into holding_area(container_id,user_id)values(?,?)");
            $query->bind = array('ii',&$result['container_id'],&$remove_by);
            $query->run();
    
            $query->query("insert into yard_log_history(container_id,stack,position,yard_activity,user_id)values(?,?,?,'APPROVE REMOVAL',?)");
            $query->bind = array('issi',&$result['container_id'],&$result['stack'],&$result['position'],&$remove_by);
            $query->run();

            $query->query("delete from yard_log where id=?");
            $query->bind = array('i',&$id);
            $query->run();
            $query->commit();
            new Respond(265);
        }

        function validate_position(){
            $id = $this->request->param('id');

            $query = new MyQuery();
            $query->query("select positioned,yard_activity from yard_log where id=?");
            $query->bind = array('i',&$id);
            $query->run();
            $result = $query->fetch_assoc();
            if ($result['positioned'] != 1 && $result['yard_activity'] == 'ASSIGN') {
                new Respond(127);
            }
            elseif($result['positioned'] != 1 && $result['yard_activity'] == 'EXAMINATION') {
                new Respond(128);
            }
        }

        function move_examination(){
            $id = $this->request->param('id');
            $user = $_SESSION['id'];
            $query = new MyTransactionQuery();
            $query->query("select container.trade_type_code as trade_type,container.id as container_id from gate_record left join container on container.id = gate_record.container_id where gate_record.id=?");
            $query->bind = array('i',&$id);
            $query->run();
            $result = $query->fetch_assoc();

            if ($result['trade_type'] != 11) {
                $query->commit();
                new Respond(180);
            }

            $query->query("select id from yard_log where container_id=?");
            $query->bind = array('i',&$result['container_id']);
            $query->run();
            $yard_result = $query->fetch_assoc();

            if(!$yard_result['id']){
                $query->query("insert into yard_log(container_id,assigned_by,yard_activity)values(?,?,'EXAMINATION')");
                $query->bind = array('ii',&$result['container_id'],&$user);
                $query->run();
            }
            else{
                $query->query("update yard_log set yard_activity='EXAMINATION',assigned_by=?,positioned=0 where id=?");
                $query->bind = array('ii',&$id,&$user);
                $query->run();
            }
            $query->query("insert into yard_log_history(container_id,yard_activity,user_id)values(?,'ASSIGN EXAMINATION',?)");
            $query->bind = array('ii',&$result['container_id'],&$user);
            $query->run();
            $query->commit();
            new Respond(270);
        }

        function approve_examination(){
            $id = $this->request->param('id');
            $yard_id = $this->request->param('yid');
            $move_by = $_SESSION['id'];

            $query = new MyTransactionQuery();
            $query->query("update gate_record set examination_status=1,examination_by=? where id=?");
            $query->bind = array('ii',&$move_by,&$id);
            $query->run();

            $query->query("select container.id as container_id from gate_record left join container on container.id = gate_record.container_id where gate_record.id=?");
            $query->bind = array('i',&$id);
            $query->run();
            $result = $query->fetch_assoc();

            $query->query("insert into yard_log_history(container_id,yard_activity,user_id)values(?,'APPROVE EXAMINATION',?)");
            $query->bind = array('ii',&$result['container_id'],&$move_by);
            $query->run();

            $query->query("delete from yard_log where id=?");
            $query->bind = array('i',&$yard_id);
            $query->run();
            $query->commit();
            new Respond(268);
        }

        function validate_move(){
            $id = $this->request->param('id');
            $query = new MyTransactionQuery();
            $query->query("select container_id from yard_log where id=?");
            $query->bind = array('i',&$id);
            $query->run();
            $result = $query->fetch_assoc();
            $trade_type = Container::getTradeType($query,$result['container_id']);
            $this->check_paid_invoice($query,$result['container_id']);
            if ($trade_type == "IMPORT") {
                $let_pass = Gate::checkLetpass($result['container_id']);
                if ($let_pass == "") {
                    $query->commit();
                    new Respond(183);
                }
                elseif(($this->check_truck_gatein($query,$let_pass)) != "GATED IN"){
                    $query->commit();
                    new Respond(184);
                }
                else{
                    new Respond(282);
                }
            }
            else{
                $query->commit();
                new Respond(282);
            }
           
           
        }

        function approve_move(){
            $id = $this->request->param('id');
            $gate_record = $this->request->param('gid');

            $approve_by = $_SESSION['id'];

            $query = new MyTransactionQuery();
            $query->query("select container_id,stack,concat(stack,bay,row,tier) as position,date from yard_log where id=?");
            $query->bind = array('i',&$id);
            $query->run();
            $result = $query->fetch_assoc();
    
            $query->query("insert into yard_log_history(container_id,stack,position,yard_activity,user_id)values(?,?,?,'APPROVE REMOVAL',?)");
            $query->bind = array('issi',&$result['container_id'],&$result['stack'],&$result['position'],&$approve_by);
            $query->run();

            $query->query("update gate_record set yard_status=1 where id=?");
            $query->bind = array('i',&$gate_record);
            $query->run();

            $trade_type = Container::getTradeType($query,$result['container_id']);

            if ($trade_type == "IMPORT") {
                $query->query("select letpass_id from letpass_container  where container_id=?");
                $query->bind = array('i',&$result['container_id']);
                $query->run();
                $result2 = $query->fetch_assoc();

                $query->query("select date from gate_truck_record where letpass_id=?");
                $query->bind = array('i',&$result2['letpass_id']);
                $query->run();
                $result3 = $query->fetch_assoc();
    
                $current_date =date("Y-m-d H:i:s");
                $time_spent = YardTime::getTimeSpent($result3['date'],$current_date);

                $query->query("update gate_truck_record set container_id=?,offload_time=? where letpass_id=?");
                $query->bind = array('isi',&$result['container_id'],&$time_spent,&$result2['letpass_id']);
                $query->run();

                $query->query("update truck_log set status=1 where container_id=?");
                $query->bind = array('i',&$result['container_id']);
                $query->run();
            }
          
            $query->query("update truck_log set load_status=1 where yard_id=?");
            $query->bind = array('i',&$id);
            $query->run();
            

            $query->query("delete from yard_log where id=?");
            $query->bind = array('i',&$id);
            $query->run();
            $query->commit();
            new Respond(267);
        }

        function check_paid_invoice($query,$container_id){
            $invoice = Gate::checkPaidQuery("",$container_id);
            $supplementary_invoice = Gate::checkPaidQuery("supplementary_",$container_id);
            if ((($invoice == "PAID") || ($invoice == "DEFERRED")) || (($supplementary_invoice == "PAID") || ($supplementary_invoice == "DEFERRED"))) {
              return true;
            }
            else{
                $query->commit();
                new Respond(180);
            }
        }

        function vehicles(){
            $container_id = $this->request->param('cid');
            $number_result="";
            $query = new MyTransactionQuery();
            $query->query("select trade_type_code from container where id=?");
            $query->bind = array('i',&$container_id);
            $query->run();
            $result = $query->fetch_assoc();
            if ($result['trade_type_code']=='11') {
                $query->query("select vehicle_number as lnse from gate_truck_record where container_id=?");
                $query->bind = array('i',&$container_id);
                $query->run();
                $number_result = $query->fetch_all(MYSQLI_ASSOC);
            }
            else{
                $query->query("select number as lnse from vehicle");
                $query->run();
                $number_result = $query->fetch_all(MYSQLI_ASSOC);
            }
            $query->commit();
            echo json_encode($number_result);
        }
    
        function move_ontruck(){
            $id = $this->request->param('id');
            $vehicle = $this->request->param('vchl');
            if ($vehicle == "") {
                new Respond(188);
            }
            $user = $_SESSION['id'];
            $query = new MyTransactionQuery();
            $query->query("select container_id,stack,concat(stack,bay,row,tier) as position from yard_log where id=?");
            $query->bind = array('i',&$id);
            $query->run();
            $result = $query->fetch_assoc();

            $query->query("select id from truck_log where vehicle_number=? and load_status=0");
            $query->bind = array('s',&$vehicle);
            $query->run();
            $result1 = $query->fetch_assoc();
            if ($result1['id']) {
                new Respond(189);
            }

            $query->query("update yard_log set yard_activity='ON TRUCK',assigned_by=?,positioned=0,approved=0,approved_by=0,position_by=0 where id=?");
            $query->bind = array('ii',&$user,&$id);
            $query->run();
            

            $query->query("insert into truck_log(yard_id,vehicle_number,container_id,user_id)values(?,?,?,?)");
            $query->bind = array('isii',&$id,&$vehicle,&$result['container_id'],&$user);
            $query->run();
    
            $query->query("insert into yard_log_history(container_id,stack,position,yard_activity,user_id)values(?,?,?,'APPROVE REMOVAL',?)");
            $query->bind = array('issi',&$result['container_id'],&$result['stack'],&$result['position'],&$user);
            $query->run();
            $query->commit();
            new Respond(283);
        }

        function check_truck_gatein($query,$letpass){
            $query->query("select gate_status from gate_truck_record where letpass_id=?");
            $query->bind = array('i',&$letpass);
            $query->run();
            $result = $query->fetch_assoc();
            return $result['gate_status'];
        }

        function validate_forty_bay($query,$bay,$container_id){
            $bay_check = new Stack();
            $forty_bay = $bay + 2;
            $container_first_id = Container::getContainerID($query,$container_id);
            $container_second_id = $bay_check->getFortyContainerId($query,$bay);
            
            $container_first_length = Container::getContainerSize($query,$container_first_id);
            $container_second_length = Container::getContainerSize($query,$container_second_id);

            if (!($bay_check->getYardBayId($query,$bay)) && !($bay_check->getYardBayId($query,$forty_bay))) {
                return true;
            }
            elseif (($container_first_length[0][0] >= 40) && ($container_second_length[0][0] >= 40)) {
                return true;
            }
            else{
                return "Cannot use this bay";
            }
        }
    }

?>