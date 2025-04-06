<?php

namespace Api;
session_start();


use Lib\ACL,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    Lib\MyQuery,
    Lib\MyTransactionQuery,
    Lib\ReportGenerator,
    Lib\Respond;
use PDO;

class VoyageReport{

    private $request;

    public function  __construct($request){
        $this->request = $request;
    }

    function table(){
        $db = new Bootstrap();
        $db = $db->database();

        $start_date = $this->request->param('stdt');
        $end_date = $this->request->param('eddt');
        

        Editor::inst($db, 'voyage')
            ->fields(
                Field::inst('voyage.reference as vref'),
                Field::inst('vessel.name as vnam'),
                Field::inst('voyage.id as 20FT')
                    ->getFormatter(function ($val){
                        $query = new MyQuery();
                        $query->query("select count(container_isotype_code.id) as 20ft from container 
                        left join container_isotype_code on container_isotype_code.id = container.iso_type_code 
                        left join voyage on voyage.id = container.voyage where container_isotype_code.length = 20 
                        and voyage.id = ?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        return $query->fetch_all()[0];
                    }),
                Field::inst('voyage.id as 40FT')
                    ->getFormatter(function ($val){
                        $query = new MyQuery();
                        $query->query("select count(container_isotype_code.id) as 20ft from container 
                        left join container_isotype_code on container_isotype_code.id = container.iso_type_code 
                        left join voyage on voyage.id = container.voyage where container_isotype_code.length = 40 
                        and voyage.id = ?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        return $query->fetch_all()[0];
                    }),
                Field::inst('voyage.id as TEU')
                    ->getFormatter(function ($val){
                        $query = new MyQuery();
                        $query->query("Select SUM(TEU) as TEU from (select COUNT(container.id) as TEU from container INNER JOIN container_isotype_code on container.iso_type_code = container_isotype_code.id where voyage = ? and container_isotype_code.length = 20
                                  UNION
                                  select COUNT(container.id) * 2 as TEU from container INNER JOIN container_isotype_code on container.iso_type_code = container_isotype_code.id where voyage = ? and container_isotype_code.length = 40) as TEUS");
                        $query->bind = array('ii', &$val, &$val);
                        $query->run();
                        return $query->fetch_all()[0] ?? 0;
                    }),
                Field::inst('voyage.id as voyid'),
                Field::inst('voyage.actual_arrival as adat')
            )
            ->on('preCreate', function ($editor,$values,$system_object='voyage-reports'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='voyage-reports'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='voyage-reports'){
                return false;
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='voyage-reports'){
                return false;
            })
            ->leftJoin('vessel','voyage.vessel_id','=','vessel.id')
            ->where(function ($q) use ($start_date,$end_date,$date){
                $start_date = $start_date != '' ? $start_date : $date;
                $end_date = $end_date != '' ? $end_date : $date;

                $q->where( 'voyage.id', '(SELECT id FROM voyage WHERE cast(voyage.actual_arrival as date) between "'.$start_date.'" and "'.$end_date.'")', 'IN', false );

            })
            ->process($_POST)
            ->json();
    }

    function report()
    {
        $start_date = $this->request->param('stdt');
        $end_date = $this->request->param('eddt');
        $date = date('Y-m-d');
        $columns = json_decode($this->request->param('src'));
        $headers = json_decode($this->request->param('head'));
        array_pop($headers);
        array_pop($columns);


        $start_date = !$start_date ? $date : $start_date;
        $end_date = !$end_date ? $date : $end_date;
        $type = $this->request->param('type');

        $query = new MyQuery();
        $query->query("select voyage.id as _id, voyage.reference as vref, vessel.name as vnam, 
        (select count(container_isotype_code.id) as 20ft from container 
                                inner join container_isotype_code on container_isotype_code.id = container.iso_type_code 
                                inner join voyage on voyage.id = container.voyage where container_isotype_code.length = 20 and voyage.id = _id) as ft20,                     
        (select count(container_isotype_code.id) as 20ft from container 
                                inner join container_isotype_code on container_isotype_code.id = container.iso_type_code 
                                inner join voyage on voyage.id = container.voyage where container_isotype_code.length = 40 and voyage.id = _id) as ft40,
        (select COUNT(container.id) from container INNER JOIN container_isotype_code on container.iso_type_code = container_isotype_code.id where container_isotype_code.length = 20 and voyage = _id) as teu20,
        (select COUNT(container.id) * 2 from container INNER JOIN container_isotype_code on container.iso_type_code = container_isotype_code.id where container_isotype_code.length = 40 and voyage = _id) as teu40,
         voyage.actual_arrival as date
         from voyage
        LEFT JOIN vessel on voyage.vessel_id = vessel.id
        where cast(voyage.actual_arrival as date) between ? and ?");
        $query->bind = array('ss', &$start_date, &$end_date);
        $run = $query->run();

        $data = array();

        while ($row = $run->fetch_assoc()) {
            $row_data = array();

            foreach ($columns as $col){
                array_push($row_data,$row[$col]);
            }
            array_push($data, $row_data);
        }


        $length = count($headers);
        $width = 190 / $length;
        $widths = array_fill(0, $length, $width);

        $report_generator = new ReportGenerator($start_date, $end_date, "Voyage Report", $headers, $data, $widths);

        if($type == "pdf") {
            $report_generator->printPdf();
        }
        else {
            $report_generator->printExcel();
        }
    }

    function details_table(){
        $db = new Bootstrap();
        $db = $db->database();

        $voyage_id = $this->request->param('vyid');

        Editor::inst($db, 'container')
            ->fields(
                Field::inst('container.number as ctnr'),
                Field::inst('container.status as stat')
                    ->getFormatter(function ($val) {
                    return  $val ? "FLAGGED" : "UNFLAGGED";
                }),
                Field::inst('container_isotype_code.code as code'),
                Field::inst('container.id as stdat')
                    ->getFormatter(function ($val){
                        $query = new MyQuery();
                        $query->query("SELECT cast(date as date) from gate_record where gate_record.container_id = ? and type = 'GATE IN'");
                        $query->bind = array('i', &$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    }),
                Field::inst('container.id as days')
                    ->getFormatter(function ($val){
                        $query = new MyQuery();
                        $query->query("SELECT cast(date as date) from gate_record where gate_record.container_id = ? and type = 'GATE OUT'");
                        $query->bind = array('i', &$val);
                        $query->run();
                        $gate_out_date = $query->fetch_num()[0];
                        if($gate_out_date){
                            $query = new MyQuery();
                            $query->query("SELECT cast(date as date) from gate_record where gate_record.container_id = ? and type = 'GATE IN'");
                            $query->bind = array('i', &$val);
                            $query->run();
                            $gate_in_date = $query->fetch_num()[0];
                            $gate_in_date =  new \DateTime($gate_in_date);
                            $gate_out_date =  new \DateTime($gate_out_date);

                            $days = $gate_in_date->diff($gate_out_date)->days;

                            return  $days;
                        }
                        else return "";
                    }),
                Field::inst('container.id as stak')
                    ->getFormatter(function ($val){
                        return "";
                    }),
                Field::inst('container.gate_status as gstat')
                    ->getFormatter(function ($val) {
                        return $val == "" ? "NOT GATED IN/OUT" : $val;
                }),
                Field::inst('container.id as pstn')
                    ->getFormatter(function ($val) {
                        return "";
                    }),
                Field::inst('agency.name as anam'),
                Field::inst('gate_record.cond as cond'),
                Field::inst('container.id as id')
            )
            ->leftJoin('voyage', 'container.voyage', '=', 'voyage.id')
            ->leftJoin('container_isotype_code', 'container_isotype_code.id', '=', 'container.iso_type_code')
            ->leftJoin('gate_record', 'gate_record.container_id', '=', 'container.id')
            ->leftJoin('depot', 'depot.id', '=', 'gate_record.depot_id')
            ->leftJoin('agency', 'agency.id', '=', 'container.agency_id')
            ->where('voyage.id', $voyage_id, '=')
            ->where(function ($q) {
                $q->where( function ( $r ) {
                    $r->where('gate_record.id', '(SELECT id FROM gate_record WHERE type = "GATE IN")', 'IN', false)
                        ->or_where('gate_record.id', null);
                });
            })
            ->process($_POST)
            ->json();
    }

    function details_report()
    {
        $voyage = $this->request->param('vyid');
        $type = $this->request->param('type');
        $columns = json_decode($this->request->param('src'));
        $headers = json_decode($this->request->param('head'));

        $query = new MyQuery();
        $query->query("SELECT container.number as ctnr, container.id as _id, container.status as stat, container_isotype_code.code  as code, container.gate_status as gstat, 
(SELECT cast(date as date) from gate_record where gate_record.container_id = _id and type = 'GATE IN' LIMIT 1) as stdat,
(SELECT cast(date as date) from gate_record where gate_record.container_id = _id and type = 'GATE IN' LIMIT 1) as gtin,
(SELECT cast(date as date) from gate_record where gate_record.container_id = _id and type = 'GATE OUT' LIMIT 1) as gtou,
agency.name  as agen, gate_record.cond  as cond, voyage.reference as voyage
FROM container
LEFT JOIN voyage on container.voyage = voyage.id
LEFT JOIN container_isotype_code on container.iso_type_code = container_isotype_code.id
LEFT JOIN gate_record on container.id = gate_record.container_id
LEFT JOIN depot on depot.id = gate_record.depot_id
LEFT JOIN agency on container.agency_id = agency.id
        where voyage.id = ? and (gate_record.type = 'GATE IN' or gate_record.id is null)");
        $query->bind = array('i', &$voyage);
        $run = $query->run();

        $data = array();

        while ($row = $run->fetch_assoc()) {
            $row_data = array();

            $gate_in_date = $row['gtin'];
            $gate_out_date = $row['gtou'];
            $container_status = $row['stat'];
            $gate_status = $row["gstat"];

            if(!$gate_in_date || !$gate_out_date) {
                $days = "";
            }
            else {
                $gate_in_date = new \DateTime($gate_in_date);
                $gate_out_date = new \DateTime($gate_out_date);
                $days = $gate_in_date->diff($gate_out_date)->days;
            }

            foreach ($columns as $col){
                if($col == "days"){
                    $row[$col] = $days;
                }
                if($col == "stat"){
                    $row[$col] = $container_status == 0 ? "UNFLAGGED" : "FLAGGED";
                }
                if($col == "gstat"){
                    $row[$col] = $gate_status == "" ? "NOT GATED IN/OUT" : $gate_status;
                }
                array_push($row_data,$row[$col]);
            }
            array_push($data, $row_data);
        }


        $length = count($headers);
        $width = 190 / $length;
        $widths = array_fill(0, $length, $width);

        // while ($row = $run->fetch_assoc()) {
        //     $gate_in_date = $row['gtin'];
        //     $gate_out_date = $row['gtou'];

        //     if(!$gate_in_date || !$gate_out_date) {
        //         $days = "";
        //     }
        //     else {
        //         $gate_in_date = new \DateTime($gate_in_date);
        //         $gate_out_date = new \DateTime($gate_out_date);

        //         $days = $gate_in_date->diff($gate_out_date)->days;
        //     }

        //     $row_data = array($row['stat'] ? "FLAGGED" : "UNFLAGGED", $row['ctnr'], $row['code'], $row['dnam'], $row['stdat'],
        //        $days, "", "", html_entity_decode($row['agen']), $row['cond']);
        //     array_push($data, $row_data);
        // }

        $query = new MyQuery();
        $query->query("select reference from voyage where id = ?");
        $query->bind = array('i', $voyage);
        $run = $query->run();
        $voyage = $run->fetch_assoc()["reference"];

        // $headers = array("State", "Container Number", "ISO Type Code", "Status", "Stock Date", "Days Spent", "Stack", "Position", "Client", "Condition");
        // $widths = array(23, 27, 18, 18, 23, 13, 13, 14, 21, 18);

        $report_generator = new ReportGenerator("", "", "Voyage Stock Report - $voyage ", $headers, $data, $widths);

        if ($type == "pdf") {
            $report_generator->printPdf();
        } else {
            $report_generator->printExcel();
        }
    }

    function total(){
        $start_date = $this->request->param('stdt');
        $end_date = $this->request->param('eddt');

        $query = new MyTransactionQuery();
        $query->query("select count(container.id) from container 
                              inner join voyage on voyage.id = container.voyage where cast(voyage.actual_arrival as date) between ? and ?");
        $query->bind = array('ss', &$start_date, &$end_date);
        $run = $query->run();
        $total = $run->fetch_num()[0] ?? "0";

        $query->query("select count(container_isotype_code.id) from container 
                        inner join container_isotype_code on container_isotype_code.id = container.iso_type_code
                        inner join voyage on voyage.id = container.voyage
                        where container_isotype_code.length = 20 and cast(voyage.actual_arrival as date) between ? and ?");
        $query->bind = array('ss', &$start_date, &$end_date);
        $run = $query->run();
        $total_20 = $run->fetch_num()[0] ?? "0";

        $query->query("select count(container_isotype_code.id) from container 
                        inner join container_isotype_code on container_isotype_code.id = container.iso_type_code
                        inner join voyage on voyage.id = container.voyage
                        where container_isotype_code.length = 40 and cast(voyage.actual_arrival as date) between ? and ?");
        $query->bind = array('ss', &$start_date, &$end_date);
        $run = $query->run();
        $total_40 = $run->fetch_num()[0] ?? "0";

        $query->query("Select SUM(TEU) from (select COUNT(container.id) as TEU, voyage.actual_arrival as date  from container 
                                  inner  join container_isotype_code on container.iso_type_code = container_isotype_code.id                                   
                                  inner join voyage on voyage.id = container.voyage
                                  where container_isotype_code.length = 20
                                  UNION
                              select COUNT(container.id) * 2 as TEU, voyage.actual_arrival as date from container 
                                  inner join container_isotype_code on container.iso_type_code = container_isotype_code.id                                   
                                  inner join voyage on voyage.id = container.voyage
                                  where container_isotype_code.length = 40) as TEUS where cast(date as date) between ? and ?");
        $query->bind = array('ss', &$start_date, &$end_date);

        $run = $query->run();
        $total_teu = $run->fetch_num()[0] ?? "0";

        new Respond(231, array('cntr' => $total, 'ft20'=>$total_20, 'ft40'=>$total_40, 'teu' => $total_teu));
    }
}

?>