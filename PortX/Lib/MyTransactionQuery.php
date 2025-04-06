<?php
namespace Lib;
class MyTransactionQuery extends \Lib\MyQuery {
    function __construct() {
        parent::__construct();
        mysqli_autocommit($this->conn, FALSE);
    }

    function run() {
        parent::param();
        mysqli_stmt_execute($this->stmt);
        #echo mysqli_stmt_error($this->stmt);
        $this->result_all = mysqli_stmt_get_result($this->stmt);
        $this->last_id = mysqli_insert_id($this->conn);
        return $this;
    }

    function rollback() {
        mysqli_rollback($this->conn);
    }

    function commit() {
        mysqli_commit($this->conn);
        mysqli_close($this->conn);
    }
}