<?php

namespace Api;
session_start();


use
    Lib\ACL,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\MyQuery;

class Country{

    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db=new Bootstrap();
        $db=$db->database();
        Editor::inst( $db, 'country' )
            ->fields(
                Field::inst('code')
                    ->setFormatter(function ($val){
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::maxLen(2, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(function ($val, $data, $field, $host){
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from country where code=?");
                        $query->bind = array('s',&$val);
                        $query->run();
                        if ($query->num_rows()){
                            $result = $query->fetch_assoc();
                            if ($result['id'] != $host['id']){
                                return "Code already exist";
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('name')
                    ->setFormatter(function ($val){
                        $val = html_entity_decode($val);
                        return ucwords($val);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen(100, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                ->validator(function ($val, $data, $field, $host){
                    $val = html_entity_decode($val);
                    $query = new MyQuery();
                    $query->query("select id from country where name=?");
                    $query->bind = array('s',&$val);
                    $query->run();
                    if ($query->num_rows()){
                        $result = $query->fetch_assoc();
                        if ($result['id'] != $host['id']){
                            return "Country already exist";
                        }
                        else{
                            return true;
                        }
                    }
                    else{
                        $val = htmlspecialchars($val);
                        $query = new MyQuery();
                        $query->query("select id from country where name=?");
                        $query->bind = array('s',&$val);
                        $query->run();
                        if ($query->num_rows()){
                            $result = $query->fetch_assoc();
                            if ($result['id'] != $host['id']){
                                return "Country already exist";
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                    }
                })
            )
            ->on('preCreate', function ($editor,$values,$system_object='udm-countries'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-countries'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-countries'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-countries'){
                ACl::verifyDelete($system_object);

                $query = new MyQuery();
                $query->query("select id from vessel where country_id=? ");
                $query->bind = array('i',&$id);
                $query->run();
                $result = $query->fetch_assoc();
                if ($result['id']){
                    return false;
                }
            })
            ->process($_POST)
            ->json();
    }
}



?>