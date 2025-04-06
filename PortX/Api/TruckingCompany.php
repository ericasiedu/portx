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
$system_object='udm-trucking-companies';

class TruckingCompany {
    function table() {
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'trucking_company')
            ->fields(
                Field::inst('name')
                    ->setFormatter(function($val) {
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 100,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) {
                        $val = html_entity_decode($val);
                        $query=new MyQuery();
                        $query->query("select id from trucking_company where name  = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if ($run->num_rows()){
                            $id = $run->fetch_num()[0];
                            if($id == $host['id']){
                                return true;
                            }
                            else {
                                return "Trucking company already exists";
                            }
                        }
                        else{
                            $val = htmlspecialchars($val);
                            $query=new MyQuery();
                            $query->query("select id from trucking_company where name  = ?");
                            $query->bind = array('s', &$val);
                            $run=$query->run();
                            if ($run->num_rows()){
                                $id = $run->fetch_num()[0];
                                if($id == $host['id']){
                                    return true;
                                }
                                else {
                                    return "Trucking company already exists";
                                }
                            }
                            else {
                                return true;
                            }
                        }
                    }))
            ->on('preCreate', function ($editor, $values, $system_object = 'udm-trucking-companies') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'udm-trucking-companies') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'udm-trucking-companies') {
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'udm-trucking-companies') {
                ACl::verifyDelete($system_object);
                $query = new MyTransactionQuery();
                $query->query("select id from gate_record where trucking_company_id = ?");
                $query->bind=array('i',&$id);
                $run = $query->run();
                if($run->num_rows() > 0){
                    return false;
                }

                $query->query("select id from vehicle where trucking_company_id = ?");
                $query->bind=array('i',&$id);
                $run = $query->run();
                if($run->num_rows() > 0){
                    return false;
                }

                $query->query("select id from vehicle_driver where trucking_company_id = ?");
                $query->bind=array('i',&$id);
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
