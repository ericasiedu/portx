<?php

namespace Api;
session_start();

use
    Lib\ACL,
    Lib\MyQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;

class DepotActivity{

    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db=new Bootstrap();
        $db=$db->database();
        Editor::inst( $db, 'depot_activity' )
            ->fields(
                Field::inst('id'),
                Field::inst('name')
                    ->setFormatter(function ($val){
                        $val = html_entity_decode($val);
                        return ucwords($val);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen(100, ValidateOptions::inst()
                        ->message('Max length of field exceeded')))
                    ->validator(function ( $val, $data, $field, $host ) {
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from depot_activity where name = ?");
                        $query->bind = array('s', &$val);
                        $query->run();
                        if ($query->num_rows() > 0) {
                            $id = $query->fetch_num()[0];
                            if($id == $host['id'])
                            {
                                return true;
                            }
                            else{
                                return 'Activity Is Already Added';
                            }
                        }
                        else{
                            $val = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("select id from depot_activity where name = ?");
                            $query->bind = array('s', &$val);
                            $query->run();
                            if ($query->num_rows() > 0) {
                                $id = $query->fetch_num()[0];
                                if($id == $host['id'])
                                {
                                    return true;
                                }
                                else{
                                    return 'Activity Is Already Added';
                                }
                            }
                            else{
                                return true;
                            }
                        }
                    }),
                Field::inst('billable'),
                Field::inst('is_default'),
                Field::inst('is_default as deft')
                    ->getFormatter(function($val) {
                        return $val? "YES" :  "NO";
                    })
            )
            ->on('preCreate', function ($editor,$values,$system_object='udm-depot-activity'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-depot-activity'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-depot-activity'){
                ACl::verifyUpdate($system_object);
                $query = new MyQuery();
                $query->query("select name  from depot_activity where id = ?");
                $query->bind=array('i',&$id);
                $query->run();
                $name = $query->fetch_assoc()["name"];
                if($id<8 && $name != $values['name']){
                    return false;
                }
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-depot-activity'){
                ACl::verifyDelete($system_object);
                if($id<8){
                    return false;
                }
                $query = new MyQuery();
                $query->query("select id  from container_log where activity_id = ?");
                $query->bind=array('i',&$id);
                $query->run();
                if($query->num_rows() > 0){
                    return false;
                }

            })
            ->where('depot_activity.billable','1')
            ->process($_POST)
            ->json();
    }
}


?>
