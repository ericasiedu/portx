<?php

namespace Api;
session_start();


use Lib\ACL,
    Lib\MyTransactionQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\MyQuery;

$system_object='udm-shipping-line';

class ShippingLine{
    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();


        Editor::inst($db, 'shipping_line')
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
                        $val = htmlspecialchars($val);
                        $query=new MyQuery();
                        $query->query("select id from shipping_line where code  = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if ($run->num_rows()){
                            $id = $run->fetch_num()[0];
                            if($id == $host['id']){
                                return true;
                            }
                            else {
                                return "Shipping line already exists";
                            }
                        }
                        else{
                            return true;
                        }
                    }),
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
            )
            ->on('preCreate', function ($editor,$values,$system_object='udm-shipping-line'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-shipping-line'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-shipping-line'){
                if($id == 1)
                    return false;
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-shipping-line'){
                if($id == 1)
                    return false;
                ACl::verifyDelete($system_object);
                $query = new MyTransactionQuery();
                $query->query("select id from container where shipping_line_id = ?");
                $query->bind=array('i',&$id);
                $run = $query->run();
                if($run->num_rows() > 0){
                    return false;
                }

                $query->query("select id from shipping_line_agent where line_id = ?");
                $query->bind=array('i',&$id);
                $run = $query->run();
                if($run->num_rows() > 0){
                    return false;
                }

                $query->query("select id from voyage where shipping_line_id = ?");
                $query->bind=array('i',&$id);
                $run = $query->run();
                if($run->num_rows() > 0){
                    return false;
                }
            })
            ->where("shipping_line.id", 1, "!=")
            ->process($_POST)
            ->json();


    }
    }
?>