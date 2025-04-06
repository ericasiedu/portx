<?php
namespace Api;

use DataTables\Editor\Field,
    Lib\MyQuery,
    Lib\MyTransactionQuery,
    Lib\Respond,
    DataTables\Bootstrap,
    DataTables\Editor;

class Dashboard{
    private $request,$response;

    public function  __construct($request,$response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function show_overview(){
        $data = array();

        $query = new MyTransactionQuery();
        $query->query("SELECT COUNT(depot_id) AS laden FROM gate_record WHERE depot_id = '1' AND date LIKE CONCAT(CURDATE(),'%')");
        $run = $query->run();
        $result = $run->fetch_assoc();
        $depot_laden = $result['laden'];

        $query->query("SELECT COUNT(depot_id) AS depot_count FROM gate_record WHERE depot_id = '2' AND date LIKE CONCAT(CURDATE(),'%')");
        $run = $query->run();
        $result = $run->fetch_assoc();
        $depot_empty = $result['depot_count'];

        $query->query("SELECT COUNT(depot_id) AS export_count FROM gate_record WHERE depot_id = '3' AND date LIKE CONCAT(CURDATE(),'%')");
        $run = $query->run();
        $result = $run->fetch_assoc();
        $depot_export = $result['export_count'];

        $query->query("select sum(payment.paid) 
              as total_amount from payment inner join invoice on invoice.id = payment.invoice_id 
              where invoice.trade_type = '1' and payment.date LIKE CONCAT(CURDATE(),'%')");
        $run = $query->run();
        $laden_invoice_amt = $run->fetch_num()[0];

        $query->query("select sum(supplementary_payment.paid) as total_amount 
              from supplementary_payment inner join supplementary_invoice 
              on supplementary_invoice.id = supplementary_payment.invoice_id 
              inner join invoice on invoice.id = supplementary_invoice.invoice_id where invoice.trade_type = '1' and supplementary_payment.date LIKE CONCAT(CURDATE(),'%')");
        $run = $query->run();
        $laden_supinvoice_amt = $run->fetch_num()[0];

        $laden_total = number_format($laden_invoice_amt + $laden_supinvoice_amt, 2);
        $data['ladamt'] = $laden_total;

        $query->query("select sum(payment.paid) 
              as total_amount from payment inner join invoice on invoice.id = payment.invoice_id 
              where invoice.trade_type = '4' and payment.date LIKE CONCAT(CURDATE(),'%')");
        $run = $query->run();
        $export_invoice_amt =  $run->fetch_num()[0];

        $query->query("select sum(supplementary_payment.paid) as total_amount 
              from supplementary_payment inner join supplementary_invoice 
              on supplementary_invoice.id = supplementary_payment.invoice_id 
              inner join invoice on invoice.id = supplementary_invoice.invoice_id where invoice.trade_type = '4'  and supplementary_payment.date LIKE CONCAT(CURDATE(),'%')");
        $run = $query->run();
        $export_supinvoice_amt = $run->fetch_num()[0];

        $export_total = number_format($export_invoice_amt + $export_supinvoice_amt,2);

        $data['expamt'] = $export_total;

        $data['lad'] = $depot_laden;
        $data['dep'] = $depot_empty;
        $data['exp'] = $depot_export;

        new Respond(200, $data);
    }

    public function table()
    {
        $db = new Bootstrap();
        $db = $db->database();

        Editor::inst($db, 'depot_activity')
            ->fields(
                Field::inst('id'),
                Field::inst('name'),
                Field::inst('id as ft20')
                    ->getFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select count(container_log.activity_id) from container_log 
                                  inner join container on container.id = container_log.container_id 
                                  inner join container_isotype_code  on container_isotype_code.id = container.iso_type_code  
                                  where container_log.activity_id = ? and container_isotype_code.length = '20' and container_log.date LIKE CONCAT(CURDATE(),'%')");
                        $query->bind = array('i', &$val);
                        $result =  $query->run();
                        return $result->fetch_num()[0] ?? "0";
                    }),
                Field::inst('id as ft40')
                    ->getFormatter(function ($val) {
                        $query = new MyQuery();
                        $query->query("select count(container_log.activity_id) from container_log 
                                  inner join container on container.id = container_log.container_id 
                                  inner join container_isotype_code  on container_isotype_code.id = container.iso_type_code  
                                  where container_log.activity_id = ? and container_isotype_code.length = '40' and container_log.date LIKE CONCAT(CURDATE(),'%')");
                        $query->bind = array('i', &$val);
                        $result =  $query->run();
                        return $result->fetch_num()[0] ?? "0";
                    })
            )
            ->where('id', 5, '>')
            ->where('id', 10, '<')
            ->process($_POST)
            ->json();
    }

    function display_iso(){
        echo $_SESSION['is_auth'];
    }
}

?>