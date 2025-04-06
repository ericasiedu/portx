<?php
#INCOMPLETE
namespace Lib;

class Config {
    public static $DB_HOST;
    function __construct() {
        $l=file_get_contents("PortX/Lib/env.json",FILE_USE_INCLUDE_PATH);
        $l=json_decode($l,true);
        $this->DB_HOST=$l['db_host'];
/*        const DB_USER=$l['db_user'];
        const DB_PASS=$l['db_pass'];
        const DB_NAME=$l['db_name'];  */
    }
}