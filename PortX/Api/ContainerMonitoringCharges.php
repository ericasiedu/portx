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


class ContainerMonitoringCharges
{

    private $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'charges_container_monitoring')
            ->fields(
                Field::inst('charges_container_monitoring.trade_type')
                ->options(Options::inst()
                    ->table('trade_type')
                    ->value('id')
                    ->label('name')
                ),
                Field::inst('charges_container_monitoring.cost_per_day')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    ))
                    ->validator(Validate::numeric()),
                Field::inst('charges_container_monitoring.date'),
                Field::inst('charges_container_monitoring.id'),
                Field::inst('charges_container_monitoring.goods'),
                Field::inst('charges_container_monitoring.currency')
                    ->options(Options::inst()
                        ->table('currency')
                        ->value('id')
                        ->label('code')
                    ),
                Field::inst('currency.code'),
                Field::inst('trade_type.name')
            )
            ->validator(function ($editor, $action, $data) {
                if ($action === Editor::ACTION_CREATE || $action === Editor::ACTION_EDIT) {
                    $qu = new MyQuery();
                    $qu->query("select id, goods, currency, trade_type from charges_container_monitoring");
                    $res = $qu->run();
                    $result = $res->fetch_all();
                    $rows = $res->num_rows();
                    for ($row = 0; $row < $rows; $row++) {
                        $record = $result[$row];
                        foreach ($data['data'] as $pkey => $values) {
                            if (($record[1] == $values["charges_container_monitoring"]['goods']) &&
                                ($record[2] == $values["charges_container_monitoring"]['currency']) &&
                                ($record[3] == $values["charges_container_monitoring"]['trade_type'])) {
                                if($record[0] == $values["charges_container_monitoring"]['id']) {
                                    return "";
                                }
                                return 'Entry Already Exists';
                            }
                        }
                    }

                }
                return "";
            })
            ->on('preCreate', function ($editor, $values, $system_object = 'udm-charges-container-monitoring') {
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor, $id, $system_object = 'udm-charges-container-monitoring') {
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'udm-charges-container-monitoring') {
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'udm-charges-container-monitoring') {
                ACl::verifyDelete($system_object);
            })
            ->leftJoin('trade_type', 'charges_container_monitoring.trade_type', '=', 'trade_type.id')
            ->leftJoin('currency', 'charges_container_monitoring.currency', '=', 'currency.id')
            ->process($_POST)
            ->json();

    }
}

?>