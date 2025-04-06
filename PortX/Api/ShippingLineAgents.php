<?php

namespace Api;
session_start();


use Lib\ACL,
    Lib\MyQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;
$system_object='udm-shipping-line-agents';

class ShippingLineAgents{
    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'shipping_line_agent')
            ->fields(
                Field::inst('shipping_line_agent.line_id')
                    ->setFormatter(function ($val) {
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from shipping_line where name = ?");
                        $query->bind =  array('s', &$val);
                        $run = $query->run();
                        if(!$run->num_rows()){
                            $val = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("select id from shipping_line where name = ?");
                            $query->bind =  array('s', &$val);
                            $run = $query->run();
                        }
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select name from shipping_line where id = ?");
                        $query->bind =  array('i', &$val);
                        $run = $query->run();
                        return $run->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A line is required')
                    ))
                    ->validator(function ($val, $data, $field, $host ){
                        $val = html_entity_decode($val);
                        $query=new MyQuery();
                        $query->query("SELECT id FROM shipping_line WHERE name = ? ");
                        $query->bind =  array('s', &$val);
                        $run = $query->run();
                        $result = $run->fetch_assoc()['id'];
                        if ($result == ''){
                            $val = htmlspecialchars($val);
                            $query=new MyQuery();
                            $query->query("SELECT id FROM shipping_line WHERE name = ? ");
                            $query->bind =  array('s', &$val);
                            $run = $query->run();
                            $result = $run->fetch_assoc()['id'];
                            if ($result == ''){
                                return "Shipping line does not exist";
                            }
                            else {
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                    })
                    ->validator(function ( $val, $data, $field, $host ) {
                        $query = new MyQuery();
                        $query->query("select id from shipping_line where name = ?");
                        $query->bind =  array('s', &$val);
                        $run = $query->run();
                        $id = $run->fetch_num()[0] ?? '';
                        if(!$id){
                            $val2 = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("select id from shipping_line where name = ?");
                            $query->bind =  array('s', &$val2);
                            $run = $query->run();
                            $id = $run->fetch_num()[0] ?? '';
                        }
                        $query=new MyQuery();
                        $query->query("select id from shipping_line_agent where line_id  = ?");
                        $query->bind = array('s', &$id);
                        $run=$query->run();
                        if ($run->num_rows()){
                            $id = $run->fetch_num()[0];
                            if($id == $host['id']){
                                return true;
                            }
                            else {
                                return "Shipping line already has an agent";
                            }
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('shipping_line_agent.code')
                    ->setFormatter(function($val) {
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A code is required')
                    ))
                    ->validator(Validate::maxLen( 10,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    )),
                Field::inst('shipping_line_agent.name')
                    ->setFormatter(function ($val){
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A name is required')
                    ))
                    ->validator(Validate::maxLen( 150,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    )),
                Field::inst('shipping_line_agent.date'),
                Field::inst('shipping_line.name')
                    ->setFormatter(function($val) {
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
            )
            ->on('preCreate', function ($editor,$values,$system_object='udm-shipping-line-agents'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-shipping-line-agents'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-shipping-line-agents'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-shipping-line-agents'){
                ACl::verifyDelete($system_object);
            })
            ->leftJoin('shipping_line', 'shipping_line_agent.line_id', '=', 'shipping_line.id')
            ->process($_POST)
            ->json();

    }
    }
?>