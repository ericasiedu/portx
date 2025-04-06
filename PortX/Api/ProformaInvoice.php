<?php
namespace Api;

session_start();
use
    Lib\ACL,
    Lib\MyQuery,
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

class ProformaInvoice{
    private $request;

    public function  __construct($request)
    {
        $this->request = $request;
    }

    function table(){

        $db = new Bootstrap();
        $db = $db->database();
        Editor::inst($db, 'proforma_invoice')
            ->fields(
                Field::inst('proforma_invoice.trade_type')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('proforma_invoice.number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_invoice.bl_number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_invoice.book_number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),    
                Field::inst('proforma_invoice.do_number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_invoice.bill_date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_invoice.due_date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_invoice.cost')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_invoice.tax')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_invoice.tax_type')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_invoice.id as note')
                ->getFormatter(function($val){
                    $query = new MyQuery();
                    $query->query("SELECT note FROM proforma_invoice_note WHERE invoice_id=?");
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
                Field::inst('proforma_invoice.customer_id')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_invoice.user_id')
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
                Field::inst('proforma_invoice.id as invn')
                    ->getFormatter(function($val){
                        $query = new MyQuery();
                        $query->query("select id from proforma_invoice_note where invoice_id=?");
                        $query->bind = array('i',&$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        return $result['id'];
                    }),
                Field::inst('proforma_invoice.id as invs')
                    ->getFormatter(function($val){
                        return $this->check_invoice_status($val) ? 'recallable' : 'not_recallable';
                    }),
                Field::inst('proforma_invoice.date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_invoice.status')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('proforma_invoice.id as invi'),
                Field::inst('trade_type.name'),
                Field::inst('tax_type.name'),
                Field::inst('customer.name'),
                Field::inst('currency.code'),
                Field::inst('proforma_invoice.id')
            )
            ->leftJoin('trade_type', 'trade_type.id', '=', 'proforma_invoice.trade_type')
            ->leftJoin('tax_type', 'tax_type.id', '=', 'proforma_invoice.tax_type')
            ->leftJoin('currency', 'currency.id', '=', 'proforma_invoice.currency')
            ->leftJoin('customer', 'customer.id', '=', 'proforma_invoice.customer_id')
            ->process($_POST)
            ->json();
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
        $query->query("select id from proforma_invoice where (status = 'UNPAID' or status = 'DEFERRED' or status='CANCELLED' or status='RECALLED') and number=?");
        $query->bind = array('s',&$invoice_number);
        $query->run();
        if ($query->num_rows() == 0){
            new Respond(123);
        }
        $result = $query->fetch_assoc();
        $invoice_id = $result['id'];
        $query->query("select id from proforma_invoice_note where invoice_id=? and note_type=?");
        $query->bind = array('is',&$invoice_id,&$note_type);
        $query->run();
        $result1 = $query->fetch_assoc();
    
        if(!$result1['id']){
            $query->query("insert into proforma_invoice_note(invoice_id,note,note_type,user_id)values(?,?,?,?)");
            $query->bind = array('issi',&$invoice_id,&$note,&$note_type,&$user_id);
            $query->run();
            $query->commit();
            new Respond(260);
        }
        else{
            $query->query("update proforma_invoice_note set note =?,user_id=? where id=?");
            $query->bind = array('sii',&$note,&$user_id,&$result1['id']);
            $query->run();
            $query->commit();
            new Respond(260);
        }
       
    }
    
    private function check_invoice_status($invoice_id){
        $query = new MyQuery();
        $query->query("select cancelled from proforma_invoice_status where invoice_id=?");
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

}

?>