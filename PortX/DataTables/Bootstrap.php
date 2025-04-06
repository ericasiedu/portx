<?php
/**
 * DataTables PHP libraries.
 *
 * PHP libraries for DataTables and DataTables Editor, utilising PHP 5.3+.
 *
 *  @author    SpryMedia
 *  @copyright 2012 SpryMedia ( http://sprymedia.co.uk )
 *  @license   http://editor.datatables.net/license DataTables Editor
 *  @link      http://editor.datatables.net
 */


namespace DataTables;
// error_reporting(E_ALL);
// ini_set('display_errors', 0);
define("DATATABLES", false);

// if (!defined('DATATABLES')) exit();


//
// Configuration
//   Load the database connection configuration options
//
class Bootstrap{
    private $sql_details;
    function __construct() {
        $sql_details = array(
            "type" => "Mysql",     // Database type: "Mysql", "Postgres", "Sqlserver", "Sqlite" or "Oracle"
            "user" => "root",          // Database user name
            "pass" => "",          // Database password
            "host" => "localhost", // Database host
            "port" => "",          // Database connection port (can be left empty for default)
            "db"   => "portx",          // Database name
            "dsn"  => "",          // PHP DSN extra information. Set as charset=utf8mb4 if you are using MySQL
            "pdoAttr" => array()   // PHP PDO attributes array. See the PHP documentation for all options
        );
        $this->sql_details=$sql_details;
    }
    function database(){
        return new Database( $this->sql_details );
    }
}

