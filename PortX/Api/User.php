<?php

namespace Api;
session_start();

use Lib\ACL,
    Lib\MyQuery,
    Lib\Respond,
    Lib\MyTransactionQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Options,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;
use Lib\Helper;


class User{

    private $request;

    public function  __construct($request)
    {
        $this->request = $request;

    }

    public function user_lock() {
        $user = $this->request->param('user_id');
        $query=new MyTransactionQuery();
        $query->query("UPDATE user SET status = '1' WHERE id = ?");
        $query->bind = array('i', &$user);
        $run=$query->run();
        $query->commit();
    }

    public function user_unlock(){
        $user = $this->request->param('user_id');
        $query=new MyTransactionQuery();
        $query->query("UPDATE user SET status = '0' WHERE id = ?");
        $query->bind = array('i', &$user);
        $run=$query->run();
        $query->commit();
    }

    public function user_reset(){
        $user = $this->request->param('user_id');
        $query=new MyTransactionQuery();
        $new_pass = password_hash('tCtl20!9', PASSWORD_DEFAULT) ;
        $query->query("UPDATE user SET password = ? WHERE id = ?");
        $query->bind = array('si', &$new_pass,&$user);
        $run=$query->run();
        $query->commit();
    }

    public function user_init(){
        $user = $_SESSION['id'];
        $details = array();
        $query=new MyQuery();
        $query->query("SELECT first_name, last_name, phone, email FROM user WHERE id = ?");
        $query->bind = array('i', &$user);
        $run=$query->run();

        $result = $run->fetch_assoc();

        $details['first'] = $result['first_name'];
        $details['last'] = $result['last_name'];
        $details['phone'] = $result['phone'];
        $details['email'] = $result['email'];

        new Respond(292, $details);
    }

    public function user_update(){
        $first = $this->request->param('first');
        $last = $this->request->param('last');
        $phone = $this->request->param('phone');

        if ($first == "" && $last == ""){
            new Respond(180);
        }

        $user = $_SESSION['id'];
        $first = trim(ucwords($first));
        $last = trim(ucwords($last));
        $phone = trim($phone);



        if (strlen($first) > 50){
            new Respond(181);
        }
        if (strlen($last) > 50){
            new Respond(182);
        }

        if(!Helper::VerifyPhoneNumber($phone) && $phone != ""){
            new Respond(11);
        }

        $query=new MyTransactionQuery();
        $query->query("UPDATE user SET first_name = ?, last_name = ?, phone = ? where id = ?");
        $query->bind = array('sssi', &$first, &$last, &$phone, &$user);
        $run=$query->run();
        $query->commit();
        new Respond(22);
    }

    public function user_change(){
        $id = $_SESSION['id'];
        $old_password = $this->request->param('oldp');
        $new_password = $this->request->param('newp');

        $query=new MyTransactionQuery();
        $query->query("SELECT password FROM user WHERE id = ?");
        $query->bind = array('i', &$id);
        $run=$query->run();
        $result = $run->fetch_assoc();

        if (password_verify($old_password, $result['password'])){
            $new_pass = password_hash($new_password, PASSWORD_DEFAULT);
            $query->query("UPDATE user SET password = ? WHERE id = ?");
            $query->bind = array('si', &$new_pass, &$id);
            $run=$query->run();
            $query->commit();

            new Respond(291);
        } else {
            $query->commit();
            new Respond(191);
        }
    }

    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'user')
            ->fields(
                Field::inst('user.first_name')
                    ->setFormatter(function($val) {
                        return mb_convert_case($val, MB_CASE_TITLE);
                    })
                    ->validator(Validate::maxLen( 50,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('user.last_name')
                    ->setFormatter(function($val) {
                        return mb_convert_case($val, MB_CASE_TITLE);
                    })
                    ->validator(Validate::maxLen( 50,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('user.phone')
                    ->validator(Validate::maxLen( 20,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) {
                        return Helper::VerifyPhoneNumber($val)  || $val == ''? true : "Invalid Phone Number";
                    }),
                Field::inst('user.email')
                    ->validator(Validate::maxLen( 80,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ( $val, $data, $field, $host ) {
                        return Helper::VerifyEmail($val) ? true : "Invalid Email";
                    })
                    ->validator(function ( $val, $data, $field, $host ) {
                        $val = html_entity_decode($val);
                        $query=new MyQuery();
                        $query->query("select id from user where email  = ?");
                        $query->bind = array('s', &$val);
                        $run=$query->run();
                        if ($run->num_rows()){
                            $id = $run->fetch_num()[0];
                            if($id == $host['id']){
                                return true;
                            }
                            else {
                                return "User already exists";
                            }
                        }
                        else{
                            $val = htmlspecialchars($val);
                            $query=new MyQuery();
                            $query->query("select id from user where email  = ?");
                            $query->bind = array('s', &$val);
                            $run=$query->run();
                            if ($run->num_rows()){
                                $id = $run->fetch_num()[0];
                                if($id == $host['id']){
                                    return true;
                                }
                                else {
                                    return "User already exists";
                                }
                            }
                            else{
                                return true;
                            }
                        }
                    }),
                Field::inst('user.password')
                    ->setFormatter(function ($val) {
                        return password_hash("tCtl20!9", PASSWORD_DEFAULT);
                    })
                    ->getFormatter(function ($val) {
                        return "secret";
                    }),
                Field::inst('user.status'),
                Field::inst('user.grp')
                    ->options( Options::inst()
                        ->table( 'user_group' )
                        ->value( 'id' )
                        ->label( 'name' )
                    )
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst( 'user_group.name' )
            )
            ->on('preCreate', function ($editor,$values,$system_object='user-account'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='user-account'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='user-account'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='user-account'){
                ACl::verifyDelete($system_object);
            })
            ->leftJoin('user_group','user.grp','=','user_group.id')
            ->where('user.id', 1, '<>')
            ->process($_POST)
            ->json();
    }

}

?>