<?php

namespace Api;
session_start();

use
    Lib\ACL,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Options,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\MyQuery;

class ReachStack{

    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db = new Bootstrap();
        $db = $db->database();


        Editor::inst($db, 'reach_stacker')
            ->fields(
                Field::inst('equipment_no')
                    ->setFormatter(function($val){
                        return strtoupper($val);
                    })
                    ->getFormatter(function($val){
                        return strtoupper($val);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('type')
            )
            ->on('preCreate', function ($editor,$values,$system_object='udm-reach-stacker'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-reach-stacker'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-reach-stacker'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-reach-stacker'){
                ACl::verifyDelete($system_object);

                $query = new MyQuery();
                $query->query("select id from yard_log where equipment_no=?");
                $query->bind = array('s',&$values['equipment_no']);
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