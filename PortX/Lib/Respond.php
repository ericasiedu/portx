<?php
namespace Lib;
class Respond{
    function __construct($state) {
        $exitbit=substr($state,0,1);
        $res=array('st'=>$state);
        if ($res1=@func_get_arg(1)){
            $res=array_merge($res,$res1);
        }
        $res=json_encode($res);
        echo $res;
        if ($exitbit == 1 || $exitbit == 'X'){
            exit;
        }
    }
}