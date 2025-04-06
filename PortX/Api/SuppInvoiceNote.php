<?php
namespace Api;

use DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Options,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;
use Lib\MyQuery;

class SuppInvoiceNote{
    private $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    function table(){
        $db=new Bootstrap();
        $db=$db->database();

        $record = $this->request->param('record');

        Editor::inst( $db, 'supplementary_note' )
        ->fields(
            Field::inst('supplementary_note.note'),
            Field::inst('supplementary_note.note_type as ntype'),
            Field::inst('supplementary_note.user_id as user')
                ->validator(Validate::notEmpty(ValidateOptions::inst()
                    ->message('Empty Field')
                ))
                ->getFormatter(function($val){
                    $query = new MyQuery();
                    $query->query("select concat(first_name,' ',last_name) as full_name from user where id=?");
                    $query->bind = array('i',&$val);
                    $query->run();
                    $result = $query->fetch_assoc();
                    return $result['full_name'];
                }),
            Field::inst('supplementary_invoice.number as numb')
        )
        ->leftJoin('supplementary_invoice', 'supplementary_invoice.id', '=', 'supplementary_note.invoice_id')
        ->where('supplementary_note.invoice_id', $record, '=')
        ->process($_POST)
        ->json();
    }
}

?>