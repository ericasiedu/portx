<?php

namespace Api;
session_start();

use Lib\ACL,
    Lib\MyQuery,
    Lib\MyTransactionQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;

$system_object='udm-ports';

class Port{
    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'port')
            ->fields(
                Field::inst('code')
                    ->setFormatter(function($val) {
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 10,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) {
                        $val = html_entity_decode($val);
                        $query=new MyQuery();
                        $query->query("select id from port where code  = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if ($run->num_rows()){
                            $id = $run->fetch_num()[0];
                            if($id == $host['id']){
                                return true;
                            }
                            else {
                                return "Port already exists";
                            }
                        }
                        else{
                            $val = htmlspecialchars($val);
                            $query=new MyQuery();
                            $query->query("select id from port where code  = ?");
                            $query->bind = array('s', &$val);
                            $run=$query->run();
                            if ($run->num_rows()){
                                $id = $run->fetch_num()[0];
                                if($id == $host['id']){
                                    return true;
                                }
                                else {
                                    return "Port already exists";
                                }
                            }
                            else{
                                return true;
                            }
                        }
                    }),
                Field::inst('name')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 100,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->setFormatter(function($val) {
                        $val = html_entity_decode($val);
                        return ucwords($val);
                    })
            )
            ->on('preCreate', function ($editor,$values,$system_object='udm-ports'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-ports'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-ports'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-ports'){
                ACl::verifyDelete($system_object);
                $query = new MyTransactionQuery();
                $query->query("select id from container where pod = ? || pol = ?");
                $query->bind=array('ii',&$id, &$id);
                $run = $query->run();
                if($run->num_rows() > 0){
                    return false;
                }

                $query->query("select id from vessel where registry_port_id = ?");
                $query->bind=array('i',&$id);
                $run = $query->run();
                if($run->num_rows() > 0){
                    return false;
                }

                $query->query("select id from voyage where prev_port_id = ? || next_port_id = ?");
                $query->bind=array('ii',&$id, &$id);
                $run = $query->run();
                if($run->num_rows() > 0){
                    return false;
                }
            })
            ->process($_POST)
            ->json();
    }
}
?>
