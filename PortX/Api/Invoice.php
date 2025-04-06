<?php

namespace Api;

// ini_set("display_errors",1);

// session_start();

use
    Lib\ACL,
    Lib\MyQuery,
    Lib\Customer,
    Lib\User,
    Lib\MyTransactionQuery,
    Lib\InvoiceBilling,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    Lib\Respond,
    Lib\TaxType\Exempt,
    Lib\TaxType\VatExempt,
    Lib\TaxType\Compound,
    Lib\TaxType\Simple,
    DataTables\Editor\ValidateOptions;
use PDO;

class Invoice{

    private $request;

    public function  __construct($request)
    {
        $this->request = $request;
    }

    function table(){
        $unapproved = $this->request->param('unap');
        $paid = $this->request->param('pid');
        $set = isset($unapproved);

        $db = new Bootstrap();
        $db = $db->database();
        Editor::inst($db, 'invoice')
            ->fields(
                Field::inst('invoice.trade_type')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('invoice.number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.bl_number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.book_number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                )),
                Field::inst('invoice.do_number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.bill_date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.due_date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.cost')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.tax')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.tax_type')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.id as note')
                    ->getFormatter(function($val){
                        $query = new MyQuery();
                        $query->query("SELECT note FROM invoice_note WHERE invoice_id=?");
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
                Field::inst('invoice.user_id')
                    ->setFormatter(function ($val) {
                        return isset($_SESSION['id']) ? $_SESSION['id'] : 1;
                    })
                    ->getFormatter(function ($val) {
                        $qu = new MyQuery();
                        $qu->query("SELECT first_name, last_name FROM user WHERE id = ?");
                        $qu->bind = array('i', &$val);
                        $res = $qu->run();
                        $result = $res->fetch_assoc();
                        $first_name = $result['first_name'];
                        $last_name = $result['last_name'];
                        $full_name = "$first_name" . " " . "$last_name";
                        return $full_name ?? '';
                    }),
                Field::inst('invoice.date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.status')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('trade_type.name'),
                Field::inst('tax_type.name'),
                Field::inst('customer.name'),
                Field::inst('currency.code'),
                Field::inst('invoice.approved')
                    ->getFormatter(function ($val) {
                        return $val ? "YES" : "NO";
                    }),
                Field::inst('customer.id')
                    ->getFormatter(function ($val, $row) {
                        $qu = new MyQuery();
                        $qu->query("select outstanding from payment where invoice_id = ? order by date desc");
                        $qu->bind = array('i', &$row['invoice.id']);
                        $res = $qu->run();
                        $outstanding = $res->fetch_num()[0] ?? '';
                        if (!$outstanding) {
                            $qu = new MyQuery();
                            $qu->query("select (cost + tax) as cost from invoice where number = ?");
                            $qu->bind = array('s', &$row['invoice.number']);
                            $res = $qu->run();
                            $result = $res->fetch_assoc();
                            $cost = $result['cost'];
                            return $cost;
                        } else
                            return $outstanding;
                    }),
                Field::inst('invoice.id as invn')
                ->getFormatter(function($val){
                    $query = new MyQuery();
                    $query->query("select id from invoice_note where invoice_id=?");
                    $query->bind = array('i',&$val);
                    $query->run();
                    $result = $query->fetch_assoc();
                    return $result['id'];
                }),
                Field::inst('invoice.id as invs')
                ->getFormatter(function($val){
                    return $this->check_invoice_status($val) ? 'recallable' : 'not_recallable';
                }),
                Field::inst('invoice.id as invi'),
                Field::inst('invoice.id')
                    ->getFormatter(function ($val, $row) {
                        $qu = new MyQuery();
                        $qu->query("SELECT invoice_id, outstanding FROM payment WHERE invoice_id = ? ");
                        $qu->bind = array('i', &$val);
                        $res = $qu->run();
                        return $res->fetch_num()[0] ?? '';
                    })
            )
            ->leftJoin('trade_type', 'trade_type.id', '=', 'invoice.trade_type')
            ->leftJoin('tax_type', 'tax_type.id', '=', 'invoice.tax_type')
            ->leftJoin('currency', 'currency.id', '=', 'invoice.currency')
            ->leftJoin('customer', 'customer.id', '=', 'invoice.customer_id')
            ->where(function ($q) use ($unapproved, $set) {
                if ($unapproved) {
                    $q->where('invoice.id', "(SELECT id FROM invoice WHERE status != 'CANCELLED' and status != 'EXPIRED' and status != 'PAID' and approved = 0)", 'IN', false);
                }

                if($set && !$unapproved){
                    $q->where('invoice.id', "(SELECT id FROM invoice WHERE status != 'CANCELLED' and status != 'EXPIRED' and status != 'PAID' and approved = 1)", 'IN', false);
                }
            })
            ->process($_POST)
            ->json();
    }

    public function get_notes($invoice_id){
        $query = new MyQuery();
        $query->query("SELECT note FROM invoice_note WHERE invoice_id=?");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        $result = $query->fetch_assoc();
        $note = $result['note'];
        return $note;
    }

    function payment_table(){
        $unapproved = $this->request->param('unap');
        $set = isset($unapproved);

        $db = new Bootstrap();
        $db = $db->database();
        Editor::inst($db, 'invoice')
            ->fields(
                Field::inst('invoice.trade_type')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('invoice.number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.bl_number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.book_number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                    ->message('A reference is required')
                )),
                Field::inst('invoice.do_number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.bill_date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.due_date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.cost')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.tax')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.tax_type')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.note'),
                Field::inst('invoice.customer_id')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.user_id')
                    ->setFormatter(function ($val) {
                        return isset($_SESSION['id']) ? $_SESSION['id'] : 1;
                    })
                    ->getFormatter(function ($val) {
                        $qu = new MyQuery();
                        $qu->query("SELECT first_name, last_name FROM user WHERE id = ?");
                        $qu->bind = array('i', &$val);
                        $res = $qu->run();
                        $result = $res->fetch_assoc();
                        $first_name = $result['first_name'];
                        $last_name = $result['last_name'];
                        $full_name = "$first_name" . " " . "$last_name";
                        return $full_name ?? '';
                    }),
                Field::inst('invoice.date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.status')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('trade_type.name'),
                Field::inst('tax_type.name'),
                Field::inst('customer.name'),
                Field::inst('currency.code'),
                Field::inst('invoice.approved')
                    ->getFormatter(function ($val) {
                        return $val ? "YES" : "NO";
                    }),
                Field::inst('customer.id')
                    ->getFormatter(function ($val, $row) {
                        $qu = new MyQuery();
                        $qu->query("select outstanding from payment where invoice_id = ? order by date desc");
                        $qu->bind = array('i', &$row['invoice.id']);
                        $res = $qu->run();
                        $outstanding = $res->fetch_num()[0] ?? '';
                        if (!$outstanding) {
                            $qu = new MyQuery();
                            $qu->query("select (cost + tax) as cost from invoice where number = ?");
                            $qu->bind = array('s', &$row['invoice.number']);
                            $res = $qu->run();
                            $result = $res->fetch_assoc();
                            $cost = $result['cost'];
                            return $cost;
                        } else
                            return $outstanding;
                    }),
                Field::inst('invoice.id')
                    ->getFormatter(function ($val, $row) {
                        $qu = new MyQuery();
                        $qu->query("SELECT invoice_id, outstanding FROM payment WHERE invoice_id = ? ");
                        $qu->bind = array('i', &$val);
                        $res = $qu->run();
                        return $res->fetch_num()[0] ?? '';
                    })
            )
            ->leftJoin('trade_type', 'trade_type.id', '=', 'invoice.trade_type')
            ->leftJoin('tax_type', 'tax_type.id', '=', 'invoice.tax_type')
            ->leftJoin('currency', 'currency.id', '=', 'invoice.currency')
            ->leftJoin('customer', 'customer.id', '=', 'invoice.customer_id')
            ->where(function ($q) use ($unapproved, $set) {
                if ($unapproved) {
                    $q->where('invoice.id', "(SELECT id FROM invoice WHERE status != 'CANCELLED' and status != 'EXPIRED' and status != 'PAID' and approved = 0)", 'IN', false);
                }

                if($set && !$unapproved){
                    $q->where('invoice.id', "(SELECT id FROM invoice WHERE status != 'CANCELLED' and status != 'EXPIRED'  and approved = 1)", 'IN', false);
                }
            })
            ->process($_POST)
            ->json();
    }

