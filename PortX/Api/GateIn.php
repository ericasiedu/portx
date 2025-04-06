<?php

namespace Api;
session_start();

use
    Lib\ACL,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Options,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\MyTransactionQuery,
    Lib\Gate,
    Lib\Respond;


class GateIn {

    private $request;

    public function __construct($request) {
        $this->request = $request;
    }

    function table() {
        $db = new Bootstrap();
        $db = $db->database();

        $query = new MyTransactionQuery();

        Editor::inst($db, 'gate_record')
            ->fields(
                Field::inst('gate_record.id as id'),
                Field::inst('gate_record.id as gid'),
                Field::inst('gate_record.container_id')
                    ->setFormatter(function ($val) use ($query) {
                        $number = preg_replace("/\s+/", "", $val);
                        $query->query("select id from container where status = 0 and gate_status != 'GATED OUT' and number =?");
                        $query->bind = array('s', &$number);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) use ($query) {
                        $query->query("select number from container where id  =?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::maxLen(11, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ($val, $data, $field, $host) use ($query) {
                        $query->query("select id, trade_type_code, status from container where gate_status != 'GATED OUT' and number =?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $status = $result['status'];
                        $container_id = $result['id'];
                        $trade_type = $result['trade_type_code'];
                        $trade_type_data = $data['trade'];

                        $query->query("select name from trade_type where code=?");
                        $query->bind = array('s',&$trade_type_data);
                        $query->run();
                        $trade_result = $query->fetch_assoc();
                        $trade_type_name = $trade_result['name'];

                        $query->query("select id from container where number=?");
                        $query->bind = array('s',&$val);
                        $query->run();
                        $number_result = $query->fetch_assoc(); 
                     
                        if ($status == 1) {
                            return 'Container cannot be gated in, it is flagged';
                        }

                        if ($host['action'] == 'create') {
                            if (is_null($container_id)) {
                                return 'Invalid Container Number';
                            }
                            $query->query("select id, cond from gate_record where type = 'GATE IN' and container_id =?");
                            $query->bind = array('i', &$container_id);
                            $query->run();
                            $result = $query->fetch_assoc();
                            $gate_record = $result['id'];
                            if ($gate_record) {
                                return 'Container already inserted';
                            }
                            if($number_result['id']){
                                if ($trade_type_data == 11) {
                                    if ($trade_type_data != $trade_type) {
                                        return "Container is not the $trade_type_name trade type";
                                    } 
                                }
                            }   
                        }

                        if ($host['action'] == 'edit') {
                            $gate_record = intval($host['id']);
                            $query->query("select container.id as container_id,gate_status from container left join gate_record on container.id = gate_record.container_id where gate_record.id = ?");
                            $query->bind = array('i', &$gate_record);
                            $query->run();
                            $result = $query->fetch_assoc();
                            $gate_status = $result['gate_status'];
                            $condition = $data['gate_record']['cond'];
                            $container_id = $result['container_id'];

                            $query->query("select id from invoice_container where container_id=?");
                            $query->bind = array("i",&$container_id);
                            $query->run();
                            $invoice_result = $query->fetch_assoc();
    
                            if($invoice_result['id']){
                                return "Cannot edit container because it been invoiced";
                            }
                            elseif($number_result['id']){
                                if ($trade_type_data != $trade_type) {
                                    return "Container is not the $trade_type_name trade type";
                                } 
                            }   
                         
                            if ($condition == 'SOUND') {
                                $query->query("delete from gate_record_container_condition where gate_record =?");
                                $query->bind = array('i', &$gate_record);
                                $query->run();
                            }
                    
                         
                        }

                        return true;
                    }),
                Field::inst('gate_record.container_id as container_id'),
                Field::inst('gate_record.gate_id')
                    ->options(Options::inst()
                        ->table('depot_gate')
                        ->value('id')
                        ->label('name')
                    ),
                Field::inst('gate_record.type'),
                Field::inst('gate_record.depot_id')
                    ->options(Options::inst()
                        ->table('depot')
                        ->value('id')
                        ->label('name')
                        ->order('id ASC')
                    )
                    ->validator(function($val, $data, $field, $host) use ($query){
                        $container_number = $data['gate_record']['container_id'];
                        $query->query("select trade_type_code from container where number=? and gate_status=''");
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
                    ->setFormatter(function ($val,$data) use ($query) {
                        $query->query("select id from vehicle where number =?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) use ($query) {
                        $query->query("select number from vehicle where id =?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ($val, $data, $field, $host) use ($query) {
                        $query->query("select id from vehicle where number  =?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        return ($query->fetch_num()[0]) ? true : 'Vehicle Not Registered';
                    }),
                Field::inst('gate_record.driver_id')
                    ->setFormatter(function ($val) use ($query) {
                        $val = html_entity_decode($val);
                        $query->query("select id from vehicle_driver where name =?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if (!$query->num_rows()) {
                            $val = htmlspecialchars($val);
                            $query->query("select id from vehicle_driver where name =?");
                            $query->bind = array('s', &$val);
                            $query->run();
                        }
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) use ($query) {
                        $query->query("select name from vehicle_driver where id =?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ($val, $data, $field, $host) use ($query) {
                        $val = html_entity_decode($val);
                        $query->query("select id from vehicle_driver where name  =?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if (!$query->num_rows()) {
                            $val = htmlspecialchars($val);
                            $query->query("select id from vehicle_driver where name  =?");
                            $query->bind = array('s', &$val);
                            $query->run();
                        }
                        return ($query->fetch_num()[0]) ? true : 'Vehicle Driver Not Registered';
                    }),
                Field::inst('gate_record.trucking_company_id')
                    ->setFormatter(function ($val) use ($query) {
                        $val = html_entity_decode($val);
                        $query->query("select id from trucking_company where name =?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if (!$query->num_rows()) {
                            $val = htmlspecialchars($val);
                            $query->query("select id from trucking_company where name =?");
                            $query->bind = array('s', &$val);
                            $query->run();
                        }
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) use ($query) {
                        $query->query("select name from trucking_company where id =?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ($val, $data, $field, $host) use ($query) {
                        $val = html_entity_decode($val);
                        $query->query("select id from trucking_company where name =?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if (!$query->num_rows()) {
                            $val = htmlspecialchars($val);
                            $query->query("select id from trucking_company where name =?");
                            $query->bind = array('s', &$val);
                            $query->run();
                        }
                        return ($query->fetch_num()[0]) ? true : 'Trucking Company Not Registered';
                    }),
                Field::inst('gate_record.special_seal')
                    ->validator(Validate::maxLen(20, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    )),
                Field::inst('gate_record.consignee')
                    ->setFormatter(function ($val) use ($query) {
                        return htmlspecialchars(html_entity_decode($val));
                    })
                    ->validator(Validate::maxLen(225, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host) use ($query) {
                        $val = htmlspecialchars(html_entity_decode($val));
                        if ($val || $data['trade'] == 21) {
                            if (!$val) {
                                return "Consignee can not be empty";
                            }
                        } else {
                            return true;
                        }

                        if($host['action'] == 'create'){
                            $container = $data['gate_record']['container_id'];
                            $query->query("select book_number from container where number  = ?  and gate_status != 'GATED OUT'");
                            $query->bind = array('s', &$container);
                            $run = $query->run();
                            $booking_number = $run->fetch_assoc()['book_number'];
    
                            $query->query("select consignee from container left join gate_record on container.id = gate_record.container_id 
                              where book_number = ? and consignee != ? and gate_record.type = 'GATE IN'");
                            $query->bind = array('ss', &$booking_number, &$val);
                            $query->run();
                            if ($query->num_rows()) {
                                $consignee = $query->fetch_assoc()['consignee'];
                                if ($consignee) {
                                    return "A different Shipper has been assigned for another container in this booking number. Consignee : $consignee";
                                }
                            }
                        }

                        return true;
                    })
                    ->setFormatter(function ($val) {
                        return ucwords($val);
                    }),
                Field::inst('gate_record.external_reference')
                    ->validator(Validate::maxLen(50, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    )),
                Field::inst('booking.act')
                    ->set(false),
                Field::inst('gate_record.cond'),
                Field::inst('gate_record.note'),
                Field::inst('gate_record.status as stat'),
                Field::inst('gate_record.waybill')
                    ->validator(Validate::maxLen(20, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    )),
                Field::inst('gate_record.date')
                    ->validator(function ($val, $data, $field, $host) {
                        $date = new \DateTime(substr($val, 0, 10));
                        $today = new \DateTime(date("Y-m-d"));
                        if ($date > $today) {
                            return 'Postdating Not Allowed';
                        }

                        if ($host['action'] == 'edit') {
                            $query = new MyTransactionQuery();
                            $query->query("select id from container where number=? and gate_status !='GATED OUT'");
                            $query->bind = array('s',&$data['gate_record']['container_id']);
                            $query->run();
                            $result = $query->fetch_assoc();
                            $container_id = $result['id'];

                            $query->query("update gate_record set date=?,pdate=? where container_id=?");
                            $query->bind = array('ssi',&$val,&$val,&$container_id);
                            $query->run();

                            $query->query("update container_log set date=?,pdate=? where container_id=?");
                            $query->bind = array('ssi',&$val,&$val,&$container_id);
                            $query->run();

                            $query->query("update proforma_container_log set date=?,pdate=? where container_id=?");
                            $query->bind = array('ssi',&$val,&$val,&$container_id);
                            $query->run();
                            $query->commit();
                        }


                        return true;
                    }),
                Field::inst('gate_record.pdate'),
                Field::inst('gate_record.user_id')
                    ->setFormatter(function ($val) {
                        return isset($_SESSION['id']) ? $_SESSION['id'] : 1;
                    })->getFormatter(function ($val) use ($query) {
                        $query->query("SELECT first_name, last_name FROM user WHERE id =?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $first_name = $result['first_name'];
                        $last_name = $result['last_name'];
                        $full_name = "$first_name" . " " . "$last_name";
                        return $full_name ?? '';
                    }),
                Field::inst('container.number as ctnum'),
                Field::inst('container_isotype_code.code as isoc'),
                Field::inst('container.content_of_goods as good'),
                Field::inst('container.icl_seal_number_1 as icsl1'),
                Field::inst('container.icl_seal_number_2 as icsl2'),
                Field::inst('container.seal_number_1 as seal1'),
                Field::inst('container.seal_number_2 as seal2'),
                Field::inst('depot.name as dpname'),
                Field::inst('depot_gate.name as dpgt'),
                Field::inst('vehicle.number as vhnm'),
                Field::inst('vehicle_driver.name as vhdr'),
                Field::inst('trucking_company.name as trnm'),
                Field::inst('voyage.reference as vyref'),
                Field::inst('container.id as ctnr'),
                Field::inst('trade_type.name as typ')
                    ->getFormatter(function ($val) {
                        return ($val == "IMPORT" || $val == "TRANSIT") ? "IMPORT" : $val;
                    })
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'gatein-records') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'gatein-records') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'gatein-records'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'gatein-records') use ($query) {
                ACl::verifyDelete($system_object);

                $number = $values['gate_record']['container_id'];
                $query->query("select gate_status, container.number, container.id from container left join gate_record on container.id = gate_record.container_id where gate_record.id = ?");
                $query->bind = array('s', &$id);
                $query->run();

                if (!$query->num_rows()) {
                    return false;
                }

                $result = $query->fetch_assoc();
                $container_id = $result['id'];
                $container_number = $result['number'];
                $status = $result['gate_status'];

                if ($status || $container_number != $number) {
                    return false;
                } else {
                    $query->query("delete from container_log where container_id =?");
                    $query->bind = array('i', &$container_id);
                    $query->run();

                    $query->query("delete from container_depot_info where container_id =?");
                    $query->bind = array('i', &$container_id);
                    $query->run();

                    $query->query("delete from proforma_container_log where container_id =?");
                    $query->bind = array('i', &$container_id);
                    $query->run();

                    $query->query("delete from proforma_container_depot_info where container_id =?");
                    $query->bind = array('i', &$container_id);
                    $query->run();

                    $query->query("update container set gate_status='' where id =?");
                    $query->bind = array('i', &$container_id);
                    $query->run();
                    $query->query("delete from gate_record_container_condition where gate_record =?");
                    $query->bind = array('i', &$id);
                    $query->run();
                    $query->commit();
                }
            })
            ->leftJoin('container', 'gate_record.container_id', '=', 'container.id')
            ->leftJoin('depot', 'gate_record.depot_id', '=', 'depot.id')
            ->leftJoin('depot_gate', 'gate_record.gate_id', '=', 'depot_gate.id')
            ->leftJoin('vehicle', 'gate_record.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('vehicle_driver', 'gate_record.driver_id', '=', 'vehicle_driver.id')
            ->leftJoin('trucking_company', 'gate_record.trucking_company_id', '=', 'trucking_company.id')
            ->leftJoin('voyage', 'container.voyage', '=', 'voyage.id')
            ->leftJoin('container_isotype_code', 'container_isotype_code.id', '=', 'container.iso_type_code')
            ->leftJoin('trade_type', 'trade_type.code', '=', 'container.trade_type_code')
            ->leftJoin('booking', 'booking.booking_number', '=', 'container.book_number')
            ->where('gate_record.type', 'GATE IN')
            ->where(function ($q) {
                $q->where('container.id', '(SELECT moved_to FROM container WHERE moved_to IS NOT NULL)',
                                 'NOT IN', false);
            })
            ->process($_POST)
            ->json();

        $query->commit();
    }

    public function gate_container() {
        $gate_id = $this->request->param('data');

        $query = new MyTransactionQuery();
        $query->query("select container.status as container_status, gate_record.status as gate_status, gate_record.cond, container.imdg_code_id, gate_record.container_id from gate_record inner join container on container.id = gate_record.container_id where gate_record.id =?");
        $query->bind = array('i', &$gate_id);
        $query->run();

        if (!$query->num_rows()) {
            exit();
        }

        $result = $query->fetch_assoc();
        $cond = $result['cond'];
        $container_id = $result['container_id'];
        $container_status = $result['container_status'];
        $gate_status = $result['gate_status'];

        if ($gate_status) {
            new Respond(116);
        }

        if ($container_status) {
            new Respond(117);
        }
        if ($cond == 'NOT SOUND') {
            $query->query("SELECT id FROM gate_record_container_condition WHERE gate_record =?");
            $query->bind = array('i', &$gate_id);
            $query->run();
            if (!$query->num_rows()) {
                new Respond(118);
            }
        }

        $query->query("insert into container_depot_history(container_id)values(?)");
        $query->bind = array('i',&$container_id);
        $query->run();


        $query->query("UPDATE gate_record SET status = 1 WHERE id =?");
        $query->bind = array('i', &$gate_id);
        $query->run();
        $query->query("UPDATE container INNER JOIN gate_record ON container.id = gate_record.container_id 
                    SET gate_status = 'GATED IN' WHERE gate_record.id = ?");
        $query->bind = array('i', &$gate_id);
        $query->run();

        $user_id = $_SESSION['id'];
        $query->query("select id from depot_activity where is_default = 1");
        $query->bind = array();
        $query->run();
        $activities = $query->fetch_all(MYSQLI_ASSOC);

        $query->query("SELECT container.trade_type_code FROM container INNER JOIN gate_record ON 
                container.id = gate_record.container_id WHERE gate_record.id = ?");
        $query->bind = array('i', &$gate_id);
        $query->run();
        // $trade_type = $query->fetch_all(MYSQLI_ASSOC);
        $query_result = $query->fetch_assoc();
        
        if ($query_result['trade_type_code'] == '70') {
            $query->query("select id from depot_activity where name = 'Lift on-lift off charges'");
            $query->bind = array();
            $query->run();
            $query_res = $query->fetch_assoc();
            $empty_default = $query_res['id'];

            
            $query->query("INSERT INTO container_log(container_id, activity_id, user_id, date)VALUES(?,?,?, now())");
            $query->bind = array('iii', &$container_id, &$empty_default, &$user_id);
            $query->run();

            $query->query("insert into container_log_history(container_id,activity_id,user_id,status)values(?,?,?,'CREATED');
            ");
            $query->bind = array('iii', &$container_id, &$empty_default, &$user_id);
            $query->run();

            $query->query("INSERT INTO proforma_container_log(container_id, activity_id, user_id, date)VALUES(?,?,?, now())");
            $query->bind = array('iii', &$container_id, &$empty_default, &$user_id);
            $query->run();

            $query->query("insert into proforma_container_log_history(container_id,activity_id,user_id,status)values(?,?,?,'CREATED');
            ");
            $query->bind = array('iii', &$container_id, &$empty_default, &$user_id);
            $query->run();

            $query->query("insert into proforma_container_depot_info(container_id,load_status,goods,full_status,user_id)values(?,'FCL','General Goods',0,?)");
            $query->bind = array('ii',&$container_id,&$user_id);
            $query->run();

        } else {
            foreach ($activities as $activity) {
                $default_activity = $activity['id'];
                $query->query("INSERT INTO container_log(container_id, activity_id, user_id, date)VALUES(?,?,?, now())");
                $query->bind = array('iii', &$container_id, &$default_activity, &$user_id);
                $query->run();

                $query->query("insert into container_log_history(container_id,activity_id,user_id,status)values(?,?,?,'CREATED');
                ");
                $query->bind = array('iii', &$container_id, &$default_activity, &$user_id);
                $query->run();
    
                $query->query("INSERT INTO proforma_container_log(container_id, activity_id, user_id, date)VALUES(?,?,?, now())");
                $query->bind = array('iii', &$container_id, &$default_activity, &$user_id);
                $query->run();

                $query->query("insert into proforma_container_log_history(container_id,activity_id,user_id,status)values(?,?,?,'CREATED');
                ");
                $query->bind = array('iii', &$container_id, &$default_activity, &$user_id);
                $query->run();
            }

            $query->query("insert into proforma_container_depot_info(container_id,load_status,goods,full_status,user_id)values(?,'FCL','General Goods',0,?)");
            $query->bind = array('ii',&$container_id,&$user_id);
            $query->run();
        }
        $query->commit();
        $gate = new Gate();
        $gate->genWaybill($gate_id);

        new Respond(211);
    }
}
?>
