<?php

namespace Api;
session_start();

use DateTime;
use Lib\ACL,
    Lib\MyQuery,
    Lib\MyTransactionQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;

$system_object='move-to-export';

class MoveToExport{
    private $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'container')
            ->fields(
                Field::inst("container.number as number")
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 10,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    )),
                Field::inst("shipping_line.name as name")
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 100,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    )),
                Field::inst("container.book_number as book_number")
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    ))
                    ->validator(Validate::maxLen( 100,ValidateOptions::inst()
                        ->message('Maximum length for field exceeded')
                    )),
                Field::inst("container.moved_to as moved_to"),
                Field::inst("container.id as id")
           )
            ->on('preCreate', function ($editor,$values,$system_object='udm-ports'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='udm-ports'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='udm-ports'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='udm-ports'){
                ACl::verifyDelete($system_object);
            })
            ->leftJoin('shipping_line', 'container.shipping_line_id', '=', 'shipping_line.id')
            ->where('container.trade_type_code', '70')
            ->where('container.book_number', NULL, '!=')
            ->where('container.book_number', "", '!=')
            ->where('container.gate_status', "GATED OUT", '!=')
            ->process($_POST)
            ->json();
    }

    function move() {
        $id = $this->request->param('id');
        $booking = $this->request->param('booking');
        $content = $this->request->param('content');

        $tranQuery = new MyTransactionQuery();
        $tranQuery->query("SELECT number, iso_type_code, shipping_line_id, 
            agency_id, gate_record.cond FROM container INNER JOIN gate_record 
            ON container.id = gate_record.container_id WHERE container.id = ?");
        $tranQuery->bind = array('i', &$id);
        $run = $tranQuery->run();
        $result = $run->fetch_assoc();

        $number = $result['number'];
        $isoTypeCode = $result['iso_type_code'];
        $shippingLineId = $result['shipping_line_id'];
        $agencyId = $result['agency_id'];
        $condition = $result['cond'];

        $tranQuery->query("INSERT INTO container (number, book_number, iso_type_code, 
            shipping_line_id, agency_id, trade_type_code, content_of_goods, 
            gate_status, full_status) 
            VALUES (?, ?, ?, ?, ?, '21', ?, 'GATED IN', 1)");
        $tranQuery->bind = array('ssiiis', &$number, &$booking, &$isoTypeCode, 
            &$shippingLineId, &$agencyId, &$content);
        $run = $tranQuery->run();

        $tranQuery->query("SELECT id FROM container WHERE number = ? 
            AND trade_type_code = '21' AND gate_status = 'GATED IN' ORDER BY id DESC");
        $tranQuery->bind = array('s', &$number);
        $run = $tranQuery->run();
        $result = $run->fetch_assoc();

        $exportId = $result['id'];
        $userId = $_SESSION['id'];
        $date = date('Y-m-d H:i:s');
        $info_category = 'General Goods';

        $tranQuery->query("INSERT INTO gate_record (container_id, type, depot_id, 
            gate_id, user_id, cond, date) VALUES (?, 'GATE IN', 3, 4, 
            ?, ?, ?)");
        $tranQuery->bind = array('iiis', &$exportId, &$userId, &$condition, &$date);
        $run = $tranQuery->run();

        $tranQuery->query("UPDATE container SET moved_to = ?, gate_status = 'MOVED' 
                        WHERE id = ?");
        $tranQuery->bind = array('ii', &$exportId, &$id);
        $run = $tranQuery->run();

        $tranQuery->query("insert into container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL',?,?)");
        $tranQuery->bind = array('isi', &$exportId, &$info_category, &$userId);
        $tranQuery->run();
        $tranQuery->query("insert into proforma_container_depot_info(container_id,load_status,goods,user_id)values(?,'FCL',?,?)");
        $tranQuery->bind = array('isi', &$exportId, &$info_category, &$userId);
        $tranQuery->run();


        $tranQuery->query("select id from depot_activity where is_default = 1");
        $tranQuery->bind = array();
        $tranQuery->run();
        $activities = $tranQuery->fetch_all(MYSQLI_ASSOC);

        foreach ($activities as $activity) {
            $default_activity = $activity['id'];
            $tranQuery->query("INSERT INTO container_log(container_id, activity_id, user_id, date)VALUES(?,?,?, now())");
            $tranQuery->bind = array('iii', &$exportId, &$default_activity, &$userId);
            $tranQuery->run();

            $tranQuery->query("insert into container_log_history(container_id,activity_id,user_id,status)values(?,?,?,'CREATED');
            ");
            $tranQuery->bind = array('iii', &$exportId, &$default_activity, &$userId);
            $tranQuery->run();

            $tranQuery->query("INSERT INTO proforma_container_log(container_id, activity_id, user_id, date)VALUES(?,?,?, now())");
            $tranQuery->bind = array('iii', &$exportId, &$default_activity, &$userId);
            $tranQuery->run();

            $tranQuery->query("insert into proforma_container_log_history(container_id,activity_id,user_id,status)values(?,?,?,'CREATED');
            ");
            $tranQuery->bind = array('iii', &$exportId, &$default_activity, &$userId);
            $tranQuery->run();
        }

        $tranQuery->commit();
    }

    function cancel() {
        $exportId = $this->request->param('id');

        $tranQuery = new MyTransactionQuery();
        $tranQuery->query("DELETE FROM container_log WHERE EXISTS (SELECT 1 FROM depot_activity 
            WHERE container_log.activity_id = depot_activity.id) 
            AND container_log.container_id = ?");
        $tranQuery->bind = array('i', &$exportId);
        $tranQuery->run();

        $tranQuery->query("DELETE FROM proforma_container_log WHERE EXISTS (SELECT 1 FROM depot_activity 
            WHERE proforma_container_log.activity_id = depot_activity.id) 
            AND proforma_container_log.container_id = ?");
        $tranQuery->bind = array('i', &$exportId);
        $tranQuery->run();

        $tranQuery->query("DELETE FROM gate_record WHERE container_id = ?");
        $tranQuery->bind = array('i', &$exportId);
        $tranQuery->run();

        $tranQuery->query("DELETE FROM container_depot_info WHERE container_id = ?");
        $tranQuery->bind = array('i', &$exportId);
        $tranQuery->run();

        $tranQuery->query("DELETE FROM proforma_container_depot_info WHERE container_id = ?");
        $tranQuery->bind = array('i', &$exportId);
        $tranQuery->run();

        $tranQuery->query("UPDATE container SET moved_to = NULL, gate_status = 'GATED IN' 
                        WHERE moved_to = ?");
        $tranQuery->bind = array('i', &$exportId);
        $tranQuery->run();

        $tranQuery->query("DELETE FROM container WHERE id = ?");
        $tranQuery->bind = array('i', &$exportId);
        $tranQuery->run();

        $tranQuery->commit();
    }
}
?>
