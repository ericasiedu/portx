<?php
namespace Lib;

use Lib\MyQuery;

class User{
    static function getUserById($user_id){
        $query = new MyQuery();
        $query->query("select concat(first_name,' ',last_name) as full_name from user where id=?");
        $query->bind = array('i',&$user_id);
        $query->run();
        $result = $query->fetch_assoc();
        return $result['full_name'];
    }
}

?>