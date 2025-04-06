<?php

namespace Lib;

class LetPassGeneration{

    public  $invoice_number;
    public  $containers;
    public  $drivers;


    public function generate()
    {
        $inv_num = $this->invoice_number;
        $result = array();
        $query = new MyQuery();
        $query->query("Select id, status from invoice where number LIKE ?");
        $query->bind = array('s', &$inv_num);
        $run = $query->run();
        $invoice_id = $run->fetch_assoc()['id'];
        $invoice_status = $run->fetch_assoc()['status'];

        $invoices = array();

        if (!is_null($invoice_id)) {
            if ($invoice_status == 'PAID') {
                $query = new MyQuery();
                $query->query("select  status, number from supplementary_invoice where invoice_id = ?");
                $query->bind = array('i', &$invoice_id);
                $run = $query->run();
                while ($resu = $run->fetch_assoc()) {
                    $number = $resu['number'];
                    $status = $resu['status'];

                    if ($status == 'UNPAID') {
                        array_push($invoices, $number);
                    }
                }
            }
        } else {
            array_push($invoices, $this->invoice_number);
        }

        $result['gtct'] = '';
        $result['flct'] = '';
        $result['lpct'] = '';

        if (count($invoices) == 0) {
            $bind_types = "";
            $bind_mask = "";

            foreach ($this->containers as $id) {
                $bind_types = $bind_types . 'i';
                $bind_mask = $bind_mask . '?,';
            }

            $bind_mask = rtrim($bind_mask, ',');

            $query = new MyQuery();
            $query->query("select number, gate_status, container.status as status, letpass_container.status  as lp_status from container 
                              left join letpass_container on container.id = letpass_container.container_id
                              where id  in (" . $bind_mask . ")  and (gate_status = 'GATED OUT' or container.status = 1 or letpass_container.status is not null)");

            $bind_data = array($bind_types);

            $containers = $this->containers;

            foreach ($containers as $id) {
                $bind_data[] = &$id;
            }

            $flagged = array();
            $gated_out = array();
            $letpassed = array();

            $query->bind = $bind_data;
            $run = $query->run();

            while ($resu = $run->fetch_assoc()) {
                $number = $resu['number'];
                $status = $resu['status'];
                $gate_status = $resu['gate_status'];
                $letpass_status = $resu['lp_status'];

                if ($status == 1) {
                    array_push($flagged, $number);
                } elseif ($gate_status == 'GATED OUT') {
                    array_push($gated_out, $number);
                }
                if (!is_null($letpass_status)) {
                    array_push($letpassed, $number);
                }
            }

            if (count($flagged) > 0) {
                $result = ['flct' => $flagged];
                new Respond(153, $result);
            }
            if (count($gated_out) > 0) {
                $result = ['gtct' => $gated_out];
                new Respond(154, $result);
            }
            if (count($letpassed) > 0) {
                $result = ['lpct' => $letpassed];
                new Respond(155, $result);
            }

            $user_id = isset($_SESSION['id']) ? $_SESSION['id'] : 1;

            $tquery = new MyTransactionQuery();
            $tquery->query("insert into letpass(invoice_id, status, user_id) values (?, 0, ?)");
            $tquery->bind = array('ii', &$invoice_id, &$user_id);
            $run = $tquery->run();

            $tquery->commit();

            $id = $run->get_last_id();

            $number = str_pad($id, 4, '0', STR_PAD_LEFT);

            $tquery = new MyTransactionQuery();

            $tquery->query("update  letpass set number = ? where id = ?");
            $tquery->bind = array('si', &$number, &$id);
            $tquery->run();

            foreach ($containers as $container) {
                $tquery->query("Insert into letpass_container(letpass_id, container_id, status) values (?, ?, 0)");
                $tquery->bind = array('ii', &$id, &$container);
                $tquery->run();
            }

            $drivers = $this->drivers;

            foreach ($drivers as $driver) {
                $driver->license = trim($driver->license);
                $driver->name = trim($driver->name);
                $driver->license = mb_convert_case($driver->license, MB_CASE_UPPER);
                $driver->name = ucwords($driver->name);
                $tquery->query("insert into letpass_driver(letpass_id, license, name) values (?, ?, ?)");
                $tquery->bind = array('iss', &$id, &$driver->license, &$driver->name);
                $tquery->run();
                
                // $tquery->query("insert into gate_truck_record(vehicle_number,vehicle_driver,letpass_id,letpass_no)values(?,?,?,?)");
                // $tquery->bind = array('ssis',&$driver->license,&$driver->name,&$id,&$number);
                // $tquery->run();
            }

            $tquery->commit();

            $result['ltps'] = $number;


        } else {
            $result['inv'] = $invoices;
            new Respond(159, $result);
        }

        return $result;
    }
}

?>