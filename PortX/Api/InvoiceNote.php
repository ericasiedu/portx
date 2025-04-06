<?php
namespace Api;

use DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Options,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;
use Lib\MyQuery;

class InvoiceNote{
    private $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    function table(){
        $db=new Bootstrap();
        $db=$db->database();

        $record = $this->request->param('record');

        Editor::inst( $db, 'invoice_note' )
        ->fields(
            Field::inst('invoice_note.note'),
            Field::inst('invoice_note.note_type as ntype'),
            Field::inst('invoice_note.user_id as user')
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
            Field::inst('invoice.number as numb')
        )
        ->leftJoin('invoice', 'invoice.id', '=', 'invoice_note.invoice_id')
        ->where('invoice_note.invoice_id', $record, '=')
        ->process($_POST)
        ->json();
    }
}

?>