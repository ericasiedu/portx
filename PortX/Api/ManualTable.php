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

class ManualTable
{
    function __construct()
    {
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'container')
            ->fields(
                Field::inst('number'),
                Field::inst('bl_number'),
                Field::inst('book_number'),
                Field::inst('seal_number_1'),
                Field::inst('container.number'),
                Field::inst('container.number'),
                Field::inst('container.number'),
                Field::inst('container.number'),
                Field::inst('container.number')
            )
            ->process($_POST)
            ->json();


    }
}
?>