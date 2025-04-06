<?php
namespace Api;
session_start();

use Lib\ACL,
    Lib\Gate,
    Lib\MyQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Options,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\MyTransactionQuery,
    Lib\Respond,
    Lib\StorageCharges,
    Lib\YardTime;

    date_default_timezone_set('UTC');

class GateOut{
    private $request;
    public $customer_id;
    public $invoice_number;

    public function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db = new Bootstrap();
        $db = $db->database();

        $query = new MyTransactionQuery();

        Editor::inst($db, 'gate_record')
            ->fields(
                Field::inst('gate_record.container_id')
                    ->setFormatter(function ($val) use ($query){
                        $query->query("select id from container where status = 0 and gate_status = 'GATED IN' and number = ?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) use ($query) {
                        $query->query("select number from container where id  = ?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        return $query->fetch_all()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ($val, $data, $field, $host) use ($query){
                        $query->query("select id, status, gate_status, trade_type_code  from container where gate_status = 'GATED IN' and number = ?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        $container_result = $query->fetch_assoc();
                        $status = $container_result['status'];
                        $gate_status = $container_result['gate_status'];
                        $container_id = $container_result['id'];
                        $trade_type = $container_result['trade_type_code'];

                        if ($status == 1) {
                            return 'Container cannot be gated out, it is flagged';
                        }

                        $query->query("select id, cond from gate_record where type = 'GATE OUT' and container_id = ? ");
                        $query->bind = array('i', &$container_id);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $gate_record = $result['id'];

                        if ($host['action'] == 'create') {
                            if ($container_id == '') {
                                return 'Container has not be preloaded';
                            } elseif ($gate_status != 'GATED IN' || $gate_status == 'GATED OUT') {
                                return 'Container has only been preloaded';
                            } elseif ($gate_record) {
                                return "Container already inserted";
                            }

                            $current_date = date('Y-m-d');

                            $query->query("SELECT invoice.id AS invoice_id, invoice.status, invoice.deferral_date FROM container 
                                  INNER JOIN invoice_container ON invoice_container.container_id = container.id INNER JOIN invoice 
                                  ON invoice.id = invoice_container.invoice_id WHERE container.id = ?");
                            $query->bind = array('i', &$container_id);
                            $query->run();
                            $result2 = $query->fetch_assoc();
                            if ($result2['invoice_id'] == '') {
                                return "Container has not been invoiced $container_id";
                            } elseif ($result2['status'] == 'UNPAID' && $result2['status'] != 'DEFERRED') {
                                return "Container has pending charges";
                            } elseif ($result2['status'] == 'DEFERRED') {
                                if ($current_date > $result2['deferral_date']) {
                                    return "Container has exceeded it's defer date";
                                }
                            }
                        }

                        if($trade_type != 21 && $trade_type != 70) {
                            $query->query("SELECT letpass_driver.license, letpass_driver.name from letpass_driver 
                            INNER JOIN letpass on letpass_driver.letpass_id = letpass.id
                            INNER JOIN invoice on invoice.id = letpass.invoice_id
                            INNER JOIN invoice_container on invoice_container.invoice_id = invoice.id
                            INNER JOIN container on invoice_container.container_id = container.id
                            INNER JOIN letpass_container on letpass_container.container_id = container.id
                            WHERE container.gate_status = 'GATED IN' and container.id = ? and letpass.status = 0");
                            $query->bind = array('i', &$container_id);
                            $query->run();
                            if($query->num_rows() == 0){
                                return "No Letpass Driver or Vehicle is available for this container";
                            }
                        }


                        if ($host['action'] == 'edit') {
                            $condition = $data['gate_record']['cond'];

                            if ($gate_status == 'GATED OUT') {
                                return 'Container cannot be edited';
                            }
                            if ($condition == 'SOUND') {
                                $query->query("delete from gate_record_container_condition where gate_record = ?");
                                $query->bind = array('i', &$gate_record);
                                $query->run();
                            }
                        }

                        return true;
                    }),
                Field::inst('gate_record.type'),
                Field::inst('trade_type.name'),
                Field::inst('gate_record.gate_id')
                    ->options(Options::inst()
                        ->table('depot_gate')
                        ->value('id')
                        ->label('name')
                    ),
                Field::inst('gate_record.depot_id')
                    ->options(Options::inst()
                        ->table('depot')
                        ->value('id')
                        ->label('name')
                        ->order('id ASC')
                    )
                    ->validator(function($val, $data, $field, $host) use ($query){
                        $container_number = $data['gate_record']['container_id'];
                        $query->query("select trade_type_code from container where number=?");
                        $query->bind = array('s',&$container_number);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $trade_type = $result['trade_type_code'];

                        $trade_code = '';
                        switch($val){
                            case "1":
                                $trade_code = "11";
                                break;
                            case "2":
                                $trade_code = "70";
                                break;
                            case "3":
                                $trade_code = "21";
                                break;
                            default:
                                $trade_code = "11";
                        }
                        if ($trade_type != $trade_code) {
                            return "Wrong container depot";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('gate_record.vehicle_id')
                    ->setFormatter(function ($val, $data) use ($query){
                        $container = $data ["gate_record"]['container_id'];
                    
                        $query->query("select trade_type_code  from container  where number  = ? and gate_status='GATED IN'");
                        $query->bind = array('s', &$container);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $trade_type = $result['trade_type_code'];

                        if($trade_type != 11){
                            $vehicle = new Gate();
                            $vehicle_id = $vehicle->checkVehicle($val);
                            return $vehicle_id;

                        }
                        else {
                            $query->query("select id from letpass_driver where license = ?");
                            $query->bind = array('s', &$val);
                            $query->run();
                            return $query->fetch_num()[0] ?? '';
                        }
                    })
                    ->getFormatter(function ($val, $data) use ($query){
                        $container = $data['gate_record.container_id'];
                        $query->query("select trade_type_code  from container  where id  = ? and (gate_status='GATED IN' or gate_status='GATED OUT')");
                        $query->bind = array('i', &$container);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $trade_type = $result['trade_type_code'];

                        if($trade_type == '21' || $trade_type == '70'){
                            $query->query("select number from vehicle where id = ?");
                            $query->bind = array('i', &$val);
                            $query->run();
                            return $query->fetch_num()[0] ?? '';
                        }
                        else {
                            $query->query("select license from letpass_driver where id = ?");
                            $query->bind = array('i', &$val);
                            $query->run();
                            return $query->fetch_num()[0] ?? '';
                        }


                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ($val, $data, $field, $host) use ($query) {
                        $container = $data['gate_record']['container_id'];
                        $query->query("select trade_type_code  from container  where number  = ? and gate_status='GATED IN'");
                        $query->bind = array('s', &$container);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $trade_type = $result['trade_type_code'];

                        if($trade_type == 21 || $trade_type == 70){
                            $vehicle = new Gate();
                            $check_vehicle = $vehicle->checkVehicle($val);
                            if ($check_vehicle){
                                return true;
                            }
                            else{
                                return 'Vehicle Not Registered';
                            }
                        }
                        else {
                            $container_num = $data['gate_record']['container_id'];
                            $query->query("select id from container where number=? and gate_status='GATED IN'");
                            $query->bind = array('s',&$container_num);
                            $query->run();
                            $result = $query->fetch_assoc();

                            $query->query("select id,status from truck_log where container_id=?");
                            $query->bind = array('i',&$result['id']);
                            $query->run();
                            $result1 = $query->fetch_assoc();
                            if (!$result1['id']) {
                                return "Vehicle has not be assigned for container";
                            }
                            elseif ($result1['status'] != 1) {
                                return "Container assign to truck has not been approved";
                            }
                            else{
                                return true;
                            }
                        }
                    }),
                Field::inst('gate_record.driver_id')
                    ->setFormatter(function ($val,$data) use ($query){
                        $container = $data["gate_record"]['container_id'];

                        $query->query("select trade_type_code  from container  where number  = ? and gate_status='GATED IN' ");
                        $query->bind = array('s', &$container);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $trade_type = $result['trade_type_code'];

                        if($trade_type == 21 || $trade_type == 70){
                            $val = html_entity_decode($val);
                            $query->query("select id from vehicle_driver where name =?");
                            $query->bind = array('s',&$val);
                            $query->run();
                            if(!$query->num_rows()){
                                $val = htmlspecialchars($val);
                                $query->query("select id from vehicle_driver where name =?");
                                $query->bind = array('s',&$val);
                                $query->run();
                            }
                            return $query->fetch_num()[0] ?? '';
                        }
                        else {
                            $query->query("select id from letpass_driver where name = ?");
                            $query->bind = array('s', &$val);
                            $query->run();
                            return $query->fetch_num()[0] ?? '';
                        }
                    })
                    ->getFormatter(function ($val, $data) use ($query){
                        $container = $data['gate_record.container_id'];
                        $query->query("select trade_type_code  from container  where id = ? and (gate_status='GATED IN' or gate_status='GATED OUT')");
                        $query->bind = array('i', &$container);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $trade_type = $result['trade_type_code'];


                        if($trade_type == 21 || $trade_type == 70){
                            $query->query("select name from vehicle_driver where id =?");
                            $query->bind = array('i',&$val);
                            $query->run();
                            return $query->fetch_num()[0] ?? '';
                        }
                        else {
                            $query->query("select name from letpass_driver where id = ?");
                            $query->bind = array('i', &$val);
                            $query->run();
                            return $query->fetch_num()[0] ?? '';
                        }
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ($val, $data, $field, $host) use ($query){
                        $container = $data['gate_record']['container_id'];
                        $query->query("select trade_type_code  from container  where number  = ? and gate_status='GATED IN'");
                        $query->bind = array('s', &$container);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $trade_type = $result['trade_type_code'];

                        if($trade_type == 21 || $trade_type == 70){
                            $val = html_entity_decode($val);
                            $query->query("select id from vehicle_driver where name  =?");
                            $query->bind = array('s',&$val);
                            $query->run();
                            if(!$query->num_rows()){
                                $val = htmlspecialchars($val);
                                $query->query("select id from vehicle_driver where name  =?");
                                $query->bind = array('s',&$val);
                                $query->run();
                            }
                            return ($query->fetch_num()[0]) ? true: 'Vehicle Driver Not Registered';
                        }
                        else {
                            $container_num = $data['gate_record']['container_id'];
                            $query->query("select letpass_driver.id from letpass_driver 
                            INNER JOIN letpass on letpass_driver.letpass_id = letpass.id
                            INNER JOIN invoice on invoice.id = letpass.invoice_id
                            INNER JOIN invoice_container on invoice_container.invoice_id = invoice.id
                            INNER JOIN container on invoice_container.container_id = container.id
                            INNER JOIN letpass_container on letpass_container.container_id = container.id
                            WHERE container.gate_status = 'GATED IN' and container.number = ? and letpass.status = 0 and name = ?");
                            $query->bind = array('ss', &$container_num, &$val);
                            $query->run();
                            if ($query->fetch_num()[0] == '') {
                                return "Driver does not exist";
                            } else {
                                return true;
                            }
                        }
                    }),
                Field::inst('gate_record.trucking_company_id')
                    ->setFormatter(function($val) use ($query){
                        $val = html_entity_decode($val);
                        $query->query("select id from trucking_company where name =?");
                        $query->bind = array('s',&$val);
                        $query->run();
                        if(!$query->num_rows()){
                            $val = htmlspecialchars($val);
                            $query->query("select id from trucking_company where name =?");
                            $query->bind = array('s',&$val);
                            $query->run();
                        }
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function($val) use ($query) {
                        $query->query("select name from trucking_company where id =?");
                        $query->bind = array('i',&$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) use ($query){
                        $val = html_entity_decode($val);
                        $query->query("select id from trucking_company where name =?");
                        $query->bind = array('s',&$val);
                        $query->run();
                        if(!$query->num_rows()){
                            $val = htmlspecialchars($val);
                            $query->query("select id from trucking_company where name =?");
                            $query->bind = array('s',&$val);
                            $query->run();
                        }
                        return ($query->fetch_num()[0]) ? true: 'Trucking Company Not Registered';
                    }),
                Field::inst('gate_record.special_seal as spsl'),
                Field::inst('gate_record.consignee as cons'),
                Field::inst('gate_record.external_reference as exref'),
                Field::inst('booking.act')
                    ->set(false),
                Field::inst('gate_record.cond'),
                Field::inst('gate_record.note as note'),
                Field::inst('gate_record.status as stat'),
                Field::inst('gate_record.date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(function ($val, $data, $field, $host) {
                        $date = new \DateTime(substr($val, 0, 10));
                        $today = new \DateTime(date("Y-m-d"));
                        if ($date > $today) {
                            return 'Postdating Not Allowed';
                        } else {
                            return true;
                        }
                    }),
                Field::inst('gate_record.pdate as pdate'),
                Field::inst('gate_record.user_id')
                    ->setFormatter(function ($val) {
                        return isset($_SESSION['id']) ? $_SESSION['id'] : 1;
                    })
                    ->getFormatter(function ($val) use ($query){
                        $query->query("SELECT first_name, last_name FROM user WHERE id = ?");
                        $query->bind = array('i', &$val);
                        $res = $query->run();
                        $result = $res->fetch_assoc();
                        $first_name = $result['first_name'];
                        $last_name = $result['last_name'];
                        $full_name = "$first_name" . " " . "$last_name";
                        return $full_name ?? '';
                    }),
                Field::inst('gate_record.deleted as del'),
                Field::inst('container_isotype_code.code as isoc'),
                Field::inst('container.number as ctnum'),
                Field::inst('container.icl_seal_number_1 as icsl1'),
                Field::inst('container.icl_seal_number_2 as icsl2'),
                Field::inst('container.seal_number_1 as seal1'),
                Field::inst('container.seal_number_2 as seal2'),
                Field::inst('container.content_of_goods as good'),
                Field::inst('depot.name as dpname'),
                Field::inst('depot_gate.name as dpgt'),
                Field::inst('voyage.reference as vyref'),
                Field::inst('container.id as ctnr'),
                Field::inst('gate_record.id as id')
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'gateout-records') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'gateout-records') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'gateout-records') {
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'gateout-records') {
                ACl::verifyDelete($system_object);

                $query = new MyTransactionQuery();
                $query->query("select id, container_id from gate_record where id=?");
                $query->bind = array('i',&$id);
                $query->run();
                $container_result = $query->fetch_assoc();
                $container_id = $container_result['container_id'];

                $query->query("select trade_type_code,gate_status from container where id = ?");
                $query->bind = array('s', &$container_id);
                $res = $query->run();
                $result = $res->fetch_assoc();
                $gate_status = $result['gate_status'];
                $trade_type = $result['trade_type_code'];

                if ($gate_status == "GATED OUT"){
                    $query->commit();
                    return false;
                }
                elseif ($trade_type == '13') {
                    $query->query("update container set trade_type_code = '11' where id= ?");
                    $query->bind = array('i',&$container_id);
                    $query->run();
                }
                else{
                    $gate_record = $container_result['id'];

                    if ($gate_record) {
                        $query->query("delete from gate_record_container_condition where gate_record = ?");
                        $query->bind = array('i', &$id);
                        $query->run();
                        $query->commit();
                        return true;
                    } else {
                        $query->commit();
                        return false;
                    }
                }

            })
            ->leftJoin('container', 'gate_record.container_id', '=', 'container.id')
            ->leftJoin('depot', 'gate_record.depot_id', '=', 'depot.id')
            ->leftJoin('depot_gate', 'gate_record.gate_id', '=', 'depot_gate.id')
            ->leftJoin('letpass_driver', 'gate_record.vehicle_id', '=', 'letpass_driver.id')
            ->leftJoin('trucking_company', 'gate_record.trucking_company_id', '=', 'trucking_company.id')
            ->leftJoin('voyage', 'container.voyage', '=', 'voyage.id')
            ->leftJoin('container_isotype_code', 'container_isotype_code.id', '=', 'container.iso_type_code')
            ->leftJoin('trade_type', 'trade_type.code', '=', 'container.trade_type_code')
            ->leftJoin('booking', 'booking.booking_number', '=', 'container.book_number')
            ->where('gate_record.type', 'GATE OUT')
            ->process($_POST)
            ->json();
        $query->commit();
    }


    public function gate_container_out(){
        $id = $this->request->param('id');
        $storage = new StorageCharges();

        $query = new MyTransactionQuery();
        $query->query("select gate_record.date as export_date,container.status,container.gate_status,container.full_status, gate_record.cond, gate_record.container_id, container.trade_type_code from gate_record inner join container on container.id = gate_record.container_id where gate_record.id = ?");
        $query->bind = array('i', &$id);
        $run = $query->run();
        $result = $run->fetch_assoc();
        $condition = $result['cond'];
        $container_id = $result['container_id'];
        $container_status = $result['status'];
        $trade_code = $result['trade_type_code'];
        $export_date = (new \DateTime($result['export_date']))->format('Y-m-d');

        if ($container_status == '1') {
            new Respond( 132);
        }

        $response = array();

        if ($condition == 'NOT SOUND') {
            $query->query("SELECT id FROM gate_record_container_condition WHERE gate_record = ?");
            $query->bind = array('i', &$id);
            $run = $query->run();
            $result = $run->fetch_assoc();
            $condition_id = $result['id'];
            if (!$condition_id) {
                new Respond( 137);
            }
        }
        if ($result['gate_status'] == "GATED OUT"){
            new Respond(138);
        }


        $query->query("select id from container_log where container_id = ? and invoiced = 0");
        $query->bind = array('i', &$container_id);
        $run = $query->run();
        if($run->num_rows()){
            new Respond(135);
        }

        $current_date = date('Y-m-d');

        $query->query("SELECT invoice.currency,voyage.actual_arrival,invoice.trade_type,invoice.customer_id,invoice.number, invoice.status, invoice.deferral_date, invoice.due_date FROM container 
              INNER JOIN invoice_container ON invoice_container.container_id = container.id 
              INNER JOIN invoice ON invoice.id = invoice_container.invoice_id LEFT JOIN voyage ON voyage.id = container.voyage WHERE container.id =? and invoice.status != 'CANCELLED' and invoice.status != 'EXPIRED'");
        $query->bind = array('i', &$container_id);
        $run = $query->run();
        $result = $run->fetch_assoc();
        $this->customer_id = $result['customer_id'];


        $query2 = new MyQuery();
        $query2->query("select * from customer_billing_group 
                inner join customer_billing on customer_billing.billing_group = customer_billing_group.id 
                where customer_billing.customer_id =? and customer_billing_group.trade_type =?");
        $query2->bind = array('ii',&$this->customer_id,&$result['trade_type']);
        $query2->run();
        $customer_billing = $query2->fetch_assoc();
        $storage->billing_group = $customer_billing['id'];
        $storage->extra_days = $customer_billing['extra_free_rent_days'];

        $import_gated_date = (new \DateTime($result['actual_arrival']))->format('Y-m-d');
        $storage->eta_date = $result['trade_type'] == 1 ? $import_gated_date : $export_date;
        $storage->trade_type__id = $result['trade_type'];
        $base_currency = $result['currency'];

        $base_query = new MyQuery();
        $base_query->query("select code from currency where id=?");
        $base_query->bind = array('i',&$base_currency);
        $base_query->run();
        $base_result = $base_query->fetch_assoc();
        $storage->base_currency = $base_result['code'];

        if($result) {
            $date = new \DateTime($result['deferral_date']);
            $defer_date = $date->format('Y-m-d');
            $this->invoice_number = $result['number'];
            if ($result['status'] == 'DEFERRED') {
                if ($current_date > $defer_date) {
                    new Respond(131);
                }
            } else if ($result['status'] == 'UNPAID') {
                new Respond(133, array("num" => $this->invoice_number));
            }
        }
        else {
            new Respond(134);
        }


        $query->query("SELECT supplementary_invoice.due_date,supplementary_invoice.status, supplementary_invoice.deferral_date, supplementary_invoice.number FROM container 
              INNER JOIN supplementary_invoice_container ON supplementary_invoice_container.container_id = container.id 
              INNER JOIN supplementary_invoice ON supplementary_invoice.id = supplementary_invoice_container.invoice_id 
              WHERE supplementary_invoice.status != 'CANCELLED' and supplementary_invoice.status != 'EXPIRED' and container.id = ? ");
        $query->bind = array('i', &$container_id);
        $query->run();
        if ($query->num_rows() > 0){
            $supplementary_result = $query->fetch_all(MYSQLI_ASSOC);

            foreach ($supplementary_result as $deferral_date){
                $date = new \DateTime($deferral_date['deferral_date']);
                $defer_date = $date->format('Y-m-d');

                if ($deferral_date['status'] == 'DEFERRED') {
                    if ($current_date > $defer_date) {
                        new Respond(131);
                    }
                }
                else if ($deferral_date['status'] == 'UNPAID') {
                    new Respond(133, array("num" => $deferral_date['number']));
                }
            }

            $date = new \DateTime($supplementary_result[0]['due_date']);
            $due_date = $date->format('Y-m-d');
            $this->check_storage($due_date,$container_id,$storage);
        }
        else {
            $invoice_paid_to = $result['due_date'];
            $date = new \DateTime($invoice_paid_to);
            $due_date = $date->format('Y-m-d');
            $this->check_storage($due_date,$container_id,$storage);
        }


        $response['gated'] = "GATED OUT";

        if($this->invoice_number != "") {
            $query1 = "UPDATE gate_record SET status = 1 WHERE id = ?";
            $query2 = "UPDATE container INNER JOIN gate_record ON container.id = gate_record.container_id 
                    SET gate_status = 'GATED OUT' WHERE gate_record.id = ? ";
            $movedQuery = "UPDATE container SET gate_status = 'GATED OUT' 
                            WHERE moved_to = $container_id AND gate_status = 'MOVED'";
            $query->query($query1);
            $query->bind = array('i', &$id);
            $query->run();
            $query->query($query2);
            $query->bind = array('i', &$id);
            $query->run();

            $this->recursiveGateStatusUpdate($container_id, $query);
            
            if($trade_code !=21 && $trade_code != 70) {
                $query2 = "SELECT letpass.id from letpass INNER JOIN letpass_container on letpass_container.letpass_id = letpass.id where letpass_container.container_id = ?";
                $query3 = "SELECT COUNT(container.id) as count FROM container 
                inner join letpass_container on container.id = letpass_container.container_id
                INNER JOIN letpass on letpass.id = letpass_container.letpass_id 
                where container.gate_status != 'GATED OUT' and letpass.id = ?";
                $query4 = "UPDATE letpass set letpass.status = 1 where letpass.id = ?";
                $query->query($query2);
                $query->bind = array('i', &$container_id);
                $run = $query->run();
                if ($letpass = $run->fetch_num()[0]) {
                    $query->query($query3);
                    $query->bind = array('i', &$letpass);
                    $count = $query->run();
                    if ($count->fetch_num()[0] === 0) {
                        $query->query($query4);
                        $query->bind = array('i', &$letpass);
                        $query->run();
                    }
                }
            }
        }
        $query->query("select id from yard_log where container_id=?");
        $query->bind = array('i', &$container_id);
        $query->run();
        $result = $query->fetch_assoc();
        if ($result['id']) {
            new Respond(139);
        }
     
        if ($trade_code == 11) {
            $query->query("select date from gate_truck_record where container_id=?");
            $query->bind = array('i', &$container_id);
            $query->run();
            $result = $query->fetch_assoc();
    
            $todays_date =date("Y-m-d H:i:s");
            $ontime_spent = YardTime::getTimeSpent($result['date'],$todays_date);
            $query->query("update gate_truck_record set gate_status='GATED OUT',onload_time=? where container_id=?");
            $query->bind = array('si',&$ontime_spent,&$container_id);
            $query->run();
        }
       

        $query->commit();
        $gate = new Gate();
        $gate->genWaybill($id);
        new Respond( 232, $response);
    }

    public function check_storage($due_date,$container_id,$storage){
        $query = new MyQuery();
        $query->query("select ucl_status from gate_record where container_id=? and type = 'GATE IN'");
        $query->bind = array('i',&$container_id);
        $query->run();
        $ucl_result = $query->fetch_assoc();
        $ucl_status = $ucl_result['ucl_status'];

        if ($ucl_status == 0){
            $current_date = date('Y-m-d');
            if ($current_date > $due_date) {
                $storage->p_date = $current_date;
                $current_charges = $storage->chargeStorage($container_id);
                $storage->p_date = $due_date;
                $previous_charges = $storage->chargeStorage($container_id);
                $storage_charges = $current_charges - $previous_charges;

                if ($storage_charges != 0){
                    new Respond(136, array("num" => $this->invoice_number));
                }
            }
        }
    }

    public function get_let_pass_details(){
        $container = $this->request->param('num');
        $query = new MyTransactionQuery();
        $query->query("select trade_type_code,id from container  where number = ? and gate_status='GATED IN'");
        $query->bind = array('s', &$container);
        $query->run();
        $result = $query->fetch_assoc();
        $trade_type = $result['trade_type_code'];
        $container_id = $result['id'];
        $drivers = array();
        $vehicles = array();
        if($trade_type == 21 || $trade_type == 70){
            $query->query("select name from vehicle_driver");
            $query->run();
            while ($row = $query->fetch_assoc()) {
                array_push($drivers, $row['name']);
            }

            $query->query("select number from vehicle");
            $query->run();
            while ($row = $query->fetch_assoc()) {
                array_push($vehicles, $row['number']);
            }
        }
        else {
            $query->query("select invoice_id from invoice_container where container_id = ?");
            $query->bind = array('i',&$container_id);
            $query->run();
            $result_invoice = $query->fetch_assoc();
            $invoice_id = $result_invoice['invoice_id'];
            $query->query("select letpass_driver.name,letpass_driver.license from letpass_driver 
                    left join letpass on letpass.id = letpass_driver.letpass_id left join letpass_container 
                    on letpass_container.letpass_id = letpass.id where letpass.invoice_id = ? 
                    and letpass_container.container_id = ? and letpass.status = 0");
            $query->bind = array('ii', &$invoice_id,&$container_id);
            $query->run();
            while ($row = $query->fetch_assoc()) {
                array_push($drivers, $row['name']);
                array_push($vehicles, $row['license']);
            }
        }

        $query->commit();
        new Respond(233,array('drv'=>$drivers, 'veh'=>$vehicles));
    }

    public function getAct() {
        $container = $this->request->param('num');
        $query = new MyQuery();
        $query->query("SELECT booking.act FROM container INNER JOIN booking 
                        ON container.book_number = booking.booking_number 
                        WHERE container.number = ? AND container.trade_type_code = '70'");
        $query->bind = array('s', &$container);
        $query->run();
        $result = $query->fetch_assoc();
        $act = $result['act'];

        if ($act)
            new Respond(234, array("act" => $act));
        else
            new Respond(235, array("act" => $act));

    }

    private function recursiveGateStatusUpdate($id, $query) {
        $idFetchQuery = "SELECT id FROM container WHERE gate_status = 'MOVED' 
                        AND moved_to = $id";
        $query->query($idFetchQuery);
        // $query->bind = array('i', &$container_id);
        $run = $query->run();
        $result = $run->fetch_assoc();

        if ($result['id'])
            $this->recursiveGateStatusUpdate($result['id'], $query);

        $updateQuery = "UPDATE container SET gate_status = 'GATED OUT' 
                        WHERE moved_to = $id AND gate_status = 'MOVED'";
        $query->query($updateQuery);
        $query->run();
    }

    function get_trucks(){
        $container = $this->request->param('cnum');
        $query = new MyTransactionQuery();
        $query->query("select id from container where number=? and gate_status='GATED IN'");
        $query->bind = array('s',&$container);
        $query->run();
        $result = $query->fetch_assoc();

        $query->query("select vehicle_number as veh from truck_log where container_id=? and load_status=1");
        $query->bind = array('i',&$result['id']);
        $query->run();
        $result1 = $query->fetch_assoc();
        echo json_encode($result1['veh']);
    }
}

?>
