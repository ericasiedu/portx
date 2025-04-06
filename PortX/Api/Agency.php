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
    Lib\MyQuery,
    Lib\Helper;

class Agency{

    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db=new Bootstrap();
        $db=$db->database();
        Editor::inst( $db, 'agency' )
            ->fields(
                Field::inst('code')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Field is required')
                    ))
                    ->setFormatter(function ($val){
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::maxLen(15, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host){
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from agency where code=?");
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
                            $val = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("select id from agency where code=?");
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
                        }
                    }),
                Field::inst('name')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Field is required')
                    ))
                    ->validator(Validate::maxLen(150, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->setFormatter(function ($val){
                        return ucwords($val);
                    })
                    ->validator(function ($val, $data, $field, $host){
                        $val = html_entity_decode($val);
                        $code = trim($data['code']);
                        $query = new MyQuery();
                        $query->query("select id from agency where name=? and code=?");
                        $query->bind = array('ss',&$val,&$code);
                        $query->run();
                        if ($query->num_rows()){
                            $result = $query->fetch_assoc();
                            if ($result['id'] != $host['id']){
                                return "Agency already exist";
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            $val = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("select id from agency where name=? and code=?");
                            $query->bind = array('ss',&$val,&$code);
                            $query->run();
                            if ($query->num_rows()){
                                $result = $query->fetch_assoc();
                                if ($result['id'] != $host['id']){
                                    return "Agency already exist";
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
                Field::inst('address_line_1')
                    ->validator(Validate::maxLen(250, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host){
                        if ($val !=""){
                            if (preg_match('/^[\h,0-9,-]+$/', $val)){
                                return "Invalid Address";
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('address_line_2')
                    ->validator(Validate::maxLen(150, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host){
                        $address1 = $data['address_line_1'];
                        if ($address1 == '' && $val !=""){
                            return "Address 1 cannot be empty";
                        }
                        if (preg_match('/^[\h,0-9,-]+$/', $val)){
                            return "Invalid Address";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('address_line_3')
                    ->validator(Validate::maxLen(100, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host){
                        $address2 = $data['address_line_1'];
                        if ($address2 == '' && $val != ''){
                            return 'Address 1 cannot be empty';
                        }
                        if (preg_match('/^[\h,0-9,-]+$/', $val)){
                            return "Invalid Address";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('telephone')
                    ->validator(Validate::maxLen(20, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host){
                        if ($val !=''){
                            return Helper::VerifyPhoneNumber($val) ? true : "Invalid Phone Number";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('email')
                    ->validator(Validate::maxLen(100, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                  ->validator(function ($val, $data, $field, $host){
                      if ($val != ''){
                          $agency = trim($val);
                          $query = new MyQuery();
                          $query->query("select id from agency where email=?");
                          $query->bind = array('s',&$agency);
                          $query->run();
                          if (!Helper::VerifyEmail($agency)){
                              return "Please enter a valid Email Address";
                          }
                          if ($query->num_rows()){
                              $result = $query->fetch_assoc();
                              if ($result['id'] != $host['id']){
                                  return "Email already exist";
                              }
                              else{
                                  return true;
                              }
                          }
                          else{
                              return true;
                          }
                      }
                      else{
                          return true;
                      }
                  }),
                Field::inst('fax')
                    ->validator(Validate::maxLen(50, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host){
                        if ($val != ''){
                            return Helper::VerifyPhoneNumber($val) ? true : "Invalid Fax Number";
                        }
                        else{
                            return true;
                        }
                    })
            )
            ->on('preCreate', function ($editor,$values,$system_object='udm-agency'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-agency'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-agency'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-agency'){
                ACl::verifyDelete($system_object);

                $query = new MyQuery();
                $query->query("select id from container where agency_id=?");
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
