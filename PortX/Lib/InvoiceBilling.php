<?php

namespace Lib;

use Lib\TaxType\Simple,
    Lib\TaxType\Compound,
    Lib\TaxType\Exempt,
    Lib\TaxType\VatExempt,
    PhpOffice\PhpSpreadsheet\Calculation\DateTime;

class InvoiceBilling{

    public $load_status;
    public $container_id;
    public $oog_status;
    public $full_status;
    public $length;
    public $description;
    public $depot_activity;
    public $depo_activity;
    public $depot_id;
    public $cost;
    public $quote_currency;
    public $goods;
    public $trade_type;
    public $monitoring_charged;
    public $tempo;
    private $mysql_conn;
    public $tax_type;
    public $container_number;
    public $invoice_id;
    public $storage_days;
    public $curDate;
    public $note;
    public $p_date;
    public $base_currency;
    public $is_proforma;
    public $customer;
    public $b_number;
    public $do_number;
    public $boe_number;
    public $release_instructions;
    public $sub_total;
    public $query;
    public $invoice_number;
    public $invoice;
    public $tax_details;
    public $result_array;
    public $number_config;
    public $sup_invoice_id;
    public $main_invoice;
    public $due_date2;
    public $status;
    public $storage_activity;
    public $monitoring_activity;
    public $storage_date;
    public $d_date;
    public $invoice_details_tbl;
    public $invoice_container_tbl;
    public $invoice_temp_tbl;
    public $user_id;
    public $customer_id;
    public $voyage_id;
    public $waiver_value;
    public $waiver_percentage;
    public $waiver_amount;
    public $apply_waiver;
    public $waiver_type;
    public $waiver_note;
    public $days;
    public $billing_group_id;
    public $monitoring_rate;
    public $monitoring_days;
    public $monitoring_start;
    public $monitoring_end;
    public $monitor_day;
    public $monitoring_date;
    public $storage_due_date;
    public $last_due_date;
    public $ucl_charges;
    public $ucl_rates;
    public $invoice_history_tbl;
    public $prefix;
    public $trade_type_code;
    public $supplementary;
    public $proforma_prefix;
    public $is_supplementary;

    public function __construct(){
        $this->curDate = Date('Y-m-d');
    }

