<?php

namespace Api;


use
    Lib\ACL,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\MJoin,
    DataTables\Editor\Options,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;
//   $system_object='udm-ports';

class DriverEditTable{
    function __construct()
    {
        $db = new Bootstrap();
        $db = $db->database();

        $let_pass_id = 0;
        if (isset($_POST['let_pass_id'])){
            $let_pass_id = $_POST['let_pass_id'];
        }

        Editor::inst($db, 'letpass_driver')
            ->fields(
                Field::inst('license')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('name')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
            )
            ->where('letpass_id', $let_pass_id)
            ->process($_POST)
            ->json();
    }
}
?>
