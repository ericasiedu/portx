<?php

namespace Api;
use Lib\MyQuery;


use
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\MJoin,
    DataTables\Editor\Options,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;

class ConditionTable{
    function __construct()
    {
        $db=new Bootstrap();
        $db=$db->database();

        $record = 0;

        if (isset($_POST['record']))
            $record = $_POST['record'];

        Editor::inst( $db, 'gate_record_container_condition' )
            ->fields(
                Field::inst('gate_record_container_condition.container_section')
                    ->setFormatter(function($val) {
                        $qu=new MyQuery();
                        $qu->query("select id from container_section where name = '$val'");
                        $res=$qu->run();
                        return $res->fetch_num()[0] ?? '';
                    }),
                Field::inst('gate_record_container_condition.damage_type')
                    ->setFormatter(function($val) {
                        $qu=new MyQuery();
                        $qu->query("select id from container_damage_type where name = '$val'");
                        $res=$qu->run();
                        return $res->fetch_num()[0] ?? '';
                    }),
                Field::inst('gate_record_container_condition.damage_severity')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('gate_record_container_condition.note'),
                Field::inst('container_section.name'),
                Field::inst('container_damage_type.name')
            )
            ->leftJoin('container_section', 'container_section.id', '=', 'gate_record_container_condition.container_section')
            ->leftJoin('container_damage_type', 'container_damage_type.id', '=', 'gate_record_container_condition.damage_type')
            ->where('gate_record_container_condition.gate_record', $record, '=')
            ->process($_POST)
            ->json();
    }
}



?>
