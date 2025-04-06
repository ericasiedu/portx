<?php
namespace Lib;
class MyQuery {
    protected $conn;
    protected $stmt;
    public $error;
    protected $result_all;
    public $bind;
    protected $last_id;

    function __construct() {
        $host = 'localhost';
        $user = 'root';
        $pass = '';
        $db = 'portx';
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->conn = mysqli_connect($host, $user, $pass, $db);
    }

    function query($query) {
        $this->stmt = mysqli_prepare($this->conn, $query);
    }

    protected function param() {
        if ($this->bind) {
            array_unshift($this->bind, $this->stmt);
            call_user_func_array('mysqli_stmt_bind_param', $this->bind);
        }
    }

    function fetch_assoc() {
        return mysqli_fetch_assoc($this->result_all);
    }

    function fetch_num() {
        return mysqli_fetch_row($this->result_all);
    }

    function fetch_all($resultype=MYSQLI_NUM) {
        return mysqli_fetch_all($this->result_all,$resultype);
    }

    function num_rows() {
        return mysqli_num_rows($this->result_all);
    }

    function run() {
        $this->param();
        mysqli_stmt_execute($this->stmt);
        #echo mysqli_stmt_error($this->stmt);
        $this->result_all = mysqli_stmt_get_result($this->stmt);
        mysqli_close($this->conn);
        return $this;
    }

    function get_last_id(){
        return $this->last_id;
    }
}