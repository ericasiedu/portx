<?php

namespace Api;

use DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Options,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;

class GateCondition{
    private $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    function table()
    {
        $db=new Bootstrap();
        $db=$db->database();

        $record = $this->request->param('record');

        Editor::inst( $db, 'gate_record_container_condition' )
            ->fields(
                Field::inst('gate_record_container_condition.gate_record')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('gate_record_container_condition.container_section'),
                Field::inst('gate_record_container_condition.damage_type')
                    ->options(Options::inst()
                        ->table('container_damage_type')
                        ->value('id')
                        ->label('name')
                    ),
                Field::inst('gate_record_container_condition.damage_severity')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Empty Field')
                    )),
                Field::inst('gate_record_container_condition.note'),
                Field::inst('container_section.name as sect'),
                Field::inst('container_damage_type.name as dmgn')
            )
            ->leftJoin('container_section', 'container_section.id', '=', 'gate_record_container_condition.container_section')
            ->leftJoin('container_damage_type', 'container_damage_type.id', '=', 'gate_record_container_condition.damage_type')
            ->where('gate_record_container_condition.gate_record', $record, '=')
            ->process($_POST)
            ->json();
    }

}


?>
