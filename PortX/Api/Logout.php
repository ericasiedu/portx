<?php
namespace Api;

use Lib\Respond;

session_start();

class Logout{

    private $request;
    function __construct($request,$response) {
        $this->request=$request;
        if (isset($_SESSION['id']) && $_SESSION['is_auth'] == 1){
            unset($_SESSION['id']);
            session_destroy();
            new Respond(101);
        }
    }
}

?>