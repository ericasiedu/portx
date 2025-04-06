<?php
namespace Lib;


abstract class Authenticate {
    public $request;
    public $response;
    public $service;
    public $app;
    public $exempt=[
        "user/dashboard",
        "user/gate_in"
    ];
    function __construct($request,$response,$service,$app) {
        $this->request=$request;
        $this->response=$response;
        $this->service=$service;
        $this->app=$app;
        if (!in_array($this->request->pathname(),$this->exempt)){
            $this->validate();
        }
    }
    public function validate(){

    }
}