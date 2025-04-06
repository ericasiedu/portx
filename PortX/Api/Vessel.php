<?php
namespace Api;
session_start();
use
    Lib\ACL,
    Lib\MyQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;
$system_object='vessel-records';


class Vessel{
    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();


        Editor::inst($db, 'vessel')
            ->fields(
                Field::inst('vessel.code')
                    ->setFormatter(function($val) {
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::maxLen( 20,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A code is required')
                    )),
                Field::inst('vessel.name')
                    ->setFormatter(function($val) {
                        $val = html_entity_decode($val);
                        return mb_convert_case($val, MB_CASE_UPPER);
                    })
                    ->validator(Validate::maxLen( 100,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A name is required')
                    )),
                Field::inst('vessel.length_over_all')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A length is required')
                    )),
                Field::inst('vessel.net_tonnage')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A net tonnage is required')
                    )),
                Field::inst('vessel.gross_tonnage')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A gross tonnage is required')
                    )),
                Field::inst('vessel.teu_capacity')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A teu capacity is required')
                    )),
                Field::inst('vessel.dead_weight_tonnage')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A dead weight tonnage is required')
                    )),
                Field::inst('vessel.imo_number')
                    ->validator(Validate::maxLen( 20,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A imo number is required')
                    )),
                Field::inst('vessel.type_id')
                    ->setFormatter(function ($val) {
                        $qu = new MyQuery();
                        $qu->query("SELECT id FROM vessel_type WHERE  name = ?");
                        $qu->bind =  array('s', &$val);
                        $res = $qu->run();
                        return $res->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) {
                        $qu = new MyQuery();
                        $qu->query("SELECT name FROM vessel_type WHERE  id = ?");
                        $qu->bind =  array('i', &$val);
                        $res = $qu->run();
                        return $res->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Vessel type is required')
                    ))
                    ->validator(function ($val, $data, $field, $host) {
                        $qu = new MyQuery();
                        $qu->query("SELECT id FROM vessel_type WHERE name = ?");
                        $qu->bind =  array('s', &$val);
                        $res = $qu->run();;
                        if ($res->fetch_num()[0] == '') {
                            return "Vessel type does not exist";
                        } else {
                            return true;
                        }
                    }),
                Field::inst('vessel.registry_port_id')
                    ->setFormatter(function ($val) {
                        $val = html_entity_decode($val);
                        $qu = new MyQuery();
                        $qu->query("SELECT id FROM port WHERE  name = ?");
                        $qu->bind =  array('s', &$val);
                        $res = $qu->run();
                        if(!$res->num_rows()){
                            $val = htmlspecialchars($val);
                            $qu = new MyQuery();
                            $qu->query("SELECT id FROM port WHERE  name = ?");
                            $qu->bind =  array('s', &$val);
                            $res = $qu->run();
                        }
                        return $res->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) {
                        $qu = new MyQuery();
                        $qu->query("SELECT name FROM port WHERE  id = ?");
                        $qu->bind =  array('i', &$val);
                        $res = $qu->run();
                        return $res->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A port name is required')
                    ))
                    ->validator(function ($val, $data, $field, $host) {
                        $val = html_entity_decode($val);
                        $qu = new MyQuery();
                        $qu->query("SELECT id FROM port WHERE  name = ?");
                        $qu->bind =  array('s', &$val);
                        $res = $qu->run();
                        if ($res->fetch_num()[0] == '') {
                            $val = htmlspecialchars($val);
                            $qu = new MyQuery();
                            $qu->query("SELECT id FROM port WHERE  name = ?");
                            $qu->bind =  array('s', &$val);
                            $res = $qu->run();
                            if ($res->fetch_num()[0] == '') {
                                return 'Registry port does not exist';
                            } else {
                                return true;
                            }
                        } else {
                            return true;
                        }
                    }),
                Field::inst('vessel.country_id')
                    ->setFormatter(function ($val) {
                        $val = html_entity_decode($val);
                        $qu = new MyQuery();
                        $qu->query("SELECT id FROM country WHERE  name = ?");
                        $qu->bind =  array('s', &$val);
                        $res = $qu->run();
                        if(!$res->num_rows()){
                            $val = htmlspecialchars($val);
                            $qu = new MyQuery();
                            $qu->query("SELECT id FROM country WHERE  name = ?");
                            $qu->bind =  array('s', &$val);
                            $res = $qu->run();
                        }
                        return $res->fetch_num()[0] ?? '';
                    })
                    ->getFormatter(function ($val) {
                        $qu = new MyQuery();
                        $qu->query("SELECT name FROM country WHERE  id = ?");
                        $qu->bind =  array('i', &$val);
                        $res = $qu->run();
                        return $res->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Country is required')
                    ))
                    ->validator(function ($val, $data, $field, $host) {
                        $val = html_entity_decode($val);
                        $qu = new MyQuery();
                        $qu->query("SELECT id FROM country WHERE  name = ?");
                        $qu->bind =  array('s', &$val);
                        $res = $qu->run();
                        if ($res->fetch_num()[0] == '') {
                            $val = htmlspecialchars($val);
                            $qu = new MyQuery();
                            $qu->query("SELECT id FROM country WHERE  name = ?");
                            $qu->bind =  array('s', &$val);
                            $res = $qu->run();
                            if ($res->fetch_num()[0] == '') {
                                return 'Country does not exist';
                            } else {
                                return true;
                            }
                        } else {
                            return true;
                        }
                    }),
                Field::inst('vessel.year_built')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Year built is required')
                    ))
                    ->validator(function ($val, $data, $field, $host) {
                        $current_date = Date('Y');
                        if (!is_numeric($val)) {
                            return 'Field accepts only numbers';
                        }
                        if ($val < 1900) {
                            return 'Cannot choose date below 1900';
                        }
                        if ($val > $current_date) {
                            return "Cannot choose future date";
                        } else {
                            return true;
                        }
                    }),
                Field::inst('vessel_type.name'),
                Field::inst('port.name'),
                Field::inst('country.name')
            )
            ->on('preCreate', function ($editor,$values,$system_object='vessel-records'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='vessel-records'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='vessel-records'){
                if($id == 1)
                    return false;
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='vessel-records'){
                if($id == 1)
                    return false;
                ACl::verifyDelete($system_object);
            })
            ->where("vessel.id", 1, "!=")
            ->leftJoin('vessel_type', 'vessel.type_id', '=', "vessel_type.id")
            ->leftJoin('port', 'vessel.registry_port_id', '=', 'port.id')
            ->leftJoin('country', 'vessel.country_id', '=', 'country.id')
            ->process($_POST)
            ->json();

    }
    }
?>