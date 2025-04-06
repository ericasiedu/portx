<?php
namespace Lib;
use Lib\MyQuery;

class DepotActivity{

    public function activity_list(){
        $qu = new MyQuery();
        $qu->query("select name from depot_activity where billable = '1'");
        $res=$qu->run();
        return $res->fetch_all();
    }

}

?>