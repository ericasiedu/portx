<?php

namespace Api;
use Lib\SuppExportPdf,
     Lib\MyTransactionQuery,
    Lib\MyQuery,
    Lib\StorageCharges,
    Lib\TaxType\Simple,
    Lib\TaxType\Compound,
    Lib\InvoiceGenerate,
    Lib\DocumentInfo;

class ProformaSuppExportInvoice {

    private $request, $response;

    public function __construct($request, $response) {
        $this->request = $request;
        $this->response = $response;
    }

    public function show_export($invoice_number) {
        $query = new MyTransactionQuery();

        $query->query("SELECT invoice.number AS main_invoice, proforma_supplementary_invoice.date AS check_date, proforma_supplementary_invoice.cost AS subtotal,proforma_supplementary_invoice.waiver_pct, proforma_supplementary_invoice.waiver_amount,proforma_supplementary_invoice.id AS invoice_id, currency.code 
              AS currency, invoice.book_number,customer.name AS customer, invoice.bill_date, proforma_supplementary_invoice.due_date, proforma_supplementary_invoice.cost, invoice.do_number, proforma_supplementary_invoice.tax, invoice.tax_type, proforma_supplementary_invoice.user_id
              FROM proforma_supplementary_invoice INNER JOIN invoice ON invoice.id = proforma_supplementary_invoice.invoice_id  
              INNER JOIN currency ON currency.id = invoice.currency INNER JOIN  customer ON customer.id = invoice.customer_id WHERE proforma_supplementary_invoice.number =?");
        $query->bind = array('s',&$invoice_number);
        $query->run();
        $inov = $query->fetch_assoc();
        $book_number = $inov['book_number'];
        $invoice_id = $inov['invoice_id'];
        $taxType = $inov['tax_type'];
        $main_invoice = $inov['main_invoice'];


        $info = new DocumentInfo();
        $docInfo = $info->getExportInvoiceInfo($book_number);

        $query->query("SELECT container.date AS eta_date, container.id AS container_id, trade_type.id AS trade_id, container.date, container.importer_address, 
                  agency.name, container.iso_type_code, container_isotype_code.length, container_isotype_code.description, 
                  container.full_status, trade_type.name AS trade_name FROM container INNER JOIN container_isotype_code ON 
                  container.iso_type_code = container_isotype_code.id INNER JOIN agency ON 
                  container.agency_id = agency.id INNER JOIN trade_type ON trade_type.code = container.trade_type_code 
                  WHERE container.book_number =? AND container.gate_status = 'GATED IN' ");
        $query->bind = array('s',&$book_number);
        $query->run();
        $result = $query->fetch_assoc();
        $storage = new StorageCharges();

        $storage->iso_type = $result['type_code'];
        $storage->trade_type__id = $result['trade_id'];
        $storage->eta_date = $result['eta_date'];
        $storage->p_date = $inov['due_date'];


        $query->query("SELECT company_tin, company_phone1, company_name, company_phone1, company_web, company_email, company_location, prefix FROM system");
        $query->run();
        $sys = $query->fetch_assoc();
        $system = $sys['prefix'];


        $query->query("SELECT COUNT(container_id) AS qty FROM proforma_supplementary_invoice_container WHERE invoice_id =?");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        $container_qty = $query->fetch_assoc();
        $qty = $container_qty['qty'];


        $query->query("SELECT cost, description, total_cost, qty FROM proforma_supplementary_invoice_details WHERE invoice_id =?");
        $query->bind = array('i',&$invoice_id);
        $query->run();

        $sub_total = $inov['subtotal'];


        $pdf = new SuppExportPdf();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetTitle('Proforma Supplementary Export Invoice');
        $pdf->is_proforma = true;
        $pdf->company_name = $sys['company_name'];
        $pdf->company_address = $sys['company_location'];
        $pdf->company_web = $sys['company_web'];
        $pdf->tin_no = $sys['company_tin'];
        $pdf->phone = $sys['company_phone1'];
        $pdf->email = $sys['company_email'];
        $pdf->booking_number = $book_number;
        $pdf->shipping_line = $result['name'];
        $pdf->bill_to = $result['importer_address'];
        $pdf->trade_type = $result['trade_name'];
        $pdf->customer = $inov['customer'];
        $pdf->status = $inov['status'];
        $pdf->main_invoice = $main_invoice;
        $pdf->container_qty = $qty;
        $pdf->invoice_no = $invoice_number;
        $pdf->sub_total = $sub_total;
        $pdf->waiver_amount =  $inov['waiver_amount'];
        $pdf->waiver_percent = $inov['waiver_pct'];
        $pdf->invoice_date = (new \DateTime($inov['check_date']))->format('d M Y'); //. ' at ' . (new \DateTime($inov['bill_date']))->format('g:ia');
        $pdf->checkDate = (new \DateTime($inov['check_date']))->format('d M Y') . ' at ' . (new \DateTime($inov['check_date']))->format('g:ia');
        $pdf->paid_up_to = (new \DateTime($inov['due_date']))->format('d M Y');
        $pdf->company_name = $sys['company_name'];
        $pdf->currency = $inov['currency'];
        $pdf->shipping_line = $docInfo['sname'];
        $pdf->vessel = $docInfo['vname'];

        $pdf->topBody();
        $pdf->firstBody();
        $pdf->tableHead();
        $pdf->SetFont('Arial', '', 8.8);
        $description_array = array();

        while ($invoice_details = $query->fetch_assoc()) {

            $description_item = array('description' => $invoice_details['description'],
                'qty' => $invoice_details['qty'],
                'cost' => $invoice_details['cost'],
                'total_cost' => $invoice_details['total_cost']);

            $key = false;

            for ($index = 0; $index < count($description_array); $index++) {
                $item = $description_array[$index];
                if ($description_item['description'] === $item['description']) {
                    $key = $index;
                    break;
                }
            }

            if ($key > -1) {
                $item = $description_array[$key];
                $item['qty'] = $item['qty'] + 1;
                $item["total_cost"] += $description_item["total_cost"];

                $description_array[$key] = $item;
            } else {
                array_push($description_array, $description_item);
            }

        }

        $newY = $pdf->GetY();
        foreach ($description_array as $description) {
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

        $pdf->total_amount = $sub_total;
        $pdf->taxType = $taxType;

        $pdf->tableBody();


        if ($taxType == 1 || $taxType == 2 || $taxType == 4) {
            $tax_tbl = 'proforma_supplementary_invoice_details_tax';
            DocumentInfo::get_taxes($pdf, $query, $invoice_id, $tax_tbl);
        }

        $user_id = $inov['user_id'];

        $query->query("SELECT first_name, last_name from user where id =?");
        $query->bind = array('i',&$user_id);
        $user = $query->run()->fetch_assoc();
        $pdf->user = $user['first_name'] . (isset($user['last_name']) ? " " . $user['last_name'] : "");

        $pdf->tableBottom();
        $pdf->secondBody();
        $pdf->SetFont('Arial', '', 8.8);
        $query->query("SELECT container.number, container.content_of_goods, container_isotype_code.code, container_isotype_code.length 
              FROM proforma_supplementary_invoice_container INNER JOIN container ON container.id = proforma_supplementary_invoice_container.container_id 
              INNER JOIN container_isotype_code ON container_isotype_code.id = container.iso_type_code WHERE proforma_supplementary_invoice_container.invoice_id =?");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        $bullet_number = 1;
        while ($container_list = $query->fetch_assoc()) {
            $pdf->Cell(5, 6, $bullet_number, 0, 0, 'C');
            $pdf->Cell(28, 6, $container_list['number'], 0, 0, 'C');
            $pdf->Cell(21, 6, $container_list['code'], 0, 0, 'C');
            $pdf->Cell(31, 6, $container_list['length'] . " " . 'Foot Container', 0, 0, 'C');
            $pdf->MultiCell(107, 6, mb_strimwidth($container_list['content_of_goods'], 0, 102) . (strlen($container_list['content_of_goods']) > 102 ? "..." : ""),
                0, 'L', 0);
            $bullet_number++;
        }
        $query->commit();
        $pdf->Output();
    }
}

?>