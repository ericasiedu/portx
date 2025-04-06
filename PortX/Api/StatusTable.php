<?php

namespace Api;
use Lib\MyQuery;

use
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\MJoin,
    DataTables\Editor\Options,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;

class StatusTable{
    function __construct() {
        $db = new Bootstrap();
        $db = $db->database();

        $status = 0;
        if (isset($_GET['status']))
            $status = $_GET['status'];

        Editor::inst( $db, 'invoice' )
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
                    ->setFormatter(function($val) {
                        return isset($_SESSION['id']) ? $_SESSION['id'] : 1;
                    })
                    ->getFormatter(function ($val) {
                        $qu = new MyQuery();
                        $qu->query("SELECT first_name, last_name FROM user WHERE id = '$val'");
                        $res = $qu->run();
                        $result = $res->fetch_assoc();
                        $first_name = $result['first_name'];
                        $last_name = $result['last_name'];
                        $full_name = "$first_name"." "."$last_name";
                        return $full_name ?? '';
                    }),
                Field::inst('invoice.date')
                    ->getFormatter(Format::dateSqlToFormat('Y-m-d')),
                Field::inst('invoice.status'),
                Field::inst('trade_type.name'),
                Field::inst('tax_type.name'),
                Field::inst('customer.name'),
                Field::inst('customer.id')
                    ->getFormatter(function($val, $row) {
//                var_dump($row[invoice.id]); exit;
                        $qu=new MyQuery();
                        $qu->query("select outstanding from payment where invoice_id = ". $row['invoice.id'] . " order by date desc");
                        $res=$qu->run();
                        $outstanding = $res->fetch_num()[0] ?? '';
                        if (!$outstanding) {
                            $qu=new MyQuery();
                            $qu->query("select cost, tax from invoice where number = '" . $row['invoice.number'] . "'");
                            $res=$qu->run();
                            $result = $res->fetch_assoc();
                            $cost = $result['cost'];
                            $tax = $result['tax'];
                            return round($cost + $tax,2);
                        } else
                            return $outstanding;
                    }),
//        Field::inst('payment.invoice_id'),
                Field::inst('invoice.id')
                    ->getFormatter(function ($val, $row){
//                var_dump($row['invoice.id']);
                        $qu=new MyQuery();
                        $qu->query("SELECT invoice_id, outstanding FROM payment WHERE invoice_id = '$val' ");
                        $res=$qu->run();
                        return $res->fetch_num()[0] ?? '';
                    })
            )
            ->leftJoin('trade_type', 'trade_type.id', '=', 'invoice.trade_type')
            ->leftJoin('tax_type', 'tax_type.id', '=', 'invoice.tax_type')
            ->leftJoin('customer', 'customer.id', '=', 'invoice.customer_id')
            ->where('invoice.status', $status, '=')
            ->process($_POST)
            ->json();


    }
}

?>