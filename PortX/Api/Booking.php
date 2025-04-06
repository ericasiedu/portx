<?php
namespace Api;
session_start();

use DataTables\Editor\Mjoin;
use DataTables\Editor\Options;
use
    Lib\ACL,
    Lib\MyQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;
use Lib\MyTransactionQuery;

$system_object='bookings';


class Booking {
    private $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    function table()
    {
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'booking')
            ->field(
                Field::inst('booking.shipping_line_id')
                    ->setFormatter(function($val) {
                        $query = new MyQuery();
                        $query->query("SELECT id FROM shipping_line WHERE name = ?");
                        $query->bind = array('s', &$val);
                        $run = $query->run();
                        $result = $run->fetch_assoc();

                        return $result['id'];
                    })
                    ->getFormatter(function($val) {
                        $query = new MyQuery();
                        $query->query("SELECT name FROM shipping_line WHERE id = ?");
                        $query->bind = array('i', &$val);
                        $run = $query->run();
                        $result = $run->fetch_assoc();

                        return $result['name'];
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Shipping line is required')
                    )),
                Field::inst('booking.customer_id')
                    ->setFormatter(function($val) {
                        $query = new MyQuery();
                        $query->query("SELECT id FROM customer WHERE name = ?");
                        $query->bind = array('s', &$val);
                        $run = $query->run();
                        $result = $run->fetch_assoc();

                        return $result['id'];
                    })
                    ->getFormatter(function($val) {
                        $query = new MyQuery();
                        $query->query("SELECT name FROM customer WHERE id = ?");
                        $query->bind = array('i', &$val);
                        $run = $query->run();
                        $result = $run->fetch_assoc();

                        return $result['name'];
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Booked by Party is required')
                    )),
                Field::inst('booking.size')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A size is required')
                    )),
                Field::inst('booking.quantity')
                    ->setFormatter(function($val) {
                        return (int) $val;     
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A quantity is required')
                    ))
                    ->validator(Validate::minNum( 1, '.', ValidateOptions::inst()
                        ->message('Minimum number of containers is 1')
                    ))
                    ->validator(function($val, $data) {
                        return $val % 1 == 0 ? true : 
                            "Quantity must be a positive integer";
                    })
                    ->validator(function ($val, $data) {
                        return ($val >= $data['container-many-count']) ?
                            true : 
                            "Quantity Exceeded: " .
                            "Please select the same number of containers as Quantity field or less";
                    }),
                    Field::inst('booking.act'),
                    Field::inst('booking.booking_number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Booking number is required')
                    )),
                Field::inst('booking.date')
            )
            ->on('preCreate', function ($editor,$values,$system_object='bookings'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='bookings'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='bookings'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='bookings'){
                ACl::verifyDelete($system_object);

                $query = new MyQuery();
                $query->query("SELECT invoice.id, invoice.status FROM invoice 
                            INNER JOIN booking 
                            ON invoice.book_number = booking.booking_number 
                            WHERE booking.id = ?");
                $query->bind = array('i', &$id);
                $run = $query->run();
                $result = $run->fetch_assoc();

                if ($result && $result['status'] != "CANCELLED") {
                    return false;
                }

                return true;
            })
            ->join(
                MJoin::inst('container')
                    ->link('booking.id', 'booking_container.booking_id')
                    ->link('container.id', 'booking_container.container_id')
                    ->fields(
                        Field::inst('id')
                            ->validator(Validate::required())
                            ->options(Options::inst()
                            ->table('container')
                            ->value('id')
                            ->label('number')
                            ->where(function ($q) {
                                $q->where('id', '(SELECT container.id FROM container INNER JOIN booking 
                                    ON container.book_number = booking.booking_number WHERE 
                                    container.trade_type_code = "70" /* AND book_number =  */)', 'IN', false);
                            })
                        ),
                        Field::inst('number')
                    )
            )
            ->validator(function($editor, $action, $data) {

                if ( $action === Editor::ACTION_CREATE || $action === Editor::ACTION_EDIT ) {
                    $flag = true;
                    foreach ( $data['data'] as $pkey => $values ) {
                        switch ($values['booking']['size']) {
                            case "20":
                                $flag = $this->checkTwentyFooter($values['container']);
                                break;
        
                            case "40":
                                $flag = $this->checkFortyFooter($values['container']);
                                break;
        
                            default:
                                $flag = $this->checkTwentyFooter($values['container']);
                                break;
                        }
                    }
                    return $flag ? "" : "Container-Size Mismatch: Container size must match size in booking";
                }

            })
            ->debug(true)
            ->process($_POST)
            ->json();
    }

    function get_containers() {
        $line = $this->request->param('line');
        $size = (int) $this->request->param('size');
        $booking = $this->request->param('booking');

        $lengthString = "('20')";

        switch ($size) {
            case "20":
                $lengthString = "('20')";
                break;

            case "40":
                $lengthString = "('40', '45')";
                break;

            default:
                $lengthString = "('20')";
                break;
        }

        $query = new MyQuery();
        $query->query("SELECT container.id, container.number FROM container INNER JOIN shipping_line 
                ON shipping_line.id = container.shipping_line_id INNER JOIN 
                container_isotype_code ON container.iso_type_code = container_isotype_code.id 
                WHERE trade_type_code = '70' AND (book_number = '' OR book_number IS NULL) 
                AND shipping_line.name = ? AND container_isotype_code.length IN $lengthString 
                AND container.gate_status = 'GATED IN'");
        $query->bind = array('s', &$line);
        $run = $query->run();
        $result = $run->fetch_all();

        $emptyContainers = $result;

        $query = new MyQuery();
        $query->query("SELECT id, number FROM container WHERE book_number = ?");
        $query->bind = array('s', &$booking);
        $run = $query->run();
        $result = $run->fetch_all();

        $rowContainers = $result;

        $data['empty'] = $emptyContainers;
        $data['row'] = $rowContainers;

        echo json_encode($data);
    }

    function get_row_containers() {
        $booking = $this->request->param('line');
        $size = (int) $this->request->param('size');

        $query = new MyQuery();
        $query->query("SELECT container.id, container.number,
                FROM container WHERE book_number = ?");
        $query->bind = array('s', &$booking);
        $run = $query->run();
        $result = $run->fetch_all();

        echo json_encode($result);

    }

    function get_edit_containers() {
        $line = $this->request->param('line');
        $size = (int) $this->request->param('size');

        $query = new MyQuery();
        $query->query("SELECT container.id, container.number, container.book_number 
                FROM container INNER JOIN shipping_line 
                ON shipping_line.id = container.shipping_line_id INNER JOIN 
                container_isotype_code ON container.iso_type_code = container_isotype_code.id 
                WHERE trade_type_code = '70' 
                AND shipping_line.name = ? AND container_isotype_code.length = ? 
                AND container.gate_status = 'GATED IN'");
        $query->bind = array('si', &$line, &$size);
        $run = $query->run();
        $result = $run->fetch_all();

        echo json_encode($result);
    }

    function assign_bookings() {
        $booking = $this->request->param('booking');
        $container_ids = $this->request->param('ids');

        $query = new MyQuery();
        $query->query("UPDATE container SET book_number = ? WHERE id IN (" .
                $container_ids . ")");
        $query->bind = array('s', &$booking);
        $run = $query->run();
        $result = $run->fetch_all();

        echo json_encode($result);
    }

    function assign_edit_bookings() {
        $booking = $this->request->param('booking');
        $container_ids = $this->request->param('ids');
        $row_ids = $this->request->param('rowids');


        $query = new MyTransactionQuery();

        if ($row_ids != "") {
            $query->query("UPDATE container SET book_number = NULL WHERE id IN (" . 
                    $row_ids . ")");
            $run = $query->run();
            $result = $run->fetch_all();
        }

        if ($container_ids != "") {
            $query->query("UPDATE container SET book_number = ? WHERE id IN (" .
                    $container_ids . ")");
            $query->bind = array('s', &$booking);
            $run = $query->run();
            $result = $run->fetch_all();
        }

        $query->commit();
    }

    function unbook_containers() {
        $ids = $this->request->param('ids');
        $query = new MyQuery();
        $query->query("UPDATE container SET book_number = NULL WHERE id IN 
                (" . $ids . ")");
        $run = $query->run();
        $result = $run->fetch_all();

        echo json_encode($result);

    }

    function checkTwentyFooter($containers) {
        $check = true;

        foreach($containers as $container) {
            $id = (int) $container["id"];

            $query = new MyQuery();
            $query->query("SELECT length FROM container_isotype_code INNER JOIN container 
                        ON container.iso_type_code = container_isotype_code.id 
                        WHERE container.id = ?");
            $query->bind = array('i', &$id);
            $run = $query->run();
            $result = $run->fetch_assoc("MYSQLI_ASSOC");

            if ($result["length"] != "20")
                $check = false;
        }

        return $check;
    }

    function checkFortyFooter($containers) {
        $check = false;

        foreach($containers as $container) {
            $id = (int) $container["id"];

            $query = new MyQuery();
            $query->query("SELECT length FROM container_isotype_code INNER JOIN container 
                        ON container.iso_type_code = container_isotype_code.id 
                        WHERE container.id = ?");
            $query->bind = array('i', &$id);
            $run = $query->run();
            $result = $run->fetch_assoc("MYSQLI_ASSOC");

            if ($result["length"] == "40" || $result["length"] == "45")
                $check = true;
        }

        return $check;
    }


}
?>