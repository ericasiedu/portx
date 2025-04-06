<?php
namespace Api;
use Lib\ACL,
    Lib\MyQuery,
    Lib\Respond;

class Login {
    function __construct($request,$response) {
        $email = $request->param('em');
        $passwd = $request->param('pass');

        $qu=new MyQuery();
        $qu->query("SELECT id, password,grp FROM user WHERE email = ?");
        $qu->bind= array('s', &$email);
        $res=$qu->run();
        $resu = $res->fetch_assoc();

        if (!($resu['id'] && password_verify($passwd, $resu['password'])))
            echo "ERROR";
        else {
            session_start();
            $user_permission=(new ACL())->getUserPermissions($resu['grp']);
            $_SESSION['id'] = $resu['id'];
            $_SESSION['is_auth'] = 1;
            $_SESSION['acl']= $user_permission;
            new Respond(2, $user_permission);
        }
    }

}