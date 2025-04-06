<?php
namespace Api;
use DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    Lib\MyQuery,
    Lib\Respond;

class ContainerHistory
{
    private $order_key;
    private $order_dir;
    private $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    public function search()
    {
        $search = $this->request->param('qury');
        if (rtrim($search) == "") {
            new Respond(140);
        } else {
            $query = new MyQuery();
            $query->query("select DISTINCT(container.id), container.number from `gate_record` 
                                 inner join container on gate_record.container_id = container.id where gate_record.id is not null
                                 and gate_record.status = 1 and number like ?");
            $query->bind = array('s', &$search);
            $results = $query->run();
            $count = $results->num_rows();
            new Respond($count > 0 ? 240 : 141);
        }
    }

    public function results()
    {
        $container = $this->request->param('cntr');
        $db = new Bootstrap();
        $db = $db->database();
        Editor::inst($db, 'container')
            ->fields(
                Field::inst('container.id as id'),
                Field::inst('container.number as num'),
                Field::inst('container.bl_number as blnum'),
                Field::inst('container.book_number as bknum'),
                Field::inst('container_isotype_code.length as len'),
                Field::inst('container.id as gtin')
                    ->getFormatter(function ($val) {
                        $qu = new MyQuery();
                        $qu->query("select date from gate_record where container_id = ? and type = 'GATE IN'");
                        $qu->bind = array('i', &$val);
                        $res = $qu->run();
                        return $res->fetch_num()[0] ?? '';
                    }),
                Field::inst('trade_type.name as trty'),
                Field::inst('container.id as gtout')
                    ->getFormatter(function ($val) {
                        $qu = new MyQuery();
                        $qu->query("select date from gate_record where container_id = ? and type = 'GATE OUT'");
                        $qu->bind = array('i', &$val);
                        $res = $qu->run();
                        return $res->fetch_num()[0] ?? '';
                    })
            )
            ->on('preCreate', function ($editor, $values, $system_object = 'udm-banks') {
                exit();
            })
            ->on('preEdit', function ($editor, $id, $values, $system_object = 'udm-banks') {
                exit();
            })
            ->on('preRemove', function ($editor, $id, $values, $system_object = 'udm-banks') {
                exit();
            })
            ->leftJoin('container_isotype_code', "container.iso_type_code", "=", "container_isotype_code.id")
            ->leftJoin('trade_type', "container.trade_type_code", "=", "trade_type.code")
            ->where(function ($q) use ($container) {
                $q->where('container.id', "(select DISTINCT(gate_record.container_id) from `gate_record` 
                                 inner join container on gate_record.container_id = container.id where gate_record.id is NOT NULL
                                 AND gate_record.status = 1 AND number like '$container')", 'IN', false);

            })
            ->where('container.number', $container, 'like')
            ->process($_POST)
            ->json();
    }

    public function view_history()
    {
        $container = $this->request->param('cntr');
        $order = $this->request->param('order');
        $this->order_key = $order[0]['column'];
        $this->order_dir = $order[0]['dir'];
        $start_offset = $this->request->param('start');
        $search_query = $this->request->param('search')['value'];

        $search_query = '*'.$search_query."*i";

        $id = 1;

        switch($this->order_key){
            case 0:
                $this->order_key = 'id';
                break;
            case 1:
                $this->order_key = 'activity';
                break;
            case 2:
                $this->order_key = 'note';
                break;
            case 3:
                $this->order_key = 'user';
                break;
            case 4:
                $this->order_key = 'date';
                break;
        }

        $data = array();

        $query = new MyQuery();
        $query->query("SELECT \"Gate In\" as activity, gate_record.id, gate_record.date,vehicle_driver.name as driver, user.first_name, user.last_name, vehicle.number as vehicle, vessel.name as vessel, voyage.reference as voyage from gate_record 
                            LEFT JOIN vehicle on vehicle.id = gate_record.vehicle_id 
                            LEFT JOIN vehicle_driver on vehicle_driver.id = gate_record.driver_id 
                            LEFT JOIN container on gate_record.container_id = container.id 
                            LEFT JOIN voyage on container.voyage = voyage.id 
                            LEFT JOIN vessel on voyage.vessel_id = vessel.id 
                            LEFT JOIN user on user.id = gate_record.user_id
                            WHERE gate_record.type = \"GATE IN\" and  container.id like ?");
        $query->bind = array('i', &$container);
        $activity_result = $query->run();
        if($activity = $activity_result->fetch_assoc()) {
            $driver = $activity['driver'];
            $vehicle = $activity['vehicle'];
            $activity_type = $activity['activity'];
            $vessel = $activity['vessel'];
            $voyage = $activity['voyage'];

            $note = "<b>Driver</b>: $driver <br><b>Vehicle</b>: $vehicle<br><b>Vessel</b>: $vessel<br><b>Voyage</b>: $voyage";

            array_push($data, array('id' => $activity['id'], 'date' => $activity['date'], 'act' => $activity_type, 'user' => rtrim($activity['first_name'] . " " . $activity['last_name']),
                "note" => $note));
        }

        $query = new MyQuery();
        $query->query("SELECT \"Moved to UCL\" as activity, gate_record.id, container_ucl_history.date, user.first_name, user.last_name, vessel.name as vessel, voyage.reference as voyage from container_ucl_history LEFT JOIN gate_record ON gate_record.container_id = container_ucl_history.container_id 
        LEFT JOIN vehicle on vehicle.id = gate_record.vehicle_id 
        LEFT JOIN vehicle_driver on vehicle_driver.id = gate_record.driver_id 
        LEFT JOIN container on gate_record.container_id = container.id 
        LEFT JOIN voyage on container.voyage = voyage.id 
        LEFT JOIN vessel on voyage.vessel_id = vessel.id 
        LEFT JOIN user on user.id = gate_record.user_id
        WHERE gate_record.type = \"GATE IN\" and  container.id like ?");
        $query->bind = array('i', &$container);
        $activity_result = $query->run();
        if($activity = $activity_result->fetch_assoc()) {
            $activity_type = $activity['activity'];
            $vessel = $activity['vessel'];
            $voyage = $activity['voyage'];

            $note = "<b>Vessel</b>: $vessel<br><b>Voyage</b>: $voyage";

            array_push($data, array('id' => $activity['id'], 'date' => $activity['date'], 'act' => $activity_type, 'user' => rtrim($activity['first_name'] . " " . $activity['last_name']),
                "note" => $note));
        }

        $query = new MyQuery();
        $query->query("SELECT \"Moved to Depot\" as activity, gate_record.id, gate_record.date, user.first_name, user.last_name, vessel.name as vessel, voyage.reference as voyage from container_depot_history LEFT JOIN gate_record ON gate_record.container_id = container_depot_history.container_id 
        LEFT JOIN vehicle on vehicle.id = gate_record.vehicle_id 
        LEFT JOIN vehicle_driver on vehicle_driver.id = gate_record.driver_id 
        LEFT JOIN container on gate_record.container_id = container.id 
        LEFT JOIN voyage on container.voyage = voyage.id 
        LEFT JOIN vessel on voyage.vessel_id = vessel.id 
        LEFT JOIN user on user.id = gate_record.user_id
        WHERE gate_record.type = \"GATE IN\" and  container.id like ?");
        $query->bind = array('i', &$container);
        $activity_result = $query->run();
        if($activity = $activity_result->fetch_assoc()) {
            $activity_type = $activity['activity'];
            $vessel = $activity['vessel'];
            $voyage = $activity['voyage'];

            $note = "<b>Vessel</b>: $vessel<br><b>Voyage</b>: $voyage";

            array_push($data, array('id' => $activity['id'], 'date' => $activity['date'], 'act' => $activity_type, 'user' => rtrim($activity['first_name'] . " " . $activity['last_name']),
                "note" => $note));
        }

        $query = new MyQuery();
        $query->query("select \"Depot\" as activity, container_log_history.id,container_log_history.note, vessel.name as vessel,voyage.reference as voyage,container_log_history.status, container.number, container_log_history.date, container_log_history.activity_id, depot_activity.name ,user.first_name, user.last_name from container_log_history 
                left join container on container_log_history.container_id = container.id 
                left join user on user.id = container_log_history.user_id left join depot_activity on depot_activity.id = container_log_history.activity_id left join voyage on voyage.id = container.voyage left join vessel on voyage.vessel_id = vessel.id
                where container.id like ?");
        $query->bind = array('i', &$container);
        $activity_result = $query->run();
        while ($activity = $activity_result->fetch_assoc()) {
            $vessel = $activity['vessel'];
            $voyage = $activity['voyage'];
            $status = $activity['status'];
            $note = $activity['note'];
            $activity_type = $activity['activity']." ".$activity['name'];

            $note = "<b>Vessel</b>: $vessel<br><b>Voyage</b>: $voyage<br><b>Note</b>: $note<br><b>Action</b>: $status";
            array_push($data, array('id' => $activity['id'], 'date' => $activity['date'], 'act' => $activity_type, 'user' => rtrim($activity['first_name'] . " " . $activity['last_name']),
                "note" => $note));
        }

        $query = new MyQuery();
        $query->query("select \"Proforma Depot\" as activity, proforma_container_log_history.id,proforma_container_log_history.note, vessel.name as vessel,voyage.reference as voyage,proforma_container_log_history.status, container.number, proforma_container_log_history.date, proforma_container_log_history.activity_id, depot_activity.name ,user.first_name, user.last_name from proforma_container_log_history 
        left join container on proforma_container_log_history.container_id = container.id 
        left join user on user.id = proforma_container_log_history.user_id left join depot_activity on depot_activity.id = proforma_container_log_history.activity_id left join voyage on voyage.id = container.voyage left join vessel on voyage.vessel_id = vessel.id
        where container.id like ?");
        $query->bind = array('i', &$container);
        $activity_result = $query->run();
        while ($activity = $activity_result->fetch_assoc()) {
            $vessel = $activity['vessel'];
            $voyage = $activity['voyage'];
            $status = $activity['status'];
            $note = $activity['note'];
            $activity_type = $activity['activity']." ".$activity['name'];

            $note = "<b>Vessel</b>: $vessel<br><b>Voyage</b>: $voyage<br><b>Note</b>: $note<br><b>Action</b>: $status";
            array_push($data, array('id' => $activity['id'], 'date' => $activity['date'], 'act' => $activity_type, 'user' => rtrim($activity['first_name'] . " " . $activity['last_name']),
                "note" => $note));
        }



        $query = new MyQuery();
        $query->query("select invoice.id, invoice.number as invoice_number,\"Invoice\" as activity, invoice_history_log.date, invoice_details.container_id, container.number,
                user.first_name, user.last_name, invoice_history_log.status from invoice_history_log
                left join invoice on invoice.id = invoice_history_log.invoice_id
                left join invoice_details on invoice.id = invoice_details.invoice_id
                left join container on container.id =  invoice_details.container_id
                left join user on user.id = invoice_history_log.user_id
                where  container.id like ?
                union
                select supplementary_invoice.id, supplementary_invoice.number as invoice_number,\"Supplementary Invoice\" as activity, supplementary_invoice_history_log.date, supplementary_invoice_details.container_id, container.number,
                user.first_name, user.last_name, supplementary_invoice_history_log.status from supplementary_invoice_history_log
                left join supplementary_invoice on supplementary_invoice.id = supplementary_invoice_history_log.invoice_id
                left join supplementary_invoice_details on supplementary_invoice.id = supplementary_invoice_details.invoice_id
                left join container on container.id =  supplementary_invoice_details.container_id
                left join user on user.id = supplementary_invoice_history_log.user_id
                where  container.id like?");
        $query->bind = array('ii', &$container, &$container);
        $invoice_result = $query->run();
        while ($invoice = $invoice_result->fetch_assoc()) {
            array_push($data, array('id' => $invoice['id'], 'date' => $invoice['date'], 'act' => $invoice['activity'], 'user' => rtrim($invoice['first_name'] . " " . $invoice['last_name']),
                "note" => "<b>Number</b>: ".$invoice['invoice_number']."<br><b>Status</b>: ".$invoice['status']));
        }


        $query = new MyQuery();
        $query->query("select proforma_invoice.id, proforma_invoice.number as invoice_number,\"Proforma Invoice\" as activity, proforma_invoice_history_log.date, proforma_invoice_details.container_id, container.number,
                user.first_name, user.last_name, proforma_invoice_history_log.status from proforma_invoice_history_log
                left join proforma_invoice on proforma_invoice.id = proforma_invoice_history_log.invoice_id
                left join proforma_invoice_details on proforma_invoice.id = proforma_invoice_details.invoice_id
                left join container on container.id =  proforma_invoice_details.container_id
                left join user on user.id = proforma_invoice.user_id
                where  container.id like ?
                union
                select proforma_supplementary_invoice.id, proforma_supplementary_invoice.number as invoice_number,\"Proforma Supplementary Invoice\" as activity, proforma_supplementary_invoice_history_log.date, supplementary_invoice_details.container_id, container.number,
                user.first_name, user.last_name, proforma_supplementary_invoice_history_log.status from proforma_supplementary_invoice_history_log
                left join proforma_supplementary_invoice on proforma_supplementary_invoice.id = proforma_supplementary_invoice_history_log.invoice_id
                left join supplementary_invoice_details on proforma_supplementary_invoice.id = supplementary_invoice_details.invoice_id
                left join container on container.id =  supplementary_invoice_details.container_id
                left join user on user.id = proforma_supplementary_invoice.user_id
                where  container.id like?");
        $query->bind = array('ii', &$container, &$container);
        $invoice_result = $query->run();
        while ($invoice = $invoice_result->fetch_assoc()) {
            array_push($data, array('id' => $invoice['id'], 'date' => $invoice['date'], 'act' => $invoice['activity'], 'user' => rtrim($invoice['first_name'] . " " . $invoice['last_name']),
                "note" => "<b>Number</b>: ".$invoice['invoice_number']."<br><b>Status</b>: ".$invoice['status']));
        }

        $query = new MyQuery();
        $query->query("select letpass.id, letpass.number as letpass_number,\"Let Pass\" as activity, letpass.date, letpass_container.container_id, container.number,
                             user.first_name, user.last_name, letpass.status from letpass
                             inner join invoice on invoice.id = letpass.invoice_id
                             inner join letpass_container on letpass_container.letpass_id = letpass.id
                             inner join container on container.id =  letpass_container.container_id
                             inner join user on user.id = letpass.user_id
                             where  container.id like ?");
        $query->bind = array('i', &$container);
        $activity_result = $query->run();
        while ($activity = $activity_result->fetch_assoc()) {
            array_push($data, array('id' => $activity['id'], 'date' => $activity['date'], 'act' => $activity['activity'], 'user' => rtrim($activity['first_name'] . " " . $activity['last_name']),
                "note" => "<b>Number</b>: ".$activity['letpass_number']));
        }

        $query = new MyQuery();
        $query->query("select payment.id, receipt_number,\"Invoice Payment\" as activity, payment.date, \"Invoice\" as type, invoice_container.container_id, container.number, invoice.number as invoice, user.first_name, user.last_name from payment
							 inner join invoice on payment.invoice_id = invoice.id
                             inner join invoice_container on invoice.id = invoice_container.invoice_id
                             inner join container on container.id =  invoice_container.container_id
                             inner join user on user.id = payment.user_id
                             where  container.id like ?
                             union
                             select supplementary_payment.id, receipt_number,\"Supplementary Invoice Payment\" as activity,supplementary_payment.date, \"Supp Invoice\" as type,supplementary_invoice_container.container_id, container.number, supplementary_invoice.number as invoice, user.first_name, user.last_name from supplementary_payment
							 inner join supplementary_invoice on supplementary_payment.invoice_id = supplementary_invoice.id
                             inner join supplementary_invoice_container on supplementary_invoice.id = supplementary_invoice_container.invoice_id
                             inner join container on container.id =  supplementary_invoice_container.container_id
                             inner join user on user.id = supplementary_payment.user_id
                             where  container.id like ?");
        $query->bind = array('ii', &$container, &$container);
        $activity_result = $query->run();
        while ($activity = $activity_result->fetch_assoc()) {
            array_push($data, array('id' => $activity['id'], 'date' => $activity['date'], 'act' => $activity['activity'], 'user' => rtrim($activity['first_name'] . " " . $activity['last_name']),
                "note" => "<b>Receipt Number</b>: " .$activity['receipt_number'] . "<br>" .$activity['type']." <b>Number</b> : ".$activity['invoice']));
        }

        $query = new MyQuery();
        $query->query("select gate_record.id, container.number, gate_record.date, \"Gate Out\" as activity, user.first_name, user.last_name
							 from gate_record
                             inner join container on gate_record.container_id = container.id 
                             inner join user on user.id = gate_record.user_id
                             where gate_record.type = \"GATE OUT\" and gate_record.status = 1
                             and container.id like ?");
        $query->bind = array('i', &$container);
        $activity_result = $query->run();
        if ($activity = $activity_result->fetch_assoc()) {
            array_push($data, array('id' => $activity['id'], 'date' => $activity['date'], 'act' => $activity['activity'], 'user' => rtrim($activity['first_name'] . " " . $activity['last_name']),
                "note" => ""));
        }

        $query = new MyQuery();
        $query->query("select \"Yard Management\" as activity,yard_log_history.stack,yard_log_history.position,yard_log_history.yard_activity,user.first_name,user.last_name, yard_log_history.date from yard_log_history left join user on user.id = yard_log_history.user_id where yard_log_history.container_id=?");
        $query->bind = array('s', &$container);
        $yard_activity = $query->run();
        while($activity = $yard_activity->fetch_assoc()) {
            array_push($data, array('act' => $activity['activity'],'date' => $activity['date'], 'user' => rtrim($activity['first_name'] . " " . $activity['last_name']),
                "note" => "<b>Action: </b>".$activity['yard_activity']."<br/>"));
        }


        usort($data, array($this,'sort_activities'));

        $filtered = [];

        foreach($data as $row) {
            $found = false;
            $row['id'] = $id++;
            foreach ($row as $key=>$value){
                if(preg_match($search_query, $value)){
                    $found =  true;
                }
            }

            if($found) {
                array_push($filtered, $row);
            }
        }

        $total = count($data);
        $total_filtered = count($filtered);

        $filtered = array_slice($filtered, $start_offset, 5);

        $rows = ['data' => $filtered, "recordsTotal" => $total, "recordsFiltered" => $total_filtered];

        new Respond(241,$rows);
    }

    function sort_activities($a, $b){
        return strnatcmp($a[$this->order_key], $b[$this->order_key]) * ($this->order_dir == 'asc' ? 1 : -1);
    }
}

?>