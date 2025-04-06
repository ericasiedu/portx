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
$system_object='udm-vehicle';

class Vehicle{
    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'vehicle')
            ->fields(
                Field::inst('vehicle.number')
                    ->setFormatter(function($val) {
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A vehicle number is required')
                    ))
                    ->validator(Validate::maxLen( 20,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) {
                        if(preg_match('/^[\h,A-Z,a-z,0-9,-]+$/', $val, $output_array)){
                            return true;
                        }
                        else {
                            return "Invalid vehicle number.";
                        }
                    })
                    ->validator(function ( $val, $data, $field, $host ) {
                        $query=new MyQuery();
                        $query->query("select id from vehicle where number  = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if ($run->num_rows()){
                            $id = $run->fetch_num()[0];
                            if($id == $host['id']){
                                return true;
                            }
                            else {
                                return "Vehicle already exists";
                            }
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('vehicle.description')
                    ->setFormatter(function($val) {
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 100,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    )),
                Field::inst('vehicle.type_id')
                    ->setFormatter(function ($val) {
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from vehicle_type where name = ?");
                        $query->bind = array('s', &$val);
                        $run = $query->run();
                        if(!$run->num_rows()){
                            $val = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("select id from vehicle_type where name = ?");
                            $query->bind = array('s', &$val);
                            $run = $query->run();
                        }
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select name from vehicle_type where id = ?");
                        $query->bind = array('i', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->validator(function ( $val, $data, $field, $host ) {
                        $val = html_entity_decode($val);
                        $query=new MyQuery();
                        $query->query("select id from vehicle_type where name = ?");
                        $query->bind = array('s', &$val);
                        $run = $query->run();
                        if ($val == '' || $run->fetch_num()[0]){
                            return true;
                        }
                        else{
                            $val = htmlspecialchars($val);
                            $query=new MyQuery();
                            $query->query("select id from vehicle_type where name = ?");
                            $query->bind = array('s', &$val);
                            $run = $query->run();
                            if ($val == '' || $run->fetch_num()[0]){
                                return true;
                            }
                            else{
                                return 'Type Does Not Exist';
                            }
                        }
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('vehicle.trucking_company_id')
                    ->setFormatter(function ($val) {
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from trucking_company where name = ?");
                        $query->bind = array('s', &$val);
                        $run = $query->run();
                        if(!$run->num_rows()){
                            $val = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("select id from trucking_company where name = ?");
                            $query->bind = array('s', &$val);
                            $run = $query->run();
                        }
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select name from trucking_company where id = ?");
                        $query->bind = array('i', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->validator(function ( $val, $data, $field, $host ) {
                        $val = html_entity_decode($val);
                        $query=new MyQuery();
                        $query->query("select id from trucking_company where name = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if ($val == '' || $run->fetch_num()[0]){
                            return true;
                        }
                        else{
                            $val = htmlspecialchars($val);
                            $query=new MyQuery();
                            $query->query("select id from trucking_company where name = ?");
                            $query->bind = array('s', &$val);
                            $run=$query->run();
                            if ($val == '' || $run->fetch_num()[0]){
                                return true;
                            }
                            else{
                                return 'Trucking Company Does Not Exist';
                            }
                        }
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
            )
            ->on('preCreate', function ($editor,$values,$system_object='udm-vehicle'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-vehicle'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-vehicle'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-vehicle'){
                ACl::verifyDelete($system_object);
                $query = new MyTransactionQuery();
                $query->query("select id from gate_record where vehicle_id = ? and type =  'GATE IN'");
                $query->bind=array('i',&$id);
                $run = $query->run();
                if($run->num_rows() > 0){
                    return false;
                }
            })
            ->leftJoin('vehicle_type', 'vehicle.type_id', '=', 'vehicle_type.id')
            ->leftJoin('trucking_company', 'vehicle.trucking_company_id', '=', 'trucking_company.id')
            ->process($_POST)
            ->json();
    }
}
?>
