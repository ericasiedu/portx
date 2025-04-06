<?php
namespace Api;
session_start();

use DataTables\Editor\MJoin;
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

$system_object='empty-bookings';


class EmptyBooking {
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
                    ->validator(function($value, $data, $field, $host) {
                        $action = $host["action"];
                        $id = $host["id"];
                        $booking = $data["booking"]["booking_number"];

                        if ($action == "edit") {
                            $rowQuantity = $this->getRowQuantity($id);

                            $numberOfContainers = $this->getBookedContainers($booking);

                            if ($rowQuantity == $numberOfContainers)
                                return "Containers already booked to capacity";
                        }

                        return true;
                    }),
                Field::inst('booking.act')
                    ->options(function () {
                        return array(
                            array( 'value' => 'Evacuation to Port', 'label' => 'Evacuation to Port' ),
                            array( 'value' => 'Evacuation to Depot', 'label' => 'Evacuation to Depot' ),
                            array( 'value' => 'Pickup (EXP)', 'label' => 'Pickup (EXP)' ),
                            array( 'value' => 'N/A', 'label' => 'N/A' )
                        );
                    }),
                Field::inst('booking.booking_number')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('Booking number is required')
                    ))
                    ->validator(function($val, $data, $field, $host) {
                        $val = preg_replace("/\s+/", "", $val);
                        $val = html_entity_decode($val);

                        if($val != "" && !ctype_alnum($val)) {
                            return "Booking number must not contain symbols.";
                        }

                        $query = new MyQuery();
                        $query->query("SELECT id FROM booking WHERE booking_number = ?");
                        $query->bind = array('s', &$val);
                        $run = $query->run();

                        if ($run->num_rows() > 0) {
                            $result = $run->fetch_assoc();
                            if ($result['id'] == $host['id']) {
                                return true;
                            }
                            else{
                                return "Booking Number exists";
                            }
                        }
                        else{
                            $val = htmlspecialchars($val);
                            $query = new MyQuery();
                            $query->query("SELECT id FROM booking WHERE booking_number = ?");
                            $query->bind = array('s', &$val);
                            $run = $query->run();
    
                            if ($run->num_rows() > 0) {
                                $result = $run->fetch_assoc();
                                if ($result['id'] == $host['id']) {
                                    return true;
                                }
                                else{
                                    return "Booking Number exists";
                                }
                            }
                            else{
                                return true;
                            }
                           
                        }
                   

                     
                    }),
                Field::inst('booking.date')
            )
            ->on('preCreate', function ($editor,$values,$system_object='empty-bookings'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='empty-bookings'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='empty-bookings'){
                ACl::verifyUpdate($system_object);

                $query = new MyQuery();
                $query->query("SELECT payment.id FROM payment INNER JOIN invoice_container 
                            ON payment.invoice_id = invoice_container.invoice_id 
                            INNER JOIN container ON container.id = invoice_container.container_id 
                            INNER JOIN booking_container 
                            ON booking_container.container_id = invoice_container.container_id 
                            WHERE payment.paid > 0 AND booking_container.booking_id = ?");
                $query->bind = array('i', &$id);
                $run = $query->run();
                $result = $run->fetch_assoc();

                if ($result) {
                    return false;
                }

                return true;
            })
            ->on('validatedEdit', function ($editor,$id, $values,$system_object='empty-bookings'){
                ACl::verifyRead($system_object);

                $booking = $values["booking"]["booking_number"];
                $query = new MyTransactionQuery();
                $query->query("UPDATE container INNER JOIN booking_container 
                            ON container.id = booking_container.container_id 
                            SET book_number = NULL WHERE booking_container.booking_id = ?");
                $query->bind = array('i', &$id);
                $query->run();

                $query->query("UPDATE container INNER JOIN booking_container 
                            ON container.id = booking_container.container_id 
                            SET book_number = ? WHERE booking_container.booking_id = ?");
                $query->bind = array('si', &$booking, &$id);
                $query->run();

                $query->commit();
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='empty-bookings'){
                ACl::verifyDelete($system_object);

                $query = new MyTransactionQuery();
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

                $query->query("UPDATE container INNER JOIN booking_container 
                            ON container.id = booking_container.container_id 
                            SET book_number = NULL WHERE booking_container.booking_id = ?");
                $query->bind = array('i', &$id);
                $query->run();

                $query->commit();

                return true;
            })
            ->debug(true)
            ->process($_POST)
            ->json();
    }

    function get_containers() {
        $line = $this->request->param('line');
        $size = (int) $this->request->param('size');

        $query = new MyQuery();
        $query->query("SELECT container.id, container.number FROM container INNER JOIN shipping_line 
                ON shipping_line.id = container.shipping_line_id INNER JOIN 
                container_isotype_code ON container.iso_type_code = container_isotype_code.id 
                WHERE trade_type_code = '70' AND (book_number = '' OR book_number IS NULL) 
                AND shipping_line.name = ? AND container_isotype_code.length = ? 
                AND container.gate_status = 'GATED IN'");
        $query->bind = array('si', &$line, &$size);
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
        $query->query("UPDATE container SET book_number = NULL WHERE id IN (" . 
                $row_ids . ")");
        $run = $query->run();
        $result = $run->fetch_all();

        $query->query("UPDATE container SET book_number = ? WHERE id IN (" .
                $container_ids . ")");
        $query->bind = array('s', &$booking);
        $run = $query->run();
        $result = $run->fetch_all();


        echo json_encode($result);
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

    private function getBookedContainers($booking) {
        $query = new MyQuery();
        $query->query("SELECT COUNT(id) AS count FROM container WHERE book_number = ?");

        $query->bind = array('s', &$booking);
        $run = $query->run();
        $result = $run->fetch_assoc();

        return $result['count'];

    }

    private function getRowQuantity($id) {
        $query = new MyQuery();
        $query->query("SELECT quantity FROM booking WHERE id = ?");
        $query->bind = array('i', &$id);
        $run = $query->run();
        $result = $run->fetch_assoc();

        return $result['quantity'];

    }
}
?>