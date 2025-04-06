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

class Tax{

    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db = new Bootstrap();
        $db = $db->database();


        Editor::inst($db, 'tax')
            ->fields(
                Field::inst('tax.type')
                    ->options(Options::inst()
                        ->table('tax_type')
                        ->value('id')
                        ->label('name')
                        ->where(function ($q){
                            $q->where('id', '(SELECT id FROM tax_type where id != 3)', 'IN', false);
                        })
                    ),
                Field::inst('tax.label')
                    ->setFormatter(function ($val){
                        return ucwords($val);
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen(20, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host){

                           $tax_type = $data['tax']['type'];
                           $val = html_entity_decode($val);
                           $query = new MyQuery();
                           $query->query("select id from tax where (label=? and type=?)");
                           $query->bind = array('si',&$val,&$tax_type);
                           $query->run();
                           if ($query->num_rows()){
                               $result = $query->fetch_assoc();
                               if ($result['id'] != $host['id']){
                                   return 'Tax already exist';
                               }
                               else{
                                   return true;
                               }
                           }
                           else{
                               $val = htmlspecialchars($val);
                               $query = new MyQuery();
                               $query->query("select id from tax where (label=? and type=?)");
                               $query->bind = array('si',&$val,&$tax_type);
                               $query->run();
                               if ($query->num_rows()){
                                   $result = $query->fetch_assoc();
                                   if ($result['id'] != $host['id']){
                                       return 'Tax already exist';
                                   }
                                   else{
                                       return true;
                                   }
                               }
                               else{
                                   return true;
                               }
                           }
                    }),
                Field::inst('tax.rate')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Field is required')
                    ))
                    ->validator(function ($val, $data, $field, $host){
                        if (!is_numeric($val)){
                            return "Input must be numbers";
                        }
                        elseif ($val > 100){
                            return "Input value cannot be more than 100";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('tax_type.name')
            )
            ->on('preCreate', function ($editor,$values,$system_object='udm-taxes'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-taxes'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-taxes'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-taxes'){
                ACl::verifyDelete($system_object);

                $query = new MyQuery();
                $query->query("select tax.id from invoice inner join tax_type on tax_type.id = invoice.tax_type 
                        inner join tax on tax.type = tax_type.id where tax.type =?");
                $query->bind = array('i',&$id);
                $query->run();
                $result = $query->fetch_assoc();
                if ($result['id']){
                    return false;
                }

            })
            ->leftJoin('tax_type', 'tax_type.id', '=', 'tax.type')
            ->process($_POST)
            ->json();

    }
    }
?>