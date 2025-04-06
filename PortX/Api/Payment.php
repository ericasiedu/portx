<?php

namespace Api;
session_start();

use
    Lib\ACL,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\ReceiptGenerate,
    Lib\MyQuery,
    Lib\MyTransactionQuery;

class Payment{

    public $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){

        $activity = $this->request->param('activity');

        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'payment')
            ->fields(
                Field::inst('payment.receipt_number')
                    ->set(Field::SET_CREATE),
                Field::inst('payment.invoice_id')
                    ->setFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select id from invoice where number =?");
                        $query->bind = array('s',&$val);
                        $result = $query->run();
                        return $result->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(function ($val, $field, $data, $host){
                        $query = new MyQuery();
                        $query->query("select id from invoice where number =?");
                        $query->bind = array('s',&$val);
                        $result = $query->run();
                        $result = $result->fetch_assoc();
                        $invoice_id = $result['id'];

                        $invoice_query=new MyQuery();
                        $invoice_query->query("SELECT invoice.status as invoice_status, invoice.approved as approved, container.status, container.gate_status FROM container INNER JOIN invoice_container 
                            ON invoice_container.container_id = container.id INNER JOIN invoice ON invoice.id = invoice_container.invoice_id 
                            WHERE invoice.id = ? ");
                        $invoice_query->bind =  array('i', &$invoice_id);
                        $invoice_query->run();
                        $result1 = $invoice_query->fetch_assoc();
                        $approved = $result1['approved'];
                        $status = $result1['status'];
                        $gate_status = $result1['gate_status'];
                        $invoice_status = $result1['invoice_status'];
                        if(!$approved)
                        {
                            return "Invoice has not been approved";
                        }
                        if ($status == 1){
                            return "Container is flagged";
                        }
                        if ($gate_status == 'GATED OUT' && $invoice_status != 'DEFERRED'){
                            return "Deferred container already gated out";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('payment.paid')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(function ($val, $data, $field, $host) {

                        $invoice_id = $data['payment']['invoice_id'];

                        $query = new MyQuery();
                        $query->query("select (cost + tax) as cost from invoice where number =?");
                        $query->bind = array('s',&$invoice_id);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $cost = $result['cost'];
                        if ($val > $cost) {
                            return "Cannot make payments more than the cost ($cost)";
                        }
                        if ($val != $cost){
                            return "Amount paid must be exact pending charge ($cost)";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('payment.outstanding'),
                Field::inst('payment.mode'),
                Field::inst('payment.bank_name')
                    ->setFormatter(function ($val) {
                        if($val != ""){
                            $query = new MyQuery();
                            $query->query("select id from bank where name =?");
                            $query->bind = array('s',&$val);
                            $query->run();
                            return $query->fetch_num()[0] ?? '';
                        }
                        else{
                            return true;
                        }
            
                    })
                    ->validator(function ($val, $data, $field, $host){
                        $payment_mode = $data['payment']['mode'];
                        $query = new MyQuery();
                        $query->query("select id from bank where name = ?");
                        $query->bind = array('s',&$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        if ($payment_mode == '2'){
                            if ($val == ''){
                                return "Bank is required";
                            }
                            elseif (!$result['id']){
                                return "Bank does not exist";
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }

                    }),
                Field::inst('payment.bank_cheque_number')
                    ->validator(function ($val, $data, $field, $host){
                        $payment_mode = $data['payment']['mode'];
                        if ($payment_mode == '2'){
                            if ($val == ''){
                                return 'Cheque is required';
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('payment.user_id')
                    ->setFormatter(function ($val) {
                        if ($_SESSION['id']){
                            return $_SESSION['id'];
                        }
                        else{
                            return false;
                        }
                    }),
                Field::inst('payment.date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.number'),
                Field::inst('invoice.trade_type'),
                Field::inst('user.first_name')

            )
            ->on('preCreate', function ($editor, $values,$system_object='invoice-payments') {

                ACL::verifyCreate($system_object);

                $receipt = new ReceiptGenerate();

                $payment_invoice = $values['payment']['invoice_id'];

                $query = new MyTransactionQuery();
                $query->query("select trade_type, id,(cost + tax) as cost from invoice where number =?");
                $query->bind = array('s',&$payment_invoice);
                $query->run();
                $type = $query->fetch_assoc();
                $trade_type = $type['trade_type'];
                $iid = $type['id'];
                $cost = $type['cost'];

                $query->query("select outstanding from payment where invoice_id = ? order by date desc");
                $query->bind = array('i',&$iid);
                $query->run();
                $type = $query->fetch_assoc();
                $outstanding = $type['outstanding'];

                $query->commit();

                if (!$outstanding) {
                    $outstanding = $cost - $values['payment']['paid'];
                }
                else {
                    $outstanding = $outstanding - $values['payment']['paid'];
                }
                $number = $receipt->generate_no($trade_type);
                $editor
                    ->field('payment.receipt_number')
                    ->setValue($number);
                $editor
                    ->field('payment.outstanding')
                    ->setValue($outstanding);
            })
            ->on('writeCreate', function ($editor, $id, $values) {
                $invoice_number = $values['payment']['invoice_id'];
                $query = new MyTransactionQuery();
                $query->query("select (cost + tax) as cost, id from invoice where number =?");
                $query->bind = array('s',&$invoice_number);
                $query->run();
                $type = $query->fetch_assoc();
                $cost = $type['cost'];

                if ($values['payment']['paid'] == $cost) {
                    $query->query("UPDATE invoice SET status = 'PAID' WHERE number =?");
                    $query->bind = array('s',&$invoice_number);
                    $query->run();

                    $user_id = $_SESSION['id'];

                    $query->query("select id from invoice where number=?");
                    $query->bind = array('s',&$invoice_number);
                    $query->run();
                    $result = $query->fetch_assoc();
                    $invoice_id = $result['id'];

                    $query->query("insert into invoice_history_log(invoice_id, status, user_id) values (?,'PAID',?)");
                    $query->bind = array('ii', &$invoice_id,&$user_id);
                    $query->run();
                }
                $query->commit();

            })
            ->on('preGet', function ($editor,$id,$system_object='invoice-payments'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='invoice-payments'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='invoice-payments'){
                ACl::verifyDelete($system_object);
            })
            ->leftJoin('invoice', 'invoice.id', '=', 'payment.invoice_id')
            ->leftJoin('user', 'user.id', '=', 'payment.user_id')
            ->where('payment.invoice_id', $activity, '=')
            ->process($_POST)
            ->json();
    }
}

?>
