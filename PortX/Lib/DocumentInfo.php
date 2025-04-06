<?php

namespace Lib;


class DocumentInfo {
    public $docArray = [];
    public $invoice_id;
    public $sub_total;
    public $query;

    public function getImportInfo($b_number) {

        $query = new MyQuery();
        $query->query("SELECT trade_type.name AS trade,container.importer_address, agency.name AS agent, vessel.name AS vname, 
                  voyage.reference, voyage.actual_arrival , voyage.actual_departure , voyage.rotation_number 
                  FROM container LEFT JOIN agency ON agency.id = container.agency_id
                  LEFT JOIN voyage ON container.voyage = voyage.id LEFT JOIN vessel ON voyage.vessel_id = vessel.id INNER JOIN trade_type ON trade_type.code = container.trade_type_code
                  WHERE container.bl_number =?");
        $query->bind = array('s',&$b_number);
        $res = $query->run();
        $result = $res->fetch_assoc();
        $this->docArray['importer_address'] = $result['importer_address'];
        $this->docArray['agent'] = $result['agent'];
        $this->docArray['vname'] = $result['vname'];
        $this->docArray['reference'] = $result['reference'];
        $this->docArray['actual_arrival'] = $result['actual_arrival'];
        $this->docArray['actual_departure'] = $result['actual_departure'];
        $this->docArray['rotation_number'] = $result['rotation_number'];
        $this->docArray['trade'] = $result['trade'];

        return $this->docArray;
    }

    public function getExportInvoiceInfo($b_number) {
        $query = "SELECT trade_type.name AS trade, shipping_line.name AS sname, vessel.name AS vname 
                  FROM container LEFT JOIN shipping_line ON shipping_line.id = container.shipping_line_id 
                  LEFT JOIN voyage ON voyage.id = container.voyage LEFT JOIN vessel ON vessel.id = voyage.vessel_id 
                  INNER JOIN trade_type ON trade_type.code = container.trade_type_code WHERE container.book_number = '$b_number'";

        $qu = new MyQuery();
        $qu->query($query);
        $res = $qu->run();
        $result = $res->fetch_assoc();
        $this->docArray['sname'] = $result['sname'];
        $this->docArray['vname'] = $result['vname'];
        $this->docArray['trade_type'] = $result['trade'];


        return $this->docArray;
    }

    public function getEmptyInvoiceInfo($b_number) {
        $query = "SELECT trade_type.name AS trade, shipping_line.name AS sname/* , vessel.name AS vname */ 
                  FROM container LEFT JOIN shipping_line ON shipping_line.id = container.shipping_line_id 
                  /* LEFT JOIN voyage ON voyage.id = container.voyage LEFT JOIN vessel ON vessel.id = voyage.vessel_id */ 
                  INNER JOIN trade_type ON trade_type.code = container.trade_type_code WHERE container.book_number = '$b_number'";

        $qu = new MyQuery();
        $qu->query($query);
        $res = $qu->run();
        $result = $res->fetch_assoc();
        $this->docArray['sname'] = $result['sname'];
        // $this->docArray['vname'] = $result['vname'];
        $this->docArray['trade_type'] = $result['trade'];


        return $this->docArray;
    }

    public function getExportInfo($container_id,$b_number) {

        $query = new MyQuery();
        $query->query("SELECT trade_type.name AS trade,gate_record.consignee, shipping_line.name AS cname, vessel.name AS vname, 
                  voyage.reference, voyage.estimated_arrival, voyage.estimated_departure, voyage.rotation_number , agency.name AS agent
                  FROM container LEFT JOIN shipping_line ON shipping_line.id = container.shipping_line_id INNER JOIN gate_record ON gate_record.container_id =  container.id
                  LEFT JOIN voyage ON container.voyage = voyage.id LEFT JOIN vessel ON voyage.vessel_id = vessel.id LEFT JOIN trade_type ON trade_type.code = container.trade_type_code
                  LEFT JOIN agency ON container.agency_id = agency.id WHERE container.id =? and container.book_number =?");
        $query->bind = array('is',&$container_id,&$b_number);
        $res = $query->run();
        $result = $res->fetch_assoc();
        $this->docArray['consignee'] = $result['consignee'];
        $this->docArray['shipping_line'] = $result['cname'];
        $this->docArray['vname'] = $result['vname'];
        $this->docArray['trade'] = $result['trade'];
        $this->docArray['agent'] = $result['agent'];
        $this->docArray['reference'] = $result['reference'];
        $this->docArray['estimated_arrival'] = $result['estimated_arrival'];
        $this->docArray['estimated_departure'] = $result['estimated_departure'];
        $this->docArray['rotation_number'] = $result['rotation_number'];


        return $this->docArray;
    }

// <<<<<<< HEAD
    public function getEmptyInfo($container_id,$b_number) {
// var_dump($b_number);die;
        $query = new MyQuery();
        $query->query("SELECT trade_type.name AS trade,gate_record.consignee, shipping_line.name AS cname/*,  vessel.name AS vname, 
                  voyage.reference, voyage.estimated_arrival, voyage.estimated_departure, voyage.rotation_number , agency.name AS agent */
                  FROM container LEFT JOIN shipping_line ON shipping_line.id = container.shipping_line_id INNER JOIN gate_record ON gate_record.container_id =  container.id
                  /* LEFT JOIN voyage ON container.voyage = voyage.id LEFT JOIN vessel ON voyage.vessel_id = vessel.id */ LEFT JOIN trade_type ON trade_type.code = container.trade_type_code
                  /* LEFT JOIN agency ON container.agency_id = agency.id */ WHERE container.id =? and container.book_number =?");
        // $query->query("SELECT trade_type.name AS trade,gate_record.consignee, shipping_line.name AS cname, vessel.name AS vname 
        //           FROM container LEFT JOIN shipping_line ON shipping_line.id = container.shipping_line_id INNER JOIN gate_record ON gate_record.container_id =  container.id
        //           WHERE container.id =? and container.book_number =?");
        $query->bind = array('is',&$container_id,&$b_number);
        $res = $query->run();
        $result = $res->fetch_assoc();
        $this->docArray['consignee'] = $result['consignee'];
        $this->docArray['shipping_line'] = $result['cname'];
        // $this->docArray['vname'] = $result['vname'];
        $this->docArray['trade'] = $result['trade'];
        // $this->docArray['agent'] = $result['agent'];
        // $this->docArray['reference'] = $result['reference'];
        // $this->docArray['estimated_arrival'] = $result['estimated_arrival'];
        // $this->docArray['estimated_departure'] = $result['estimated_departure'];
        // $this->docArray['rotation_number'] = $result['rotation_number'];


        return $this->docArray;
    }

    // public function getInvoiceDetails($receipt_no){
// =======
    public function getInvoiceDetails($receipt_no,$supplementary){
// >>>>>>> LIBRARY
        $query = new MyQuery();
        $query->query("SELECT  company_tin, company_phone1, company_name, company_phone1, company_web, company_email, company_location, prefix FROM system");
        $query->run();
        $result = $query->fetch_assoc();

        

        if($supplementary){
            $query2 = new MyQuery();
            $query2->query("SELECT date AS recieve_date, outstanding FROM supplementary_payment WHERE receipt_number = '$receipt_no'");
            $query2->run();
            $result1 = $query2->fetch_assoc();
            $this->docArray['recieve_date'] = $result1['recieve_date'];
            $this->docArray['outstanding'] = $result1['outstanding'];
        }
        else{
            $query1 = new MyQuery();
            $query1->query("SELECT date AS recieve_date, outstanding FROM payment WHERE receipt_number =?");
            $query1->bind = array('s',&$receipt_no);
            $query1->run();
            $inv = $query1->fetch_assoc();
            $this->docArray['invoice_outstanding'] = $inv['outstanding'];
            $this->docArray['recieve_date'] = $inv['recieve_date'];
        }

        $this->docArray['company_tin'] = $result['company_tin'];
        $this->docArray['company_phone1'] = $result['company_phone1'];
        $this->docArray['company_name'] = $result['company_name'];
        $this->docArray['company_phone1'] = $result['company_phone1'];
        $this->docArray['company_web'] = $result['company_web'];
        $this->docArray['company_email'] = $result['company_email'];
        $this->docArray['company_location'] = $result['company_location'];
        $this->docArray['prefix'] = $result['prefix'];

        return $this->docArray;

    }

    public static function get_taxes($pdf,$query,$invoice_id,$tax_tbl)
    {

        $query->query("SELECT description,cost FROM `$tax_tbl` WHERE invoice_id =?");
        $query->bind = array('i',&$invoice_id);
        $loopRes = $query->run();
        $tuu = new MyQuery();
        $tuu->query("SELECT SUM(cost) AS tax_total FROM `$tax_tbl` WHERE invoice_id = '$invoice_id'");
        $resii = $tuu->run();
        $resuu = $resii->fetch_assoc();
        $total_taxed = $resuu['tax_total'];
        while ($tax_details = $loopRes->fetch_assoc()) {
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(127, 6, '', '', 0);
            $pdf->Cell(38, 6, $tax_details['description'], 'LRB', 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(25, 6, $tax_details['cost'], 'L,R,B', 1, 'C');
        }

        $pdf->vat_total = $total_taxed;
        $pdf->total_amount = round($total_taxed + $pdf->total_amount, 2);
    }
}