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
    Lib\MyTransactionQuery,
    Lib\StorageCharges,
    Lib\ACL;

class ProformaSuppInvoice{

    private $request;
    public $invoice_id;
    public $customer_id;
    public $storage_date;

    function __construct($request){
        $this->request = $request;
    }

    function table() {
        $db = new Bootstrap();
        $db = $db->database();
        Editor::inst( $db, 'proforma_supplementary_invoice' )
            ->fields(
                Field::inst('invoice.trade_type as type')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('proforma_supplementary_invoice.number as spnum')
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
                Field::inst('proforma_supplementary_invoice.bill_date as bdate')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_supplementary_invoice.due_date as ddate')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_supplementary_invoice.cost as cost')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_supplementary_invoice.tax as tax')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('invoice.tax_type as txtyp')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_supplementary_invoice.note as note'),
                Field::inst('proforma_supplementary_invoice.user_id as uid')
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
                Field::inst('proforma_supplementary_invoice.date as date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_supplementary_invoice.status as stat'),
                Field::inst('trade_type.name as ttyp'),
                Field::inst('tax_type.name as txnam'),
                Field::inst('customer.name as name'),
                Field::inst('currency.code as code'),
                Field::inst('proforma_supplementary_invoice.id as supid')
                    ->getFormatter(function ($val, $row){
                        $qu=new MyQuery();
                        $qu->query("SELECT invoice_id, outstanding FROM supplementary_payment WHERE invoice_id = '$val' ");
                        $res=$qu->run();
                        return $res->fetch_num()[0] ?? '';
                    })
            )
            ->on('preCreate', function ($editor,$values,$system_object='proforma-supplementary-invoice-records'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='proforma-supplementary-invoice-records'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='proforma-supplementary-invoice-records'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='proforma-supplementary-invoice-records'){
                ACl::verifyDelete($system_object);
            })
            ->leftJoin('invoice','invoice.id','=','proforma_supplementary_invoice.invoice_id')
            ->leftJoin('trade_type', 'trade_type.id', '=', 'invoice.trade_type')
            ->leftJoin('tax_type', 'tax_type.id', '=', 'invoice.tax_type')
            ->leftJoin('customer', 'customer.id', '=', 'invoice.customer_id')
            ->leftJoin('currency', 'currency.id', '=', 'invoice.currency')
            ->process($_POST)
            ->json();

    }
}

?>