<?php

namespace Api;
// session_start();

use Lib\ACL,
    Lib\MyQuery,
    Lib\User,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    Lib\ReportGenerator,
    Lib\MyTransactionQuery,
    Lib\Respond;

$system_object='invoice-reports';

class InvoiceReport{
    private $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    function table() {

        $db = new Bootstrap();
        $db = $db->database();

        $date = date('Y-m-d');

        $start_date = $this->request->param('stdt');
        $end_date = $this->request->param('eddt');

        $trade_type = $this->request->param('trty');
        $tax_type = $this->request->param('tax');
        $payment_status = $this->request->param('pstat');
        $invoice_status = $this->request->param('istat');

        $query = new MyQuery();
        $query->query('drop view if exists invoice_report');
        $query->run();


        $query = new MyQuery();
        $query_command ="select invoice.deferral_date,invoice.deferral_by,invoice.waiver_by,invoice.cancelled_by,invoice.id,invoice.number,invoice.due_date,invoice.bill_date,invoice.bl_number,invoice.do_number,trade_type.name as trade_type,(invoice.cost + invoice.tax) as total,customer.name 
        as customer, tax_type.name as tax_type, invoice.status,invoice.waiver_pct,invoice.waiver_amount,concat(user.first_name,' ',user.last_name) as full_name, invoice.date,invoice.id as note,
        (select sum(cost) from invoice_details where product_key = 1 and invoice_id=invoice.id) as handling,
        (select sum(cost) from invoice_details where product_key = 2 and invoice_id=invoice.id) as transfer,
        (select sum(invoice_details.cost) from invoice_details left join depot_activity on depot_activity.id = invoice_details.product_key  where depot_activity.name like 'Partial Unstu%' and invoice_id=invoice.id) as partial_unstuff,
        (select sum(cost) from invoice_details where product_key = 7 and invoice_id=invoice.id) as unstuff,
        (select sum(cost) from invoice_details where product_key = 'S' and invoice_id=invoice.id) as p_storage,
        (select sum(cost) from invoice_details where product_key not in (1,2,7,62,63,64,65,67,68,86,'S') and invoice_id=invoice.id) as ancilar,
        (select sum(cost) from invoice_details_tax where description like 'GET%' and invoice_id=invoice.id) as gtax,
        (select sum(cost) from invoice_details_tax where description like 'Covid-19%' and invoice_id=invoice.id) as covtax,
        (select sum(cost) from invoice_details_tax where description like 'NHIL%' and invoice_id=invoice.id) as nhtax, 
        (select sum(cost) from invoice_details_tax where description like 'VAT%' and invoice_id=invoice.id) as vat,
        (select count(distinct(container_id)) FROM invoice_details WHERE invoice_id = invoice.id) as qty from invoice 
        left join customer on customer.id = invoice.customer_id left join tax_type on tax_type.id = invoice.tax_type left join trade_type on trade_type.id = invoice.trade_type 
        left join user on user.id = invoice.user_id where cast(invoice.date as date) between '".$start_date."' and '".$end_date."'
union
        select supplementary_invoice.deferral_by,supplementary_invoice.cancelled_by,supplementary_invoice.waiver_by,supplementary_invoice.deferral_date,supplementary_invoice.id,supplementary_invoice.number,supplementary_invoice.due_date,invoice.bill_date,invoice.bl_number,invoice.do_number,trade_type.name 
        as trade_type,(supplementary_invoice.cost + supplementary_invoice.tax) as total,customer.name as customer, tax_type.name 
        as tax_type,supplementary_invoice.status,supplementary_invoice.waiver_pct,supplementary_invoice.waiver_amount, concat(user.first_name,' ',user.last_name) as full_name, supplementary_invoice.date,supplementary_invoice.id as note,
        (select sum(cost) from supplementary_invoice_details where product_key=1 and invoice_id = supplementary_invoice_details.id) as handling,
        (select sum(cost) from supplementary_invoice_details where product_key=2 and invoice_id = supplementary_invoice_details.id) as transfer,
        (select sum(cost) from supplementary_invoice_details where product_key in (62,63,64,65,67,68,86) and invoice_id=supplementary_invoice_details.id) as partial_unstuff,
        (select sum(cost) from supplementary_invoice_details where product_key = 'S' and invoice_id=supplementary_invoice_details.id) as p_storage,(select sum(cost) from supplementary_invoice_details where product_key = 7 and invoice_id=supplementary_invoice_details.id) as unstuff, 
        (select sum(cost) from supplementary_invoice_details where product_key not in (1,2,7,62,63,64,65,67,68,86,'S') and invoice_id=supplementary_invoice_details.id) as ancilar,
        (select sum(cost) from supplementary_invoice_details_tax where description like 'GET%' and invoice_id=supplementary_invoice_details_tax.id) as gtax,
        (select sum(cost) from supplementary_invoice_details_tax where description like 'Covid-19%' and invoice_id=supplementary_invoice_details_tax.id) as covtax,
        (select sum(cost) from supplementary_invoice_details_tax where description like 'NHIL%' and invoice_id=supplementary_invoice_details_tax.id) as nhtax, 
        (select sum(cost) from supplementary_invoice_details_tax where description like 'VAT%' and invoice_id=supplementary_invoice_details_tax.id) as vat, 
        (select count(distinct(container_id)) FROM supplementary_invoice_details WHERE invoice_id = supplementary_invoice_details.id) as qty
        from supplementary_invoice left join invoice on invoice.id = supplementary_invoice.invoice_id left join customer on customer.id = invoice.customer_id left join tax_type on tax_type.id = invoice.tax_type 
        left join trade_type on trade_type.id = invoice.trade_type left join user on user.id = supplementary_invoice.user_id where cast(supplementary_invoice.date as date) between '".$start_date."' and '".$end_date."'";
        $query->query("create view invoice_report as $query_command");
        $query->run();

        Editor::inst($db)
            ->readTable('invoice_report')
            ->fields(
                Field::inst('trade_type as trty'),
                Field::inst('number as num'),
                Field::inst('bl_number as blnum'),
                Field::inst('do_number as dnum'),
                Field::inst('bill_date as bdate'),
                Field::inst('due_date as ddate'),
                Field::inst('handling as hand'),
                Field::inst('transfer'),
                Field::inst('partial_unstuff as partst')
                    ->getFormatter(function($val){
                        return $val == null ? 0 : $val;
                    }),
                Field::inst('unstuff')
                    ->getFormatter(function($val){
                        return $val == null ? 0 : $val;
                    }),
                Field::inst('ancilar')
                    ->getFormatter(function($val){
                        return $val == null ? 0 : $val;
                    }),
                Field::inst('p_storage as stor')
                    ->getFormatter(function($val){
                        return $val == null ? 0 : $val;
                    }),
                Field::inst('gtax'),
                Field::inst('covtax'),
                Field::inst('nhtax'),
                Field::inst('vat'),
                Field::inst('total'),
                Field::inst('qty'),
                Field::inst('waiver_pct as wpct'),
                Field::inst('waiver_amount as wamt'),
                Field::inst('tax_type as tax'),
                Field::inst('note')
                ->getFormatter(function($val,$data){
                    $query = new MyQuery();
                    $invoice_prefex = substr($data['number'],0,1);

                   if ($invoice_prefex == 'S') {
                        $query->query("SELECT note FROM supplementary_note WHERE invoice_id=?");
                        $query->bind = array('i',&$val);
                   }
                   else{
                        $query->query("SELECT note FROM invoice_note WHERE invoice_id=?");
                        $query->bind = array('i',&$val);
                   }
                    $query->run();

                    if($query->num_rows() > 0){
                        $note_array = array();
                        while($result = $query->fetch_assoc()){
                            array_push($note_array,$result['note']);
                        }
                        return implode(". ",$note_array);
                    }
                    else{
                        return "";
                    }
                }),
                Field::inst('customer as cust'),
                Field::inst('deferral_by as dfnam')
                ->getFormatter(function($val){
                    if($val != 0){
                        return User::getUserById($val);
                    }
                    else{
                        return "NA";
                    }
                }),
                Field::inst('cancelled_by as clnam')
                ->getFormatter(function($val){
                    if($val != 0){
                        return User::getUserById($val);
                    }
                    else{
                        return "NA";
                    }
                }),
                Field::inst('waiver_by as wvnam')
                ->getFormatter(function($val){
                    if($val != 0){
                        return User::getUserById($val);
                    }
                    else{
                        return "NA";
                    }
                }),
                Field::inst('full_name as fname'),
                Field::inst('date'),
                Field::inst('status as stat')
            )
            ->on('preCreate', function ($editor,$values,$system_object='invoice-reports'){
                exit();
            })
            ->on('preGet', function ($editor,$id,$system_object='invoice-reports'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='invoice-reports'){
                exit();
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='invoice-reports'){
                exit();
            })
            ->where(function ($q) use ($payment_status){
               if ($payment_status == 'ALL'){
                   $q->where('number', "(SELECT number FROM invoice_report)",'IN',false);
               }
               if ($payment_status == 'PAID'){
                   $q->where('number','(SELECT number FROM invoice_report WHERE status = "'.$payment_status.'")','IN',false);
               }
                elseif ($payment_status == 'UNPAID'){
                    $q->where('number','(SELECT number FROM invoice_report WHERE status != "PAID")','IN',false);
                }
            })
            ->where(function ($q) use ($invoice_status){
                if ($invoice_status == 'ALL'){
                    $q->where('number', "(SELECT number FROM invoice_report)",'IN',false);
                }
                elseif ($invoice_status == 'CANCELLED' || $invoice_status == 'EXPIRED'){
                    $q->where('number', '(SELECT number FROM invoice_report WHERE status = "'.$invoice_status.'" )','IN',false);
                }
                elseif ($invoice_status == 'DEFERRED'){
                    $q->where('number','(SELECT number FROM invoice_report WHERE deferral_date > 0  AND status NOT IN ("EXPIRED","UNPAID","CANCELLED"))','IN',false);
                }
                elseif ($invoice_status == 'WAIVED'){
                    $q->where('number','(SELECT number FROM invoice_report WHERE waiver_amount > 0)','IN',false);
                }
                elseif ($invoice_status == 'RECALLED'){
                    $q->where('number', '(SELECT number FROM invoice_report WHERE status = "'.$invoice_status.'" )','IN',false);
                }

            })
            ->where(function ($q) use ($trade_type){
                if ($trade_type =="ALL"){
                    $q->where('number', "(SELECT number FROM invoice_report)",'IN',false);
                }
                elseif($trade_type == "1"){
                    $q->where('number','(SELECT number FROM invoice_report WHERE trade_type = "IMPORT")','IN',false);
                }
                elseif($trade_type == "4"){
                    $q->where('number','(SELECT number FROM invoice_report WHERE trade_type = "EXPORT")','IN',false);
                }
                elseif($trade_type == "8"){
                    $q->where('number','(SELECT number FROM invoice_report WHERE trade_type = "EMPTY")','IN',false);
                }
                elseif($trade_type == "3"){
                    $q->where('number','(SELECT number FROM invoice_report WHERE trade_type = "TRANSIT")','IN',false);
                }
            })
            ->where(function ($q) use ($tax_type){
                if ($tax_type == 'ALL'){
                    $q->where('number', "(SELECT number FROM invoice_report)",'IN',false);
                }
                elseif($tax_type == '1'){
                    $q->where('number','(SELECT number FROM invoice_report WHERE tax_type = "Simple")','IN',false);
                }
                elseif($tax_type == '2'){
                    $q->where('number','(SELECT number FROM invoice_report WHERE tax_type = "Compound")','IN',false);
                }
                elseif($tax_type == '3'){
                    $q->where('number','(SELECT number FROM invoice_report WHERE tax_type = "Total Exempt")','IN',false);
                }
                elseif($tax_type == '4'){
                    $q->where('number','(SELECT number FROM invoice_report WHERE tax_type = "Vat Exempt")','IN',false);
                }
            })
            ->process($_POST)
            ->json();
    }

