<?php
namespace Api;
use Lib\ExportPdf,
    Lib\MyTransactionQuery,
    Lib\MyQuery,
    Lib\DocumentInfo;

class ProformaExportInvoice{
    private $request,$response;

    public function __construct($request,$response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function show_export($invoice_number){

        $query=new MyTransactionQuery();

        $query->query("SELECT date AS check_date, cost AS subtotal, waiver_pct, waiver_amount,proforma_invoice.id AS invoice_id, currency.code AS currency, proforma_invoice.book_number, customer.name as cust,
            proforma_invoice.bill_date, proforma_invoice.due_date, proforma_invoice.cost, proforma_invoice.do_number, proforma_invoice.tax, proforma_invoice.tax_type, user_id FROM proforma_invoice 
            INNER JOIN currency ON proforma_invoice.currency = currency.id
            INNER JOIN customer on proforma_invoice.customer_id  = customer.id WHERE proforma_invoice.number =?");
        $query->bind = array('s',&$invoice_number);
        $query->run();
        $inov = $query->fetch_assoc();
        $book_number = $inov['book_number'];
        $invoice_id = $inov['invoice_id'];
        $taxType = $inov['tax_type'];
        $customer = $inov['cust'];

        $info = new DocumentInfo();

        $query->query("SELECT container.id AS container_id, trade_type.id AS trade_id, container.date, gate_record.consignee, 
                  shipping_line.name, container.iso_type_code, container_isotype_code.length, container_isotype_code.description, 
                  container.full_status, trade_type.name AS trade_name FROM container INNER JOIN container_isotype_code ON 
                  container.iso_type_code = container_isotype_code.id INNER JOIN shipping_line ON 
                  container.shipping_line_id = shipping_line.id INNER JOIN gate_record ON gate_record.container_id = container.id INNER JOIN trade_type ON trade_type.code = container.trade_type_code 
                  WHERE container.book_number =? AND container.gate_status = 'GATED IN' ");
        $query->bind = array('s',&$book_number);
        $query->run();
        $result = $query->fetch_assoc();

     

        $query->query("SELECT company_tin, company_phone1, company_name, company_phone1, company_web, company_email, company_location, prefix FROM system");
        $query->bind = array();
        $query->run();
        $sys = $query->fetch_assoc();


        $query->query("SELECT COUNT(distinct(container_id)) AS qty FROM proforma_invoice_details WHERE invoice_id =?");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        $container_qty = $query->fetch_assoc();
        $qty = $container_qty['qty'];

        $query->query("SELECT container_id AS id FROM proforma_invoice_details WHERE invoice_id =?");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        $container_query = $query->fetch_assoc();
        $container_id =  $container_query['id'];

        // $docInfo = $info->getExportInfo($container_id,$book_number);
        $docInfo = $this->getTradeInfo($container_id,$book_number);


        $query->query("SELECT cost, description, total_cost, qty FROM proforma_invoice_details WHERE invoice_id =?");
        $query->bind = array('i',&$invoice_id);
        $details_query = $query->run();

        $waive_query = new MyQuery();
        $waive_query->query("select sum(cost) as waive_total from proforma_invoice_details where invoice_id='$invoice_id'");
        $waive_query->run();
        $waive_sub_total = $waive_query->fetch_assoc();

        $sub_total = $inov['subtotal'];



        $pdf = new ExportPdf();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetTitle($result['trade_id'] == 4 ?'Proforma Export Invoice':'Proforma Empty Invoice');
        $pdf->invoice_title = 'PRO FORMA INVOICE';
        $pdf->company_name = $sys['company_name'];
        $pdf->company_address = $sys['company_location'];
        $pdf->company_web = $sys['company_web'];
        $pdf->tin_no = $sys['company_tin'];
        $pdf->phone = $sys['company_phone1'];
        $pdf->email = $sys['company_email'];
        $pdf->booking_number = $book_number;
        $pdf->shipping_line = $result['name'];
        $pdf->bill_to = $result['consignee'];
        $pdf->trade_type = $result['trade_name'];
        $pdf->status = $inov['status'];
        $pdf->customer = $customer;
        $pdf->container_qty = $qty;
        $pdf->invoice_no = $invoice_number;
        $pdf->sub_total = $sub_total;
        $pdf->waiver_amount =  $inov['waiver_amount'] > $waive_sub_total['waive_total'] ? $waive_sub_total['waive_total']  : $inov['waiver_amount'];
        $pdf->waiver_percent = $inov['waiver_pct'];
        $pdf->invoice_date = (new \DateTime($inov['check_date']))->format('d M Y'); //. ' at ' . (new \DateTime($inov['bill_date']))->format('g:ia');
        $pdf->checkDate = (new \DateTime($inov['check_date']))->format('d M Y') . ' at ' . (new \DateTime($inov['check_date']))->format('g:ia');
        $pdf->paid_up_to = (new \DateTime($inov['due_date']))->format('d M Y');
        $pdf->currency = $inov['currency'];
        $pdf->company_name = $sys['company_name'];
        $pdf->shipping_line = $docInfo['shipping_line'];
        $pdf->trade_type = $docInfo['trade'];
        $pdf->vessel = $docInfo['vname'];

        $pdf->voyage_no = $docInfo['reference'];

        $pdf->company_name = html_entity_decode($pdf->company_name);
        $pdf->company_address = html_entity_decode($pdf->company_address);
        $pdf->address = html_entity_decode($pdf->address);
        $pdf->shipping_line = html_entity_decode($pdf->shipping_line);
        $pdf->bill_to = html_entity_decode($pdf->bill_to);
        $pdf->voyage_no = html_entity_decode($pdf->voyage_no);
        $pdf->importer = html_entity_decode($pdf->importer);
        $pdf->customer = html_entity_decode($pdf->customer);
        $pdf->vessel = html_entity_decode($pdf->vessel);
        $pdf->good_description = html_entity_decode($pdf->good_description);

        $pdf->topBody();
        $pdf->firstBody();
        $pdf->tableHead();
        $pdf->SetFont('Arial','',8.8);

        $description_array = array();

        while ($invoice_details = $details_query->fetch_assoc()){

            $description_item = array('description' => $invoice_details['description'],
                'qty' =>$invoice_details['qty'],
                'cost' => $invoice_details['cost'],
                'total_cost' => $invoice_details['total_cost']);

            $key = false;//array_search($description_item['description'], array_column($description_array, 'description'));

            for($index = 0 ; $index <count($description_array); $index++){
                $item = $description_array[$index];
                if($description_item['description'] === $item['description']) {
                    $key = $index;

                    break;
                }
            }


            if($key>-1) {
                $item = $description_array[$key];
                $item['qty']= $item['qty'] + 1;
                $item["total_cost"] += $description_item["total_cost"];

                $description_array[$key] = $item;
            }
            else {
                array_push($description_array, $description_item);
            }

        }

        $newY = $pdf->GetY();
        foreach ($description_array as $description)
        {
            $pdf->SetY($newY);
            $y = $pdf->GetY();
            $pdf->MultiCell(127,7,$description['description'],'L');
            $newY = $pdf->GetY();
            if($newY - $y > 7){
                $pdf->SetY($newY - 7);
                $pdf->SetX(127 + 10);
                $pdf->Cell(15, 7, "", 'L,R', 0);
                $pdf->Cell(23, 7, "", 'L,R', 0);
                $pdf->Cell(25, 7, "", 'L,R', 1);
                $pdf->SetY($y);
            }
            $pdf->SetXY(127+10, $y);
            $x = $pdf->GetX();
            $pdf->MultiCell(15,7,$description['qty'],'L,R','C');
            $pdf->SetXY($x + 15, $y);
            $x = $pdf->GetX();
            $pdf->MultiCell(23,7,number_format($description['cost'], 2),'L,R','C');
            $pdf->SetXY($x + 23, $y);
            $x = $pdf->GetX();
            $pdf->MultiCell(25,7,number_format($description['total_cost'], 2),'L,R','C');
            $pdf->SetY($newY);
        }

        $user_id = $inov['user_id'];

        $pdf->total_amount = $sub_total;
        $pdf->taxType = $taxType;

        $pdf->tableBody();


        if ($taxType == 1 || $taxType == 2 || $taxType == 4) {
            $tax_tbl = 'proforma_invoice_details_tax';
            DocumentInfo::get_taxes($pdf, $query, $invoice_id,$tax_tbl);
        }

        $query->query("SELECT first_name, last_name from user where id =?");
        $query->bind = array('i',&$user_id);
        $query->run();
        $user = $query->fetch_assoc();
        $pdf->user = $user['first_name'].( isset($user['last_name'])? " ".$user['last_name'] : "");

        $pdf->tableBottom();
        $pdf->secondBody();
        $pdf->SetFont('Arial','',8.8);
        $query->query("SELECT distinct(container.number), container.content_of_goods, container_isotype_code.code, container_isotype_code.length FROM proforma_invoice_details INNER JOIN proforma_invoice 
            ON proforma_invoice.id = proforma_invoice_details.invoice_id INNER JOIN container ON container.id = proforma_invoice_details.container_id 
            INNER JOIN container_isotype_code ON container_isotype_code.id = container.iso_type_code WHERE proforma_invoice.id =?");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        $bullet_number = 1;
        while ($container_list = $query->fetch_assoc()) {
            $pdf->Cell(5, 6, $bullet_number.". ", 0, 0, 'C');
            $pdf->Cell(28, 6, $container_list['number'], 0, 0, 'C');
            $pdf->Cell(21, 6, $container_list['code'], 0, 0, 'C');
            $pdf->Cell(31, 6, $container_list['length']." ".'Foot Container', 0, 0, 'C');
            $pdf->MultiCell(107, 6, mb_strimwidth($container_list['content_of_goods'], 0, 102).(strlen($container_list['content_of_goods']) > 102 ? "...": ""),
                0, 'L', 0);
            $bullet_number++;
        }
        $pdf->Output();
        $query->commit();
    }

    function getTradeInfo($container_id,$book_number) {
        $info = new DocumentInfo();
        return $info->getExportInfo($container_id,$book_number);
    }

}

?>