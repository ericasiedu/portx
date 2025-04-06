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

$system_object='udm-banks';


class Bank
{
    private $request;

    public function __construct($request)
    {
        
        $this->request = $request;
    }

    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();
        Editor::inst($db, 'bank')
            ->fields(
                Field::inst('name')
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
                        $query->query("select id from bank where name = ?");
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
                    })
                    ->setFormatter(function($val) {
                        return ucwords($val);
                    })
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'udm-banks') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'udm-banks') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'udm-banks') {
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'udm-banks') {
                ACl::verifyDelete($system_object);
                $query = new MyQuery();
                $query->query("select id from payment where bank_name = ?");
                $query->bind=array('i',&$id);
                $query->run();
                if($query->num_rows() > 0){
                    return false;
                }

                $query = new MyQuery();
                $query->query("select id from supplementary_payment where bank_name = ?");
                $query->bind=array('i',&$id);
                $query->run();
                if($query->num_rows() > 0){
                    return false;
                }
            })
            ->process($_POST)
            ->json();
    }

    function getBanks(){
        $query = new MyQuery();
        $query->query('select name from bank limit 10');
        $query->run();
        $result = $query->fetch_assoc();
        echo json_encode($result);
    }
}

?>