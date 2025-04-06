<?php
namespace Lib;

class System{
    public $name;
    public $tin;
    public $location;
    public $phone_1;
    public $phone_2;
    public $mail;
    public $web;
    public $logo;
    public $tax;
    public $prefix;
    public $idn_seperator;
    public $sw_version;
    public $sw_date;

    function updateSystem(){
        $query = new MyTransactionQuery();
        $query->query("update system set company_name =?,company_tin =?,company_location=?,company_phone1=?,company_phone2=?,company_email=?,company_web=?,company_logo=?,tax_type=?,prefix=?,idn_separator=?,sw_version=?,sw_release_date=? where id ='1' ");
        $query->bind = array('ssssssssissss' ,&$this->name, &$this->tin, &$this->location, &$this->phone_1, &$this->phone_2, &$this->mail, &$this->web, &$this->logo, &$this->tax, &$this->prefix, &$this->idn_seperator, &$this->sw_version, &$this->sw_date);
        $query->run();
        $query->commit();
    }

}

?>