<?php

namespace Api;
session_start();

use Lib\ACL,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    Lib\MyTransactionQuery,
    Lib\MyQuery,
    Lib\ReportGenerator;
$system_object='depot-reports';

class StockReport{
    private $request;

    public function  __construct($request)
    {
        $this->request = $request;


    }

    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();


        $trade_type = $this->request->param('trty');

        $gate_mask = '?';
        $gate_dtypes = 's';

        $gate_status = array('GATED IN');

        Editor::inst($db, 'shipping_line')
            ->fields(
                Field::inst('shipping_line.id'),
                Field::inst('shipping_line.name as sname'),
                Field::inst('shipping_line.id as 22U1')
                    ->getFormatter(function($val, $data) use ( $gate_status, $trade_type, $gate_mask, $gate_dtypes){
                        $shipping_id = $data['shipping_line.id'];

                        $query =  new MyQuery();
                        $query->query("select COUNT(id) as count from container
                                          where shipping_line_id = ?
                                          and container.trade_type_code like ? and container.gate_status in (".$gate_mask.")
                                          and  iso_type_code in 
                                          (select  id from container_isotype_code where code = '22U1')");
                        $query->bind = array('is'.$gate_dtypes, &$shipping_id, &$trade_type);

                        for($i = 0 ; $i < count($gate_status); $i ++)
                        {
                            $query->bind[5 + $i] =& $gate_status[$i];
                        }

                        $run = $query->run();
                        $count = $run->fetch_assoc()['count'];

                        return $count;
                    }),
                Field::inst('shipping_line.id as 22G1')
                    ->getFormatter(function($val, $data) use ( $gate_status, $trade_type, $gate_mask, $gate_dtypes){
                        $shipping_id = $data['shipping_line.id'];

                        $query =  new MyQuery();
                        $query->query("select COUNT(id) as count from container
                                          where shipping_line_id = ?
                                          and container.trade_type_code like ? and container.gate_status in (".$gate_mask.")
                                          and  iso_type_code in 
                                          (select  id from container_isotype_code where code = '22G1')");
                        $query->bind = array('is'.$gate_dtypes, &$shipping_id, &$trade_type);
                        for($i = 0 ; $i < count($gate_status); $i ++)
                        {
                            $query->bind[5 + $i] =& $gate_status[$i];
                        }
                        $run = $query->run();
                        $count = $run->fetch_assoc()['count'];

                        return $count;
                    }),
                Field::inst('shipping_line.id as 22P1')
                    ->getFormatter(function($val, $data) use ( $gate_status, $trade_type, $gate_mask, $gate_dtypes){
                        $shipping_id = $data['shipping_line.id'];

                        $query =  new MyQuery();
                        $query->query("select COUNT(id) as count from container
                                          where shipping_line_id = ?
                                          and container.trade_type_code like ? and container.gate_status in (".$gate_mask.")
                                          and  iso_type_code in 
                                          (select  id from container_isotype_code where code = '22P1')");
                        $query->bind = array('is'.$gate_dtypes, &$shipping_id, &$trade_type);
                        for($i = 0 ; $i < count($gate_status); $i ++)
                        {
                            $query->bind[5 + $i] =& $gate_status[$i];
                        }
                        $run = $query->run();
                        $count = $run->fetch_assoc()['count'];

                        return $count;
                    }),
                Field::inst('shipping_line.id as 45G1')
                    ->getFormatter(function($val, $data) use ($gate_status, $trade_type, $gate_mask, $gate_dtypes){
                        $shipping_id = $data['shipping_line.id'];

                        $query = new MyQuery();
                        $query->query("select COUNT(id) as count from container
                                          where shipping_line_id = ?
                                          and container.trade_type_code like ? and container.gate_status in (".$gate_mask.")
                                          and  iso_type_code in 
                                          (select  id from container_isotype_code where code = '45G1')");
                        $query->bind = array('is'.$gate_dtypes, &$shipping_id, &$trade_type);
                        for($i = 0 ; $i < count($gate_status); $i ++)
                        {
                            $query->bind[5 + $i] =& $gate_status[$i];
                        }
                        $run = $query->run();
                        $count = $run->fetch_assoc()['count'];

                        return $count;
                    }),
                Field::inst('shipping_line.id as 42U1')
                    ->getFormatter(function($val, $data) use ( $gate_status, $trade_type, $gate_mask, $gate_dtypes){
                        $shipping_id = $data['shipping_line.id'];

                        $query =  new MyQuery();
                        $query->query("select COUNT(id) as count from container
                                          where shipping_line_id = ?
                                          and container.trade_type_code like ? and container.gate_status in (".$gate_mask.")
                                          and  iso_type_code in 
                                          (select  id from container_isotype_code where code = '42U1')");
                        $query->bind = array('is'.$gate_dtypes, &$shipping_id, &$trade_type);
                        for($i = 0 ; $i < count($gate_status); $i ++)
                        {
                            $query->bind[5 + $i] =& $gate_status[$i];
                        }
                        $res = $query->run();
                        $count = $res->fetch_assoc()['count'];

                        return $count;
                    }),
                Field::inst('shipping_line.id as 45R1')
                    ->getFormatter(function($val, $data) use ( $gate_status, $trade_type, $gate_mask, $gate_dtypes){
                        $shipping_id = $data['shipping_line.id'];

                        $query =  new MyQuery();
                        $query->query("select COUNT(id) as count from container
                                          where shipping_line_id = ?
                                          and container.trade_type_code like ? and container.gate_status in (".$gate_mask.")
                                          and  iso_type_code in 
                                          (select  id from container_isotype_code where code = '45R1')");
                        $query->bind = array('is'.$gate_dtypes, &$shipping_id, &$trade_type);
                        for($i = 0 ; $i < count($gate_status); $i ++)
                        {
                            $query->bind[5 + $i] =& $gate_status[$i];
                        }
                        $run = $query->run();
                        $count = $run->fetch_assoc()['count'];

                        return $count;
                    }),
                Field::inst('shipping_line.id as 42P1')
                    ->getFormatter(function($val, $data) use ($gate_status, $trade_type, $gate_mask, $gate_dtypes){
                        $shipping_id = $data['shipping_line.id'];

                        $query =  new MyQuery();
                        $query->query("select COUNT(id) as count from container
                                          where shipping_line_id = ?
                                          and container.trade_type_code like ? and container.gate_status in (".$gate_mask.")
                                          and  iso_type_code in 
                                          (select  id from container_isotype_code where code = '42P1')");
                        $query->bind = array('is'.$gate_dtypes, &$shipping_id, &$trade_type);
                        for($i = 0 ; $i < count($gate_status); $i ++)
                        {
                            $query->bind[5 + $i] =& $gate_status[$i];
                        }
                        $res = $query->run();
                        $count = $res->fetch_assoc()['count'];

                        return $count;
                    }),
                Field::inst('shipping_line.id as OTHER')
                    ->getFormatter(function($val, $data) use ( $gate_status, $trade_type, $gate_mask, $gate_dtypes){
                        $shipping_id = $data['shipping_line.id'];

                        $query =  new MyQuery();
                        $query->query("select COUNT(container.id) as count from container 
                                inner join gate_record on gate_record.container_id = container.id 
                                where shipping_line_id =? and container.trade_type_code  like ? and container.gate_status in (".$gate_mask.") and  iso_type_code 
                                in (select  id from container_isotype_code where code not in ('22U1','22G1','22P1','45G1','42U1','45R1','42P1'))");
                        $query->bind = array('is'.$gate_dtypes, &$shipping_id, &$trade_type);
                        for($i = 0 ; $i < count($gate_status); $i ++)
                        {
                            $query->bind[5 + $i] =& $gate_status[$i];
                        }
                        $run = $query->run();
                        $count = $run->fetch_assoc()['count'];

                        return $count;
                    }),
                Field::inst('shipping_line.id as TU')
                    ->getFormatter(function($val, $data) use ( $gate_status, $trade_type, $gate_mask, $gate_dtypes){
                        $shipping_id = $data['shipping_line.id'];

                        $query =  new MyQuery();
                        $query->query("select COUNT(id) as count from container
                                          where shipping_line_id = ?
          and container.trade_type_code like ? and container.gate_status in ($gate_mask)");
                        $query->bind = array('is'.$gate_dtypes, &$shipping_id, &$trade_type);
                        for($i = 0 ; $i < count($gate_status); $i ++)
                        {
                            $query->bind[5 + $i] =& $gate_status[$i];
                        }
                        $run = $query->run();
                        $count = $run->fetch_assoc()['count'];

                        return $count;
                    }),
                Field::inst('shipping_line.id as TTEU')
                    ->getFormatter(function($val, $data) use ($gate_status, $trade_type, $gate_mask, $gate_dtypes){
                        $shipping_id = $data['shipping_line.id'];

                        $query =  new MyQuery();
                        $query->query("select container.id, container_isotype_code.length from container 
                                          left join container_isotype_code on container.iso_type_code = container_isotype_code.id 
                                          where shipping_line_id = ?
                                          and container.trade_type_code like ? and container.gate_status in ($gate_mask)");
                        $query->bind = array('is'.$gate_dtypes, &$shipping_id, &$trade_type);
                        for($i = 0 ; $i < count($gate_status); $i ++)
                        {
                            $query->bind[5 + $i] =& $gate_status[$i];
                        }
                        $run = $query->run();

                        $total_teu = 0;

                        while ($container = $run->fetch_assoc()) {
                            $length = intval($container['length']);
                            $teu = number_format($length / 20, 2);
                            $total_teu += $teu;
                        }

                        return $total_teu;
                    })
            )
            ->on('preCreate', function ($editor,$values,$system_object='depot-reports'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='depot-reports'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='depot-reports'){
//                ACl::verifyUpdate($system_object);
                return false;
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='depot-reports'){
//                ACl::verifyDelete($system_object);
                return false;
            })
            ->where(function ($q) use ( $trade_type){

                $q->where( 'shipping_line.id', "(select  DISTINCT(container.shipping_line_id) from container
                                                LEFT JOIN gate_record on gate_record.container_id = container.id
                                                where container.gate_status = 'GATED IN'
                                                and container.trade_type_code LIKE  '$trade_type')", 'IN', false );

            })
            ->process($_POST)
            ->json();
    }


    function line_table(){
        $db = new Bootstrap();
        $db = $db->database();

        $line_id = $this->request->param('slid') ?? 0;
        $trade_type = $this->request->param('trty');
        $query = new MyTransactionQuery();

        Editor::inst($db, 'container')
            ->fields(
                Field::inst('container.number as num'),
                Field::inst('container.status as flag')
                    ->getFormatter(function ($val) {
                        return  $val ? "FLAGGED" : "UNFLAGGED";
                    }),
                Field::inst('container_isotype_code.code as code'),
                Field::inst('container.gate_status as gstat')
                ->getFormatter(function ($val) {
                    return $val == "" ? "UNGATED IN/OUT" : $val;
                }),
                Field::inst('container.id as sdate')
                    ->getFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select cast(date as date) from gate_record where gate_record.container_id = ? and type = 'GATE IN'");
                        $query->bind = array('i', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    }),
                Field::inst('container.id as days')
                    ->getFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select cast(date as date) from gate_record where gate_record.container_id = ? and type = 'GATE OUT'");
                        $query->bind = array('i', &$val);
                        $run = $query->run();
                        $gate_out_date = $run->fetch_num()[0];
                        $end_date = date('Y-m-d');
                        if ($gate_out_date) {
                            $end_date = $gate_out_date;
                        }
                        $query = new MyQuery();
                        $query->query("select cast(date as date) from gate_record where gate_record.container_id = ? and type = 'GATE IN'");
                        $query->bind = array('i', &$val);
                        $run = $query->run();
                        $gate_in_date = $run->fetch_num()[0];
                        $gate_in_date = new \DateTime($gate_in_date);
                        $end_date = new \DateTime($end_date);

                        $days = $gate_in_date->diff($end_date)->days;

                        return ($days ? $days : 0) + 1;
                    }),
                Field::inst('container.id as stack')
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
                Field::inst('container.id as pos')
                    ->getFormatter(function($val) use ($query){
                        $query->query("select stack,bay,row,tier,yard_activity from yard_log where container_id =?");
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
                Field::inst('agency.name as agnt'),
                Field::inst('gate_record.cond as cond')
            )
            ->leftJoin('shipping_line', 'container.shipping_line_id', '=', 'shipping_line.id')
            ->leftJoin('container_isotype_code', 'container_isotype_code.id', '=', 'container.iso_type_code')
            ->leftJoin('gate_record', 'gate_record.container_id', '=', 'container.id')
            ->leftJoin('depot', 'depot.id', '=', 'gate_record.depot_id')
            ->leftJoin('agency', 'agency.id', '=', 'container.agency_id')
            ->where('shipping_line.id', $line_id, '=')
            ->where('container.gate_status', "GATED IN", '=')
            ->where('container.trade_type_code', $trade_type, 'LIKE')
            ->where(function ($q) {
                $q->where('gate_record.id', '(SELECT id FROM gate_record WHERE type = "GATE IN")', 'IN', false);
            })
            ->process($_POST)
            ->json();
            $query->commit();
    }

    function report(){

            $trade_type = $this->request->param('typ');
            $type =  $this->request->param('rtyp');
            $trade_type = $trade_type == '*' ? '%' : $trade_type;
            $columns = json_decode($this->request->param('src'));
            $headers = json_decode($this->request->param('head'));
            array_pop($columns);
            array_pop($headers);

            $query = new MyQuery();
            $query->query("select distinct(shipping_line.name) as sname,shipping_line.id as ship_id,(select COUNT(id) as 22G1 from container where shipping_line_id = ship_id 
            and container.trade_type_code like ? and container.gate_status in ('GATED IN') and  iso_type_code in (select  id from container_isotype_code where code = '22G1')) as 22G1,
            (select COUNT(id) as 22U1 from container where shipping_line_id = ship_id and container.trade_type_code like ? and container.gate_status in ('GATED IN') 
            and  iso_type_code in (select  id from container_isotype_code where code = '22U1')) as 22U1,(select COUNT(id) as 22P1 from container where shipping_line_id = ship_id 
            and container.trade_type_code like ? and container.gate_status in ('GATED IN') and  iso_type_code in (select  id from container_isotype_code where code = '22P1')) as 22P1,
            (select COUNT(id) as 45G1 from container where shipping_line_id =ship_id and container.trade_type_code like ? and container.gate_status in ('GATED IN') 
            and  iso_type_code in (select  id from container_isotype_code where code = '45G1')) as 45G1,(select COUNT(id) as 42U1 from container where shipping_line_id = ship_id 
            and container.trade_type_code like ? and container.gate_status in ('GATED IN') and  iso_type_code in (select  id from container_isotype_code where code = '42U1')) as 42U1,
            (select COUNT(id) as 45R1 from container where shipping_line_id = ship_id and container.trade_type_code like ? and container.gate_status in ('GATED IN') 
            and  iso_type_code in (select  id from container_isotype_code where code = '45R1')) as 45R1,(select COUNT(id) as 42P1 from container where shipping_line_id = ship_id 
            and container.trade_type_code like ? and container.gate_status in ('GATED IN') and  iso_type_code in (select  id from container_isotype_code where code = '42P1')) as 42P1,
            (select COUNT(container.id) as other_count from container inner join gate_record on gate_record.container_id = container.id where shipping_line_id = ship_id 
            and container.trade_type_code  like ? and container.gate_status in ('GATED IN') and  iso_type_code in (select  id from container_isotype_code where code not in ('22U1','22G1','22P1','45G1','42U1','45R1','42P1'))) as OTHER,
            (select COUNT(id) from container where shipping_line_id = ship_id and container.trade_type_code like ? and container.gate_status in ('GATED IN')) as TU,
            (select sum(container_isotype_code.length)/20 from container left join container_isotype_code on container.iso_type_code = container_isotype_code.id where shipping_line_id = ship_id and container.trade_type_code like ? and container.gate_status in ('GATED IN')) as TTEU
            from container left join shipping_line on container.shipping_line_id = shipping_line.id where container.gate_status = 'GATED IN' and container.trade_type_code like ? order by shipping_line.name asc");
            $query->bind = array('sssssssssss',&$trade_type,&$trade_type,&$trade_type,&$trade_type,&$trade_type,&$trade_type,&$trade_type,&$trade_type,&$trade_type,&$trade_type,&$trade_type);
            $res = $query->run();


            $data = array();

            while ($row = $res->fetch_assoc()) {
                $row_data = array();
    
                foreach ($columns as $col){
                    array_push($row_data,$row[$col]);
                }
                array_push($data, $row_data);
            }
    
            $length = count($headers);
            $width = 190 / $length;
            $widths = array_fill(0, $length, $width);

            $report_generator = new ReportGenerator("", "", "Stock Report", $headers, $data, $widths);

            if($type == "xsl") {
                $report_generator->printExcel();    
            }
            else {
                $report_generator->printPdf();
            }
        }

    function line_report(){

            $date = date('Y-m-d');

            $line_id = $this->request->param('slid');
            $trade_type = $this->request->param('trty');
            $type =  $this->request->param('rtyp');
            $columns = json_decode($this->request->param('src'));
            $headers = json_decode($this->request->param('head'));

            $trade_type_query = '';

            if ($trade_type != '%'){
                $trade_type_query = "and container.trade_type_code= '$trade_type'";
            }

            $ship_query = new MyQuery();
            $ship_query->query("select name from shipping_line where id =?");
            $ship_query->bind = array('i',&$line_id);
            $ship_query->run();
            $ship_result = $ship_query->fetch_assoc();
            $shipping_line = $ship_result['name'];

            $query = new MyQuery();
            $query->query("select container.id as cid,container.status as flag,container.number as num,container_isotype_code.code,container.gate_status as gstat,gate_record.date as sdate,agency.name as agnt,gate_record.cond, 
                    container.id from container left join container_isotype_code on container.iso_type_code = container_isotype_code.id 
                    left join gate_record on container.id = gate_record.container_id left join agency on container.agency_id = agency.id 
                    left join depot on gate_record.depot_id = depot.id where container.shipping_line_id =?  $trade_type_query and container.gate_status = 'GATED IN' and gate_record.type = 'GATE IN'");
            $query->bind = array('i',&$line_id);
            $res = $query->run();

            $data = array();

            while ($row = $res->fetch_assoc()) {
                $row_data = array();

                $state = $row['flag'] == 0 ? "UNFLAGGED" : "FLAGGED";
      
                $stack_query = new MyQuery();
                $stack_query->query("select id,yard_activity from yard_log where container_id=?");
                $stack_query->bind = array('i',&$row['cid']);
                $stack_query->run();
                $stack_result = $stack_query->fetch_assoc();
           

                $days_query = new MyTransactionQuery();
                      $days_query->query("select cast(date as date) from gate_record where gate_record.container_id = ? and type = 'GATE OUT'");
                      $days_query->bind = array('i', &$row['id']);
                      $run = $days_query->run();
                      $gate_out_date = $run->fetch_num()[0];
                      $end_date = date('Y-m-d');
                      if ($gate_out_date) {
                          $end_date = $gate_out_date;
                      }
                      $days_query->query("select cast(date as date) from gate_record where gate_record.container_id = ? and type = 'GATE IN'");
                      $days_query->bind = array('i', &$row['id']);
                      $run = $days_query->run();
                      $gate_in_date = $run->fetch_num()[0];
                      $gate_in_date = new \DateTime($gate_in_date);
                      $end_date = new \DateTime($end_date);
                      $days_query->commit();

                      $days = $gate_in_date->diff($end_date)->days;

                      $days_spent = ($days ? $days : 0) + 1;
    
                foreach ($columns as $col){
                    if($col == "flag"){
                        $row[$col] = $state;
                    }
                    if($col == "stack"){
                        if ((!$stack_result['id'])) {
                            $row[$col] = "Not in Stack";
                        }
                        elseif (($stack_result['yard_activity'] == 'IN STACK') || ($stack_result['yard_activity'] == 'ASSIGN')) {
                            $query = new MyQuery();
                            $query->query("select stack from yard_log where container_id=?");
                            $query->bind = array('i',&$row['cid']);
                            $query->run();
                            $result = $query->fetch_assoc();
                            $row[$col] = $result['stack'];
                        }
                        else{
                            $row[$col] = "Not in Stack";
                        }                       
                    }
                    if ($col == "pos") {
                        if ((!$stack_result['id'])) {
                            $row[$col] = "Not Positioned";
                        }
                        elseif (($stack_result['yard_activity'] == 'IN STACK') || ($stack_result['yard_activity'] == 'ASSIGN')) {
                            $query = new MyQuery();
                            $query->query("select stack,bay,row,tier from yard_log where container_id=?");
                            $query->bind = array('i',&$row['cid']);
                            $query->run();
                            $result = $query->fetch_assoc();
                            $row[$col] = $result['stack'].$result['bay'].$result['row'].$result['tier'];
                        }
                        else{
                            $row[$col] = "Not Positioned";
                        }
                    }
        
                    
                    if($col == "days"){
                        $row[$col] = $days_spent;
                    }
                    array_push($row_data,$row[$col]);
                }
                array_push($data, $row_data);
            }
    
            $length = count($headers);
            $width = 190 / $length;
            $widths = array_fill(0, $length, $width);

            $report_generator = new ReportGenerator("", "", $shipping_line." Report", $headers, $data, $widths);


            if($type == "xsl") {
                $report_generator->printExcel();    
            }
            else {
                $report_generator->printPdf();
            }
    }
  
}
?>