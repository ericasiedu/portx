<?php

namespace Api;
session_start();


use Lib\ACL,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    Lib\MyQuery,
    Lib\ReportGenerator,
    Lib\MyTransactionQuery;

$system_object='gate-reports';
ACL::verifyRead($system_object);

class GateReport
{
    private $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();

        $date = date('Y-m-d');

        $start_date = $this->request->param('stdt');
        $end_date = $this->request->param('eddt');

        $trade_type = $this->request->param('trty');
        $gate_status = $this->request->param('gtst');

        $query = new MyTransactionQuery();

        Editor::inst($db, 'gate_record')
            ->fields(
                Field::inst('gate_record.id as id'),
                Field::inst('trade_type.name as trname')
                    ->getFormatter(function($val,$data){
                        $gate_type = $data['gate_record.type'];
                        return ($gate_type == "GATE IN" && $val == "TRANSIT") ? "IMPORT" : $val;
                    }),
                Field::inst('gate_record.type as type'),
                Field::inst('vehicle.number as vhid'),
                Field::inst('trucking_company.name as tkname'),
                Field::inst('gate_record.special_seal as spsl'),
                Field::inst('gate_record.external_reference as exref'),
                Field::inst('booking.act as act'),
                Field::inst('gate_record.cond as cond'),
                Field::inst('gate_record.note as note'),
                Field::inst('gate_record.container_id as cons')
                    ->getFormatter(function($val) use ($query){
                        $query->query("select container.trade_type_code,agency.name from container left join agency on agency.id = container.agency_id where container.id=?");
                        $query->bind = array("i",&$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                    
                        $query->query("select consignee from gate_record where container_id=?");
                        $query->bind = array("i",&$val);
                        $query->run();
                        $result2 = $query->fetch_assoc();
                    
                        return $result['trade_type_code'] == '11' ? htmlspecialchars(html_entity_decode($result['name'])) : htmlspecialchars(html_entity_decode($result2['consignee']));
                    }),
                Field::inst('gate_record.date as date'),
                Field::inst('gate_record.pdate as pdate'),
                Field::inst('concat(user.first_name, "", user.last_name) as user'),
                Field::inst('container.number as ctnum'),
                Field::inst('container.shipping_line_id as ship')
                    ->getFormatter(function($val) use ($query){
                        $query->query("select name from shipping_line where id=?");
                        $query->bind = array('i',&$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        return $result['name'];
                    }),
                Field::inst('container_isotype_code.code as code'),
                Field::inst('container.content_of_goods as goods')
                ->getFormatter(function($val){
                    return trim($val,"=");
                }),
                Field::inst('container.icl_seal_number_1 as icsl1'),
                Field::inst('container.icl_seal_number_2 as icsl2'),
                Field::inst('container.seal_number_1 as seal1'),
                Field::inst('container.seal_number_2 as seal2'),
                Field::inst('depot.name as dpname'),
                Field::inst('depot_gate.name as gtname'),
                Field::inst('gate_record.vehicle_id as vhnum')
                    ->getFormatter(function($val, $data) use ($query){
                        $gate_type = $data['gate_record.type'];

                        if($gate_type == "GATE IN"){
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
                    }),
                Field::inst('gate_record.driver_id as dvname')
                    ->getFormatter(function($val, $data) use ($query){
                        $gate_type = $data['gate_record.type'];

                        if($gate_type == "GATE IN"){
                            $query->query("select name from vehicle_driver where id =?");
                            $query->bind = array('s',&$val);
                            $query->run();
                            return $query->fetch_num()[0] ?? '';
                        }
                        else {
                            $query->query("select name from letpass_driver where id = ?");
                            $query->bind = array('i', &$val);
                            $query->run();
                            return $query->fetch_num()[0] ?? '';
                        }
                    }),
                Field::inst('trucking_company.name as tkname'),
                Field::inst('voyage.reference as vyref'),
                Field::inst('container.id as ctid')
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'gate-reports') {
                exit();
            })
            ->on('preGet', function ($editor, $id, $system_object = 'gate-reports') {
                ACL::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'gate-reports') {
                exit();
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'gate-reports') {
                exit();

            })
            ->leftJoin('container', 'gate_record.container_id', '=', 'container.id')
            ->leftJoin('depot', 'gate_record.depot_id', '=', 'depot.id')
            ->leftJoin('user', 'user.id', '=', 'gate_record.user_id')
            ->leftJoin('depot_gate', 'gate_record.gate_id', '=', 'depot_gate.id')
            ->leftJoin('vehicle', 'gate_record.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('vehicle_driver', 'gate_record.driver_id', '=', 'vehicle_driver.id')
            ->leftJoin('trucking_company', 'gate_record.trucking_company_id', '=', 'trucking_company.id')
            ->leftJoin('voyage', 'container.voyage', '=', 'voyage.id')
            ->leftJoin('container_isotype_code', 'container_isotype_code.id', '=', 'container.iso_type_code')
            ->leftJoin('trade_type', 'trade_type.code', '=', 'container.trade_type_code')
            ->leftJoin('booking', 'booking.booking_number', '=', 'container.book_number')
            ->where(function ($q) use ($start_date, $end_date) {

                $q->where('gate_record.id', '(select id from gate_record where cast(date as date)
                between "' . $start_date . '" and "' . $end_date . '")', 'IN', false);

            })
            ->where(function ($q) use ($gate_status) {
                if ($gate_status == '*') {
                    $q->where('gate_record.id', "(SELECT gate_record.id FROM gate_record)", 'IN', false);
                } else {
                    $q->where('gate_record.id', '(SELECT gate_record.id FROM gate_record WHERE type = "' . $gate_status . '")', 'IN', false);
                }
            })
            ->where(function ($q) use ($trade_type) {
                if ($trade_type == '*') {
                    $q->where('container.id', "(SELECT id FROM container)", 'IN', false);
                } else {
                    $q->where('container.id', '(SELECT id FROM container WHERE trade_type_code = ' . $trade_type . ')', 'IN', false);

                }
            })
            ->process($_POST)
            ->json();
            $query->commit();
    }

  function report(){

            $start_date = $this->request->param('sdat');
            $end_date = $this->request->param('edat');
            $trade_type = $this->request->param('typ');
            $gate_status  = $this->request->param('gate_st');
            $type =  $this->request->param('rtyp');
            $columns = json_decode($this->request->param('src'));
            $headers = json_decode($this->request->param('head'));


            $trade_type = $trade_type == '*' ? '%' : $trade_type;
            $gate_status = $gate_status == '*' ? '%' : $gate_status;


            $query = new MyQuery();
            $query->query("SELECT container.number as ctnum,shipping_line.name as ship,container_isotype_code.code,voyage.reference as vyref,trade_type.name as trname,depot_gate.name as gtname,depot.name as dpname,gate_record.vehicle_id as vhnum,gate_record.driver_id as dvname,trucking_company.name as tkname,gate_record.date,gate_record.type as gtype,
            gate_record.consignee as cons,gate_record.container_id,gate_record.special_seal as spsl,gate_record.pdate,container.icl_seal_number_1 as icsl1,container.icl_seal_number_2 as icsl2,container.seal_number_1 as seal1,container.seal_number_2 as seal2,gate_record.special_seal,container.content_of_goods as goods,
            gate_record.external_reference as exref, booking.act, gate_record.cond,gate_record.note,concat(user.first_name, ' ', user.last_name) as user from gate_record left join container on gate_record.container_id = container.id left join depot on gate_record.depot_id = depot.id left join depot_gate on gate_record.gate_id = depot_gate.id 
            left join vehicle on gate_record.vehicle_id = vehicle.id left join vehicle_driver on gate_record.driver_id = vehicle_driver.id 
            left join trucking_company on gate_record.trucking_company_id = trucking_company.id 
            left join voyage on container.voyage = voyage.id left join container_isotype_code on container_isotype_code.id = container.iso_type_code 
            left join shipping_line on shipping_line.id = container.shipping_line_id left join user on user.id = gate_record.user_id 
            left join trade_type on trade_type.code = container.trade_type_code LEFT JOIN booking ON booking.booking_number = container.book_number 
            where trade_type.code like ? and cast(gate_record.date as date) between ? and ? and gate_record.type like ?");
            $query->bind = array('ssss',&$trade_type,&$start_date,&$end_date,&$gate_status);
            $res = $query->run();



            $data = array();

            while ($row = $res->fetch_assoc()) {
                $row_data = array();
    
                foreach ($columns as $col){
                    if($col == "cons"){
                        $query1 = new MyTransactionQuery();
                        $query1->query("select container.trade_type_code,agency.name from container left join agency on agency.id = container.agency_id where container.id=?");
                        $query1->bind = array("i",&$row['container_id']);
                        $query1->run();
                        $result = $query1->fetch_assoc();
                        
                        $query1->query("select consignee from gate_record where container_id=?");
                        $query1->bind = array("i",&$row['container_id']);
                        $query1->run();
                        $result2 = $query1->fetch_assoc();
                        $query1->commit();
                        $row[$col] = $result['trade_type_code'] == '11' ? htmlspecialchars_decode($result['name']) : htmlspecialchars_decode($result2['consignee']);
                    }
                    if($col == "dvname"){
                        if ($row['gtype'] == "GATE IN") {
                            $query = new MyQuery();
                            $query->query("select name from vehicle_driver where id =?");
                            $query->bind = array('i',&$row['dvname']);
                            $query->run();
                            $result = $query->fetch_assoc();
                            $row[$col] = htmlspecialchars_decode($result['name']);
                        }
                        else{
                            $query = new MyQuery();
                            $query->query("select name from letpass_driver where id = ?");
                            $query->bind = array('i', &$row['dvname']);
                            $query->run();
                            $result = $query->fetch_assoc();
                            $row[$col] = htmlspecialchars_decode($result['name']);
                        }
                    }
                    if($col == "ship"){
                        if($row['ship'] == ""){
                            $row[$col] = "NA";
                        }
                        else{
                            $row[$col] = htmlspecialchars_decode($row['ship']);
                        }
                    }
                    if($col == "trname"){
                        if($row['trname'] == ""){
                            $row[$col] = "NA";
                        }
                        else{
                            $row[$col] = ($row['gtype'] == "GATE IN" && $row['trname'] == "TRANSIT") ? "IMPORT" : htmlspecialchars_decode($row['trname']);
                        }
                    }
                    if ($col == "spsl"){
                        if($row['spsl'] == ""){
                            $row[$col] = "NA";
                        }
                    }
                    if ($col == "note"){
                        if($row['note'] == ""){
                            $row[$col] = "NA";
                        }
                    }
                    if ($col == "icsl1"){
                        if($row['icsl1'] == ""){
                            $row[$col] = "NA";
                        }
                    }
                    if ($col == "icsl2"){
                        if($row['icsl2'] == ""){
                            $row[$col] = "NA";
                        }
                    }
                    if ($col == "seal1"){
                        if($row['seal1'] == ""){
                            $row[$col] = "NA";
                        }
                    }
                    if ($col == "seal2"){
                        if($row['seal2'] == ""){
                            $row[$col] = "NA";
                        }
                    }
                    if ($col == "act"){
                        if($row['act'] == ""){
                            $row[$col] = "";
                        }
                    }
                    if ($col == "exref"){
                        if($row['exref'] == ""){
                            $row[$col] = "NA";
                        }
                    }
                    if ($col == "goods"){
                        if($row['goods'] == ""){
                            $row[$col] = "NA";
                        }
                        else{
                            $row[$col] = trim($row['goods'],"=");
                        }
                    }
                    if ($col == "vhnum"){
                        if($row['gtype'] == "GATE IN"){
                            $query = new MyQuery();
                            $query->query("select number from vehicle where id = ?");
                            $query->bind = array('i', &$row['vhnum']);
                            $query->run();
                            $result = $query->fetch_assoc();
                            $row[$col] = $result['number'];
                        }
                        else {
                            $query = new MyQuery();
                            $query->query("select license from letpass_driver where id = ?");
                            $query->bind = array('i', &$row['vhnum']);
                            $query->run();
                            $result = $query->fetch_assoc();
                            $row[$col] = $result['license'];
                        }
                    }
                    if ($col == "act") {
                        if ($row['act'] == null || $row['act'] == "") {
                            $row[$col] = "N/A";
                        }
                    }
                
                    array_push($row_data,$row[$col]);
                }
                array_push($data, $row_data);
            }
    
            $length = count($headers);
            $width = 190 / $length;
            $widths = array_fill(0, $length, $width);


            $report_generator = new ReportGenerator($start_date, $end_date, "Gate Report", $headers, $data, $widths);

            if($type == "xsl") {
                $report_generator->printExcel();    
            }
            else {
                $report_generator->printPdf();
            }
        }

    }
?>