    public function get_primary_activity_details($activity_id){
        $query = new MyQuery();
        $query->query("select depot_activity.id, depot_activity.name, charges_container_depot_activity.cost, currency.code
              from charges_container_depot_activity inner join depot_activity on depot_activity.id = charges_container_depot_activity.activity
              inner join ".$this->proforma_prefix."container_log on ".$this->proforma_prefix."container_log.activity_id = charges_container_depot_activity.activity
              inner join currency on charges_container_depot_activity.currency = currency.id
              inner join container on container.id = ".$this->proforma_prefix."container_log.container_id where container.id = ? and currency.code = ?
              and charges_container_depot_activity.trade_type = ? and charges_container_depot_activity.activity = ? 
              and charges_container_depot_activity.full_status = ? and charges_container_depot_activity.load_status = 'ANY' 
              and charges_container_depot_activity.container_length = ? and charges_container_depot_activity.goods = ? 
              and charges_container_depot_activity.oog_status = ?");
        $query->bind = array('isiiiisi', &$this->container_id, &$this->base_currency, &$this->trade_type, &$activity_id, &$this->full_status, &$this->length, &$this->goods, &$this->oog_status);
        $run = $query->run();
        $result = $run->fetch_assoc();

        if(!$run->num_rows())
        {
            $query = new MyQuery();
            $query->query("select depot_activity.id, depot_activity.name, charges_container_depot_activity.cost, currency.code
              from charges_container_depot_activity inner join depot_activity on depot_activity.id = charges_container_depot_activity.activity
              inner join ".$this->proforma_prefix."container_log on ".$this->proforma_prefix."container_log.activity_id = charges_container_depot_activity.activity
              inner join currency on charges_container_depot_activity.currency = currency.id
              inner join container on container.id = ".$this->proforma_prefix."container_log.container_id where container.id = ? and currency.code != ?
              and charges_container_depot_activity.trade_type = ? and charges_container_depot_activity.activity = ? 
              and charges_container_depot_activity.full_status = ? and charges_container_depot_activity.load_status = 'ANY' 
              and charges_container_depot_activity.container_length = ? and charges_container_depot_activity.goods = ? 
              and charges_container_depot_activity.oog_status = ?");
            $query->bind = array('isiiiisi', &$this->container_id, &$this->base_currency, &$this->trade_type, &$activity_id, &$this->full_status, &$this->length, &$this->goods, &$this->oog_status);
            $run = $query->run();
            $result = $run->fetch_assoc();
        }
        $this->depo_activity = $result['name'];
        $this->depot_id = $result['id'];
        $this->cost = $result['cost'];
        $this->quote_currency = $result['code'];
    }

    public function get_stuffing_restuffing_details($activity_id){
        $query = new MyQuery();
        $query->query("select depot_activity.id, depot_activity.name, charges_container_depot_activity.cost, currency.code
              from charges_container_depot_activity inner join depot_activity on depot_activity.id = charges_container_depot_activity.activity
              inner join ".$this->proforma_prefix."container_log on ".$this->proforma_prefix."container_log.activity_id = charges_container_depot_activity.activity
              inner join currency on charges_container_depot_activity.currency = currency.id
              inner join container on container.id = ".$this->proforma_prefix."container_log.container_id where container.id = ? and currency.code = ?
        and charges_container_depot_activity.trade_type = ? and charges_container_depot_activity.activity = ?
        and charges_container_depot_activity.full_status = ? and charges_container_depot_activity.load_status = ?
        and charges_container_depot_activity.container_length = ? and charges_container_depot_activity.goods = ?");
        $query->bind = array('isiiisis', &$this->container_id, &$this->base_currency, &$this->trade_type, &$activity_id, &$this->full_status, &$this->load_status, &$this->length, &$this->goods);
        $run = $query->run();
        $result = $run->fetch_assoc();
        if(!$run->num_rows())
        {
            $query = new MyQuery();
            $query->query("select depot_activity.id, depot_activity.name, charges_container_depot_activity.cost, currency.code
              from charges_container_depot_activity inner join depot_activity on depot_activity.id = charges_container_depot_activity.activity
              inner join ".$this->proforma_prefix."container_log on ".$this->proforma_prefix."container_log.activity_id = charges_container_depot_activity.activity
              inner join currency on charges_container_depot_activity.currency = currency.id
              inner join container on container.id = ".$this->proforma_prefix."container_log.container_id where container.id = ? and currency.code != ?
        and charges_container_depot_activity.trade_type = ? and charges_container_depot_activity.activity = ?
        and charges_container_depot_activity.full_status = ? and charges_container_depot_activity.load_status = ?
        and charges_container_depot_activity.container_length = ? and charges_container_depot_activity.goods = ?");
            $query->bind = array('isiiisis', &$this->container_id, &$this->base_currency, &$this->trade_type, &$activity_id, &$this->full_status, &$this->load_status, &$this->length, &$this->goods);
            $run = $query->run();
            $result = $run->fetch_assoc();
        }
        $this->depo_activity = $result['name'];
        $this->depot_id = $result['id'];
        $this->cost = $result['cost'];
        $this->quote_currency = $result['code'];
    }

    public function get_secondary_activity($activity_id){
        $query = new MyQuery();
        $query->query("select depot_activity.id, depot_activity.name, charges_container_depot_activity.cost, currency.code
              from charges_container_depot_activity inner join depot_activity on depot_activity.id = charges_container_depot_activity.activity
              inner join ".$this->proforma_prefix."container_log on ".$this->proforma_prefix."container_log.activity_id = charges_container_depot_activity.activity
              inner join currency on charges_container_depot_activity.currency = currency.id
              inner join container on container.id = ".$this->proforma_prefix."container_log.container_id where container.id = ? and currency.code = ?
        and charges_container_depot_activity.trade_type = ? and charges_container_depot_activity.activity = ?
        and charges_container_depot_activity.full_status = ? and charges_container_depot_activity.load_status = 'ANY'
        and charges_container_depot_activity.container_length = ? and charges_container_depot_activity.goods = ?");
        $query->bind = array('isiiiis', &$this->container_id, &$this->base_currency, &$this->trade_type, &$activity_id, &$this->full_status, &$this->length, &$this->goods);
        $run = $query->run();
        $result = $run->fetch_assoc();
        if (!$run->num_rows()) {
            $query = new MyQuery();
            $query->query("select depot_activity.id, depot_activity.name, charges_container_depot_activity.cost, currency.code
              from charges_container_depot_activity inner join depot_activity on depot_activity.id = charges_container_depot_activity.activity
              inner join ".$this->proforma_prefix."container_log on ".$this->proforma_prefix."container_log.activity_id = charges_container_depot_activity.activity
              inner join currency on charges_container_depot_activity.currency = currency.id
              inner join container on container.id = ".$this->proforma_prefix."container_log.container_id where container.id = ? and currency.code != ?
        and charges_container_depot_activity.trade_type = ? and charges_container_depot_activity.activity = ?
        and charges_container_depot_activity.full_status = ? and charges_container_depot_activity.load_status = 'ANY'
        and charges_container_depot_activity.container_length = ? and charges_container_depot_activity.goods = ?");
            $query->bind = array('isiiiis', &$this->container_id, &$this->base_currency, &$this->trade_type, &$activity_id, &$this->full_status, &$this->length, &$this->goods);
            $run = $query->run();
            $result = $run->fetch_assoc();
        }
        $this->depo_activity = $result['name'];
        $this->depot_id = $result['id'];
        $this->cost = $result['cost'];
        $this->quote_currency = $result['code'];
    }

    public function insert_depot_activity(){
        $full = $this->full_status == 1 ? "FULL" : "EMPTY";
        $this->depot_activity = "$this->depo_activity" . " " . "$this->length"  ." "."Foot container".", ";
        $this->depot_activity = "$this->depot_activity" . " " . "$this->description";
        $this->depot_activity = "$this->depot_activity" . " " . "$full"." " . "Terminal";
    }

    public function create_storage_info(){
        $this->storage_activity = "Storage charge ".$this->length." Foot, "."for period of ";
        $this->storage_activity = "$this->storage_activity"."$this->storage_date"." "."to"." "."$this->d_date"." "."("."$this->storage_days"." "."$this->days".")";
        return $this->storage_activity;
    }

    public function create_ucl_info(){
        $this->ucl_charges = "UCL Charges for  ".$this->length." "."Foot container";
        return $this->ucl_charges;
    }

    public function create_monitoring_info() {
        $this->monitoring_activity = "$this->goods Monitoring Charge for period of ";
        $this->monitoring_activity = "$this->monitoring_activity" . "$this->monitoring_start" . " " . "to" . " " . "$this->monitoring_end" . " " . "(" . "$this->monitoring_days" . " " . "$this->monitor_day" . ")";
        return $this->monitoring_activity;
    }

    public function load_container($container_id,$is_proforma = false){
        $this->proforma_prefix = $is_proforma ? "proforma_" : "";

        $query = new MyQuery();
        $query->query("SELECT container.id AS container_id, trade_type.id AS trade_type, container_isotype_code.length, container.oog_status, container_isotype_code.description, " . $this->proforma_prefix ."container_depot_info.load_status, " . $this->proforma_prefix . "container_depot_info.goods, container.full_status 
        FROM " . $this->proforma_prefix . "container_depot_info INNER JOIN container ON container.id = " . $this->proforma_prefix . "container_depot_info.container_id INNER JOIN trade_type ON trade_type.code = container.trade_type_code INNER JOIN container_isotype_code 
        ON container.iso_type_code = container_isotype_code.id WHERE " . $this->proforma_prefix ."container_depot_info.container_id = ?");
        $query->bind =  array('i', &$container_id);
        $run = $query->run();
        $result = $run->fetch_assoc();
        $this->length = $result['length'];
        $this->oog_status = $result['oog_status'];
        $this->description = $result['description'];
        $this->load_status = $result['load_status'];
        $this->goods = $result['goods'];
        $this->full_status = $result['full_status'];
        $this->trade_type = $this->trade_type == '13' || $this->trade_type == 3 ? 3 : $result['trade_type'];
        $this->container_id = $result['container_id'];

    }

    public function update_invoice_no_config($number, $trade_id){
        $query=new MyTransactionQuery();
        $query->query("UPDATE ".$this->proforma_prefix.$this->supplementary."invoice_config SET number = ?  WHERE trade_type = ?");
        $query->bind = array('ii', &$number, &$trade_id);
        $query->run();
        $query->commit();
    }

    public function generate_no($trade_id,$preview = true){
        $query=new MyQuery();
        $query->query("SELECT trade_type, prefix, number FROM ". $this->proforma_prefix.$this->supplementary."invoice_config WHERE trade_type = ? ");
        $query->bind = array('i', &$trade_id);
        $run=$query->run();
        $result = $run->fetch_assoc();
        $initial = $result['number'];
        $this->prefix = $result['prefix'];
        $invoice = str_pad(++$initial,8,'0',STR_PAD_LEFT);
        $number = "$invoice";
        if ($preview) {
            $this->update_invoice_no_config($number,$trade_id);
        }
        return $number;
    }

    public function load_generic_tax(){
        $this->query->query("SELECT id FROM `$this->invoice` WHERE number = ?");
        $this->query->bind = array('s', &$this->invoice_number);
        $run = $this->query->run();
        $result = $run->fetch_assoc();
        $this->invoice_id = $result['id'];

        $query = new MyQuery();
        $query->query("SELECT rate, label FROM tax WHERE type = ?");
        $query->bind = array('i', &$this->tax_type);
        $res4 = $query->run();

        while ($result = $res4->fetch_assoc()){
            $tax_label = $result['label'];
            $rates = $result['rate'];

            $tax_details = "$tax_label"."("."$rates"."%".")";
            $tax_rate = $rates/100 * $this->sub_total;

            $this->query->query("INSERT INTO `$this->tax_details` (invoice_id, description, rate, cost)
              VALUES(?,?,?,?)");
            $this->query->bind =  array('isdd', &$this->invoice_id, &$tax_details, &$rates, &$tax_rate);
            $this->query->run();
        }
    }

    public function load_ghana_tax(){
        $this->query->query("SELECT id FROM `$this->invoice` WHERE number = ?");
        $this->query->bind = array('s', &$this->invoice_number);
        $run = $this->query->run();
        $result = $run->fetch_assoc();
        $this->invoice_id = $result['id'];

        $tax_component = 0;
        $query = new MyQuery();
        $query->query("SELECT rate, label FROM tax WHERE type = ?  AND label != 'VAT'");
        $query->bind = array('i', &$this->tax_type);
        $res4 = $query->run();

        while ($result = $res4->fetch_assoc()){
            $tax_label = $result['label'];
            $rates = $result['rate'];
            $tax_details = "$tax_label"."("."$rates"."%".")";
            $tax_rate = $rates/100 * $this->sub_total;
            $tax_component += round($tax_rate,2);

            $this->query->query("INSERT INTO `$this->tax_details` (invoice_id, description, rate, cost)
              VALUES(?,?,?,?)");
            $this->query->bind =  array('isdd', &$this->invoice_id, &$tax_details, &$rates, &$tax_rate);
            $this->query->run();
        }
        $this->query->query("SELECT rate, label FROM tax WHERE type = ? AND label = 'VAT'");
        $this->query->bind = array('i', &$this->tax_type);
        $run = $this->query->run();
        $result = $run->fetch_assoc();
        $vat_rate = $result['rate'];
        $vat_label = $result['label'];

        $vat_details = "$vat_label"."("."$vat_rate"."%".")";
        $vat_total = round($tax_component + $this->sub_total,2);
        $total_vat = round($vat_rate/100 * $vat_total,2);

        $this->query->query("INSERT INTO `$this->tax_details` (invoice_id, description, rate, cost)
              VALUES(?,?,?,?)");
        $this->query->bind =  array('isdd', &$this->invoice_id, &$vat_details, &$vat_rate, &$total_vat);
        $this->query->run();
    }

    public function vat_tax_exempt(){
        $this->query->query("SELECT id FROM `$this->invoice` WHERE number = ?");
        $this->query->bind = array('s', &$this->invoice_number);
        $run = $this->query->run();
        $result = $run->fetch_assoc();
        $this->invoice_id = $result['id'];

        $tax_component = 0;
        $query = new MyQuery();
        $query->query("SELECT rate, label FROM tax WHERE type = 2 AND label != 'VAT'");
        $res4 = $query->run();

        while ($result = $res4->fetch_assoc()){
            $tax_label = $result['label'];
            $rates = $result['rate'];
            $tax_details = "$tax_label"."("."$rates"."%".")";
            $tax_rate = $rates/100 * $this->sub_total;

            $this->query->query("INSERT INTO `$this->tax_details` (invoice_id, description, rate, cost)
              VALUES(?,?,?,?)");
            $this->query->bind =  array('isdd', &$this->invoice_id, &$tax_details, &$rates, &$tax_rate);
            $this->query->run();
        }

    }

    public function insert_container_invoice_details($number_container){
        $this->query->query("SELECT id FROM `$this->invoice` WHERE number = ?");
        $this->query->bind = array('s', &$this->invoice_number);
        $run = $this->query->run();
        $result = $run->fetch_assoc();
        $invoice_id = $result['id'];

        $this->query->query("INSERT INTO `$this->invoice_details_tbl`(invoice_id,description,product_key,container_id,cost, exchange_rate,qty,total_cost) SELECT ?,description,product_key,container_id,cost, exchange_rate,qty, total_cost FROM `$this->invoice_temp_tbl`");
        $this->query->bind = array('i', &$invoice_id);
        $this->query->run();

        $this->query->query("select DISTINCT container_id from `$this->invoice_details_tbl` where invoice_id = ?");
        $this->query->bind = array('i', &$invoice_id);
        $this->query->run();

        $containers =  $this->query->fetch_all();

        foreach ($containers as $container){
            $container = $container[0];
            $this->query->query("INSERT INTO `$this->invoice_container_tbl`(invoice_id,container_id)VALUES(?,?)");
            $this->query->bind = array('ii', &$invoice_id, &$container);
            $this->query->run();
        }
    }

    public function insert_invoice_history(){
        $this->query->query("SELECT id FROM `$this->invoice` WHERE number = ?");
        $this->query->bind = array('s', &$this->invoice_number);
        $run = $this->query->run();
        $result = $run->fetch_assoc();
        $invoice_id = $result['id'];


        $this->query->query("insert into `$this->invoice_history_tbl`(invoice_id, status, user_id) values (?,'CREATED',?)");
        $this->query->bind = array('ii', &$invoice_id,&$this->user_id);
        $this->query->run();
    }

    public function insert_invoice($tax,$b_number){
        $this->user_id = isset($_SESSION['id']) ? $_SESSION['id'] : 1;
        $query = "INSERT INTO ". $this->proforma_prefix ."invoice(number,trade_type,`$b_number`,do_number,due_date,cost,waiver_pct,waiver_amount, waiver_note,currency,tax,tax_type,note,customer_id,user_id,status, boe_number, release_instructions,billing_group)
                   VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'UNPAID',?,?,?)";
        $this->query->query($query);
        $this->query->bind = array("sisssdddsidisiissi", &$this->invoice_number, &$this->trade_type, &$this->b_number, &$this->do_number,  &$this->p_date, &$this->sub_total, &$this->waiver_percentage, &$this->waiver_amount, &$this->waiver_note, &$this->base_currency, &$tax, &$this->tax_type, &$this->note, &$this->customer_id,&$this->user_id, &$this->boe_number, &$this->release_instructions,&$this->billing_group_id);
        $this->query->run();
    }

    public function insert_proforma_invoice($tax,$b_number){
        $this->user_id = isset($_SESSION['id']) ? $_SESSION['id'] : exit();
        $query = "INSERT INTO proforma_invoice(number,trade_type,`$b_number`,do_number,due_date,cost,waiver_pct,waiver_amount, waiver_note,currency,tax,tax_type,note,customer_id,user_id, boe_number, release_instructions,billing_group)
                   VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $this->query->query($query);
        $this->query->bind = array("sisssdddsidisiissi", &$this->invoice_number, &$this->trade_type, &$this->b_number, &$this->do_number,  &$this->p_date, &$this->sub_total, &$this->waiver_percentage, &$this->waiver_amount, &$this->waiver_note, &$this->base_currency, &$tax, &$this->tax_type, &$this->note, &$this->customer_id,&$this->user_id, &$this->boe_number, &$this->release_instructions,&$this->billing_group_id);
        $this->query->run();
    }

    public function insert_sup_invoice($tax){
        $query1 = "INSERT INTO supplementary_invoice(invoice_id,number,due_date,cost,waiver_pct,waiver_amount,waiver_note,tax,note,user_id,status)
        VALUES('$this->invoice_id','$this->invoice_number','$this->p_date','$this->sub_total','$this->waiver_percentage','$this->waiver_amount','$this->waiver_note','$tax','$this->note','$this->user_id','UNPAID')";
        $this->query->query($query1);
        $this->query->run();
    }

    public function insert_profroma_sup_invoice($tax){
        $query1 = "INSERT INTO proforma_supplementary_invoice(invoice_id,number,due_date,cost,waiver_pct,waiver_amount,waiver_note,tax,note,user_id)
        VALUES('$this->invoice_id','$this->invoice_number','$this->p_date','$this->sub_total','$this->waiver_percentage','$this->waiver_amount','$this->waiver_note','$tax','$this->note','$this->user_id')";
        $this->query->query($query1);
        $this->query->run();
    }

    public function container_log_update($container){
        $this->query->query("update ". $this->proforma_prefix ."container_log set invoiced = '1' where container_id = ?");
        $this->query->bind = array('i', &$container);
        $this->query->run();
    }

    public  function calculate_charges($number_container){
        $this->proforma_prefix = $this->is_proforma ? "proforma_" : "";
        $storage = new StorageCharges();
        $storage->storage_cost = 0;

        $this->query=new MyTransactionQuery();

        $this->query->query("select id from customer where name = ?");
        $this->query->bind = array('s', &$this->customer);
        $run = $this->query->run();
        $result = $run->fetch_assoc();
        $this->customer_id = $result['id'];

        $this->query->query("SELECT id, name AS trade_name FROM trade_type WHERE code = ?");
        $this->query->bind = array('s', &$this->trade_type);
        $run = $this->query->run();
        $result = $run->fetch_assoc();
        $trade_id = $result['id'];

        $this->query->query("select * from customer_billing_group 
                inner join customer_billing on customer_billing.billing_group = customer_billing_group.id 
                where customer_billing.customer_id =? and customer_billing_group.trade_type =?");
        $this->query->bind = array('ii', &$this->customer_id, &$trade_id);
        $this->query->run();
        $customer_billing = $this->query->fetch_assoc();
        $this->billing_group_id = $customer_billing['id'];

        $storage->billing_group = $this->billing_group_id;
        $storage->extra_days = $customer_billing['extra_free_rent_days'];

        $charges = 0;

        $bNumber = $this->b_number;
        $tType = $this->trade_type;
        $numberString = $tType == '11' || $tType == '13' ? "bl_number" : "book_number";

        foreach ($number_container as  $container_number) {
            $this->query->query("SELECT gate_record.date, container.id AS container_id, voyage.actual_arrival AS act_arrival, container_isotype_code.id AS iso_type, container_isotype_code.id AS iso_type,container_isotype_code.length, trade_type.id AS trade_id FROM container 
			INNER JOIN gate_record on container.id = gate_record.container_id
            LEFT JOIN voyage ON container.voyage = voyage.id 
            INNER JOIN trade_type ON container.trade_type_code = trade_type.code 
            INNER JOIN container_isotype_code ON container.iso_type_code = container_isotype_code.id 
            WHERE container.number = ? AND container.gate_status <> 'GATED OUT'
            AND container.$numberString = ?");
            $this->query->bind = array('ss', &$container_number, &$bNumber);
            $run = $this->query->run();
            $result = $run->fetch_assoc();
            $container_id = $result['container_id'];
            $storage->trade_type__id = $tType == '13' ? 3 : $result['trade_id'];
            $actual_arrival = $result['act_arrival'];
            $expo_date = $result['date'];
            $storage->p_date = $this->p_date;
            $storage->eta_date = ($result['trade_id'] == 1) ? $actual_arrival : $expo_date;
            $date4 = new \DateTime($this->p_date);
            $this->d_date = $date4->format('Y-M-d');
            $this->monitoring_date = (new \DateTime($actual_arrival))->format('Y-M-d');

            $this->load_container($container_id,$this->proforma_prefix);
            $date1 = strtotime($this->storage_date);
            $date2 = strtotime($this->p_date);
            $diff = $date2 - $date1;
            $diffs = abs(round($diff / 86400));
            $date3 = $diffs  + 1;
            $this->storage_days = $date3;

            $query = new MyQuery();
            $query->query("select activity_id from ".$this->proforma_prefix."container_log inner join depot_activity 
                  on depot_activity.id = ".$this->proforma_prefix."container_log.activity_id where depot_activity.billable = '1' and invoiced = false and container_id =?");
            $query->bind = array('i', &$container_id);
            $query->run();

            while ($activity_result = $query->fetch_assoc()) {
                switch ($activity_result['activity_id']) {
                    case(1):
                        $this->get_primary_activity_details($activity_result['activity_id']);
                        break;
                    case(2):
                        $this->get_primary_activity_details($activity_result['activity_id']);
                        break;
                    case(6):
                        $this->get_stuffing_restuffing_details($activity_result['activity_id']);
                        break;
                    default:
                        $this->get_secondary_activity($activity_result['activity_id']);
                        break;
                }
                $this->checkActivityCharges($activity_result['activity_id']);

                $charges += $this->cost;

                $this->cost = 0;
            }

            $this->query->query("select gate_record.id from gate_record left join container on container.id = gate_record.container_id where ucl_status = 1 and container_id = ? and container.gate_status <> 'GATED OUT'");
            $this->query->bind = array('i', &$container_id);
            $this->query->run();
            $ucl_check = $this->query->fetch_assoc();

            if ($ucl_check['id']) {
                $charges += $this->calculate_ucl_charges($container_number);
            }
            else {
                $storage->base_currency = $this->base_currency;
                if ($this->trade_type != 8) {
                    $storage_charges = $storage->chargeStorage($container_id, $this->is_proforma);

                    $this->monitoring_start = new \DateTime($storage->eta_date);
                    $this->monitoring_start = date_format($this->monitoring_start, "Y-M-d");
                    $this->monitoring_end = new \DateTime($this->p_date);
                    $this->monitoring_end = date_format($this->monitoring_end, "Y-M-d");
                    $this->monitoring_days = (abs(round(strtotime($this->monitoring_end) - strtotime($this->monitoring_start))) / 86400) + 1;

                    $this->monitor_day = $this->monitoring_days == 1 || $this->monitoring_days == 0 ? 'day' : "days";

                    $this->storage_days = $storage->days_charged;
                    $this->days = $this->storage_days == 1 || $this->storage_days == 0 ? 'day' : "days";


                    $extra_date = $this->storage_days - 1;

                    $inital_date = date_create($this->p_date);
                    $extra_start_date = date_sub($inital_date, date_interval_create_from_date_string($extra_date . "day"));
                    $this->storage_date = date_format($extra_start_date, 'Y-M-d');
                    $monitoring_charges = $this->calculate_monitoring_charges();

                    $charges+= $storage_charges + $monitoring_charges;
                } else {
                    $storage_charges = $storage->chargeStorage($container_id, $this->is_proforma);

                    $this->storage_days = $storage->days_charged;
                    $this->days = $this->storage_days == 1 || $this->storage_days == 0 ? 'day' : "days";


                    $extra_date = $this->storage_days - 1;

                    $inital_date = date_create($this->p_date);
                    $extra_start_date = date_sub($inital_date, date_interval_create_from_date_string($extra_date . "day"));
                    $this->storage_date = date_format($extra_start_date, 'Y-M-d');

                    $charges+= $storage_charges;
                }
            }
        }

        return round($charges, 2);
    }

    public function generate_invoice($containers) {
        $this->is_supplementary = false;
        $this->proforma_prefix = $this->is_proforma ? "proforma_" : "";
        $this->invoice_temp_tbl = $this->proforma_prefix . 'invoice_details_temp';
        $this->supplementary = $this->is_supplementary ? "supplementary_" : "";
        $this->query = new MyTransactionQuery();
        $this->query->query("CREATE TEMPORARY TABLE IF NOT EXISTS `$this->invoice_temp_tbl`(
                id int(11) not null auto_increment,
                description tinytext not null,
                product_key varchar(5) not null,
                container_id int(11) unsigned not null,
                cost decimal(18,2) not null,
                exchange_rate int(11) not null ,
                qty int(10) not null,
                total_cost decimal(18,2) not null,
                primary key (id)
                )");
        $this->query->run();
        $this->query->query("SELECT idn_separator, prefix FROM system WHERE id = 1 ");
        $run = $this->query->run();
        $result = $run->fetch_assoc();
        $system = $result['prefix'];
        $separator = $result['idn_separator'];

        $this->query->query("SELECT id, name AS trade_name FROM trade_type WHERE code = ?");
        $this->query->bind = array('s', &$this->trade_type);
        $run = $this->query->run();
        $result = $run->fetch_assoc();
        $trade_id = $result['id'];

        $this->number_config = $this->proforma_prefix . 'invoice_config';
        $invoice_no = $this->generate_no($trade_id);


        $this->query->query("select id from customer where name = ?");
        $this->query->bind = array('s', &$this->customer);
        $run = $this->query->run();
        $result = $run->fetch_assoc();
        $this->customer_id = $result['id'];

        $this->query->query("select * from customer_billing_group 
                inner join customer_billing on customer_billing.billing_group = customer_billing_group.id 
                where customer_billing.customer_id =? and customer_billing_group.trade_type =?");
        $this->query->bind = array('ii', &$this->customer_id, &$trade_id);
        $this->query->run();
        $customer_billing = $this->query->fetch_assoc();
        $this->billing_group_id = $customer_billing['id'];



        $init_month = strtoupper(date('M'));
        $init_date = date('Y').$init_month;
        $this->invoice_number = $system != '' ? "$system" . "$separator" . "$this->prefix" . "$separator" . $init_date . $invoice_no : "$this->prefix" . "$separator" . $init_date . $invoice_no;

        $storage = new StorageCharges();
        $storage->billing_group = $this->billing_group_id;
        $storage->extra_days = $customer_billing['extra_free_rent_days'];


        $tax = new Compound();

        if ($this->billing_group_id > 0) {
            $this->tax_type = $customer_billing['tax_type'];
        }

        if ($this->tax_type == 1) {
            $tax = new Simple();
        } elseif ($this->tax_type == 3) {
            $tax = new Exempt();
        } elseif ($this->tax_type == 4) {
            $tax = new VatExempt();
        }

        $bNumber = $this->b_number;
        $tType = $this->trade_type;
        $numberString = $tType == '11' || $tType == '13' ? "bl_number" : "book_number";

        $storage->storage_cost = 0;
        foreach ($containers as $container_number) {
            $this->query->query("SELECT gate_record.date, container.id AS container_id, voyage.actual_arrival AS act_arrival, container_isotype_code.id AS iso_type, container_isotype_code.id AS iso_type,container_isotype_code.length, trade_type.id AS trade_id FROM container 
			INNER JOIN gate_record on container.id = gate_record.container_id
            LEFT JOIN voyage ON container.voyage = voyage.id 
            INNER JOIN trade_type ON container.trade_type_code = trade_type.code 
            INNER JOIN container_isotype_code ON container.iso_type_code = container_isotype_code.id 
            WHERE container.number = ? AND container.gate_status <> 'GATED OUT'
            AND container.$numberString = ?");
            $this->query->bind = array('ss', &$container_number, $bNumber);
            $run = $this->query->run();
            $result = $run->fetch_assoc();
            $container_id = $result['container_id'];
            $storage->trade_type__id = $tType == '13' ? 3 : $result['trade_id'];
            $actual_arrival = $result['act_arrival'];
            $expo_date = $result['date'];
            $storage->p_date = $this->p_date;
            $storage->eta_date = ($result['trade_id'] == 1) ? $actual_arrival : $expo_date;
            $date4 = new \DateTime($this->p_date);
            $this->d_date = $date4->format('Y-M-d');
            $this->monitoring_date = (new \DateTime($actual_arrival))->format('Y-M-d');
            if ($trade_id == '4') {
                $this->query->query("Update container set voyage=? where id = ?");
                $this->query->bind = array('ii', &$this->voyage_id, &$container_id);
                $this->query->run();
            }

            $this->load_container($container_id,$this->proforma_prefix);

            $this->get_activities($container_id);

            if ($this->billing_group_id > 0) {
                $this->storage_days = $storage->days_charged;
                $extra_date = $this->storage_days - 1;
                $inital_date = date_create($this->p_date);
                $extra_start_date = date_sub($inital_date, date_interval_create_from_date_string($extra_date . "day"));
                $this->storage_date = date_format($extra_start_date, 'Y-M-d');
            }

            $this->query->query("select gate_record.id from gate_record left join container on container.id = gate_record.container_id where ucl_status = 1 and container_id = ? and container.gate_status <> 'GATED OUT'");
            $this->query->bind = array('i', &$container_id);
            $this->query->run();
            $ucl_check = $this->query->fetch_assoc();

            if ($ucl_check['id']) {
                $ucl_fixed_charges = $this->calculate_ucl_charges($container_number);

                $this->query->query("INSERT INTO `$this->invoice_temp_tbl`(description,product_key,container_id,cost, exchange_rate, qty, total_cost)
                              VALUES(?,'U',?,?, ?,1,? *1)");
                $ucl_info = $this->create_ucl_info();
                $this->query->bind = array('siddd', &$ucl_info, &$container_id, &$ucl_fixed_charges, &$this->ucl_rates, &$ucl_fixed_charges);
                $this->query->run();
            } else {
                $storage->base_currency = $this->base_currency;
                if ($this->trade_type != 8) {
                    $storage_charges = $storage->chargeStorage($container_id, $this->is_proforma);

                    $this->monitoring_start = new \DateTime($storage->eta_date);
                    $this->monitoring_start = date_format($this->monitoring_start, "Y-M-d");
                    $this->monitoring_end = new \DateTime($this->p_date);
                    $this->monitoring_end = date_format($this->monitoring_end, "Y-M-d");
                    $this->monitoring_days = (abs(round(strtotime($this->monitoring_end) - strtotime($this->monitoring_start))) / 86400) + 1;

                    $this->monitor_day = $this->monitoring_days == 1 || $this->monitoring_days == 0 ? 'day' : "days";

                    $this->storage_days = $storage->days_charged;
                    $this->days = $this->storage_days == 1 || $this->storage_days == 0 ? 'day' : "days";


                    $extra_date = $this->storage_days - 1;

                    $inital_date = date_create($this->p_date);
                    $extra_start_date = date_sub($inital_date, date_interval_create_from_date_string($extra_date . "day"));
                    $this->storage_date = date_format($extra_start_date, 'Y-M-d');
                    $monitoring_charges = $this->calculate_monitoring_charges();
                } else {
                    $storage_charges = $storage->chargeStorage($container_id, $this->is_proforma);

                    // $this->monitoring_start = new \DateTime($storage->eta_date);
                    // $this->monitoring_start = date_format($this->monitoring_start, "Y-M-d");
                    // $this->monitoring_end = new \DateTime($this->p_date);
                    // $this->monitoring_end = date_format($this->monitoring_end, "Y-M-d");
                    // $this->monitoring_days = (abs(round(strtotime($this->monitoring_end) - strtotime($this->monitoring_start))) / 86400) + 1;

                    // $this->monitor_day = $this->monitoring_days == 1 || $this->monitoring_days == 0 ? 'day' : "days";

                    $this->storage_days = $storage->days_charged;
                    $this->days = $this->storage_days == 1 || $this->storage_days == 0 ? 'day' : "days";


                    $extra_date = $this->storage_days - 1;

                    $inital_date = date_create($this->p_date);
                    $extra_start_date = date_sub($inital_date, date_interval_create_from_date_string($extra_date . "day"));
                    $this->storage_date = date_format($extra_start_date, 'Y-M-d');
            

                }
                if ($storage->storage_calculated) {
                    $this->query->query("INSERT INTO `$this->invoice_temp_tbl`(description,product_key,container_id,cost, exchange_rate, qty, total_cost)
                              VALUES(?,'S',?,?, ?,1,? *1)");
                    $storage_info = $this->create_storage_info();
                    $this->query->bind = array('siddd', &$storage_info, &$container_id, &$storage_charges, &$storage->rate, &$storage_charges);
                    $this->query->run();
                }


                if ($this->monitoring_charged && $monitoring_charges > 0) {
                    $this->query->query("INSERT INTO `$this->invoice_temp_tbl`(description,product_key,container_id,cost, exchange_rate, qty, total_cost)
                              VALUES(?,'M',?,?, ?,1,? *1)");
                    $monitoring_info = $this->create_monitoring_info();
                    $this->query->bind = array('siddd', &$monitoring_info, &$container_id, &$monitoring_charges, &$this->monitoring_rate, &$monitoring_charges);
                    $this->query->run();
                }
            }
            $this->container_log_update($container_id);
           
        }

        $this->query->query("SELECT SUM(total_cost) AS subtotal FROM `$this->invoice_temp_tbl`");
        $this->query->bind = array();
        $res3 = $this->query->run();
        $result = $res3->fetch_assoc();

        $this->sub_total = $result['subtotal'];



        if ($customer_billing['waiver_pct'] > 0) {
            $this->waiver_type = 1;
            $this->waiver_value = $customer_billing['waiver_pct'];
            $this->apply_waiver = true;
        } elseif ($customer_billing['waiver_amount'] > 0) {
            $this->waiver_type = 2;
            $this->waiver_value = $customer_billing['waiver_amount'];
            $this->apply_waiver = true;
        }

        if ($this->apply_waiver) {
            switch ($this->waiver_type) {
                case 1:
                    $this->waiver_percentage = $this->waiver_value;
                    $this->waiver_value = max(0, min(100, $this->waiver_value));
                    $amount = ($this->waiver_value / 100) * $this->sub_total;
                    $this->waiver_amount = round($amount, 2);
                    break;
                case 2:
                    $this->waiver_amount = $this->waiver_value;
                    $percentage = $this->waiver_amount * 100 / $this->sub_total;
                    $this->waiver_percentage = max(0, min(100, $percentage));
                    break;
                default:
                    $this->waiver_amount = 0;
                    $this->waiver_percentage = 0;
                    break;
            }
        } else {
            $this->waiver_amount = 0;
            $this->waiver_percentage = 0;
        }

        $sub_total = $this->sub_total - $this->waiver_amount;

        $this->sub_total = $sub_total < 0 ? 0 : $sub_total;

        $this->query->query("SELECT id FROM currency WHERE code = ?");
        $this->query->bind = array('s', &$this->base_currency);
        $run = $this->query->run();
        $result = $run->fetch_assoc();
        $this->base_currency = $result['id'];


        $b_number = $trade_id == '1' || $trade_id == '3' ? 'bl_number' : 'book_number';
        if ($trade_id == '1' || $trade_id == '3' || $trade_id == '4' || $trade_id == '8') {
            $gen_tax = $tax->generateTax($this->sub_total);

            $this->invoice = $this->proforma_prefix . 'invoice';

            $this->tax_type = $this->billing_group_id > 0 ? $customer_billing['tax_type'] : $this->tax_type;

            $this->billing_group_id = $this->billing_group_id > 0 ? $this->billing_group_id : 0;

            $this->insert_invoice($gen_tax, $b_number);

            $this->tax_details = $this->proforma_prefix . 'invoice_details_tax';

            if ($this->tax_type == '1') {
                $this->load_generic_tax();
            } elseif ($this->tax_type == '2') {
                $this->load_ghana_tax();
            } elseif ($this->tax_type == '4') {
                $this->vat_tax_exempt();
            }

        }

        $this->invoice_details_tbl = $this->proforma_prefix . 'invoice_details';
        $this->invoice_container_tbl = $this->proforma_prefix . 'invoice_container';
        $this->invoice_history_tbl = $this->proforma_prefix . 'invoice_history_log';
        $this->insert_container_invoice_details($containers);
        $this->insert_invoice_history();

        $this->query->commit();

        new Respond(2211, array('invn' => $this->invoice_number));
    }

    public function calculate_monitoring_charges(){
        $query = new MyQuery();
        $query->query("select cost_per_day, currency.code as currency from charges_container_monitoring left join currency on currency.id = charges_container_monitoring.currency where goods like ?  and currency = ?  and trade_type = ?");
        $query->bind = array('sii', &$this->goods, &$this->base_currency, &$this->trade_type);
        $run = $query->run();
        if(!$run->num_rows()){
            $query = new MyQuery();
            $query->query("select cost_per_day, currency.code as currency from charges_container_monitoring  left join currency on currency.id = charges_container_monitoring.currency  where goods like ? and trade_type = ?");
            $query->bind = array('si', &$this->goods, &$this->trade_type);
            $run = $query->run();
            if(!$run->num_rows()){
                return 0;
            }
        }
        $charge = $run->fetch_assoc();
        $quote_currency = $charge['currency'];
        $cost_per_day = $charge['cost_per_day'];

        if(!$cost_per_day) {
            return 0;
        }

        $total_cost = $this->monitoring_days * $cost_per_day;
        $this->monitoring_rate = 0;

        if ($this->base_currency != $quote_currency) {
            $query = new MyQuery();
            $query->query("select id, buying, selling from exchange_rate where base in (select id from currency where code =  ? ) and quote in (select id from currency where code =  ?) order by date DESC");
            $query->bind = array('ss', &$this->base_currency, &$quote_currency);
            $run = $query->run();

            $isBase = true;

            if (!$run->num_rows()) {
                $query = new MyQuery();
                $query->query("select id, buying, selling from exchange_rate where quote in (select id from currency where code =  ? ) and base in (select id from currency where code =  ?) order by date DESC");
                $query->bind = array('ss', &$this->base_currency, &$quote_currency);
                $run = $query->run();

                $isBase = false;

                if (!$run->num_rows()) {
                    new Respond(1211, array('base' => $this->base_currency, 'quote' => $quote_currency));
                }
            }

            $rates = $run->fetch_assoc();
            if ($isBase) {
                $exc_rate = $rates['buying'];

                $total_cost= $total_cost / $exc_rate;
            } else {
                $exc_rate = $rates['selling'];

                $total_cost = $total_cost * $exc_rate;
            }

            $this->monitoring_rate = $rates['id'];
        }

        $this->monitoring_charged = true;
        return round($total_cost,2);
    }

    public function calculate_sup_charges($container_number){
        $this->proforma_prefix = $this->is_proforma ? "proforma_" : "";


        $storage = new StorageCharges();
        $storage->storage_cost = 0;

        $this->query=new MyTransactionQuery();
        $charges = 0;

        $tType = $this->trade_type_code;
        $bNumber = $this->getSupplementaryBNumber($this->main_invoice, $this->trade_type);
        $numberString = $tType == '11' || $tType == '13' ? "bl_number" : "book_number";

        foreach ($container_number as  $container_nu) {

            $this->query->query("SELECT gate_record.date, container.id AS container_id, voyage.actual_arrival AS act_arrival, 
                                      depot_activity.id AS activity_id, container_isotype_code.id AS iso_type, 
                                      container_isotype_code.id AS iso_type,container_isotype_code.length, trade_type.id AS trade_id FROM container 
                                      LEFT JOIN container_log ON container.id = container_log.container_id 
                                      LEFT JOIN voyage ON container.voyage = voyage.id
                                      LEFT JOIN gate_record ON gate_record.container_id = container.id
                                      LEFT JOIN depot_activity ON container_log.activity_id = depot_activity.id 
                                      LEFT JOIN trade_type ON container.trade_type_code = trade_type.code 
                                      LEFT JOIN container_isotype_code ON container.iso_type_code = container_isotype_code.id
                  WHERE container.number = '$container_nu' AND container.gate_status <> 'GATED OUT'
                                    AND container.$numberString = '$bNumber'");
            $container_query = $this->query->run();
            $container_result = $container_query->fetch_assoc();
            $container_no = $container_result['container_id'];



            $this->query->query("select due_date,trade_type,customer_id, id from invoice where number = '$this->main_invoice'");
            $this->query->run();
            $result_invoice =  $this->query->fetch_assoc();
            $main_invoice_date = $result_invoice['due_date'];
            $invoice_id = $result_invoice['id'];
            $trade_id = $result_invoice['trade_type'];
            $this->customer_id = $result_invoice['customer_id'];

            $this->query->query("select * from customer_billing_group 
                inner join customer_billing on customer_billing.billing_group = customer_billing_group.id 
                where customer_billing.customer_id =? and customer_billing_group.trade_type =?");
            $this->query->bind = array('ii', &$this->customer_id, &$trade_id);
            $this->query->run();
            $customer_billing = $this->query->fetch_assoc();
            $this->billing_group_id = $customer_billing['id'];


            $this->query->query("select id,status,due_date from supplementary_invoice where invoice_id = '$invoice_id' and status !='CANCELLED' and status !='EXPIRED' order by id desc");
            $this->query->run();
            $last_due_date = $this->query->num_rows() > 0 ? $this->query->fetch_assoc()['due_date'] : $main_invoice_date;


            $storage->trade_type__id = $this->trade_type;
            $bill_date = $container_result['date'];
            $actual_arrival = $container_result['act_arrival'];
            $storage->p_date = $this->p_date;
            $storage->sup_storage_start_date = $last_due_date;
            $storage->eta_date = $container_result['trade_id'] == 1 ? $actual_arrival : $bill_date;
            $storage->extra_days = $customer_billing['extra_free_rent_days'];
            $storage->billing_group = $customer_billing['id'];
            $date4 = new \DateTime($this->p_date);
            $this->d_date = $date4->format('Y-M-d');
            $this->storage_date = $last_due_date;

            $this->load_container($container_no);


            $date1 = strtotime($this->storage_date);
            $date2 = strtotime($this->p_date);
            $diff = $date2 - $date1;
            $diffs = abs(round($diff / 86400));
            $date3 = $diffs;
            $this->storage_days = $date3;
            $this->days = $this->storage_days == 1 || $this->storage_days == 0 ? 'day' : "days";

            $this->query->query("select activity_id from container_log inner join depot_activity 
                  on depot_activity.id = container_log.activity_id where depot_activity.billable = '1' and invoiced = false and container_id = '$container_no'");
            $this->query->run();
            $activity_query = $this->query->num_rows();
            $activity_query1 = $this->query->fetch_all(MYSQLI_ASSOC);


            foreach ($activity_query1 as $activity_result){
                switch ($activity_result['activity_id']) {
                    case(6):
                        $this->get_stuffing_restuffing_details($activity_result['activity_id']);
                        $this->insert_depot_activity();
                        break;
                    default:
                        $this->get_secondary_activity($activity_result['activity_id']);
                        $this->insert_depot_activity();
                        break;
                }

                $this->checkActivityCharges($activity_result['activity_id']);

                $charges += $this->cost;
                $this->cost = 0;
            }

            $this->query->query("SELECT cost  from invoice_details WHERE container_id =? AND product_key LIKE \"S\" AND invoice_id =?");
            $this->query->bind = array('ii', &$container_no, &$invoice_id);
            $total_supp_result = $this->query->run();
            $total_supp_cost = $total_supp_result->fetch_assoc()["cost"];

            $total_supp_cost = $total_supp_cost ? $total_supp_cost : 0;




            $this->query->query("SELECT SUM(supplementary_invoice_details.cost) as cost_sum
                          from supplementary_invoice_details
                          inner join supplementary_invoice on supplementary_invoice.id =supplementary_invoice_details.invoice_id
                          WHERE supplementary_invoice_details.container_id =? AND supplementary_invoice_details.product_key LIKE 'S' and supplementary_invoice.status = 'PAID'");

            $this->query->bind = array('i', &$container_no);
            $invoice_result = $this->query->run();
            $invoice_storage_cost = $invoice_result->fetch_assoc()["cost_sum"];

            $invoice_storage_cost = $invoice_storage_cost ? $invoice_storage_cost : 0;

            $charge_till_date = $total_supp_cost + $invoice_storage_cost;

            $storage->base_currency = $this->base_currency;

            $storage_charges = $storage->chargeStorage($container_no);

            $storage_charges = $storage_charges - $charge_till_date;


            $charges += $storage_charges;

            $this->monitoring_start = date_add(new \DateTime($last_due_date), date_interval_create_from_date_string("1 day"));
            $this->monitoring_start = date_format($this->monitoring_start, "Y-m-d");
            $this->monitoring_end = new \DateTime($this->p_date);
            $this->monitoring_end = date_format($this->monitoring_end, "Y-m-d");
            $this->monitoring_days = round(strtotime($this->monitoring_end) - strtotime($this->monitoring_start)) / 86400 + 1;
            $monitoring_charges = $this->calculate_monitoring_charges();

            if ($this->p_date < $this->storage_due_date || $this->p_date <= $this->last_due_date) {
                if ($activity_query == 0 && $monitoring_charges <= 0) {
                    new Respond(167, array('dd' => $this->monitoring_charged ? $this->monitoring_start : $this->storage_due_date));
                }
            }

            $charges += $monitoring_charges;

        }
        return round($charges, 2);
    }

    public function sup_invoicing($container_number){

        $this->result_array = array();

        $this->proforma_prefix =  $this->is_proforma ? "proforma_" : "";

        $this->invoice_temp_tbl = $this->proforma_prefix.'supplementary_invoice_details_temp';
        $this->query=new MyTransactionQuery();
        $this->query->query("CREATE TEMPORARY TABLE IF NOT EXISTS `$this->invoice_temp_tbl`(
                id int(11) not null auto_increment,
                description tinytext not null,
                product_key varchar(5) not null,
                container_id int(11) unsigned not null,
                cost decimal(18,2) not null,
                exchange_rate int(11) not null ,
                qty int(10) not null,
                total_cost decimal(18,2) not null,
                primary key (id)
                )");
        $this->query->run();

        $this->number_config = $this->proforma_prefix.'supplementary_invoice_config';


        $this->query->query("SELECT idn_separator, prefix FROM system WHERE id = 1 ");
        $resi=$this->query->run();
        $sys = $resi->fetch_assoc();
        $system = $sys['prefix'];
        $seperator = $sys['idn_separator'];

        $this->is_supplementary = true;
        $this->supplementary = $this->is_supplementary ? "supplementary_" : "";
        $invoice_no = $this->generate_no($this->trade_type);
        $init_month = strtoupper(date('M'));
        $init_date = date('Y').$init_month;
        $this->invoice_number = $system != '' ? "$system"."$seperator"."$this->prefix"."$seperator". $init_date .$invoice_no : "$this->prefix"."$seperator". $init_date .$invoice_no;



        $storage = new StorageCharges();
        $tax = new Compound();
        if ($this->tax_type == 1){
            $tax = new Simple();
        }
        elseif ($this->tax_type == 3){
            $tax = new Exempt();
        }
        elseif ($this->tax_type == 4){
            $tax = new VatExempt();
        }
        $storage->storage_cost = 0;

        $tType = $this->trade_type_code;
        $bNumber = $this->getSupplementaryBNumber($this->main_invoice, $this->trade_type);
        $numberString = $tType == '11' || $tType == '13' ? "bl_number" : "book_number";

        foreach ($container_number as  $container_nu) {
            $this->query->query("SELECT gate_record.date, container.id AS container_id, voyage.actual_arrival AS act_arrival, depot_activity.id AS activity_id, container_isotype_code.id AS iso_type, container_isotype_code.id AS iso_type,container_isotype_code.length, trade_type.id AS trade_id 
                  FROM container INNER JOIN container_log ON container.id = container_log.container_id LEFT JOIN voyage ON container.voyage = voyage.id 
                  INNER JOIN gate_record ON gate_record.container_id = container.id
                  INNER JOIN depot_activity ON container_log.activity_id = depot_activity.id INNER JOIN trade_type 
                  ON container.trade_type_code = trade_type.code INNER JOIN container_isotype_code ON container.iso_type_code = container_isotype_code.id 
                  WHERE container.number =? AND container.gate_status <> 'GATED OUT'
                  AND container.$numberString = ?");
            $this->query->bind = array('ss',&$container_nu, &$bNumber);
            $this->query->run();
            $container_result = $this->query->fetch_assoc();
            $container_no = $container_result['container_id'];


            $query = new MyQuery();
            $query->query("select due_date,customer_id,trade_type, id from invoice where number =?");
            $query->bind = array('s',&$this->main_invoice);
            $query->run();
            $result_invoice = $query->fetch_assoc();
            $main_invoice_date = $result_invoice['due_date'];
            $invoice_id = $result_invoice['id'];
            $this->customer_id = $result_invoice["customer_id"];
            $trade_id = $result_invoice['trade_type'];

            $this->query->query("select * from customer_billing_group 
                inner join customer_billing on customer_billing.billing_group = customer_billing_group.id 
                where customer_billing.customer_id =? and customer_billing_group.trade_type =?");
            $this->query->bind = array('ii',&$this->customer_id,&$trade_id);
            $this->query->run();
            $customer_billing = $this->query->fetch_assoc();
            $this->billing_group_id = $customer_billing['id'];


            $this->query->query("select supplementary_invoice.id,due_date 
                      from supplementary_invoice left join supplementary_invoice_container 
                      on supplementary_invoice.id = supplementary_invoice_container.invoice_id 
                      where supplementary_invoice.invoice_id =? and supplementary_invoice_container.container_id =? order by id desc");
            $this->query->bind = array('ii',&$invoice_id,&$container_no);
            $date_query = $this->query->run();
            $supp_result = $date_query->fetch_assoc();
            $last_due_date = $date_query->num_rows() > 0 ? $supp_result['due_date'] : $main_invoice_date;


            $storage->trade_type__id = $this->trade_type;
            $actual_arrival = $container_result['act_arrival'];
            $gated_date = $container_result['trade_id'] == 1 ? $actual_arrival : $container_result['date'];
            $storage->p_date = $this->p_date;
            $storage->sup_storage_start_date = $last_due_date;
            $storage->eta_date = (new \DateTime($gated_date))->format('Y-m-d');
            $storage->extra_days = $customer_billing['extra_free_rent_days'] != null ? $customer_billing['extra_free_rent_days'] : 0;
            $storage->billing_group = $customer_billing['id'];
            $date4 = new \DateTime($this->p_date);
            $this->d_date = $date4->format('Y-M-d');
            $date5 = new \DateTime($last_due_date);
            $storage_d = $date5->format('Y-M-d');
            $this->storage_date =  $storage_d;
            $this->monitoring_date =  (new \DateTime($actual_arrival))->format('Y-M-d');

            $prefix = $this->proforma_prefix;

            $this->load_container($container_no);
            $this->proforma_prefix = $prefix;


            $this->get_activities($container_no);

            $this->query->query("SELECT SUM(supplementary_invoice_details.cost) as cost_sum from supplementary_invoice_details 
                          inner join supplementary_invoice on supplementary_invoice.id = supplementary_invoice_details.invoice_id 
                          WHERE supplementary_invoice_details.container_id =? AND supplementary_invoice_details.product_key LIKE \"S\" AND supplementary_invoice.status = 'PAID'");
            $this->query->bind = array('i',&$container_no);
            $total_supp_result = $this->query->run();
            $total_supp_cost = $total_supp_result->fetch_assoc()["cost_sum"];

            $total_supp_cost = $total_supp_cost ? $total_supp_cost : 0;


            $this->query->query("SELECT cost  from invoice_details WHERE container_id =? AND product_key LIKE \"S\" AND invoice_id =?");
            $this->query->bind = array('ii',&$container_no,&$invoice_id);
            $invoice_result = $this->query->run();
            $invoice_storage_cost = $invoice_result->fetch_assoc()["cost"];
            $invoice_storage_cost = $invoice_storage_cost ? $invoice_storage_cost : 0;

            $charge_till_date = $total_supp_cost + $invoice_storage_cost;
            $storage->base_currency = $this->base_currency;
            $storage_charges = $storage->chargeStorage($container_no);
            $storage_charges = $storage_charges - $charge_till_date;

            $this->monitoring_start = date_add(new \DateTime($last_due_date), date_interval_create_from_date_string("1 day"));
            $this->monitoring_start = date_format($this->monitoring_start, "Y-M-d");
            $this->monitoring_end = new \DateTime($this->p_date);
            $this->monitoring_end = date_format($this->monitoring_end, "Y-M-d");
            $this->monitoring_days = (abs(round(strtotime($this->monitoring_end) - strtotime($this->monitoring_start)))/86400) + 1;

            $monitoring_charges = $this->calculate_monitoring_charges();
            $this->monitor_day = $this->monitoring_days == 1 || $this->monitoring_days == 0 ? 'day' : "days";
            $storage->rate = $storage->rate == NULL ? 0 : $storage->rate;

            $this->query->query("select gate_record.id from gate_record left join container on container.id = gate_record.container_id where ucl_status = 1 and container_id = ? and container.gate_status <> 'GATED OUT'");
            $this->query->bind = array('i', &$container_no);
            $this->query->run();
            $ucl_check = $this->query->fetch_assoc();

            if ($ucl_check['id']){
                $ucl_fixed_charges = $this->calculate_ucl_charges($container_nu);

                $this->query->query("INSERT INTO `$this->invoice_temp_tbl`(description,product_key,container_id,cost, exchange_rate, qty, total_cost)
                              VALUES(?,'U',?,?, ?,1,? *1)");
                $ucl_info = $this->create_ucl_info();
                $this->query->bind = array('siddd', &$ucl_info, &$container_no, &$ucl_fixed_charges, &$this->ucl_rates, &$ucl_fixed_charges);
                $this->query->run();
            }
            else{
                if ($storage_charges != 0){

                    $due_last_date = date_create($last_due_date);
                    $due_last_date = date_add($due_last_date,date_interval_create_from_date_string("1 day"));
                    $free_days_end = date_add(date_create($storage->eta_date), date_interval_create_from_date_string(($storage->standard_free_days+$storage->extra_days)."day"));
                    $first_day_charged = date_format($free_days_end,'Y-m-d');
                    $first_day_charged = date_add(date_create($first_day_charged),date_interval_create_from_date_string("1 day"));
                    $current_pay_start_date = $first_day_charged < $due_last_date ? date_format($due_last_date,'Y-M-d') : date_format($first_day_charged,'Y-M-d');

                    $start_date = strtotime($current_pay_start_date);
                    $end_date = strtotime($this->p_date);
                    $billable_days = $end_date - $start_date;
                    $days_charged = abs(round($billable_days / 86400));
                    $this->storage_days =  $days_charged+1;
                    $this->days = $this->storage_days == 1 ? 'day' : "days";
                    $this->storage_date = $current_pay_start_date;

                    $this->query->query("INSERT INTO `$this->invoice_temp_tbl`(description,product_key,container_id,cost, exchange_rate, qty, total_cost)
                              VALUES(?,'S',?,?, ?,1,? *1)");
                    $storage_info = $this->create_storage_info();
                    $this->query->bind = array('siddd', &$storage_info, &$container_no, &$storage_charges, &$storage->rate, &$storage_charges);
                    $this->query->run();
                }

                if($this->p_date >$last_due_date && $monitoring_charges > 0) {
                    $this->query->query("INSERT INTO `$this->invoice_temp_tbl`(description,product_key,container_id,cost, exchange_rate, qty, total_cost)
                              VALUES(?,'M',?,?, ?,1,? *1)");
                    $monitoring_info = $this->create_monitoring_info();
                    $this->query->bind = array('siddd', &$monitoring_info, &$container_no, &$monitoring_charges, &$this->monitoring_rate, &$monitoring_charges);
                    $this->query->run();
                }
            }

            $this->container_log_update($container_no);
        }

        $this->proforma_prefix = $prefix;


        $this->query->bind = array();
        $this->query->query("SELECT SUM(total_cost) AS subtotal FROM $this->invoice_temp_tbl");
        $res3 = $this->query->run();
        $result12 = $res3->fetch_assoc();
        $this->sub_total = $result12['subtotal'];

        if($this->apply_waiver) {
            switch ($this->waiver_type){
                case 1:
                    $this->waiver_value = max(0, min(100, $this->waiver_value));
                    $amount = ($this->waiver_value / 100) * $this->sub_total;
                    $this->waiver_amount = round( $amount,2);
                    break;
                case 2:
                    $percentage = $this->waiver_amount *100 / $this->sub_total;
                    $this->waiver_percentage = max(0, min(100, $percentage));
                    break;
                default:
                    $this->waiver_amount = 0;
                    $this->waiver_percentage = 0;
                    break;
            }
        }

        $sub_total = $this->sub_total - $this->waiver_amount;
        $this->sub_total = $sub_total < 0 ? 0 : $sub_total;



        $this->user_id = isset($_SESSION['id']) ? $_SESSION['id'] : 1;

        if ($this->trade_type == '1' || $this->trade_type == '3' || $this->trade_type == '4' || $this->trade_type == '8'){
            $gen_tax = $tax->generateTax($this->sub_total);
            if(!$this->is_proforma) {
                $this->insert_sup_invoice($gen_tax);
            }
            else {
                $this->insert_profroma_sup_invoice($gen_tax);
            }
            $this->invoice = $this->proforma_prefix.'supplementary_invoice';
            $this->tax_details = $this->proforma_prefix.'supplementary_invoice_details_tax';
            if ($this->tax_type == '1') {
                $this->load_generic_tax();
            }
            elseif($this->tax_type == '2'){
                $this->load_ghana_tax();
            }
            elseif ($this->tax_type == '4'){
                $this->vat_tax_exempt();
            }
        }


        $this->result_array['d_date'] = $this->due_date2;
        $this->invoice = $this->proforma_prefix.'supplementary_invoice';
        $this->invoice_details_tbl = $this->proforma_prefix.'supplementary_invoice_details';
        $this->invoice_history_tbl = $this->proforma_prefix.'supplementary_invoice_history_log';


        $this->invoice_container_tbl = $this->proforma_prefix.'supplementary_invoice_container';
        $this->insert_container_invoice_details($container_number);
        $this->insert_invoice_history();

        $this->query->commit();

        new Respond(2035,array('ttyp'=>$this->trade_type,'sinv'=>$this->invoice_number));
    }

    private function getActivity($id){
        $this->query->query("select name from depot_activity where id = ?");
        $this->query->bind = array('i', &$id);
        $run = $this->query->run();
        return $run->fetch_assoc()['name'];
    }

    private function calculate_ucl_charges($container){
        $ucl_charges = "";
        $query = new MyTransactionQuery();
        $query->query("select 20ft_charge,40ft_charge,45ft_charge from ucl");
        $query->run();
        $ucl_result = $query->fetch_assoc();

        $query->query("select container_isotype_code.length from container left join container_isotype_code on container_isotype_code.id = container.iso_type_code where container.number = ? and container.gate_status != 'GATED OUT'");
        $query->bind = array('s',&$container);
        $query->run();
        $result = $query->fetch_assoc();

        switch ($result['length']){
            case 20:
                $this->ucl_rates = $ucl_result['20ft_charge'];
                $ucl_charges = $ucl_result['20ft_charge'];
                break;
            case 40:
                $this->ucl_rates = $ucl_result['40ft_charge'];
                $ucl_charges = $ucl_result['40ft_charge'];
                break;
            case 45:
                $ucl_charges = $ucl_result['45ft_charge'];
                break;
        }
        $query->commit();
        return round($ucl_charges,2);

    }

    private function checkActivityCharges($activity_id){
        if (!$this->quote_currency) {
            $activity = $this->getActivity($activity_id);

            if ($activity) {
                new Respond(1210, array('len' => $this->length, 'stat' => $activity_id == 6 ? $this->load_status : "ANY", "act" => $activity, "good" => $this->goods));
            }
        }

        if ($this->base_currency != $this->quote_currency) {
            $this->query->query("select id, buying, selling from exchange_rate where base in (select id from currency where code =  ? ) and quote in (select id from currency where code =  ?) order by date DESC");
            $this->query->bind = array('ss', &$this->base_currency, &$this->quote_currency);
            $run = $this->query->run();

            $isBase = true;

            if (!$run->num_rows()) {
                $this->query->query("select id, buying, selling from exchange_rate where quote in (select id from currency where code =  ? ) and base in (select id from currency where code =  ?) order by date DESC");
                $this->query->bind = array('ss', &$this->base_currency, &$this->quote_currency);
                $run = $this->query->run();

                $isBase = false;

                if (!$run->num_rows()) {
                    $this->query->rollback();
                    $this->query->commit();
                    new Respond(1211, array('base' => $this->base_currency, 'quote' => $this->quote_currency));
                }
            }

            $rates = $run->fetch_assoc();
            if ($isBase) {
                $exc_rate = $rates['buying'];

                $this->cost = $this->cost / $exc_rate;
            } else {
                $exc_rate = $rates['selling'];

                $this->cost = $this->cost * $exc_rate;
            }
        }
    }

    public function get_activities($container_no){
        $activity_query = new MyQuery();
        $activity_query->query("select activity_id from ".$this->proforma_prefix."container_log inner join depot_activity 
                  on depot_activity.id = ".$this->proforma_prefix."container_log.activity_id where depot_activity.billable = '1' and invoiced = false and container_id =?");
        $activity_query->bind = array('i',&$container_no);
        $activity_query->run();

        while ($activity_result = $activity_query->fetch_assoc()) {


            switch ($activity_result['activity_id']) {
                case(1):
                    $this->get_primary_activity_details($activity_result['activity_id']);
                    $this->insert_depot_activity();
                    break;
                case(2):
                    $this->get_primary_activity_details($activity_result['activity_id']);
                    $this->insert_depot_activity();
                    break;
                case(6):
                    $this->get_stuffing_restuffing_details($activity_result['activity_id']);
                    $this->insert_depot_activity();
                    break;
                default:
                    $this->get_secondary_activity($activity_result['activity_id']);
                    $this->insert_depot_activity();
                    break;
            }

            $this->checkActivityCharges($activity_result['activity_id']);

            $rate = 0;

            $this->query->query("INSERT INTO `$this->invoice_temp_tbl`(description,product_key,container_id,cost, exchange_rate,qty,total_cost)
                                    VALUES(?,?,?,?,?,1,? *1)");
            $this->query->bind = array('siiddd', &$this->depot_activity, &$this->depot_id, &$container_no, &$this->cost, &$rate, &$this->cost);
            $this->query->run();

        }
    }

    private function getSupplementaryBNumber($invoiceNumber, $tradeType) {
        $queryString = "";

        switch($tradeType) {
            case 1:
            case 3:
                $queryString = "SELECT bl_number FROM invoice WHERE number = ?";
                break;

            case 4:
                $queryString = "SELECT book_number FROM invoice WHERE number = ?";
                break;

            default:
                $queryString = "SELECT book_number FROM invoice WHERE number = ?";
                break;
        }
        $qu=new MyQuery();
        $qu->query($queryString);
        $qu->bind = array('s', &$invoiceNumber);
        $res=$qu->run();

        $result = $res->fetch_assoc();

        $number = '';

        switch($tradeType) {
            case 1:
            case 3:
                $number = $result['bl_number'];
                break;

            case 4:
                $number = $result['book_number'];
                break;
            
            default:
                $number = $result['book_number'];
                break;               
        }

        return $number;        
    }

}

?>