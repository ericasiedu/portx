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


class StorageRentCharges {

    private $request;

    function __construct($request){
        $this->request = $request;
    }

    function table(){
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'charges_storage_rent_teu')
            ->fields(
                Field::inst('charges_storage_rent_teu.trade_type')
                    ->options(Options::inst()
                        ->table('trade_type')
                        ->value('id')
                        ->label('name')
                    ),
                Field::inst('charges_storage_rent_teu.full_status')
                    ->setFormatter(function ($val) {
                        return $val == "NO" ? 0 : 1;
                    })
                    ->getFormatter(function ($val) {
                        return $val == 1 ? "YES" : "NO";
                    }),
                Field::inst('charges_storage_rent_teu.free_days')
                    ->validator(Validate::maxLen(3, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(Validate::numeric()),
                Field::inst('charges_storage_rent_teu.first_billable_days')
                    ->validator(Validate::maxLen(3, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                ->validator(Validate::notEmpty(ValidateOptions::inst()
                    ->message('Empty Field')
                ))
                ->validator(Validate::numeric()),
                Field::inst('charges_storage_rent_teu.first_billable_days_cost')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(Validate::numeric()),
                Field::inst('charges_storage_rent_teu.second_billable_days')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(Validate::maxLen(3, ValidateOptions::inst()
                        ->message('Max length of field exceeded')
                    ))
                    ->validator(Validate::numeric()),
                Field::inst('charges_storage_rent_teu.second_billable_days_cost')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                ->validator(Validate::numeric()),
                Field::inst('charges_storage_rent_teu.allother_billable_days_cost')
                ->validator(Validate::notEmpty(ValidateOptions::inst()
                ->message('Empty Field')
                ))
                ->validator(Validate::numeric()),
                Field::inst('charges_storage_rent_teu.currency')
                    ->options(Options::inst()
                        ->table('currency')
                        ->value('id')
                        ->label('code')
                    ),
                Field::inst('charges_storage_rent_teu.date'),
                Field::inst('charges_storage_rent_teu.goods'),
                Field::inst('trade_type.name'),
                Field::inst('currency.code')
            )
    ->validator(function($editor, $action, $data) {
        if ($action === Editor::ACTION_CREATE || $action === Editor::ACTION_EDIT) {
            $qu=new MyQuery();
            $qu->query("select trade_type, full_status, free_days, first_billable_days, first_billable_days_cost, second_billable_days, second_billable_days_cost, allother_billable_days_cost, currency, goods from charges_storage_rent_teu");
            $res=$qu->run();
            $result = $res->fetch_all();
            $rows = $res->num_rows();
            for ($row = 0; $row < $rows; $row++) {
                $record = $result[$row];
                foreach ($data['data'] as $pkey => $values) {
                    if (($record[0] == $values["charges_storage_rent_teu"]['trade_type']) &&
                        (($record[1] ? "YES" : "NO") == $values["charges_storage_rent_teu"]['full_status']) &&
                        ($record[2] == $values["charges_storage_rent_teu"]['free_days']) &&
                        ($record[3] == $values["charges_storage_rent_teu"]['first_billable_days']) &&
                        ($record[4] == $values["charges_storage_rent_teu"]['first_billable_days_cost']) &&
                        ($record[5] == $values["charges_storage_rent_teu"]['second_billable_days']) &&
                        ($record[6] == $values["charges_storage_rent_teu"]['second_billable_days_cost']) &&
                        ($record[7] == $values["charges_storage_rent_teu"]['allother_billable_days_cost']) &&
                        ($record[8] == $values["charges_storage_rent_teu"]['currency']) &&
                        ($record[9] == $values["charges_storage_rent_teu"]['goods']))
                    return 'Entry Already Exists';
                }
            }

        }
        return "";
    })
            ->on('preCreate', function ($editor,$values,$system_object='udm-storage-rent-charges'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-storage-rent-charges'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-storage-rent-charges'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-storage-rent-charges'){
                ACl::verifyDelete($system_object);
            })
            ->leftJoin('trade_type', 'charges_storage_rent_teu.trade_type', '=', 'trade_type.id')
            ->leftJoin('currency', 'charges_storage_rent_teu.currency', '=', 'currency.id')
            ->process($_POST)
            ->json();

    }
    }
?>