    public function defer_invoice()
    {
        $defer_date = $this->request->param('defd');
        $defer_date = date("Y-m-d 23:59:59", strtotime($defer_date));
        $invoice_number = $this->request->param('invn');
        $type = $this->request->param('invt');
        $date = date('Y-m-d');
        $deferred_note = $this->request->param('defn') ?? "";

        $defer_date = $defer_date." 23:59:59";
        $user_id = $_SESSION['id'];

        $invoice_table = "invoice";
        $invoice_history_table = "invoice_history_log";
        if ($type == 2) {
            $invoice_table = "supplementary_invoice";
            $invoice_history_table = "supplementary_invoice_history_log";
        }

        $query = new MyQuery();
        $query->query("SELECT id,approved from $invoice_table  WHERE number = ? ");
        $query->bind = array('s', &$invoice_number);
        $run = $query->run();
        $result = $run->fetch_assoc();
        $approved = $result['approved'];
        $invoice_id = $result['id'];
        if (!$approved) {
            new Respond(1117);
        }

        if ($defer_date >= $date) {
            $query = new MyTransactionQuery();
            $query->query("update $invoice_table set deferral_by = ?, status = 'DEFERRED', deferral_note = ?,  deferral_date = ? 
                      where number = ?");
            $query->bind = array('isss',&$user_id,&$deferred_note, &$defer_date, &$invoice_number);
            $query->run();

            $query->query("insert into $invoice_history_table(invoice_id, status, user_id) values (?,'DEFERRED',?)");
            $query->bind = array('ii', &$invoice_id,&$user_id);
            $query->run();

            $query->commit();
            new Respond(2114);
        } else {
            new Respond(1125);
        }
    }

    public function add_waiver() {
        $waiver_type = $this->request->param('wvrt');
        $waiver = $this->request->param('wvr');
        $waiver_note = $this->request->param('wvrn');
        $invoice_number = $this->request->param('invn');
        $is_profoma = $this->request->param('prof') ?? false;
        $type = $this->request->param('invt');
        if ($waiver <= 0) {
            new Respond(1122);
        }

        $query = new MyQuery();

        $proforma_prefix = $is_profoma ? "proforma_" : "";


        if ($type == 1) {
            if (!$is_profoma) {
                ACl::verifyCreate("invoice-waivers");
            } else {
                ACl::verifyCreate("proforma-invoice-records");
            }
            $invoice_table = $proforma_prefix . "invoice";
            $invoice_history_table =  $proforma_prefix . "invoice_history_log";
            $query->query("SELECT id, " . ($is_profoma ? "" : "approved, status,") . " cost, waiver_amount, tax_type from " . $proforma_prefix . "invoice  WHERE number = ? ");
            $query->bind = array('s', &$invoice_number);
        } elseif ($type == 2) {
            if (!$is_profoma) {
                ACl::verifyCreate("supplementary-invoice-waivers");
            } else {
                ACl::verifyCreate("proforma-supplementary-invoice-records");
            }
            $invoice_table = $proforma_prefix . "supplementary_invoice";
            $invoice_history_table =  $proforma_prefix . "supplementary_invoice_history_log";
            $query->query("SELECT " . $proforma_prefix . "supplementary_invoice.id, " . ($is_profoma ? "" : "supplementary_invoice.status, supplementary_invoice.approved as approved, ") . " " . $proforma_prefix . "supplementary_invoice.cost, " . $proforma_prefix . "supplementary_invoice.waiver_amount, invoice.tax_type from " . $proforma_prefix . "supplementary_invoice  
                    inner join invoice on invoice.id = " . $proforma_prefix . "supplementary_invoice.invoice_id WHERE " . $proforma_prefix . "supplementary_invoice.number = ?");
            $query->bind = array('s', &$invoice_number);
        }

        $run = $query->run();
        $row = $run->fetch_assoc();
        $approved = $row['approved'];
        $cost = $row['cost'];
        $waiver_amt = $row['waiver_amount'];
        $tax_type = $row['tax_type'];
        $id = $row['id'];

        if(!$is_profoma) {
            if (!$approved) {
                new Respond(1117);
            }
        }

        $status = $row['status'];
        if ($status != "UNPAID" && $status != "DEFERRED") {
            new Respond(1120);
        }

        if ($waiver_amt > 0) {
            new Respond(1121);
        }

        $waiver_pct = 0;

        if ($waiver_type == 0) {
            if ($waiver > $cost) {
                new Respond(1124);
            }

            $waiver_pct = $waiver / $cost * 100;

            $waiver_pct = max(0, min(100, $waiver_pct));

            $waiver_amt = $waiver;
        } else if ($waiver_type == 1) {
            if ($waiver > 100) {
                new Respond(1124);
            }

            $waiver_amt = $waiver / 100 * $cost;

            $waiver_amt = round($waiver_amt, 2);

            $waiver_pct = $waiver;
        } else {
            new Respond(1123);
        }

        $sub_total = $cost - $waiver_amt;

        $tax = new Compound();

        if ($tax_type == 1) {
            $tax = new Simple();
        } elseif ($tax_type == 3) {
            $tax = new Exempt();
        } elseif ($tax_type == 4) {
            $tax = new VatEXempt();
        }

        $billing = new InvoiceBilling();

        $query = new MyTransactionQuery();
        $query->query("delete from " . $invoice_table . "_details_tax where invoice_id = ?");
        $query->bind = array("i", &$id);
        $query->run();


        $billing->invoice_id = $id;
        $billing->query = $query;
        $billing->invoice = $invoice_table;
        $billing->tax_details = $invoice_table . '_details_tax';
        $billing->invoice_number = $invoice_number;
        $billing->sub_total = $sub_total;
        $billing->tax_type = $tax_type;

        $total_tax = $tax->generateTax($sub_total);

        $user_id = $_SESSION['id'];

        $query->query("update $invoice_table set cost = ?, tax = ?, waiver_pct = ?, waiver_amount = ?, waiver_note = ?, waiver_by = ? where id = ? ");
        $query->bind = array("ddddsdi", &$sub_total, &$total_tax, &$waiver_pct, &$waiver_amt, &$waiver_note, &$user_id, &$id);
        $query->run();

        $query->query("insert into $invoice_history_table(invoice_id, status, user_id) values (?,'WAIVED',?)");
        $query->bind = array('ii', &$billing->invoice_id,&$user_id);
        $query->run();

        if ($tax_type == '1') {
            $billing->load_generic_tax();
        } elseif ($tax_type == '2') {
            $billing->load_ghana_tax();
        } elseif ($tax_type == '4') {
            $billing->vat_tax_exempt();
        }

        $query->commit();

        new Respond(2113);
    }

    public function get_containers(){
        $b_number = $this->request->param('bnum');
        $trade_type = $this->request->param('tdty');
        $customer = $this->request->param('cust');
        $voyage = $this->request->param('vygi');
        $tax_type = $this->request->param('txty');
        $is_proforma = $this->request->param('prof') == 1;

        $b_number_field = $trade_type == '11' || $trade_type == '13' ? "bl_number" : "book_number";

        $this->check_tax($tax_type);

        $response = array();
        $query = new MyTransactionQuery();
        $query->query("select $b_number_field from container where $b_number_field = ?");
        $query->bind = array('s', &$b_number);
        $run = $query->run();
        $result = $run->fetch_assoc();

        if (!Customer::getCustomerID($customer)) {
            new Respond(1111);
        }

        if (!$result["$b_number_field"]) {
            new Respond(1110);
        } else {
            $query->query("SELECT container.number, voyage.actual_arrival,voyage, actual_departure, voyage.reference FROM container INNER JOIN voyage ON voyage.id = container.voyage WHERE container.$b_number_field = ?");
            $query->bind = array('s', &$b_number);
            $run = $query->run();
            $result = $run->fetch_assoc();
            $actual_arrival = $result['actual_arrival'];
            $actual_departure = $result['actual_departure'];
            $voyage_name = $result['reference'];

            if (($actual_arrival == 0 || $actual_departure == 0) && ($trade_type != '21' && $trade_type != '70')) {
                new Respond(1112, array('vyg'=>$voyage_name));
            } else {
                $table_prefix = $is_proforma ? "proforma_" : "";
                $query->query("SELECT DISTINCT number, gate_status, container.id, length FROM container 
                                    INNER JOIN ".$table_prefix."container_log ON container.id =  ".$table_prefix."container_log.container_id
                                    LEFT JOIN container_isotype_code on container_isotype_code.id = container.iso_type_code
                                    WHERE container.gate_status = 'GATED IN' AND container.$b_number_field = ? and  ".$table_prefix."container_log.container_id not in (select container_id from  ".$table_prefix."container_depot_info)");
                $query->bind = array('s', &$b_number);
                $run = $query->run();
                $result = $run->fetch_all(MYSQLI_ASSOC);
                if ($result) {
                    $container_numbers = array();
                    foreach ($result as $row) {
                        array_push($container_numbers, $row['container.number']." (".$row['length']." Ft.)");
                    }

                    $response["cntr"] = $container_numbers;

                }
                $query->query ("select distinct(container.number) as container_number, length from container 
                                    left JOIN  ".$table_prefix."invoice_container on  ".$table_prefix."invoice_container.container_id = container.id 
                                    LEFT JOIN  ".$table_prefix."invoice on  ".$table_prefix."invoice.id =  ".$table_prefix."invoice_container.invoice_id
                                    LEFT JOIN container_isotype_code on container_isotype_code.id = container.iso_type_code
                            where gate_status = 'GATED IN' and ( ".$table_prefix."invoice_container.id is NULL".( $is_proforma ? "" : " or ".$table_prefix."invoice.id in 
                            (select id from  ".$table_prefix."invoice  where cast(deferral_date as date)  > 0 and cast(deferral_date as date ) < CURRENT_DATE and status = \"DEFERRED\" )")." ) and
                container.$b_number_field = ?");
                $query->bind = array('s', &$b_number);
                $run = $query->run();
                $result = $run->fetch_all(MYSQLI_ASSOC);
                if ($result) {
                    $container_numbers = array();
                    foreach ($result as $row) {
                        array_push($container_numbers, $row['container_number']." (".$row['length']." Ft.)");
                    }

                    $response["uninv"] = $container_numbers;
                }
            }
        }
        new Respond(2110, $response);
    }

    private function check_export_voyage($voyage)
    {
        $query = new MyQuery();
        $query->query("select id, actual_arrival, actual_departure from voyage where reference = ?");
        $query->bind = array('s', &$voyage);
        $run = $query->run();
        $result = $run->fetch_assoc();

        if ($run->num_rows() > 0) {
            $arrival = $result['actual_arrival'];
            $departure = $result['actual_departure'];
            $date_message = "";

            if ($arrival == 0) {
                $date_message .= "Actual Arrival ";
            }
            if ($departure == 0) {
                if ($arrival == 0) {
                    $date_message .= "and ";
                }

                $date_message .= "Actual Departure ";
            }

            if ($date_message) {
                new Respond(1118);
            }
            new Respond(2111, array('vyg' => $result['id']));
        } else {
            new Respond(1114);
        }
    }

    public function check_voyage() {
        $voyage = $this->request->param('vygi');
        $trade_type = $this->request->param('tdty');


        if ($trade_type == '21') {
            $this->check_export_voyage($voyage);
        } else {
            new Respond(1113);
        }
    }

    public function get_billing_group() {
        $customer = $this->request->param('cust');
        $trade_type = $this->request->param('trty');

        if($customer){
            $query = new MyQuery();
            $query->query("select customer_billing_group.name, trade_type.code from customer_billing_group 
									inner join trade_type on customer_billing_group.trade_type = trade_type.id
                                    inner join customer_billing on customer_billing.billing_group = customer_billing_group.id 
                                  inner join customer on customer.id = customer_billing.customer_id 
                                  where trade_type.code = ? and customer.name = ?");
            $query->bind = array('is', &$trade_type, &$customer);
            $query->run();
            if($name = $query->fetch_assoc()['name']){
                new Respond(2211, array("name"=>$name));
            }
            else {
                new Respond(1119);
            }
        }
    }

    public function add_invoice(){
        $p_date  = $this->request->param('pdate');
        $trade_type = $this->request->param('trty');
        $bl_number = $this->request->param('bnum');
        $containers = $this->request->param('cont');
        $tax_type = $this->request->param('tax');
        $currency = $this->request->param('curr');
        $note = $this->request->param('note');
        $customer = $this->request->param('cust');
        $waiver_value = 0;
        $is_waiver_applied = false;
        $waiver_type = 1;
        $waiver_note = "";
        $boe_number = $this->request->param('bonum');
        $do_number = $this->request->param('dnum');
        $release = $this->request->param('rel');
        $number_container = json_decode($containers);
        $billing = new InvoiceBilling();
        $billing->voyage_id = $this->request->param('voy');
        $billing->is_proforma = $this->request->param('prof');
        $billing->p_date = $p_date != null ? $p_date : date('Y-m-d');
        $billing->trade_type = $trade_type;
        $billing->b_number = $bl_number;
        $billing->do_number = $do_number;
        $billing->boe_number = $boe_number;
        $billing->release_instructions = $release;
        $billing->tax_type = $tax_type;
        $billing->waiver_note = $waiver_note;
        $billing->base_currency = $currency;
        $billing->note = $note;
        $billing->customer = $customer;
        $billing->waiver_amount = $waiver_type == 2? $waiver_value : 0;
        $billing->waiver_percentage = $waiver_type == 1? $waiver_value : 0;
        $billing->waiver_value = $waiver_value;
        $billing->apply_waiver = $is_waiver_applied;
        $billing->waiver_type = $waiver_type;

        $charges = $billing->calculate_charges($number_container);

        if ($charges > 0){
            $billing->trade_type = $trade_type;
            $billing->generate_invoice($number_container);
        }
        else {
            new Respond(1212);
        }

    }

    public function container_check(){
        $tradeType = $this->request->param('trty');
        $bNumber = $this->request->param('bnum');
        $containerSel = json_decode($this->request->param('ctnr'));
        $is_proforma = $this->request->param('prof') == 1;

        $query = new MyTransactionQuery();

        if ($tradeType != '70') {
            $this->check_container_info($containerSel,$is_proforma, $tradeType);
        }

        $this->check_ucl_container($containerSel, $query);

        $this->flag_check($containerSel, $query);
        $this->check_invoice($tradeType, $bNumber, $containerSel, $is_proforma, $query);

        new Respond(2112);
    }

    public function check_ucl_container($containerSel, $db_link){
        $bind_types = "";
        $bind_mask = "";
        foreach ($containerSel as $id) {
            $bind_types = $bind_types . 's';
            $bind_mask = $bind_mask . '?,';
        }

        $bind_mask = rtrim($bind_mask, ',');
        $bind_data = array($bind_types);

        foreach ($containerSel as $id) {
            $bind_data[] = &$id;
        }
       $db_link->query("select ucl_status from container left join gate_record on gate_record.container_id = container.id where container.gate_status <>'GATED OUT' and container.number in ($bind_mask)");
       $db_link->bind = $bind_data;
       $db_link->run();


        if($db_link->num_rows()) {
            $ucl_container = array();
            $depot_container = array();
            while($container_sel = $db_link->fetch_assoc()){

                if ($container_sel['ucl_status'] == 1){
                    array_push($ucl_container,$container_sel['ucl_status']);
                }
                if ($container_sel['ucl_status'] == 0){
                    array_push($depot_container,$container_sel['ucl_status']);
                }
            }

            if (!empty($ucl_container) && !empty($depot_container)){
                new Respond(1560);
            }

            if (!empty($ucl_container)){
                new Respond(1570);
            }

        }
    }

    public function get_charges(){
        $p_date  = $this->request->param('p_date');
        $trade_type = $this->request->param('trade');
        $containers = $this->request->param('container');
        $tax_type = $this->request->param('tax_type');
        $currency = $this->request->param('currency');
        $customer = $this->request->param('cust');
        $number_container = json_decode($containers);
        $charge = new InvoiceBilling();
        $charge->p_date = $p_date;
        $charge->trade_type = $trade_type;
        $charge->tax_type = $tax_type;
        $charge->base_currency = $currency;
        $charge->customer = $customer;
        $charge->calculate_charges($number_container);
    }

    public function check_tax($tax_type){

        if ($tax_type != 3 && $tax_type != 4) {
            $query = new MyQuery();
            $query->query("select id from tax where type =?");
            $query->bind = array('i',&$tax_type);
            $query->run();
            $result = $query->fetch_assoc();
            if (!$result['id']) {
                new Respond(1119);
            }   
        }
    }

    private function flag_check($containers, $db_link)
    {
        $result = array();
        $count = 0;

        $bind_types = "";
        $bind_mask = "";

        foreach ($containers as $id) {
            $bind_types = $bind_types . 's';
            $bind_mask = $bind_mask . '?,';
        }

        $bind_mask = rtrim($bind_mask, ',');

        $db_link->query("SELECT number FROM container WHERE number  in ($bind_mask)  AND gate_status != 'GATED OUT' AND status = 1");
        $bind_data = array($bind_types);
        foreach ($containers as $id) {
            $bind_data[] = &$id;
        }

        $db_link->bind = $bind_data;

        $run = $db_link->run();

        if($run->num_rows()) {
            $res = $run->fetch_all();
            while ($count < count($res)) {
                array_push($result, $res[$count++][0]);
            }

            new Respond(1115, array('flag'=>$result));
        }

    }

    private function check_invoice($tradeType, $bNumber, $containerSel, $is_proforma,MyTransactionQuery $db_link){
        $return = array();
        $bind_types = "is";
        $bind_mask = "";
        foreach ($containerSel as $id) {
            $bind_types = $bind_types . 's';
            $bind_mask = $bind_mask . '?,';
        }

        $table_prefix = $is_proforma ? "proforma_" : "";

        $bind_mask = rtrim($bind_mask, ',');
        $bind_data = array($bind_types, &$tradeType, &$bNumber);

        foreach ($containerSel as $id) {
            $bind_data[] = &$id;
        }

        $b_field = $tradeType == 11 ? "bl_number" : "book_number";
        $db_link->query( "SELECT  ".$table_prefix."invoice.number FROM  ".$table_prefix."invoice INNER JOIN  ".$table_prefix."invoice_container ON  ".$table_prefix."invoice.id =  ".$table_prefix."invoice_container.invoice_id
                  INNER JOIN container ON  ".$table_prefix."invoice_container.container_id = container.id WHERE  ".$table_prefix."invoice.trade_type = (SELECT id
                  FROM trade_type WHERE code = ?) AND  ".$table_prefix."invoice.$b_field = ?  AND container.number IN ($bind_mask)
                  AND  ".$table_prefix."invoice.status != 'CANCELLED' and  ".$table_prefix."invoice.status != 'EXPIRED'".($is_proforma ? "" : " and deferral_date > CURRENT_DATE "));
        $db_link->bind = $bind_data;
        $run=$db_link->run();

        $result = $run->fetch_all();

        if($run->num_rows()) {
            $invoice = $result[0][0];

            if ($invoice) {
                $db_link->query("SELECT status FROM  ".$table_prefix."invoice WHERE number = ?");
                $db_link->bind = array('s', &$invoice);
                $run = $db_link->run();
                $result = $run->fetch_assoc();
                $status = $result['status'];

                if ($status == 'PAID') {
                    $return[] = "PAID";

                } elseif ($status == 'UNPAID') {
                    $return[] = "UNPAID";
                }
                $return[] = $invoice;

                new Respond(1116, array('invs' => $return));
            }
        }
    }

    private function check_invoice_status($invoice_id){
        $query = new MyQuery();
        $query->query("select cancelled from invoice_status where invoice_id=?");
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

    public function add_note(){
        $invoice_number = $this->request->param('invn');
        $note = $this->request->param('note');
        $note_type = $this->request->param('ntype');
        $user_id = $_SESSION['id'];

     

        if ($note == ""){
            new Respond(122);
        }
        $query = new MyTransactionQuery();
        $query->query("select id from invoice where (status = 'UNPAID' or status = 'DEFERRED' or status='CANCELLED' or status='RECALLED') and number=?");
        $query->bind = array('s',&$invoice_number);
        $query->run();
        if ($query->num_rows() == 0){
            new Respond(123);
        }
        $result = $query->fetch_assoc();
        $invoice_id = $result['id'];
        $query->query("select id from invoice_note where invoice_id=? and note_type=?");
        $query->bind = array('is',&$invoice_id,&$note_type);
        $query->run();
        $result1 = $query->fetch_assoc();
    
        if(!$result1['id']){
            $query->query("insert into invoice_note(invoice_id,note,note_type,user_id)values(?,?,?,?)");
            $query->bind = array('issi',&$invoice_id,&$note,&$note_type,&$user_id);
            $query->run();
            $query->commit();
            new Respond(260);
        }
        else{
            $query->query("update invoice_note set note =?,user_id=? where id=?");
            $query->bind = array('sii',&$note,&$user_id,&$result1['id']);
            $query->run();
            $query->commit();
            new Respond(260);
        }
       
    }

    public function check_container_info($containerSel,$is_proforma, $tradeType){
        $table_prefix = $is_proforma ? "proforma_" : "";
        $query = new MyTransactionQuery();
        foreach($containerSel as $containers){
            $query->query("select id from container where number = ? 
                            AND trade_type_code = ?");
            $query->bind = array('ss',&$containers, &$tradeType);
            $query->run();
            $result = $query->fetch_assoc();
            $query->query("select id from ".$table_prefix."container_depot_info where container_id=?");
            $query->bind = array("i",&$result['id']);
            $query->run();
            $result1 = $query->fetch_assoc();
        // var_dump($result1['id']);die;
            if(!$result1['id']){
                $query->commit();
                new Respond(1432);
            }
        }
        $query->commit();
    }

}

?>