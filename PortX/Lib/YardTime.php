<?php
namespace Lib;

class YardTime{

    static function getTimeSpent($date_1,$date_2){
        $date1 = strtotime($date_1);
        $date2 = strtotime($date_2);

        $diff = abs($date2 - $date1);
        $hours = floor($diff / (60 * 60));
        $minutes = floor(($diff - $hours * 60 * 60) / 60);
        $seconds = $diff - $hours * 60 * 60 - $minutes * 60;
        return $hours.":".$minutes.":".$seconds;
    }
}

?>