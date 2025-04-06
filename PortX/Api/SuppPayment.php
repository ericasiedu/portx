<?php

namespace Api;


use
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\SuppRecieptGenerate,
    Lib\MyQuery,
    Lib\MyTransactionQuery;
use Lib\ACL;


class SuppPayment{

    public $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){

        $activity = $this->request->param('activity');

        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'supplementary_payment')
            ->fields(
                Field::inst('supplementary_payment.receipt_number')
                    ->set(Field::SET_CREATE),
                Field::inst('supplementary_payment.invoice_id')
                    ->setFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select id from supplementary_invoice where number =?");
                        $query->bind = array('s',&$val);
                        $result = $query->run();
                        return $result->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(function ($val, $field, $data, $host){
                        $query = new MyQuery();
                        $query->query("select id, approved from supplementary_invoice where number =?");
                        $query->bind = array('s',&$val);
                        $query_result = $query->run();
                        $result = $query_result->fetch_assoc();
                        $invoice_id = $result['id'];
                        $approved = $result['approved'];

                        if(!$approved) {
                            return "Supplementary Invoice has not been approved";
                        }

                        $supp_query=new MyQuery();
                        $supp_query->query("SELECT container.status FROM container INNER JOIN supplementary_invoice_container 
                              ON supplementary_invoice_container.container_id = container.id INNER JOIN supplementary_invoice 
                              ON supplementary_invoice.id = supplementary_invoice_container.invoice_id 
                              WHERE supplementary_invoice.id =?");
                        $supp_query->bind = array('i',&$invoice_id);
                        $supp_result = $supp_query->run();
                        $result = $supp_result->fetch_assoc();
                        $status = $result['status'];
                        if ($status == 1){
                            return "Container is flagged";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('supplementary_payment.paid')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(function ($val, $data, $field, $host) {
                        $supp_invoice_number = $data['supplementary_payment']['invoice_id'];
                        $query = new MyQuery();
                        $query->query("select (cost + tax) as cost from supplementary_invoice where number =?");
                        $query->bind = array('s',&$supp_invoice_number);
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
                Field::inst('supplementary_payment.outstanding'),
                Field::inst('supplementary_payment.user_id')
                    ->setFormatter(function ($val) {
                        if ($_SESSION['id']){
                            return $_SESSION['id'];
                        }
                        else{
                            return false;
                        }
                    }),
                Field::inst('supplementary_payment.date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('supplementary_invoice.number'),
                Field::inst('supplementary_payment.mode'),
                Field::inst('supplementary_payment.bank_name')
                    ->setFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select id from bank where name =?");
                        $query->bind = array('s',&$val);
                        $result = $query->run();
                        return $result->fetch_num()[0] ?? '';
                    })
                    ->validator(function ($val, $data, $field, $host){

                        $payment_mode = $data['supplementary_payment']['mode'];

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
                Field::inst('supplementary_payment.bank_cheque_number')
                    ->validator(function ($val, $data, $field, $host){

                        $payment_mode = $data['supplementary_payment']['mode'];
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
                Field::inst('invoice.trade_type'),
                Field::inst('user.first_name')

            )
            ->on('preGet', function ($editor, $id, $system_object = 'supplementary-invoice-payments') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'supplementary-invoice-payments') {
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'supplementary-invoice-payments') {
                ACl::verifyCreate($system_object);
            })
            ->on('preCreate', function ($editor, $values,$system_object='supplementary-invoice-payments') {
                ACl::verifyCreate($system_object);
                $receipt = new SuppRecieptGenerate();

                $supp_invoice_number = $values['supplementary_payment']['invoice_id'];

                $query = new MyTransactionQuery();
                $query->query("select (supplementary_invoice.cost + supplementary_invoice.tax) as cost, invoice.trade_type, supplementary_invoice.id from supplementary_invoice 
                inner join invoice on invoice.id = supplementary_invoice.invoice_id where supplementary_invoice.number =?");
                $query->bind = array('s', &$supp_invoice_number);
                $res = $query->run();
                $result = $res->fetch_assoc();
                $trade_type = $result['trade_type'];
                $iid = $result['id'];
                $cost = $result['cost'];


                $query->query("select outstanding from supplementary_payment where invoice_id =? order by date desc");
                $query->bind = array('i', &$iid);
                $query->run();
                $result = $res->fetch_assoc();
                $outstanding = $result['outstanding'];

                $query->commit();

                if (!$outstanding) {
                    $outstanding = $cost - $values['supplementary_payment']['paid'];
                } else {
                    $outstanding = $outstanding - $values['supplementary_payment']['paid'];
                }
                $number = $receipt->generate_supp_re_no($trade_type);
                $editor
                    ->field('supplementary_payment.receipt_number')
                    ->setValue($number);
                $editor
                    ->field('supplementary_payment.outstanding')
                    ->setValue($outstanding);
            })
            ->on('writeCreate', function ($editor, $id, $values) {

                $invoice_number = $values['supplementary_payment']['invoice_id'];

                $query = new MyTransactionQuery();
                $query->query("select (cost + tax) as cost, id from supplementary_invoice where number =?");
                $query->bind = array('s',&$invoice_number);
                $query->run();
                $result = $query->fetch_assoc();
                $cost = $result['cost'];

                $user_id = $_SESSION['id'];
               
                if ($values['supplementary_payment']['paid'] == $cost) {
                    $query->query("UPDATE supplementary_invoice SET status = 'PAID' WHERE number =?");
                    $query->bind = array('s',&$invoice_number);
                    $query->run();

                    $query->query("select id from supplementary_invoice where number=?");
                    $query->bind = array('s',&$invoice_number);
                    $query->run();
                    $result = $query->fetch_assoc();
                    $invoice_id = $result['id'];

                    $query->query("insert into supplementary_invoice_history_log(invoice_id, status, user_id) values (?,'PAID',?)");
                    $query->bind = array('ii', &$invoice_id,&$user_id);
                    $query->run();
                }
                $query->commit();

            })
            ->leftJoin('supplementary_invoice', 'supplementary_invoice.id', '=', 'supplementary_payment.invoice_id')
            ->leftJoin('invoice','invoice.id','=','supplementary_invoice.invoice_id')
            ->leftJoin('user', 'user.id', '=', 'supplementary_payment.user_id')
            ->leftJoin('supplementary_invoice_container','supplementary_invoice_container.invoice_id','=','supplementary_invoice.id')
            ->where('supplementary_payment.invoice_id', $activity, '=')
            ->process($_POST)
            ->json();
    }
}

?>