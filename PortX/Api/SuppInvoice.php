<?php

namespace Api;
session_start();

use
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\MyQuery,
    Lib\Respond,
    Lib\InvoiceBilling,
    Api\InvoicePreview,
    Lib\MyTransactionQuery,
    Lib\StorageCharges;

class SuppInvoice{

    private $request;
    public $invoice_id;
    public $customer_id;
    public $storage_date;

    function __construct($request){
        $this->request = $request;
    }

    function table() {
        $unapproved = $this->request->param('unap');
        $pay = $this->request->param('payo');
        $set = isset($unapproved);

        $db = new Bootstrap();
        $db = $db->database();
        $query = new MyTransactionQuery();
        Editor::inst( $db, 'supplementary_invoice' )
            ->fields(
                Field::inst('invoice.trade_type as type')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('supplementary_invoice.number as spnum')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.bl_number as blnum')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.do_number as dnum')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('supplementary_invoice.bill_date as bdate')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('supplementary_invoice.due_date as ddate')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('supplementary_invoice.cost as cost')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('supplementary_invoice.tax as tax')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.tax_type as txtyp')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('supplementary_invoice.id as note')
                    ->getFormatter(function($val){
                        $query = new MyQuery();
                        $query->query("SELECT note FROM supplementary_note WHERE invoice_id=?");
                        $query->bind = array('i',&$val);
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
                Field::inst('invoice.customer_id')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('supplementary_invoice.user_id as uid')
                    ->setFormatter(function($val) {
                        return isset($_SESSION['id']) ? $_SESSION['id'] : 1;
                    })
                    ->getFormatter(function ($val) use($query){
                        $query->query("SELECT first_name, last_name FROM user WHERE id = '$val'");
                        $query->run();
                        $result = $query->fetch_assoc();
                        $first_name = $result['first_name'];
                        $last_name = $result['last_name'];
                        $full_name = "$first_name"." "."$last_name";
                        return $full_name ?? '';
                    }),
                Field::inst('supplementary_invoice.date as date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('supplementary_invoice.status as stat')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('supplementary_invoice.approved as appr')
                    ->getFormatter(function ($val) {
                        return $val ? "YES" : "NO";
                    }),
                Field::inst('trade_type.name as ttyp'),
                Field::inst('tax_type.name as txnam'),
                Field::inst('customer.name as name'),
                Field::inst('currency.code as code'),
                Field::inst('customer.id as cust')
                    ->getFormatter(function($val, $row) use($query){
                        $query->query("select outstanding from supplementary_payment where invoice_id = ". $row['supplementary_invoice.id'] . " order by date desc");
                        $query->run();
                        $outstanding = $query->fetch_num()[0] ?? '';
                        if (!$outstanding) {
                            $qu=new MyQuery();
                            $qu->query("select (cost + tax) as cost from supplementary_invoice where number = '" . $row['supplementary_invoice.number'] . "'");
                            $res=$qu->run();
                            $result = $res->fetch_assoc();
                            $cost = $result['cost'];
                            return $cost;
                        } else
                            return $outstanding;
                    }),
                Field::inst('supplementary_invoice.id as invn')
                ->getFormatter(function($val){
                    $query = new MyQuery();
                    $query->query("select id from supplementary_note where invoice_id=?");
                    $query->bind = array('i',&$val);
                    $query->run();
                    $result = $query->fetch_assoc();
                    return $result['id'];
                }),
                Field::inst('supplementary_invoice.id as invs')
                ->getFormatter(function($val){
                    return $this->check_invoice_status($val) ? 'recallable' : 'not_recallable';
                }),
                Field::inst('supplementary_invoice.id as invi'),
                Field::inst('supplementary_invoice.id as supid')
                    ->getFormatter(function ($val, $row){
                        $qu=new MyQuery();
                        $qu->query("SELECT invoice_id, outstanding FROM supplementary_payment WHERE invoice_id = '$val' ");
                        $res=$qu->run();
                        return $res->fetch_num()[0] ?? '';
                    })
            )
            ->leftJoin('invoice','invoice.id','=','supplementary_invoice.invoice_id')
            ->leftJoin('trade_type', 'trade_type.id', '=', 'invoice.trade_type')
            ->leftJoin('tax_type', 'tax_type.id', '=', 'invoice.tax_type')
            ->leftJoin('customer', 'customer.id', '=', 'invoice.customer_id')
            ->leftJoin('currency', 'currency.id', '=', 'invoice.currency')
            ->where(function ($q) use ($unapproved, $set, $pay) {
                if ($unapproved) {
                    $q->where('supplementary_invoice.id', "(SELECT id FROM supplementary_invoice WHERE status != 'CANCELLED' and status != 'EXPIRED' and status != 'PAID' and approved = 0)", 'IN', false);
                }

                if($set && !$unapproved){
                    $q->where('supplementary_invoice.id', "(SELECT id FROM supplementary_invoice WHERE status != 'CANCELLED' and status != 'EXPIRED' ". (!$pay ? "and status != 'PAID'": "")." and approved = 1)", 'IN', false);
                }
            })
            ->process($_POST)
            ->json();
        $query->commit();

    }

    
    private function check_invoice_status($invoice_id){
        $query = new MyQuery();
        $query->query("select cancelled from supplementary_status where invoice_id=?");
        $query->bind = array("i",&$invoice_id);
        $query->run();
        $result = $query->fetch_assoc();
        if($result['cancelled'] == 1){
            return true;
        }
        else{
            return false;
        }
    }

    public function sup_containers(){
        $sup_invoice_number = $this->request->param('invn');

        $query = new MyTransactionQuery();
        $query->query("select id,currency,customer_id,trade_type,due_date, status from invoice where number =?");
        $query->bind = array('s',&$sup_invoice_number);
        $query->run();
        $main_due_date = $query->fetch_assoc();
        $this->invoice_id = $main_due_date['id'];
        $invoice_status = $main_due_date['status'];
        // $current_date = date('Y-m-d');
        $current_date = '2022-02-09';

        $response = array();

        $query->query("select due_date from supplementary_invoice where invoice_id =? and status !='CANCELLED' and status !='EXPIRED' order by id desc");
        $query->bind = array('i',&$this->invoice_id);
        $query->run();

        $last_due_date = $query->num_rows() > 0 ? $query->fetch_assoc()['due_date'] : $main_due_date['due_date'];


        
        if (!$this->invoice_id){
            $query->commit();
            new Respond(216);
        }
        elseif ($invoice_status == 'CANCELLED'){
            $query->commit();
            new Respond(217);
        }
        elseif ($invoice_status == 'EXPIRED'){
            $query->commit();
            new Respond(218);
        }
        elseif ($invoice_status != 'PAID'){
            $query->commit();
            new Respond(220);
        }
        elseif ($current_date > $last_due_date){

            $query->query("select distinct container.number,container_isotype_code.length from invoice_container 
                  inner join container on container.id = invoice_container.container_id 
                  inner join invoice on invoice.id = invoice_container.invoice_id 
                  left join container_isotype_code on container_isotype_code.id = container.iso_type_code where current_date > invoice.due_date 
                  and invoice.id =? and container.gate_status <> 'GATED OUT' and invoice.status='PAID'");
            $query->bind = array('i',&$this->invoice_id);
            $query->run();
            $result2 = $query->fetch_all();

            if ($result2) {
                $container_numbers = array();
                foreach ($result2 as $row) {
                    array_push($container_numbers, $row[0]." (".$row[1]." Ft.)");
                }
                $response["cntr"] = $container_numbers;

                new Respond(2000,$response);
            }
            else{
                new Respond(130);
            }
            $query->commit();
        }

        else{
            $query->query("select distinct container.number, container_isotype_code.length from invoice_container inner join container
                on container.id = invoice_container.container_id  inner join container_log on container_log.container_id = container.id left join container_isotype_code on container_isotype_code.id = container.iso_type_code
                where invoice_container.invoice_id =? and container.gate_status <> 'GATED OUT'");
            $query->bind = array('i',&$this->invoice_id);
            $query->run();
            $result2 = $query->fetch_all();

            if ($result2) {
                $container_numbers = array();
                foreach ($result2 as $row) {
                    array_push($container_numbers, $row[0]." (".$row[1]." Ft.)");

                }
                $response["cntr"] = $container_numbers;
                $query->commit();
                new Respond(2000,$response);
            }
            else{
                $query->commit();
                new Respond(130,$response);
            }
        }
        $query->commit();

    }

    public function check_date(){
        $containers = $this->request->param('cntrs');
        $gen_sup_invoice = new InvoiceBilling();
        $gen_sup_invoice->main_invoice = $this->request->param('sinv');
        $gen_sup_invoice->p_date = $this->request->param('cdate');

        $container_number  = json_decode($containers);
        $number_container = implode('", "', $container_number);

        $query = new MyTransactionQuery();
        $query->query("select distinct(invoice.due_date),container.id as container_id,gate_record.ucl_status, container.status, invoice.id, invoice.tax_type, invoice.trade_type, currency.code from invoice_container 
              left join container on invoice_container.container_id = container.id 
              left join invoice on invoice.id = invoice_container.invoice_id 
              left  join currency on invoice.currency = currency.id
              left join gate_record on gate_record.container_id = container.id
              where container.number  in (\"" . $number_container . "\") and container.gate_status != 'GATED OUT'");
        $query->run();
        $result_date = $query->fetch_assoc();
        $invoice_id =  $result_date['id'];

        if ($result_date["ucl_status"] == 1){
            $this->check_activity($query,$number_container);
        }

        $gen_sup_invoice->due_date2 = $result_date['due_date'];
        $query->query("select due_date from supplementary_invoice where invoice_id=?  and status !='CANCELLED' and status != 'EXPIRED' order by id desc");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        if($query->num_rows() > 0) {
            $sup_due_date = $query->fetch_assoc();
            $gen_sup_invoice->due_date2 = $sup_due_date['due_date'];
        }

        $gen_sup_invoice->trade_type = $result_date['trade_type'];
        $gen_sup_invoice->tax_type = $result_date['tax_type'];
        $gen_sup_invoice->invoice_id = $invoice_id;
        $gen_sup_invoice->status = $result_date['status'];
        $gen_sup_invoice->base_currency = $result_date['code'];



        if ($gen_sup_invoice->status == 1){
            $flagged_containers = $this->flag_check($query,$container_number);
            $query->commit();
            new Respond(1240,array('flag'=>$flagged_containers));
        }
        else {

            $container_invoice = $this->check_sup_invoice($query,$container_number,$invoice_id);
            if (!empty($container_invoice)) {
                $query->commit();
                new Respond(1241, array('unsup' => $container_invoice));
            } else {
                $query->commit();
                new Respond(238);
            }
        }
        $query->commit();
    }

    public function flag_check($query,$containers){
        $result = array();
        $count = 0;
        $container_nu = implode('", "', $containers);

        $query->query("SELECT number FROM container WHERE number  in (\"" . $container_nu . "\")  AND gate_status != 'GATED OUT' AND status = 1");
        $query->run();
        $query_result = $query->fetch_all();
        while ($count < count($query_result)) {
            array_push($result, $query_result[$count++][0]);
        }
        return $result;

    }
    public function check_activity($query,$containers){
        $query->query("select container_log.id from container_log left join container on container.id = container_log.container_id 
                where container.gate_status <> 'GATED OUT' and container_log.invoiced = 0 and container.number in (\"".$containers."\")");
        $query->run();
        if ($query->num_rows() == 0){
            $query->commit();
            new Respond(1126);
        }
    }
    public function check_sup_invoice($query,$containerSel,$invoice_id){
        $container_check = array();

        $query_invoice = "SELECT supplementary_invoice.number FROM supplementary_invoice 
                  LEFT JOIN supplementary_invoice_container ON supplementary_invoice.id = supplementary_invoice_container.invoice_id
                  LEFT JOIN container ON supplementary_invoice_container.container_id = container.id 
                  where container.number IN (\"" . implode('", "', $containerSel) . "\") AND supplementary_invoice.status = 'UNPAID'
                  and supplementary_invoice.invoice_id = '$invoice_id'";
        $query->query($query_invoice);
        $query->run();

        while ($result_invoice = $query->fetch_assoc()) {
            array_push($container_check, $result_invoice['number']);
        }

        return $container_check;
    }

    public function get_supp_charges(){
        $containers = $this->request->param('cont');
        $p_date = $this->request->param('pdat');
        $proforma = $this->request->param('prof');
        $proforma_prefix = $proforma ? "proforma_" : "";
        $container_number = json_decode($containers);
        $number_container = implode('", "', $container_number);


        $storage = new StorageCharges();

        $query = new MyTransactionQuery();
        $query->query("select container.id as container_id,container.status,invoice.currency,invoice.customer_id,invoice.trade_type,invoice.id as invoice_id,invoice.due_date,invoice.trade_type, currency.code from invoice_container inner join container on invoice_container.container_id = container.id 
        left join invoice on invoice.id = invoice_container.invoice_id
        left join currency on currency.id = invoice.currency  where container.number in  (\"" . $number_container . "\") and container.gate_status <> 'GATED OUT'");
        $query->run();
        $result = $query->fetch_assoc();
        $invoice_id = $result['invoice_id'];
        $main_invoice_date = $result['due_date'];
        $customer_id = $result['customer_id'];
        $storage->base_currency = $result['code'];

   

        if ($result['status'] == 1){
            $flagged_containers = $this->flag_check($query,$container_number);
            new Respond(1600,array('flag'=>$flagged_containers));
        }


        $query->query("select voyage.actual_arrival,gate_record.date 
                          from container inner join invoice_container on invoice_container.container_id = container.id 
                          inner join voyage on voyage.id = container.voyage inner join gate_record on gate_record.container_id = container.id 
                          where invoice_container.invoice_id =?");
        $query->bind = array('i', &$invoice_id);
        $query->run();
        $result_query = $query->fetch_assoc();
        $export_date = $result_query['date'];


        $query->query("select id,status,due_date from supplementary_invoice where invoice_id =? and status !='CANCELLED' and status !='EXPIRED' order by id desc");

        $query->bind = array('i', &$invoice_id);
        $query->run();
        $last_due_date = $query->num_rows() > 0 ? $query->fetch_assoc()['due_date'] : $main_invoice_date;


        $query->query("select customer_billing_group.id,customer_billing_group.extra_free_rent_days from customer_billing_group 
                left join customer_billing on customer_billing.billing_group = customer_billing_group.id 
                where customer_billing.customer_id =? and customer_billing_group.trade_type =?");
        $query->bind = array('ii', &$customer_id, &$result['trade_type']);
        $query->run();
        $customer_billing = $query->fetch_assoc();
        $storage->billing_group = $customer_billing['id'];
        $storage->extra_days = $customer_billing['extra_free_rent_days'];
        $import_gated_date = (new \DateTime($result_query['actual_arrival']))->format('Y-m-d');
        $storage->eta_date = $result['trade_type'] == 1 ? $import_gated_date : $export_date;
        $storage->trade_type__id = $result['trade_type'];
        $date5 = new \DateTime($last_due_date);
        $storage_d = $date5->format('Y-M-d');
        $this->storage_date =  $storage_d;

        $storage->p_date = $p_date;

        $tradeId = $result['trade_type'];
        $query->query("SELECT code FROM trade_type WHERE id = $tradeId"); ///////
    
        $query->run();
        $resultQuery = $query->fetch_assoc();
        

        $charge_check = new InvoiceBilling();
        $charge_check->trade_type = $result['trade_type'];
        $charge_check->base_currency = $result['code'];
        $charge_check->main_invoice = $this->request->param('minc');
        $charge_check->p_date = $p_date;
        $charge_check->is_proforma = $proforma_prefix;

        $charge_check->trade_type_code = $resultQuery['code'];

        $free_days = $storage->total_free_days;
        $free_days = $free_days ?? 0;

        $due_last_date = date_create($this->storage_date);
        $due_last_date = date_add($due_last_date,date_interval_create_from_date_string("1 day"));
        $due_last_date = date_format($due_last_date,'Y-M-d');

        $free_days_end = date_add(date_create($storage->eta_date), date_interval_create_from_date_string(($free_days)."day"));
        $free_days_end = date_format($free_days_end,'Y-M-d');

        $current_pay_start_date = $free_days_end < $due_last_date ? $due_last_date : $free_days_end;

        $this->storage_date = $current_pay_start_date;

        if ($storage->billing_group > 0 || $storage->billing_group != null){
            if ($query->num_rows() == 0){
                $this->due_date = $storage->days_charged;
                $inital_date = date_create($storage->eta_date);
                $extra_start_date = date_add($inital_date,date_interval_create_from_date_string($storage->total_free_days."day"));
                $this->storage_date = date_format($extra_start_date,'Y-M-d');
            }
        }


        $due_date = $storage->billing_group > 0 || $storage->billing_group != null? (new \DateTime($this->storage_date))->format('Y-m-d') : $storage->p_date ;

        $charge_check->storage_due_date = $due_date;
        $charge_check->last_due_date = $last_due_date;


        $charges = $charge_check->calculate_sup_charges($container_number);


        if ($charges > 0){
            $query->commit();
            new Respond(2021, array('amt'=>$charges));
        }
        else{
            $query->commit();
            new Respond(1212);
        }

    }

    public function add_supp_invoice(){
        $containers = $this->request->param('cntrs');
        $gen_sup_invoice = new InvoiceBilling();
        $gen_sup_invoice->p_date  = $this->request->param('pdate');
        $gen_sup_invoice->note = $this->request->param('note');
        $gen_sup_invoice->main_invoice = $this->request->param('minc');
        $proforma = $this->request->param('prof');

        $waiver_value = 0;
        $is_waiver_applied = false;
        $waiver_type = 1;
        $waiver_note = "";


        $container_no  = json_decode($containers);

        $bind_types = "";
        $bind_mask = "";

        foreach($container_no as $container_number) {
            $bind_types = $bind_types.'i';
            $bind_mask = $bind_mask . '?,';
        }

        $bind_mask = rtrim($bind_mask, ',');

        $query = new MyQuery();
        $query->query("select distinct(invoice.due_date), currency.code, invoice.id, invoice.tax_type, invoice.trade_type from invoice_container
              inner join container on invoice_container.container_id = container.id
              inner join invoice on invoice.id = invoice_container.invoice_id
              INNER join currency on currency.id = invoice.currency 
              where container.gate_status <> 'GATED OUT' and container.number  in ($bind_mask) and invoice.number = ?");
        $bind_data =array($bind_types."s");

        foreach ($container_no as $container_number) {
            array_push($bind_data, $container_number);
        }
        array_push($bind_data, $gen_sup_invoice->main_invoice);

        $query->bind = $bind_data;
        $res = $query->run();
        $result = $res->fetch_assoc();

        $gen_sup_invoice->trade_type = $result['trade_type'];
        $gen_sup_invoice->tax_type = $result['tax_type'];
        $gen_sup_invoice->invoice_id = $result['id'];
        $gen_sup_invoice->waiver_note = $waiver_note;
        $gen_sup_invoice->waiver_amount = $waiver_type == 2? $waiver_value : 0;
        $gen_sup_invoice->waiver_percentage = $waiver_type == 1? $waiver_value : 0;
        $gen_sup_invoice->waiver_value = $waiver_value;
        $gen_sup_invoice->apply_waiver = $is_waiver_applied;
        $gen_sup_invoice->waiver_type = $waiver_type;
        $gen_sup_invoice->base_currency = $result['code'];
        $gen_sup_invoice->is_proforma = $proforma ? "proforma_" : "";

        $tradeId = $result['trade_type'];

        $query = new MyQuery();
        $query->query("SELECT code FROM trade_type WHERE id = $tradeId"); ///////

        $query->run();
        $resultQuery = $query->fetch_assoc();

        $gen_sup_invoice->trade_type_code = $resultQuery['code'];
        
        $charges = $gen_sup_invoice->calculate_sup_charges($container_no);

        if ($charges > 0){
            $gen_sup_invoice->sup_invoicing($container_no);
        }
        else{
            new Respond(1212);
        }

    }

    public function previewSupplementaryInvoice() {
        $containers = $this->request->param('cntrs');
        $gen_sup_invoice = new InvoicePreview($this->request, $this->response);
        $gen_sup_invoice->p_date  = $this->request->param('pdate');
        $gen_sup_invoice->note = $this->request->param('note');
        $gen_sup_invoice->main_invoice = $this->request->param('minc');

    
        $proforma = $this->request->param('prof');

        $waiver_value = 0;
        $is_waiver_applied = false;
        $waiver_type = 1;
        $waiver_note = "";


        $container_no  = json_decode($containers);

        $bind_types = "";
        $bind_mask = "";

        foreach($container_no as $container_number) {
            $bind_types = $bind_types.'i';
            $bind_mask = $bind_mask . '?,';
        }

        $bind_mask = rtrim($bind_mask, ',');



        $query = new MyQuery();
        $query->query("select distinct(invoice.due_date), currency.code, invoice.id, invoice.tax_type, invoice.trade_type from invoice_container
              inner join container on invoice_container.container_id = container.id
              inner join invoice on invoice.id = invoice_container.invoice_id
              INNER join currency on currency.id = invoice.currency 
              where container.gate_status <> 'GATED OUT' and container.number  in ($bind_mask) and invoice.number = ?");
        $bind_data =array($bind_types."s");

        foreach ($container_no as $container_number) {
            array_push($bind_data, $container_number);
        }
        array_push($bind_data, $gen_sup_invoice->main_invoice);

        $query->bind = $bind_data;
        $res = $query->run();
        $result = $res->fetch_assoc();

        $gen_sup_invoice->trade_type = $result['trade_type'];
        $gen_sup_invoice->tax_type = $result['tax_type'];
        $gen_sup_invoice->invoice_id = $result['id'];
        $gen_sup_invoice->waiver_note = $waiver_note;
        $gen_sup_invoice->waiver_amount = $waiver_type == 2? $waiver_value : 0;
        $gen_sup_invoice->waiver_percentage = $waiver_type == 1? $waiver_value : 0;
        $gen_sup_invoice->waiver_value = $waiver_value;
        $gen_sup_invoice->apply_waiver = $is_waiver_applied;
        $gen_sup_invoice->waiver_type = $waiver_type;
        $gen_sup_invoice->base_currency = $result['code'];
        $gen_sup_invoice->is_proforma = $proforma ? "proforma_" : "";

        $query = new MyQuery();
        $query->query("SELECT code FROM trade_type WHERE id = $gen_sup_invoice->trade_type");
        $res = $query->run();
        $result = $res->fetch_assoc();

        $gen_sup_invoice->trade_type_code = $result['code'];


        $charges = $gen_sup_invoice->calculate_sup_charges($container_no);



        if ($charges > 0){
            $gen_sup_invoice->sup_invoicing($container_no);
            $gen_sup_invoice->previewSupplementaryInvoice($container_no);
        }
        else{
            new Respond(1212);
        }


    }

    public function add_note(){
        $invoice_number = $this->request->param('invn');
        $note = $this->request->param('note');
        $note_type = $this->request->param('ntype');
        $user_id = $_SESSION['id'];

     

        if ($note == ""){
            new Respond(122);
        }
        $query = new MyTransactionQuery();
        $query->query("select id from supplementary_invoice where (status = 'UNPAID' or status = 'DEFERRED' or status='CANCELLED' or status='RECALLED') and number=?");
        $query->bind = array('s',&$invoice_number);
        $query->run();
        if ($query->num_rows() == 0){
            new Respond(123);
        }
        $result = $query->fetch_assoc();
        $invoice_id = $result['id'];
        $query->query("select id from supplementary_note where invoice_id=? and note_type=?");
        $query->bind = array('is',&$invoice_id,&$note_type);
        $query->run();
        $result1 = $query->fetch_assoc();
    
        if(!$result1['id']){
            $query->query("insert into supplementary_note(invoice_id,note,note_type,user_id)values(?,?,?,?)");
            $query->bind = array('issi',&$invoice_id,&$note,&$note_type,&$user_id);
            $query->run();
            $query->commit();
            new Respond(260);
        }
        else{
            $query->query("update supplementary_note set note =?,user_id=? where id=?");
            $query->bind = array('sii',&$note,&$user_id,&$result1['id']);
            $query->run();
            $query->commit();
            new Respond(260);
        }
       
    }

   
}

?>