<?php

namespace Api;
session_start();

use
    Lib\ACL,
    Lib\MyQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Options,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;


class DepotActivityCharges {

    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db=new Bootstrap();
        $db=$db->database();
        Editor::inst( $db, 'charges_container_depot_activity' )
            ->fields(
                Field::inst('charges_container_depot_activity.trade_type')
                    ->options(Options::inst()
                        ->table('trade_type')
                        ->value('id')
                        ->label('name')
                    ),
                Field::inst('charges_container_depot_activity.container_length'),
                Field::inst('charges_container_depot_activity.load_status')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('charges_container_depot_activity.goods'),
                Field::inst('charges_container_depot_activity.oog_status')
                    ->setFormatter(function($val) {
                        return $val == "NO" ? 0 : 1;
                    })
                    ->getFormatter(function($val) {
                        return $val == 1 ? "YES" : "NO";
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('charges_container_depot_activity.activity')
                    ->options(Options::inst()
                        ->table('depot_activity')
                        ->value('id')
                        ->label('name')
                        ->order('id ASC')
                        ->where(function ($q){
                            $q->where('id', '(SELECT id FROM depot_activity WHERE billable = 1)', 'IN', false);
                        })
                    )
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('charges_container_depot_activity.full_status')
                    ->setFormatter(function($val) {
                        return $val == "NO" ? 0 : 1;
                    })
                    ->getFormatter(function($val) {
                        return $val == 1 ? "YES" : "NO";
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('charges_container_depot_activity.cost')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(function ($val){
                        if (!is_numeric($val)){
                            return "invalid data";
                        }
                        else{
                            return true;
                        }
                    })
                    ->validator(Validate::numeric()),
                Field::inst('charges_container_depot_activity.currency')
                    ->options(Options::inst()
                        ->table('currency')
                        ->value('id')
                        ->label('code')
                    ),
                Field::inst('charges_container_depot_activity.date'),
                Field::inst('charges_container_depot_activity.id'),
                Field::inst('trade_type.name'),
                Field::inst('depot_activity.name'),
                Field::inst('currency.code')
            )
    ->validator(function($editor, $action, $data) {



        if ($action === Editor::ACTION_CREATE || $action === Editor::ACTION_EDIT) {
            $qu=new MyQuery();
            $qu->query("select charges_container_depot_activity.trade_type, charges_container_depot_activity.container_length,
                              charges_container_depot_activity.load_status, charges_container_depot_activity.goods,
                              charges_container_depot_activity.oog_status, activity,
                              charges_container_depot_activity.full_status, charges_container_depot_activity.cost,
                              charges_container_depot_activity.currency,charges_container_depot_activity.id from charges_container_depot_activity");
            $res=$qu->run();
            $result = $res->fetch_all();
            $rows = $res->num_rows();
            for ($row = 0; $row < $rows; $row++) {
                $record = $result[$row];
                foreach ($data['data'] as $pkey => $values) {

                  $id = substr($pkey,4);

                    if ($record[0] == $values["charges_container_depot_activity"]['trade_type'] &&
                        $record[1] == $values["charges_container_depot_activity"]['container_length'] &&
                        $record[2] == $values["charges_container_depot_activity"]['load_status'] &&
                        $record[3] == $values["charges_container_depot_activity"]['goods'] &&
                        ($record[4] ? "YES" : "NO") == $values["charges_container_depot_activity"]['oog_status'] &&
                        $record[5] == $values["charges_container_depot_activity"]['activity'] &&
                        ($record[6] ? "YES" : "NO")  == $values["charges_container_depot_activity"]['full_status'] &&
                        $record[8] == $values["charges_container_depot_activity"]['currency'] &&
                        $record[9] != $id)

                        return "Entry Already Exists";
                }
            }
        }
        return "";
    })
            ->on('preCreate', function ($editor,$values,$system_object='udm-depot-activity-charges'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-depot-activity-charges'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-depot-activity-charges'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-depot-activity-charges'){
                ACl::verifyDelete($system_object);
            })
            ->leftJoin('trade_type', 'charges_container_depot_activity.trade_type', '=', 'trade_type.id')
            ->leftJoin('depot_activity', 'charges_container_depot_activity.activity', '=', 'depot_activity.id')
            ->leftJoin('currency', 'charges_container_depot_activity.currency', '=', 'currency.id')
            ->process($_POST)
            ->json();
    }
}


?>