<?php
namespace Api;
session_start();
use Lib\ACL,
    Lib\MyQuery,
    Lib\Respond,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;

    $system_object='udm-stack';

class Stack{
    private $request;

    public function __construct($request)
    {
        
        $this->request = $request;
    }

    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();
        Editor::inst($db, 'stack')
            ->fields(
                Field::inst('name')
                    ->setFormatter(function($val){
                        return ucwords($val);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 100,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host) {
                        $value = trim($val);
                        $value = htmlspecialchars($value);
                        $query = new MyQuery();
                        $query->query("select id from stack where name = ?");
                        $query->bind = array('s', &$value);
                        $query->run();
                        if($query->num_rows()){
                            $result = $query->fetch_assoc();
                            if ($result['id'] != $host['id']){
                                return 'Name already exist';
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                    }),
                    Field::inst('date'),
                    Field::inst('stack_type')
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'udm-stack') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'udm-stack') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'udm-stack') {
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'udm-stack') {
                ACl::verifyDelete($system_object);
                $query = new MyQuery();
                $stack = $values['name'];
                $query->query("select id from yard_log where stack = ?");
                $query->bind=array('s',&$stack);
                $query->run();
                if($query->num_rows() > 0){
                    return false;
                }
            })
            ->process($_POST)
            ->json();
    }

}


?>