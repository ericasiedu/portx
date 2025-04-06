<?php

namespace Api;
session_start();

use Lib\ACL,
    Lib\MyQuery,
    DataTables\Bootstrap,
    DataTables\Editor,
    DataTables\Editor\Field,
    Lib\ReportGenerator,
    Lib\MyTransactionQuery,
    Lib\Respond;

$system_object='summary-remittances';

class SummaryRemittance {
    private $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    function table() {

        $db = new Bootstrap();
        $db = $db->database();

        $date = date('Y-m-d');
        $start_date = $this->request->param('stdt');
        $end_date = $this->request->param('endt');

        $order = $this->request->param('order');
        $columns = $this->request->param('columns');
        $order_key = $order[0]['column'];
        $order_dir = $order[0]['dir'];
        $order_col = $columns[$order_key]["data"];
        $start_offset = $this->request->param('start');
        $draw = $this->request->param('draw');
        $length = $this->request->param('length');
        $search_query = $this->request->param('search')['value'];
        $search_query = "%$search_query%";
        $start_date = $start_date != '' ? $start_date : $date;
        $end_date = $end_date != '' ? $end_date : $date;

        // var_dump($start_date); exit;

        $id = 1;

        $query = new MyQuery();
        // $query->query('drop view if exists invoice_report');
        // $query->run();


        $query = new MyQuery();
        $query_command = "SELECT * FROM (SELECT payment.date, invoice.number, container_isotype_code.length / 20 AS teu,
        (SELECT SUM(invoice_details.cost) FROM invoice_details INNER JOIN depot_activity ON invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Unstuff%' AND depot_activity.name NOT LIKE 'Unstuffing/Re-stuffing%' AND invoice_details.invoice_id = payment.invoice_id) AS stripping,       
        (SELECT SUM(cost) FROM invoice_details WHERE product_key NOT IN (SELECT id FROM depot_activity WHERE name NOT LIKE 'Unstuffing/Re-stuffing%' 
        AND name LIKE 'Unstuff%' OR name LIKE 'Handling%' OR name LIKE 'Transfer%') AND product_key <> 'S' AND invoice_id = payment.invoice_id) AS dd_cost,       
        (SELECT SUM(invoice_details.cost) FROM invoice_details INNER JOIN depot_activity ON invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Handling%' AND invoice_details.invoice_id = payment.invoice_id) AS handling_cost,        
        (SELECT SUM(cost) FROM invoice_details WHERE product_key = 'S' AND invoice_id = payment.invoice_id) AS storage_cost,         
        (SELECT SUM(invoice_details.cost) FROM invoice_details INNER JOIN depot_activity ON invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Transfer%' AND invoice_details.invoice_id = payment.invoice_id) AS transport_cost,
        invoice.waiver_amount, invoice.cost AS invoice_cost,        
        (SELECT cost FROM invoice_details_tax WHERE description LIKE 'VAT%' AND invoice_id = payment.invoice_id) AS vat,        
        (SELECT cost FROM invoice_details_tax WHERE description LIKE 'COVID%' AND invoice_id = payment.invoice_id) AS covid19,        
        (SELECT cost FROM invoice_details_tax WHERE description LIKE 'WHT%' AND invoice_id = payment.invoice_id) AS wht,        
        (SELECT cost FROM invoice_details_tax WHERE description LIKE 'GET%' AND invoice_id = payment.invoice_id) AS getfund,        
        payment_mode.name, payment.paid, payment.outstanding, shipping_line.name AS shipline, vessel.name AS vessel, 
        customer.name AS consignee FROM payment LEFT JOIN invoice 
        ON payment.invoice_id = invoice.id LEFT JOIN invoice_container ON invoice.id = invoice_container.invoice_id 
        LEFT JOIN container ON invoice_container.container_id = container.id LEFT JOIN container_isotype_code 
        ON container.iso_type_code = container_isotype_code.id LEFT JOIN payment_mode ON payment.mode = payment_mode.id 
        LEFT JOIN voyage ON container.voyage = voyage.id LEFT JOIN shipping_line ON voyage.shipping_line_id = shipping_line.id 
        LEFT JOIN vessel ON voyage.vessel_id = vessel.id LEFT JOIN customer ON invoice.customer_id = customer.id 
        WHERE CAST(payment.date AS date) BETWEEN ? AND ?
        UNION
        SELECT supplementary_payment.date, supplementary_invoice.number, container_isotype_code.length / 20 AS teu,
        (SELECT SUM(supplementary_invoice_details.cost) FROM supplementary_invoice_details INNER JOIN depot_activity ON supplementary_invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Unstuff%' AND depot_activity.name NOT LIKE 'Unstuffing/Re-stuffing%' AND supplementary_invoice_details.invoice_id = supplementary_payment.invoice_id) AS stripping,       
        (SELECT SUM(cost) FROM supplementary_invoice_details WHERE product_key NOT IN (SELECT id FROM depot_activity WHERE name NOT LIKE 'Unstuffing/Re-stuffing%' 
        AND name LIKE 'Unstuff%' OR name LIKE 'Handling%' OR name LIKE 'Transfer%') AND product_key <> 'S' AND invoice_id = supplementary_payment.invoice_id) AS dd_cost,       
        (SELECT SUM(supplementary_invoice_details.cost) FROM supplementary_invoice_details INNER JOIN depot_activity ON supplementary_invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Handling%' AND supplementary_invoice_details.invoice_id = supplementary_payment.invoice_id) AS handling_cost,       
        (SELECT SUM(cost) FROM supplementary_invoice_details WHERE product_key = 'S' AND invoice_id = supplementary_payment.invoice_id) AS storage_cost,         
        (SELECT SUM(supplementary_invoice_details.cost) FROM supplementary_invoice_details INNER JOIN depot_activity ON supplementary_invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Transfer%' AND supplementary_invoice_details.invoice_id = supplementary_payment.invoice_id) AS transport_cost,       
        supplementary_invoice.waiver_amount, supplementary_invoice.cost AS invoice_cost,        
        (SELECT cost FROM supplementary_invoice_details_tax WHERE description LIKE 'VAT%' AND invoice_id = supplementary_payment.invoice_id) AS vat,        
        (SELECT cost FROM supplementary_invoice_details_tax WHERE description LIKE 'COVID%' AND invoice_id = supplementary_payment.invoice_id) AS covid19,        
        (SELECT cost FROM supplementary_invoice_details_tax WHERE description LIKE 'WHT%' AND invoice_id = supplementary_payment.invoice_id) AS wht,        
        (SELECT cost FROM supplementary_invoice_details_tax WHERE description LIKE 'GET%' AND invoice_id = supplementary_payment.invoice_id) AS getfund,        
        payment_mode.name, supplementary_payment.paid, supplementary_payment.outstanding, shipping_line.name AS shipline, vessel.name AS vessel, 
        customer.name AS consignee FROM supplementary_payment LEFT JOIN supplementary_invoice 
        ON supplementary_payment.invoice_id = supplementary_invoice.id LEFT JOIN supplementary_invoice_container ON supplementary_invoice.id = supplementary_invoice_container.invoice_id 
        LEFT JOIN container ON supplementary_invoice_container.container_id = container.id LEFT JOIN container_isotype_code 
        ON container.iso_type_code = container_isotype_code.id LEFT JOIN payment_mode ON supplementary_payment.mode = payment_mode.id 
        LEFT JOIN voyage ON container.voyage = voyage.id LEFT JOIN shipping_line ON voyage.shipping_line_id = shipping_line.id 
        LEFT JOIN vessel ON voyage.vessel_id = vessel.id LEFT JOIN invoice ON invoice.id = supplementary_invoice.invoice_id LEFT JOIN customer ON invoice.customer_id = customer.id 
        WHERE CAST(supplementary_payment.date AS date) BETWEEN ? AND ?) AS summary_remittances";

        $query->query($query_command);
        $query->bind = array("ssss",&$start_date, &$end_date, &$start_date, &$end_date);
        $query->run();

        $added = 0;
        $index = 0;

        $filtered = array();

        while($row = $query->fetch_assoc()) {
            if($added >= $length){
                break;
            }

            if($index >= $start_offset) {
                array_push($filtered, $row);
                $added++;
            }

            $index++;
        }

        $total = $query->num_rows();

        $rows = ['data' => $filtered, "recordsTotal" => $total, "recordsFiltered" => $total, "draw" => $draw];
        // echo $rows;

        new Respond(241,$rows);

    }

    function report()
    {
        $date = date('Y-m-d');
        $start_date = $this->request->param('stdt');
        $end_date = $this->request->param('endt');

        $type = $this->request->param('type');
        $columns = json_decode($this->request->param('src'));
        $headers = json_decode($this->request->param('head'));
        // echo($headers);

        $id = 1;


        $query = new MyQuery();
        $q ="SELECT * FROM (SELECT payment.date, invoice.number, container_isotype_code.length / 20 AS teu,
        (SELECT SUM(invoice_details.cost) FROM invoice_details INNER JOIN depot_activity ON invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Unstuff%' AND depot_activity.name NOT LIKE 'Unstuffing/Re-stuffing%' AND invoice_details.invoice_id = payment.invoice_id) AS stripping,       
        (SELECT SUM(cost) FROM invoice_details WHERE product_key NOT IN (SELECT id FROM depot_activity WHERE name NOT LIKE 'Unstuffing/Re-stuffing%' 
        AND name LIKE 'Unstuff%' OR name LIKE 'Handling%' OR name LIKE 'Transfer%') AND product_key <> 'S' AND invoice_id = payment.invoice_id) AS dd_cost,       
        (SELECT SUM(invoice_details.cost) FROM invoice_details INNER JOIN depot_activity ON invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Handling%' AND invoice_details.invoice_id = payment.invoice_id) AS handling_cost,        
        (SELECT SUM(cost) FROM invoice_details WHERE product_key = 'S' AND invoice_id = payment.invoice_id) AS storage_cost,         
        (SELECT SUM(invoice_details.cost) FROM invoice_details INNER JOIN depot_activity ON invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Transfer%' AND invoice_details.invoice_id = payment.invoice_id) AS transport_cost,
        invoice.waiver_amount, invoice.cost AS invoice_cost,        
        (SELECT cost FROM invoice_details_tax WHERE description LIKE 'VAT%' AND invoice_id = payment.invoice_id) AS vat,        
        (SELECT cost FROM invoice_details_tax WHERE description LIKE 'COVID%' AND invoice_id = payment.invoice_id) AS covid19,        
        (SELECT cost FROM invoice_details_tax WHERE description LIKE 'WHT%' AND invoice_id = payment.invoice_id) AS wht,        
        (SELECT cost FROM invoice_details_tax WHERE description LIKE 'GET%' AND invoice_id = payment.invoice_id) AS getfund,        
        payment_mode.name, payment.paid, payment.outstanding, shipping_line.name AS shipline, vessel.name AS vessel, 
        customer.name AS consignee FROM payment LEFT JOIN invoice 
        ON payment.invoice_id = invoice.id LEFT JOIN invoice_container ON invoice.id = invoice_container.invoice_id 
        LEFT JOIN container ON invoice_container.container_id = container.id LEFT JOIN container_isotype_code 
        ON container.iso_type_code = container_isotype_code.id LEFT JOIN payment_mode ON payment.mode = payment_mode.id 
        LEFT JOIN voyage ON container.voyage = voyage.id LEFT JOIN shipping_line ON voyage.shipping_line_id = shipping_line.id 
        LEFT JOIN vessel ON voyage.vessel_id = vessel.id LEFT JOIN customer ON invoice.customer_id = customer.id 
        WHERE CAST(payment.date AS date) BETWEEN ? AND ?
        UNION
        SELECT supplementary_payment.date, supplementary_invoice.number, container_isotype_code.length / 20 AS teu,
        (SELECT SUM(supplementary_invoice_details.cost) FROM supplementary_invoice_details INNER JOIN depot_activity ON supplementary_invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Unstuff%' AND depot_activity.name NOT LIKE 'Unstuffing/Re-stuffing%' AND supplementary_invoice_details.invoice_id = supplementary_payment.invoice_id) AS stripping,       
        (SELECT SUM(cost) FROM supplementary_invoice_details WHERE product_key NOT IN (SELECT id FROM depot_activity WHERE name NOT LIKE 'Unstuffing/Re-stuffing%' 
        AND name LIKE 'Unstuff%' OR name LIKE 'Handling%' OR name LIKE 'Transfer%') AND product_key <> 'S' AND invoice_id = supplementary_payment.invoice_id) AS dd_cost,       
        (SELECT SUM(supplementary_invoice_details.cost) FROM supplementary_invoice_details INNER JOIN depot_activity ON supplementary_invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Handling%' AND supplementary_invoice_details.invoice_id = supplementary_payment.invoice_id) AS handling_cost,       
        (SELECT SUM(cost) FROM supplementary_invoice_details WHERE product_key = 'S' AND invoice_id = supplementary_payment.invoice_id) AS storage_cost,         
        (SELECT SUM(supplementary_invoice_details.cost) FROM supplementary_invoice_details INNER JOIN depot_activity ON supplementary_invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Transfer%' AND supplementary_invoice_details.invoice_id = supplementary_payment.invoice_id) AS transport_cost,       
        supplementary_invoice.waiver_amount, supplementary_invoice.cost AS invoice_cost,        
        (SELECT cost FROM supplementary_invoice_details_tax WHERE description LIKE 'VAT%' AND invoice_id = supplementary_payment.invoice_id) AS vat,        
        (SELECT cost FROM supplementary_invoice_details_tax WHERE description LIKE 'COVID%' AND invoice_id = supplementary_payment.invoice_id) AS covid19,        
        (SELECT cost FROM supplementary_invoice_details_tax WHERE description LIKE 'WHT%' AND invoice_id = supplementary_payment.invoice_id) AS wht,        
        (SELECT cost FROM supplementary_invoice_details_tax WHERE description LIKE 'GET%' AND invoice_id = supplementary_payment.invoice_id) AS getfund,        
        payment_mode.name, supplementary_payment.paid, supplementary_payment.outstanding, shipping_line.name AS shipline, vessel.name AS vessel, 
        customer.name AS consignee FROM supplementary_payment LEFT JOIN supplementary_invoice 
        ON supplementary_payment.invoice_id = supplementary_invoice.id LEFT JOIN supplementary_invoice_container ON supplementary_invoice.id = supplementary_invoice_container.invoice_id 
        LEFT JOIN container ON supplementary_invoice_container.container_id = container.id LEFT JOIN container_isotype_code 
        ON container.iso_type_code = container_isotype_code.id LEFT JOIN payment_mode ON supplementary_payment.mode = payment_mode.id 
        LEFT JOIN voyage ON container.voyage = voyage.id LEFT JOIN shipping_line ON voyage.shipping_line_id = shipping_line.id 
        LEFT JOIN vessel ON voyage.vessel_id = vessel.id LEFT JOIN invoice ON invoice.id = supplementary_invoice.invoice_id LEFT JOIN customer ON invoice.customer_id = customer.id 
        WHERE CAST(supplementary_payment.date AS date) BETWEEN ? AND ?) AS summary_remittances";
        $query->query($q);
        $query->bind = array("ssss",&$start_date, &$end_date, &$start_date, &$end_date);
        $run = $query->run();

        $data = array();

        while ($row = $run->fetch_assoc()) {
            $row_data = array();

            foreach ($columns as $col){
                array_push($row_data,$row[$col]);
            }
            array_push($data, $row_data);
        }

        $totals = $this->get_totals_array();
        $visible_totals = array();

        foreach ($columns as $column) {
            array_push($visible_totals, is_null($totals[$column . '_total']) ? "" : $totals[$column . '_total']);
        }
        // var_dump($data);

        $length = count($headers);
        $width = 190 / $length;
        $widths = array_fill(0, $length, $width);

        $report_generator = new ReportGenerator($start_date, $end_date, "Summary Remittances", $headers, $data, $widths, $visible_totals);

        if($type == "pdf") {
            $report_generator->printPdf();
        }
        else {
            $report_generator->printExcel();
        }
    }

    function total() {
        $totals = $this->get_totals_array();

        new Respond(232, array(
            'tstp'=>$totals['stripping_total'],
            'ttdd'=>$totals['dd_cost_total'],
            'thd'=>$totals['handling_cost_total'],
            'tstrg'=>$totals['storage_cost_total'],
            'ttsp'=>$totals['transport_cost_total'],
            'ttvt'=>$totals['vat_total'],
            'tcvd'=>$totals['covid19_total'],
            'twht'=>$totals['wht_total'],
            'tgfd'=>$totals['getfund_total'],
            'ttpd'=>$totals['paid_total'],
            'ttwv'=>$totals['waiver_amount_total'],
            'tinv'=>$totals['invoice_cost_total'],
            'outb'=>$totals['outstanding_total']
        ));
    }

    private function get_totals_array() {
        $start_date = $this->request->param('stdt');
        $end_date = $this->request->param('endt');


        $query = new MyQuery();
        $query->query("SELECT SUM(stripping) AS totalStripping, SUM(dd_cost) AS totalDd, SUM(handling_cost) AS totalHandling, SUM(storage_cost) 
        AS totalStorage, SUM(transport_cost) AS totalTransport, SUM(waiver_amount) AS totalWaiver, SUM(invoice_cost) AS totalInvoice, 
        SUM(vat) AS totalVat, SUM(covid19) AS totalCovid, SUM(wht) AS totalWht, SUM(getfund) AS totalGetfund, SUM(paid) AS totalPaid, 
        SUM(outstanding) AS totalOutstanding FROM (SELECT payment.date, invoice.number, container_isotype_code.length / 20 AS teu,
        (SELECT SUM(invoice_details.cost) FROM invoice_details INNER JOIN depot_activity ON invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Unstuff%' AND depot_activity.name NOT LIKE 'Unstuffing/Re-stuffing%' AND invoice_details.invoice_id = payment.invoice_id) AS stripping,       
        (SELECT SUM(cost) FROM invoice_details WHERE product_key NOT IN (SELECT id FROM depot_activity WHERE name NOT LIKE 'Unstuffing/Re-stuffing%' 
        AND name LIKE 'Unstuff%' OR name LIKE 'Handling%' OR name LIKE 'Transfer%') AND product_key <> 'S' AND invoice_id = payment.invoice_id) AS dd_cost,       
        (SELECT SUM(invoice_details.cost) FROM invoice_details INNER JOIN depot_activity ON invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Handling%' AND invoice_details.invoice_id = payment.invoice_id) AS handling_cost,        
        (SELECT SUM(cost) FROM invoice_details WHERE product_key = 'S' AND invoice_id = payment.invoice_id) AS storage_cost,         
        (SELECT SUM(invoice_details.cost) FROM invoice_details INNER JOIN depot_activity ON invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Transfer%' AND invoice_details.invoice_id = payment.invoice_id) AS transport_cost,
        invoice.waiver_amount, invoice.cost AS invoice_cost,        
        (SELECT cost FROM invoice_details_tax WHERE description LIKE 'VAT%' AND invoice_id = payment.invoice_id) AS vat,        
        (SELECT cost FROM invoice_details_tax WHERE description LIKE 'COVID%' AND invoice_id = payment.invoice_id) AS covid19,        
        (SELECT cost FROM invoice_details_tax WHERE description LIKE 'WHT%' AND invoice_id = payment.invoice_id) AS wht,        
        (SELECT cost FROM invoice_details_tax WHERE description LIKE 'GET%' AND invoice_id = payment.invoice_id) AS getfund,        
        payment_mode.name, payment.paid, payment.outstanding, shipping_line.name AS shipline, vessel.name AS vessel, 
        customer.name AS consignee FROM payment LEFT JOIN invoice 
        ON payment.invoice_id = invoice.id LEFT JOIN invoice_container ON invoice.id = invoice_container.invoice_id 
        LEFT JOIN container ON invoice_container.container_id = container.id LEFT JOIN container_isotype_code 
        ON container.iso_type_code = container_isotype_code.id LEFT JOIN payment_mode ON payment.mode = payment_mode.id 
        LEFT JOIN voyage ON container.voyage = voyage.id LEFT JOIN shipping_line ON voyage.shipping_line_id = shipping_line.id 
        LEFT JOIN vessel ON voyage.vessel_id = vessel.id LEFT JOIN customer ON invoice.customer_id = customer.id 
        WHERE CAST(payment.date AS date) BETWEEN ? AND ?
        UNION
        SELECT supplementary_payment.date, supplementary_invoice.number, container_isotype_code.length / 20 AS teu,
        (SELECT SUM(supplementary_invoice_details.cost) FROM supplementary_invoice_details INNER JOIN depot_activity ON supplementary_invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Unstuff%' AND depot_activity.name NOT LIKE 'Unstuffing/Re-stuffing%' AND supplementary_invoice_details.invoice_id = supplementary_payment.invoice_id) AS stripping,       
        (SELECT SUM(cost) FROM supplementary_invoice_details WHERE product_key NOT IN (SELECT id FROM depot_activity WHERE name NOT LIKE 'Unstuffing/Re-stuffing%' 
        AND name LIKE 'Unstuff%' OR name LIKE 'Handling%' OR name LIKE 'Transfer%') AND product_key <> 'S' AND invoice_id = supplementary_payment.invoice_id) AS dd_cost,       
        (SELECT SUM(supplementary_invoice_details.cost) FROM supplementary_invoice_details INNER JOIN depot_activity ON supplementary_invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Handling%' AND supplementary_invoice_details.invoice_id = supplementary_payment.invoice_id) AS handling_cost,       
        (SELECT SUM(cost) FROM supplementary_invoice_details WHERE product_key = 'S' AND invoice_id = supplementary_payment.invoice_id) AS storage_cost,         
        (SELECT SUM(supplementary_invoice_details.cost) FROM supplementary_invoice_details INNER JOIN depot_activity ON supplementary_invoice_details.product_key = depot_activity.id 
        WHERE depot_activity.name LIKE 'Transfer%' AND supplementary_invoice_details.invoice_id = supplementary_payment.invoice_id) AS transport_cost,       
        supplementary_invoice.waiver_amount, supplementary_invoice.cost AS invoice_cost,        
        (SELECT cost FROM supplementary_invoice_details_tax WHERE description LIKE 'VAT%' AND invoice_id = supplementary_payment.invoice_id) AS vat,        
        (SELECT cost FROM supplementary_invoice_details_tax WHERE description LIKE 'COVID%' AND invoice_id = supplementary_payment.invoice_id) AS covid19,        
        (SELECT cost FROM supplementary_invoice_details_tax WHERE description LIKE 'WHT%' AND invoice_id = supplementary_payment.invoice_id) AS wht,        
        (SELECT cost FROM supplementary_invoice_details_tax WHERE description LIKE 'GET%' AND invoice_id = supplementary_payment.invoice_id) AS getfund,        
        payment_mode.name, supplementary_payment.paid, supplementary_payment.outstanding, shipping_line.name AS shipline, vessel.name AS vessel, 
        customer.name AS consignee FROM supplementary_payment LEFT JOIN supplementary_invoice 
        ON supplementary_payment.invoice_id = supplementary_invoice.id LEFT JOIN supplementary_invoice_container ON supplementary_invoice.id = supplementary_invoice_container.invoice_id 
        LEFT JOIN container ON supplementary_invoice_container.container_id = container.id LEFT JOIN container_isotype_code 
        ON container.iso_type_code = container_isotype_code.id LEFT JOIN payment_mode ON supplementary_payment.mode = payment_mode.id 
        LEFT JOIN voyage ON container.voyage = voyage.id LEFT JOIN shipping_line ON voyage.shipping_line_id = shipping_line.id 
        LEFT JOIN vessel ON voyage.vessel_id = vessel.id LEFT JOIN invoice ON invoice.id = supplementary_invoice.invoice_id LEFT JOIN customer ON invoice.customer_id = customer.id 
        WHERE CAST(supplementary_payment.date AS date) BETWEEN ? AND ?) AS summary_remittances");
    
        $query->bind = array('ssss', &$start_date, &$end_date, &$start_date, &$end_date);
        $query->run();
        $results = $query->fetch_assoc();


        $total_stripping = $results['totalStripping'] != null ? $results['totalStripping'] : '0.00';
        $totalDd = $results['totalDd'] != null ? $results['totalDd'] : '0.00';
        $totalHandling = $results['totalHandling'] != null ? $results['totalHandling'] : '0.00';
        $totalStorage = $results['totalStorage'] != null ? $results['totalStorage'] : '0.00';
        $totalTransport = $results['totalTransport'] != null ? $results['totalTransport'] : '0.00';
        $totalVat = $results['totalVat'] != null ? $results['totalVat'] : '0.00';
        $totalCovid = $results['totalCovid'] != null ? $results['totalCovid'] : '0.00';
        $totalWht = $results['totalWht'] != null ? $results['totalWht'] : '0.00';
        $totalGetfund = $results['totalGetfund'] != null ? $results['totalGetfund'] : '0.00';
        $totalPaid = $results['totalPaid'] != null ? $results['totalPaid'] : '0.00';
        $totalWaiver = $results['totalWaiver'] != null ? $results['totalWaiver'] : '0.00';
        $totalInvoice = $results['totalInvoice'] != null ? $results['totalInvoice'] : '0.00';
        $totalOutstanding = $results['totalOutstanding'] != null ? $results['totalOutstanding'] : '0.00';

        $totals_array['stripping_total'] = $total_stripping;
        $totals_array['dd_cost_total'] = $totalDd;
        $totals_array['handling_cost_total'] = $totalHandling;
        $totals_array['storage_cost_total'] = $totalStorage;
        $totals_array['transport_cost_total'] = $totalTransport;
        $totals_array['waiver_amount_total'] = $totalWaiver;
        $totals_array['invoice_cost_total'] = $totalInvoice;
        $totals_array['vat_total'] = $totalVat;
        $totals_array['covid19_total'] = $totalCovid;
        $totals_array['wht_total'] = $totalWht;
        $totals_array['getfund_total'] = $totalGetfund;
        $totals_array['paid_total'] = $totalPaid;
        $totals_array['outstanding_total'] = $totalOutstanding;

        return $totals_array;
    }

}


?>