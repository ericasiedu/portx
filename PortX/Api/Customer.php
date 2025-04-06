<?php

namespace Api;
session_start();

use
    Lib\ACL,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\Options,
    DataTables\Editor\ValidateOptions,
    Lib\MyQuery,
    Lib\Helper,
    Lib\MyTransactionQuery;

class Customer{

    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db=new Bootstrap();
        $db=$db->database();
        Editor::inst( $db, 'customer' )
            ->fields(
                Field::inst('code')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty field')
                    ))
                    ->validator(Validate::maxLen(15, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->setFormatter(function ($val){
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(function ($val, $data, $field, $host){
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from customer where code=?");
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
                            $query->query("select id from customer where code=?");
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
                        ->message('Empty field')
                    ))
                    ->validator(Validate::maxLen(50, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->setFormatter(function ($val){
                        $val = html_entity_decode($val);
                        return ucwords($val);
                    })
                    ->validator(function ($val, $data, $field, $host){
                        $code = $data['code'];
                        $val = html_entity_decode($val);
                        $query = new MyQuery();
                        $query->query("select id from customer where (name=? and code=?)");
                        $query->bind = array('ss',&$val,&$code);
                        $query->run();
                        if ($query->num_rows()){
                            $result = $query->fetch_assoc();
                            if ($result['id'] != $host['id']){
                                return "Customer already exist";
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            $val = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("select id from customer where (name=? and code=?)");
                            $query->bind = array('ss',&$val,&$code);
                            $query->run();
                            if ($query->num_rows()){
                                $result = $query->fetch_assoc();
                                if ($result['id'] != $host['id']){
                                    return "Customer already exist";
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
                    ->validator(Validate::maxLen(50, ValidateOptions::inst()
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
                    ->validator(Validate::maxLen(50, ValidateOptions::inst()
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
                    ->validator(Validate::maxLen(50, ValidateOptions::inst()
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
                        if ($val !=""){
                            $phone_number = trim($val);
                            return Helper::VerifyPhoneNumber($phone_number) ? true : "Invalid Phone Number";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('email')
                    ->validator(Validate::maxLen(50, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host){
                        if ($val !=""){
                            $email = trim($val);
                            if (!Helper::VerifyEmail($email)){
                                return "Please enter a valid Email Address";
                            }
                            $query = new MyQuery();
                            $query->query("select id from customer where email=?");
                            $query->bind = array('s',&$email);
                            $query->run();
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
                        if ($val !=""){
                            $fax_number = trim($val);
                            return Helper::VerifyPhoneNumber($fax_number) ? true : "Invalid Fax Number";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('credit_limit'),
                Field::inst('id')
            )
            ->on('preCreate', function ($editor,$values,$system_object='udm-customers'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-customers'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-customers'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-customers'){
                ACl::verifyDelete($system_object);

                $query = new MyTransactionQuery();
                $query->query("delete from customer_billing where customer_id=?");
                $query->bind = array('i',&$id);
                $query->run();
                $query->commit();
            })
            ->process($_POST)
            ->json();
    }

    function customer_billing_table(){
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'customer_billing_group')
            ->fields(
                Field::inst('customer_billing_group.name')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty field')
                    ))
                    ->validator(Validate::maxLen( 50,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host){
                        $value = trim($val);

                        $query = new MyQuery();
                        $query->query("select id from customer_billing_group where name=?");
                        $query->bind = array('s',&$value);
                        $query->run();

                        if (!preg_match('/^[\h,A-Z,a-z,0-9,-]+$/', $value)){
                            return "Name cannot contain symbols";
                        }
                        elseif ($query->num_rows()){
                            $result = $query->fetch_assoc();
                            if ($result['id'] != $host['id']){
                                return 'Name already exist';
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('customer_billing_group.extra_free_rent_days')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty field')
                    ))
                    ->validator(Validate::maxLen( 3,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(function ($val, $data, $field, $host){
                        $value = trim($val);
                        if (!is_numeric($value)){
                            return "Input must be number";
                        }
                        elseif ($value > 1000){
                            return "Cannot enter more than 1000 days";
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('customer_billing_group.tax_type')
                    ->options(Options::inst()
                        ->table('tax_type')
                        ->value('id')
                        ->label('name')
                    ),
                Field::inst('customer_billing_group.trade_type')
                    ->options(Options::inst()
                        ->table('trade_type')
                        ->value('id')
                        ->label('name')
                    ),
                Field::inst('tax_type.name as tax'),
                Field::inst('trade_type.name as trade_type'),
                Field::inst('customer_billing_group.waiver_pct')
                    ->validator(function ($val, $data, $field, $host){
                        $value = trim($val);

                        if ($data['waiver_type'] == "1"){
                            if ($value == ""){
                                return "Empty field";
                            }
                            elseif (!is_numeric($value)){
                                return "Input must be numberic";
                            }
                            elseif ($value > 100){
                                return "Percentage value cannot be more than 100";
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                    }),
                Field::inst('customer_billing_group.waiver_amount')
                    ->validator(function ($val, $data, $field, $host){
                        $value = trim($val);
                        if ($data['waiver_type'] == "2"){
                            if ($value == ""){
                                return "Empty field";
                            }
                            elseif (!is_numeric($value)){
                                return "Input must be numberic";
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                    })
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'udm-customer_billing_groups') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'udm-customer_billing_groups') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'udm-customer_billing_groups') {
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'udm-customer_billing_groups') {
                ACl::verifyDelete($system_object);
                $query = new MyQuery();
                $query->query("select id from invoice where billing_group = ?");
                $query->bind=array('i',&$id);
                $query->run();
                if($query->num_rows() > 0){
                    return false;
                }

            })
            ->leftJoin('tax_type','tax_type.id','=','customer_billing_group.tax_type')
            ->leftJoin('trade_type','trade_type.id','=','customer_billing_group.trade_type')
            ->process($_POST)
            ->json();
    }

    function billing_group_table(){

        $db = new Bootstrap();
        $db = $db->database();

        $customer = $this->request->param('bgid') ?? 0;

        Editor::inst($db, 'customer_billing')
            ->fields(
                Field::inst('customer_billing.customer_id'),
                Field::inst('customer_billing.billing_group')
                    ->getFormatter(function ($val){
                        $query = new MyQuery();
                        $query->query("select name from customer_billing_group where id=?");
                        $query->bind = array('i',&$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->validator(function ($val, $field, $data, $host){
                        $customer = $field['customer_billing']['customer_id'];
                        $billing_group = $field['customer_billing']['billing_group'];
                        $query = new MyQuery();
                        $query->query("select id from customer_billing where customer_id = ? and billing_group = ?");
                        $query->bind = array("ii", &$customer, &$billing_group);
                        $query->run();
                        if($query->num_rows()){
                            return "Billing group already exist for this customer";
                        }
                        $query = new MyQuery();
                        $query->query("select trade_type.id from customer_billing left JOIN customer_billing_group on customer_billing.billing_group = customer_billing_group.id left join trade_type on trade_type.id = customer_billing_group.trade_type where customer_billing.id = ?");
                        $id =  $host['id'];
                        $query->bind = array("i", &$id);
                        $query->run();
                        $trade_type_id = $query->fetch_assoc()['id'];
                        $query = new MyQuery();
                        $query->query("select trade_type from customer_billing_group where id=?");
                        $query->bind = array('i', &$val);
                        $query->run();
                        if($query->num_rows()) {
                            $trade_type = $query->fetch_assoc()['trade_type'];
                            $query = new MyQuery();
                            $query->query("select count(*) as count, customer_billing_group.name, trade_type.id as trade from customer left join customer_billing on customer_billing.customer_id = customer.id 
                                                  left join customer_billing_group on customer_billing_group.id = customer_billing.billing_group
                                                    left JOIN trade_type on trade_type.id = customer_billing_group.trade_type where trade_type.id = ? and customer.id = ?");
                            $query->bind = array('ii', &$trade_type, &$customer);
                            $query->run();
                            $result = $query->fetch_assoc();
                            $count = $result['count'];
                            $new_trade_type_id = $result['trade'];
                            if ($count > 0) {
                                if($trade_type_id != $new_trade_type_id) {
                                    $name = $result['name'];
                                    return "A billing group of the same trade type exists for this customer. Billing Group: $name";
                                }
                            }
                            return true;
                        }
                        return "Customer billing group does not exist";
                    })
                    ->options(Options::inst()
                        ->table('customer_billing_group')
                        ->value('id')
                        ->label('name')
                    )
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'udm-customer_billing_groups') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'udm-customer_billing_groups') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'udm-customer_billing_groups') {
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'udm-customer_billing_groups') {
                ACl::verifyDelete($system_object);

            })
            ->leftJoin('customer_billing_group','customer_billing_group.id','=','customer_billing.billing_group')
            ->where('customer_billing.customer_id', $customer, '=')
            ->process($_POST)
            ->json();
    }
}


?>