    function report(){
        $date = date('Y-m-d');

        $start_date = $this->request->param('stdt');
        $end_date = $this->request->param('eddt');

        $trade_type = $this->request->param('trty');
        $tax_type = $this->request->param('tax');
        $payment_status = $this->request->param('pstat');
        $invoice_status = $this->request->param('istat');

        $start_date = !$start_date ? $date : $start_date;
        $end_date = !$end_date ? $date : $end_date;
        $type = $this->request->param('type');
        $columns = json_decode($this->request->param('src'));
        $headers = json_decode($this->request->param('head'));

        $not = $payment_status == "UNPAID" ? "NOT" : "";
        if(!$not) {
            if($payment_status == "ALL") {
                $payment_status = "%";
            }
        }
        else {
            $payment_status = "PAID";
        }

        $invoice_deferred = "";
        $supp_deferred = "";
        $invoice_waivered = "";
        $supp_waivered = "";

        switch ($invoice_status){
            case  "ALL":
                $invoice_status = "%";
                break;
            case  "DEFERRED";
                $invoice_status = "%";
                $invoice_deferred = "invoice.deferral_date > 0  AND invoice.status NOT IN ('EXPIRED','UNPAID','CANCELLED') and";
                $supp_deferred = "supplementary_invoice.deferral_date > 0  AND supplementary_invoice.status NOT IN ('EXPIRED','UNPAID','CANCELLED') and";
                break;
            case "WAIVED":
                $invoice_status = "%";
                $invoice_waivered = "invoice.waiver_amount > 0  and";
                $supp_waivered = "supplementary_invoice.waiver_amount > 0  and";
                break;
        }

        if($trade_type == "ALL"){
            $trade_type = '%';
        }

        if($tax_type == "ALL"){
            $tax_type = "%";
        }

        $query = new MyQuery();

        
        $q ="select trade_type.name as trty,invoice.number as num,invoice.bl_number as blnum,invoice.do_number as dnum,invoice.bill_date as bdate,invoice.due_date as ddate,(invoice.cost + invoice.tax) as total,
                invoice.waiver_pct as wpct,invoice.deferral_by,invoice.waiver_by,invoice.cancelled_by,invoice.waiver_amount as wamt, tax_type.name as tax,invoice.id as note,customer.name as cust,
                concat(user.first_name,user.last_name) as fname,invoice.date, invoice.status as stat, invoice.id,invoice.deferral_date, 
                (select sum(cost) from invoice_details where product_key = 1 and invoice_id=invoice.id) as hand,
                (select sum(cost) from invoice_details where product_key = 2 and invoice_id=invoice.id) as transfer,
                (select sum(cost) from invoice_details where product_key in (62,63,64,65,67,68,86) and invoice_id=invoice.id) as partst,
                (select sum(cost) from invoice_details where product_key = 7 and invoice_id=invoice.id) as unstuff,
                (select sum(cost) from invoice_details where product_key = 'S' and invoice_id=invoice.id) as stor,
                (select sum(cost) from invoice_details where product_key not in (1,2,7,62,63,64,65,67,68,86,'S') and invoice_id=invoice.id) as ancilar,
                (select sum(cost) from invoice_details_tax where description like 'GET%' and invoice_id=invoice.id) as gtax,
                (select sum(cost) from invoice_details_tax where description like 'Covid-19%' and invoice_id=invoice.id) as covtax,
                (select sum(cost) from invoice_details_tax where description like 'NHIL%' and invoice_id=invoice.id) as nhtax, 
                (select sum(cost) from invoice_details_tax where description like 'VAT%' and invoice_id=invoice.id) as vat,
                (select count(distinct(container_id)) FROM invoice_details WHERE invoice_id = invoice.id) as qty
                from invoice left join customer on customer.id = invoice.customer_id 
                left join tax_type on tax_type.id = invoice.tax_type 
                left join trade_type on trade_type.id = invoice.trade_type 
                left join user on user.id = invoice.user_id 
                where $invoice_deferred $invoice_waivered invoice.status $not LIKE ? and  invoice.status like ? and trade_type.id like ? and cast(invoice.date as date) between ? and ? and tax_type.id LIKE ?
              union
                select trade_type.name as trty,supplementary_invoice.number as num,invoice.bl_number as blnum,invoice.do_number as dnum,invoice.bill_date as bdate,supplementary_invoice.due_date as ddate,(supplementary_invoice.cost + supplementary_invoice.tax) as total,
                supplementary_invoice.waiver_pct as wpct,supplementary_invoice.deferral_by,supplementary_invoice.waiver_by,supplementary_invoice.cancelled_by,supplementary_invoice.waiver_amount as wamt,tax_type.name as tax,
                supplementary_invoice.id as note,customer.name as cust,concat(user.first_name,user.last_name) as fname,supplementary_invoice.date,supplementary_invoice.status as stat,supplementary_invoice.deferral_date,supplementary_invoice.id,
                (select sum(cost) from supplementary_invoice_details where product_key=1 and invoice_id = supplementary_invoice_details.id) as hand,
        (select sum(cost) from supplementary_invoice_details where product_key=2 and invoice_id = supplementary_invoice_details.id) as transfer,
        (select sum(cost) from supplementary_invoice_details where product_key in (62,63,64,65,67,68,86) and invoice_id=supplementary_invoice_details.id) as partst,
        (select sum(cost) from supplementary_invoice_details where product_key = 'S' and invoice_id=supplementary_invoice_details.id) as stor,(select sum(cost) from supplementary_invoice_details where product_key = 7 and invoice_id=supplementary_invoice_details.id) as unstuff, 
        (select sum(cost) from supplementary_invoice_details where product_key not in (1,2,7,62,63,64,65,67,68,86,'S') and invoice_id=supplementary_invoice_details.id) as ancilar,
        (select sum(cost) from supplementary_invoice_details_tax where description like 'GET%' and invoice_id=supplementary_invoice_details_tax.id) as gtax,
        (select sum(cost) from supplementary_invoice_details_tax where description like 'Covid-19%' and invoice_id=supplementary_invoice_details_tax.id) as covtax,
        (select sum(cost) from supplementary_invoice_details_tax where description like 'NHIL%' and invoice_id=supplementary_invoice_details_tax.id) as nhtax, 
        (select sum(cost) from supplementary_invoice_details_tax where description like 'VAT%' and invoice_id=supplementary_invoice_details_tax.id) as vat, 
        (select count(distinct(container_id)) FROM supplementary_invoice_details WHERE invoice_id = supplementary_invoice_details.id) as qty
                from supplementary_invoice 
                left join invoice on invoice.id = supplementary_invoice.invoice_id 
                left join customer on customer.id = invoice.customer_id 
                left join tax_type on tax_type.id = invoice.tax_type 
                left join trade_type on trade_type.id = invoice.trade_type 
                left join user on user.id = supplementary_invoice.user_id 
                where  $supp_deferred $supp_waivered supplementary_invoice.status $not LIKE ? and supplementary_invoice.status like ? and trade_type.id like ? and cast(supplementary_invoice.date as date) between ? and ?  and tax_type.id LIKE ?";
        $query->query($q);
        $query->bind = array('ssssssssssss', &$payment_status, &$invoice_status, &$trade_type, &$start_date, &$end_date, &$tax_type, &$payment_status, &$invoice_status, &$trade_type, &$start_date, &$end_date, &$tax_type );
        $run = $query->run();

        $data = array();

        while ($row = $run->fetch_assoc()) {
            $row_data = array();

            foreach ($columns as $col){
                if($col == 'wvnam'){
                    if($row['waiver_by'] == 0){
                        $row[$col] = 'NA';
                    }
                    else{
                        $row[$col] = User::getUserById($row['waiver_by']);
                    }
                }
                if($col == 'clnam'){
                    if($row['cancelled_by'] == 0){
                        $row[$col] = 'NA';
                    }
                    else{
                        $row[$col] = User::getUserById($row['cancelled_by']);
                    }
                }
                if($col == 'dfnam'){
                    if($row['deferral_by'] == 0){
                        $row[$col] = 'NA';
                    }
                    else{
                        $row[$col] = User::getUserById($row['deferral_by']);
                    }
                }
                if($col == 'note'){
                    $query = new MyQuery();
                    $invoice_prefex = substr($row['num'],0,1);
                   if ($invoice_prefex == 'S') {
                        $query->query("SELECT note FROM supplementary_note WHERE invoice_id=?");
                        $query->bind = array('i',&$row['note']);
                   }
                   else{
                        $query->query("SELECT note FROM invoice_note WHERE invoice_id=?");
                        $query->bind = array('i',&$row['note']);
                   }
                    $query->run();
                    if($query->num_rows() > 0){
                        $note_array = array();
                        while($result = $query->fetch_assoc()){
                            array_push($note_array,$result['note']);
                        }
                        $row[$col] = implode(". ",$note_array);
                    }
                    else{
                        $row[$col] = "";
                    }
                }
                
                array_push($row_data,$row[$col]);
            }
            array_push($data, $row_data);
        }

        $totals =$this->get_totals_array($start_date,$end_date,$trade_type,$tax_type,$invoice_status,$payment_status);

        $visible_totals = array();

        foreach ($columns as $column) {
            array_push($visible_totals, is_null($totals[$column]) ? "" : $totals[$column]);
        }

        $length = count($headers);
        $width = 190 / $length;
        $widths = array_fill(0, $length, $width);

        $report_generator = new ReportGenerator($start_date, $end_date, "Invoice Report", $headers, $data, $widths,$visible_totals);

        if($type == "pdf") {
            $report_generator->printPdf();
        }
        else {
            $report_generator->printExcel();
        }
    }

