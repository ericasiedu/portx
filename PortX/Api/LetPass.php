<?php

namespace Api;
session_start();

use DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions,
    Lib\ACL,
    Lib\LetPassGeneration,
    Lib\LetPassPdf,
    Lib\MyQuery,
    Lib\MyTransactionQuery,
    Lib\Respond;

class LetPass{

    private $request,$response;

    public function  __construct($request,$response){
        $this->request = $request;
        $this->response = $response;
    }

    public  function  generate(){
        $containers = json_decode($this->request->param('ctsl'));
        $drivers = json_decode($this->request->param('drvs'));
        $invoice_number = $this->request->param('invn');

        $result = array();

        if(trim($invoice_number) == ""){
            new Respond(156);
        }
        elseif (count($containers) == 0){
            new Respond(157);
        }
        elseif (count($drivers) == 0){
            new Respond(158);
        }
        else
        {
            $let_pass = new LetPassGeneration();
            $let_pass->containers = $containers;
            $let_pass->drivers = $drivers;
            $let_pass->invoice_number = $invoice_number;

            $result = $let_pass->generate();
        }

        new Respond(251, $result);
    }

    public function show_let_pass($letpass_number){
        $pdf = new LetPassPdf($letpass_number);
        $pdf->generate();
    }

    public function get_unpaid_invoices()
    {
        $result = array('inv'=> 0, 'cont'=> 0, 'e'=> 0);
        $invoices = array();
        $invoice = $_POST['invn'];
        $query = new MyQuery();
        $query->query("select id, status from invoice where number like ?");
        $query->bind = array('s', &$invoice);
        $run = $query->run();
        $resu = $run->fetch_assoc();
        $id = $resu['id'];
        $status = $resu['status'];
        if (!is_null($id)) {
            if ($status == 'PAID') {
                $query = new MyQuery();
                $query->query("select  status, number from supplementary_invoice where invoice_id = ?");
                $query->bind = array('i', &$id);
                $run = $query->run();
                while ($resu = $run->fetch_assoc()) {
                    $number = $resu['number'];
                    $status = $resu['status'];

                    if($status == 'UNPAID'){
                        array_push($invoices, $number);
                    }
                }
            } else if ($status == 'DEFERRED') {

            } else {
                array_push($invoices, $invoice);
            }

            if(count($invoices) == 0) {
                $containers = array();
                $query = new MyQuery();
                $query->query("select  container.id, container.number from invoice_container 
                                  inner join container on container.id = invoice_container.container_id  
                                  left  join letpass_container on letpass_container.container_id = container.id
                                  where invoice_id = ? and container.status = 0 and container.gate_status like 'GATED IN' and letpass_container.container_id is null");
                $query->bind = array('i', &$id);
                $run = $query->run();
                while ($resu = $run->fetch_assoc()) {
                    $number = $resu['number'];
                    $container_id = $resu['id'];

                    array_push($containers, ['num' => $number, 'id' => $container_id]);
                }

                $result['cont'] = $containers;
            }

            $result['inv'] = $invoices;
            new Respond(252, $result);
        }
        else {
            new Respond(152);
        }
    }

    public function check_container(){
        $containers = json_decode($this->request->param('cnsl'));
        $bind_types = "";
        $bind_mask = "";

        foreach($containers as $id) {
            $bind_types = $bind_types.'i';
            $bind_mask = $bind_mask . '?,';
        }

        $bind_mask = rtrim($bind_mask, ',');

        $query = new MyQuery();
        $query->query("select number, gate_status, container.status as status, letpass_container.status  as lp_status from container 
                              left join letpass_container on container.id = letpass_container.container_id
                              where id  in (" . $bind_mask . ")  and (gate_status = 'GATED OUT' or container.status = 1 or letpass_container.status is not null)");

        $bind_data =array($bind_types);

        foreach ($containers as $id) {
            array_push($bind_data, $id);
        }

        $flagged = array();
        $gated_out = array();
        $letpassed = array();

        $query->bind = $bind_data;
        $run = $query->run();
        while($resu = $run->fetch_assoc()){
            $number = $resu['number'];
            $status = $resu['status'];
            $gate_status = $resu['gate_status'];$letpass_status = $resu['lp_status'];

            if ($status == 1) {
                array_push($flagged, $number);
            } elseif ($gate_status == 'GATED OUT') {
                array_push($gated_out, $number);
            }
            if(!is_null($letpass_status)){
                array_push($letpassed, $number);
            }
        }

        if(count($flagged) > 0){
            $result = ['flct'=> $flagged];
            new Respond(153, $result);
        }
        if(count($gated_out) > 0){
            $result = ['gtct'=>$gated_out];
            new Respond(154, $result);
        }
        if(count($letpassed) > 0){
            $result = ['lpct'=>$letpassed];
            new Respond(155, $result);
        }

        new Respond(253);
    }

    public function table(){
        $db = new Bootstrap();
        $db = $db->database();


        Editor::inst($db, 'letpass')
            ->fields(
                Field::inst('letpass.number as lnum')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('letpass.invoice_id as invd')
                    ->getFormatter(function ($val){
                        $query =new MyQuery();
                        $query->query("select number from invoice where id = ?");
                        $query->bind = array('i',&$val);
                        $query->run();
                        return $query->fetch_num()[0] ?? '';
                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('letpass.date as date')
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('letpass.status as stat')
                    ->getFormatter(function ($val){

                        if ($val == 0){
                            return "GATED IN";
                        }
                        else{
                            return "GATED OUT";
                        }

                    })
                    ->validator(Validate::notEmpty(ValidateOptions::inst()
                        ->message('A reference is required')
                    )),
                Field::inst('letpass.id as id')

            )
            ->on('preCreate', function ($editor,$values,$system_object='let-pass-records'){
                ACl::verifyCreate($system_object);
            })
            ->on('preGet', function ($editor,$id,$system_object='let-pass-records'){
                ACl::verifyRead($system_object);
            })
            ->on('preEdit', function ($editor,$id,$values,$system_object='let-pass-records'){
                ACl::verifyUpdate($system_object);
            })
            ->on('preRemove', function ($editor,$id,$values,$system_object='let-pass-records'){
                ACl::verifyDelete($system_object);

                $let_padd_id = $values['letpass']['id'];
                $query = new MyTransactionQuery();
                $query->query("delete from letpass_driver where letpass_id = ?");
                $query->bind = array('s',&$let_padd_id);
                $query->run();
                $query->query("delete from letpass_container where letpass_id = ?");
                $query->bind = array('s',&$let_padd_id);
                $query->run();
                $query->commit();
            })
            ->leftJoin('invoice','letpass.invoice_id','=','invoice.id')
            ->process($_POST)
            ->json();
    }

}

?>