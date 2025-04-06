<?php
namespace Lib;

class Helper{

    private static $network_regex = array( 'Glo' => '/^023\d{7}$/',
        'Fixed' => '/^03\d{8}$/',
        'MTN' => '/^024\d{7}$|^05[4,5,9]\d{7}$/',
        'Vodafone' => '/^0[2,5]0\d{7}$/',
        'AirtelTigo' => '/^0[2,5][6,7]\d{7}$/',
        'Expresso' => '/^028\d{7}$/');

    static function VerifyEmail($email) {
        $mail = filter_var($email, FILTER_SANITIZE_EMAIL);
        if ($email == $mail) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return true;
            }
        }
        return false;
    }
    static function VerifyPhoneNumber($number){
        foreach (Helper::$network_regex as $network => $reg) {
            if (preg_match($reg, $number, $match)) {
                return true;
            }
        }

        return false;
    }
}

?>