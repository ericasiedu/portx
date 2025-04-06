<?php
namespace Lib\Authentication;
session_start();
use Lib\Authenticate;

class Agent extends Authenticate{
    public $exempt=[
        "/api/tally_export/export",
        "/api/tally_export/export_file",
        "/api/login"
    ];
    public function validate(){
        if ($_SESSION['is_auth'] != 1){
            header("Location:/");
        }
    }
}