    function total_cost(){
        $start_date = $this->request->param('stdt');
        $end_date = $this->request->param('eddt');
        $trade_type = $this->request->param('trty');
        $tax_type = $this->request->param('tax');
        $payment_status = $this->request->param('pstat');
        $invoice_status = $this->request->param('istat');

        $totals = $this->get_totals_array($start_date,$end_date,$trade_type,$tax_type,$invoice_status,$payment_status);

        new Respond(232, array('total' => $totals['total'],'qty'=>$totals['qty'],'stor'=>$totals['stor'],'hand'=>$totals['hand'],'transfer'=>$totals['transfer'],'partst'=>$totals['partst'],'unstuff'=>$totals['unstuff'],'ancilar'=>$totals['ancilar'],'gtax'=>$totals['gtax'],'nhtax'=>$totals['nhtax'],'vat'=>$totals['vat'],'covtax'=>$totals['covtax'],'wpct' => $totals['wpct'],'wamt' => $totals['wamt']));
    }

    private function get_totals_array($start_date,$end_date,$trade_type,$tax_type,$invoice_status,$payment_status){
        $trade_type = $trade_type == 'ALL' ? "%" : $trade_type;
        $tax_type = $tax_type == "ALL" ? "%" : $tax_type;
        $invoice_status = $invoice_status == 'ALL' ? "%" : $invoice_status;
        $payment_status = $payment_status == 'ALL' ? "%" : $payment_status;


        $query = new MyTransactionQuery();
        $query->query("select sum(cost + tax) as total from invoice 
                where cast(date as date) between ?  and ? and trade_type like ? and tax_type like ? and status like ? and status like ?");
        $query->bind = array('ssssss',&$start_date,&$end_date,&$trade_type,&$tax_type,&$invoice_status,&$payment_status);
        $query->run();
        $result = $query->fetch_assoc();
        $total_cost = $result['total'];
        $total_cost = $total_cost == null ? 0 : $total_cost;


        $query->query("select sum(supplementary_invoice.cost + supplementary_invoice.tax) as total_cost
                from supplementary_invoice left join invoice on invoice.id = supplementary_invoice.invoice_id 
                where cast(supplementary_invoice.date as date) between ? and ? and invoice.trade_type like ? and invoice.tax_type like ? and  supplementary_invoice.status like ? and supplementary_invoice.status like ?");
        $query->bind = array('ssssss',&$start_date,&$end_date,&$trade_type,&$tax_type,&$invoice_status,&$payment_status);
        $query->run();
        $total_result = $query->fetch_assoc();
        $total = $total_result['total_cost'];

        $total = $total == null ? 0 : $total;
        $invoice_total_cost = $total_cost + $total;

        $query->query("select sum(waiver_amount) from 
        (select invoice.waiver_amount as waiver_amount,tax_type,code,invoice.status as invoice_status,invoice.status as payment_status,invoice.date as date from invoice left join trade_type on trade_type.id = invoice.trade_type left join tax_type on tax_type.id = invoice.tax_type
         UNION
         select supplementary_invoice.waiver_amount as waiver_amount,tax_type,code,supplementary_invoice.status as invoice_status,supplementary_invoice.status as payment_status,supplementary_invoice.date as date from supplementary_invoice left join invoice on invoice.id = supplementary_invoice.invoice_id left join trade_type on trade_type.id = invoice.trade_type left join tax_type on tax_type.id = invoice.tax_type
        ) as view where cast(date as date) between ? and ? and code like ? and tax_type like ? and invoice_status like ? and payment_status like ?");
        $query->bind =  array('ssssss',&$start_date,&$end_date,&$trade_type, &$tax_type, &$invoice_status,&$payment_status);
        $query->run();
        $waiver_amount_total = number_format($query->fetch_num()[0],2) ?? "0.00";

        $query->query("select sum(waiver_percent) as waiver_percent from 
        (select invoice.waiver_pct as waiver_percent,tax_type,code,invoice.status as invoice_status,invoice.status as payment_status,invoice.date as date from invoice left join trade_type on trade_type.id = invoice.trade_type left join tax_type on tax_type.id = invoice.tax_type
         UNION
         select supplementary_invoice.waiver_pct as waiver_percent,tax_type,code,supplementary_invoice.status as invoice_status,supplementary_invoice.status as payment_status,supplementary_invoice.date as date from supplementary_invoice left join invoice on invoice.id = supplementary_invoice.invoice_id left join trade_type on trade_type.id = invoice.trade_type left join tax_type on tax_type.id = invoice.tax_type
        ) as view where cast(date as date) between ? and ? and code like ? and tax_type like ? and invoice_status like ? and payment_status like ?");
        $query->bind =  array('ssssss',&$start_date,&$end_date,&$trade_type, &$tax_type, &$invoice_status,&$payment_status);
        $query->run();
        $waiver_percent = $query->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
        (select invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, invoice.status as status, invoice.date as date, code, invoice_details_tax.description as description
              from invoice 
              inner join payment on payment.invoice_id = invoice.id
              inner join invoice_details_tax on invoice.id  = invoice_details_tax.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type                
              inner join payment_mode on payment_mode.id = payment.mode
          UNION
          select supplementary_invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, supplementary_invoice.date as date, code, supplementary_invoice_details_tax.description, supplementary_invoice.status as status
              from supplementary_invoice 
                 inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id   
              inner join invoice on invoice.id = supplementary_invoice.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type  
              inner join supplementary_invoice_details_tax on supplementary_invoice.id  = supplementary_invoice_details_tax.invoice_id
              inner join payment_mode on payment_mode.id = supplementary_payment.mode
                          ) as view 
              where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ? and status like ?
        and description like \"GET Fund%\"");
        $query->bind =  array('ssssss', &$tax_type, &$trade_type, &$start_date, &$end_date, &$payment_status, &$invoice_status);
        $run =  $query->run();
        $get_fund = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
        (select invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, invoice.status as status, invoice.date as date, code, invoice_details_tax.description as description
              from invoice 
              inner join payment on payment.invoice_id = invoice.id
              inner join invoice_details_tax on invoice.id  = invoice_details_tax.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type                
              inner join payment_mode on payment_mode.id = payment.mode
          UNION
          select supplementary_invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, supplementary_invoice.date as date, code, supplementary_invoice_details_tax.description, supplementary_invoice.status as status
              from supplementary_invoice 
                 inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id   
              inner join invoice on invoice.id = supplementary_invoice.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type  
              inner join supplementary_invoice_details_tax on supplementary_invoice.id  = supplementary_invoice_details_tax.invoice_id
              inner join payment_mode on payment_mode.id = supplementary_payment.mode
                          ) as view 
              where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ? and status like ?
        and description like \"VAT%\"");
        $query->bind =  array('ssssss', &$tax_type, &$trade_type, &$start_date, &$end_date, &$payment_status, &$invoice_status);
        $run =  $query->run();
        $vat = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
        (select invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, invoice.status as status, invoice.date as date, code, invoice_details_tax.description as description
              from invoice 
              inner join payment on payment.invoice_id = invoice.id
              inner join invoice_details_tax on invoice.id  = invoice_details_tax.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type                
              inner join payment_mode on payment_mode.id = payment.mode
          UNION
          select supplementary_invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, supplementary_invoice.date as date, code, supplementary_invoice_details_tax.description, supplementary_invoice.status as status
              from supplementary_invoice 
                 inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id   
              inner join invoice on invoice.id = supplementary_invoice.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type  
              inner join supplementary_invoice_details_tax on supplementary_invoice.id  = supplementary_invoice_details_tax.invoice_id
              inner join payment_mode on payment_mode.id = supplementary_payment.mode
                          ) as view 
              where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ? and status like ?
        and description like \"NHIL%\"");
        $query->bind =  array('ssssss', &$tax_type, &$trade_type, &$start_date, &$end_date, &$payment_status,&$invoice_status);
        $run =  $query->run();
        $nhil = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from 
        (select invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, invoice.status as status, invoice.date as date, code, invoice_details_tax.description as description
              from invoice 
              inner join payment on payment.invoice_id = invoice.id
              inner join invoice_details_tax on invoice.id  = invoice_details_tax.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type                
              inner join payment_mode on payment_mode.id = payment.mode
          UNION
          select supplementary_invoice_details_tax.cost as cost, tax_type, payment_mode.name as mode, supplementary_invoice.date as date, code, supplementary_invoice_details_tax.description, supplementary_invoice.status as status
              from supplementary_invoice 
                 inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id   
              inner join invoice on invoice.id = supplementary_invoice.invoice_id
              inner join trade_type on trade_type.id = invoice.trade_type  
              inner join supplementary_invoice_details_tax on supplementary_invoice.id  = supplementary_invoice_details_tax.invoice_id
              inner join payment_mode on payment_mode.id = supplementary_payment.mode
                          ) as view 
              where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ? and status like ?
        and description like \"Covid-19%\"");
        $query->bind =  array('ssssss', &$tax_type, &$trade_type, &$start_date, &$end_date, &$payment_status, &$invoice_status);
        $run =  $query->run();
        $covid = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from (select invoice_details.cost as cost,invoice.tax_type as tax_type, payment_mode.name as mode, invoice.status as status, invoice.date as date, code, invoice_details.product_key as product_key from invoice_details 
        inner join invoice on invoice.id = invoice_details.invoice_id
        inner join payment on payment.invoice_id = invoice.id
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = payment.mode
        UNION
        select supplementary_invoice_details.cost as cost,invoice.tax_type as tax_type, payment_mode.name as mode, supplementary_invoice.status as status, invoice.date as date, code, supplementary_invoice_details.product_key as product_key from supplementary_invoice_details 
        inner join supplementary_invoice on supplementary_invoice.id = supplementary_invoice_details.invoice_id
        inner join invoice on invoice.id = supplementary_invoice.invoice_id
        inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id 
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = supplementary_payment.mode   
       ) as view 
         where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ? and status like ?
        and product_key = 1");
        $query->bind =  array('ssssss', &$tax_type, &$trade_type, &$start_date, &$end_date, &$payment_status, &$invoice_status);
        $run =  $query->run();
        $handling = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from (select invoice_details.cost as cost,invoice.tax_type as tax_type, payment_mode.name as mode, invoice.status as status, invoice.date as date, code, invoice_details.product_key as product_key from invoice_details 
        inner join invoice on invoice.id = invoice_details.invoice_id
        inner join payment on payment.invoice_id = invoice.id
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = payment.mode
        UNION
        select supplementary_invoice_details.cost as cost,invoice.tax_type as tax_type, payment_mode.name as mode, supplementary_invoice.status as status, invoice.date as date, code, supplementary_invoice_details.product_key as product_key from supplementary_invoice_details 
        inner join supplementary_invoice on supplementary_invoice.id = supplementary_invoice_details.invoice_id
        inner join invoice on invoice.id = supplementary_invoice.invoice_id
        inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id 
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = supplementary_payment.mode   
       ) as view 
         where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ? and status like ?
        and product_key = 2");
        $query->bind =  array('ssssss', &$tax_type, &$trade_type, &$start_date, &$end_date, &$payment_status, &$invoice_status);
        $run =  $query->run();
        $transfer = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from (select invoice_details.cost as cost,invoice.tax_type as tax_type, payment_mode.name as mode, invoice.status as status, invoice.date as date, code, depot_activity.name as product_key from invoice_details 
        inner join invoice on invoice.id = invoice_details.invoice_id
        inner join payment on payment.invoice_id = invoice.id
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = payment.mode
        inner join depot_activity on depot_activity.id = invoice_details.product_key
        UNION
        select supplementary_invoice_details.cost as cost,invoice.tax_type as tax_type, payment_mode.name as mode, supplementary_invoice.status as status, invoice.date as date, code, depot_activity.name as product_key from supplementary_invoice_details 
        inner join supplementary_invoice on supplementary_invoice.id = supplementary_invoice_details.invoice_id
        inner join invoice on invoice.id = supplementary_invoice.invoice_id
        inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id 
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = supplementary_payment.mode
        inner join depot_activity on depot_activity.id = supplementary_invoice_details.product_key    
       ) as view 
         where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ? and status like ?
        and product_key like 'Partial Unstu%'");
        $query->bind =  array('ssssss', &$tax_type, &$trade_type, &$start_date, &$end_date, &$payment_status, &$invoice_status);
        $run =  $query->run();
        $partial_unstuff = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from (select invoice_details.cost as cost,invoice.tax_type as tax_type, payment_mode.name as mode, invoice.status as status, invoice.date as date, code, invoice_details.product_key as product_key from invoice_details 
        inner join invoice on invoice.id = invoice_details.invoice_id
        inner join payment on payment.invoice_id = invoice.id
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = payment.mode
        UNION
        select supplementary_invoice_details.cost as cost,invoice.tax_type as tax_type, payment_mode.name as mode, supplementary_invoice.status as status, invoice.date as date, code, supplementary_invoice_details.product_key as product_key from supplementary_invoice_details 
        inner join supplementary_invoice on supplementary_invoice.id = supplementary_invoice_details.invoice_id
        inner join invoice on invoice.id = supplementary_invoice.invoice_id
        inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id 
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = supplementary_payment.mode   
       ) as view 
         where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ? and status like ?
        and product_key = 7");
        $query->bind =  array('ssssss', &$tax_type, &$trade_type, &$start_date, &$end_date, &$payment_status, &$invoice_status);
        $run =  $query->run();
        $unstuffing = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from (select invoice_details.cost as cost,invoice.tax_type as tax_type, payment_mode.name as mode, invoice.status as status, invoice.date as date, code, invoice_details.product_key as product_key from invoice_details 
        inner join invoice on invoice.id = invoice_details.invoice_id
        inner join payment on payment.invoice_id = invoice.id
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = payment.mode
        UNION
        select supplementary_invoice_details.cost as cost,invoice.tax_type as tax_type, payment_mode.name as mode, supplementary_invoice.status as status, invoice.date as date, code, supplementary_invoice_details.product_key as product_key from supplementary_invoice_details 
        inner join supplementary_invoice on supplementary_invoice.id = supplementary_invoice_details.invoice_id
        inner join invoice on invoice.id = supplementary_invoice.invoice_id
        inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id 
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = supplementary_payment.mode   
       ) as view 
         where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ? and status like ?
        and product_key not in (1,2,7,62,63,64,65,67,68,86,'S')");
        $query->bind =  array('ssssss', &$tax_type, &$trade_type, &$start_date, &$end_date, &$payment_status, &$invoice_status);
        $run =  $query->run();
        $ancillary_costs = $run->fetch_num()[0] ?? "0.00";

        $query->query("select sum(cost) from (select invoice_details.cost as cost,invoice.tax_type as tax_type, payment_mode.name as mode, invoice.status as status, invoice.date as date, code, invoice_details.product_key as product_key from invoice_details 
        inner join invoice on invoice.id = invoice_details.invoice_id
        inner join payment on payment.invoice_id = invoice.id
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = payment.mode
        UNION
        select supplementary_invoice_details.cost as cost,invoice.tax_type as tax_type, payment_mode.name as mode, supplementary_invoice.status as status, invoice.date as date, code, supplementary_invoice_details.product_key as product_key from supplementary_invoice_details 
        inner join supplementary_invoice on supplementary_invoice.id = supplementary_invoice_details.invoice_id
        inner join invoice on invoice.id = supplementary_invoice.invoice_id
        inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id 
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = supplementary_payment.mode   
       ) as view 
         where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ? and status like ?
        and product_key = 'S'");
        $query->bind =  array('ssssss', &$tax_type, &$trade_type, &$start_date, &$end_date, &$payment_status, &$invoice_status);
        $run =  $query->run();
        $storage_cost = $run->fetch_num()[0] ?? "0.00";

        $query->query("select count(distinct(qty)) from (select invoice_details.container_id as qty,invoice.tax_type as tax_type, payment_mode.name as mode, invoice.status as status, invoice.date as date, code from invoice_details 
        inner join invoice on invoice.id = invoice_details.invoice_id
        inner join payment on payment.invoice_id = invoice.id
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = payment.mode
        UNION
        select supplementary_invoice_details.container_id as qty,invoice.tax_type as tax_type, payment_mode.name as mode, supplementary_invoice.status as status, invoice.date as date, code from supplementary_invoice_details 
        inner join supplementary_invoice on supplementary_invoice.id = supplementary_invoice_details.invoice_id
        inner join invoice on invoice.id = supplementary_invoice.invoice_id
        inner join supplementary_payment on supplementary_payment.invoice_id = supplementary_invoice.id 
        inner join trade_type on trade_type.id = invoice.trade_type                
        inner join payment_mode on payment_mode.id = supplementary_payment.mode   
       ) as view 
         where tax_type like ? and code like ? and cast(date as date) between ? and ? and mode like ? and status like ?");
        $query->bind =  array('ssssss', &$tax_type, &$trade_type, &$start_date, &$end_date, &$payment_status, &$invoice_status);
        $run =  $query->run();
        $container_qty = $run->fetch_num()[0] ?? "0";

        $totals_array = array();
        $totals_array['total'] = $invoice_total_cost <= 0 || $invoice_total_cost == null ? "0.00" : number_format($invoice_total_cost,2);
        $totals_array['wamt'] = $waiver_amount_total;
        $totals_array['wpct'] = number_format($waiver_percent,2);
        $totals_array['gtax'] = number_format($get_fund,2);
        $totals_array['vat'] = number_format($vat,2);
        $totals_array['nhtax'] = number_format($nhil,2);
        $totals_array['covtax'] = number_format($covid,2);
        $totals_array['hand'] = number_format($handling,2);
        $totals_array['transfer'] = number_format($transfer,2);
        $totals_array['partst'] = number_format($partial_unstuff,2);
        $totals_array['unstuff'] = number_format($unstuffing,2);
        $totals_array['ancilar'] = number_format($ancillary_costs,2);
        $totals_array['stor'] = number_format($storage_cost,2);
        $totals_array['qty'] = $container_qty;
        return $totals_array;
    }

}   


?>