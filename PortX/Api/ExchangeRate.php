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

class ExchangeRate{

    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db = new Bootstrap();
        $db = $db->database();


        Editor::inst($db, 'exchange_rate')
            ->fields(
                Field::inst('base')
                    ->setFormatter(function ($val){
                        $query=new MyQuery();
                        $query->query("select id from currency where code = ?");
                        $query->bind = array('s',&$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val){
                        $query=new MyQuery();
                        $query->query("select code from currency where id = ?");
                        $query->bind = array('i',&$val);
                        $query->run();
                        return $query->fetch_all()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Base field is required')
                    ))
                    ->validator(Validate::maxLen(10, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host ){
                        $query = new MyQuery();
                        $query->query("select id from currency where code = ?");
                        $query->bind = array('s',&$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $currency_id = $result['id'];

                        if ($host['action'] == 'create'){
                            if (!$currency_id){
                                return "Currency does not exist";
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('quote')
                    ->setFormatter(function ($val){
                        $query=new MyQuery();
                        $query->query("select id from currency where code = ?");
                        $query->bind = array('s',&$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val){
                        $query=new MyQuery();
                        $query->query("select code from currency where id = ?");
                        $query->bind = array('i',&$val);
                        $query->run();
                        return $query->fetch_all()[0] ?? '';
                    })
                    ->validator(Validate::maxLen(10, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Quote field is required')
                    ))
                    ->validator(function ($val, $data, $field, $host ){

                        $base = $data['base'];

                        $query = new MyQuery();
                        $query->query("select id from currency where code = ?");
                        $query->bind = array('s',&$val);
                        $query->run();
                        $result = $query->fetch_assoc();
                        $currency_id = $result['id'];

                        if ($host['action'] == 'create'){
                            if (!$currency_id){
                                return "Currency does not exist";
                            }
                            elseif ($val == $base){
                                return "These pair is not allowed";
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('buying')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Buying field is required')
                    ))
                    ->validator(function ($val, $data, $field, $host ){

                        if ($val <= 0){
                            return 'Cannot enter values equal to or less than zero';
                        }
                        else{
                            return true;
                        }

                    }),
                Field::inst('selling')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Selling field is required')
                    ))
                    ->validator(function ($val, $data, $field, $host ){

                        if ($val <= 0){
                            return 'Cannot enter values equal to or less than zero';
                        }
                        else{
                            return true;
                        }

                    }),
                Field::inst('user_id')
                    ->setFormatter(function ($val){
                        return isset($_SESSION['id']) ? $_SESSION['id'] : 1;
                    })
                    ->getFormatter(function ($val){
                        $query = new MyQuery();
                        $query->query("SELECT first_name, last_name FROM user WHERE id = '$val'");
                        $query->run();
                        $result = $query->fetch_assoc();
                        $first_name = $result['first_name'];
                        $last_name = $result['last_name'];
                        $full_name = "$first_name"." "."$last_name";
                        return $full_name ?? '';
                    }),
                Field::inst('date')
            )
            ->on('preCreate', function ($editor,$values,$system_object='udm-exchange_rate'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-exchange_rate'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-exchange_rate'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-exchange_rate'){
                ACl::verifyDelete($system_object);

                $query = new MyQuery();
                $query->query("select id from invoice_details where exchange_rate = ?
                        UNION
                        select id from supplementary_invoice_details where exchange_rate =?");
                $query->bind = array('ii',&$id,&$id);
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
