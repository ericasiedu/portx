<?php

namespace Api;
use Lib\ExportReceiptPdf;
use Lib\MyTransactionQuery;
use Lib\DocumentInfo;
use Lib\MyQuery;

class SuppExportReciept {
    private $request,$response;


    public function __construct($request,$response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function showReceipt($receipt_no){

        $query= new MyTransactionQuery();
        $query->query("SELECT invoice.boe_number,invoice.do_number,currency.code,payment_mode.name AS payment_mode,supplementary_invoice.number, supplementary_payment.paid, supplementary_payment.date, invoice.book_number, invoice.do_number,concat(user.first_name,' ',user.last_name)as full_name, supplementary_invoice.id, supplementary_invoice.cost 
              FROM supplementary_payment INNER JOIN supplementary_invoice ON supplementary_payment.invoice_id = supplementary_invoice.id 
              INNER JOIN invoice ON invoice.id = supplementary_invoice.invoice_id INNER JOIN user ON user.id = supplementary_payment.user_id INNER JOIN payment_mode ON payment_mode.id = supplementary_payment.mode INNER JOIN currency ON currency.id = invoice.currency
              WHERE supplementary_payment.receipt_number =?");
        $query->bind = array('s',&$receipt_no);
        $query->run();
        $inov = $query->fetch_assoc();
        $book_number = $inov['book_number'];
        $invoice_id = $inov['id'];
        $invoice_no = $inov['number'];

        $query->query("SELECT container_id AS id FROM supplementary_invoice_details WHERE invoice_id =?");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        $container_query = $query->fetch_assoc();
        $container_id =  $container_query['id'];

        $info = new DocumentInfo();
        $docInfo = $info->getExportInfo($container_id, $book_number);
        $suppExportInfo = $info->getInvoiceDetails($receipt_no,$supplementary=true);


        $query->query("SELECT COUNT(container_id) AS qty FROM supplementary_invoice_container WHERE invoice_id =?");
        $query->bind = array('i',&$invoice_id);
        $tu_res = $query->run();
        $container_qty = $tu_res->fetch_assoc();
        $qty = $container_qty['qty'];

        $query->commit();




        $pdf = new ExportReceiptPdf();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetTitle('Export Receipt');
        $pdf->company_name = $suppExportInfo['company_name'];
        $pdf->company_address = $suppExportInfo['company_location'];
        $pdf->company_web = $suppExportInfo['company_web'];
        $pdf->tin_no = $suppExportInfo['company_tin'];
        $pdf->phone = $suppExportInfo['company_phone1'];
        $pdf->email = $suppExportInfo['company_email'];
        $pdf->bl_number = $book_number;
        $pdf->payment_mode = $inov['payment_mode'];
        $pdf->do_number = $inov['do_number'];
        $pdf->boe_no = $inov['boe_number'];
        $pdf->user = $inov['full_name'];
        $pdf->reciept_no = $receipt_no;
        $pdf->invoice_no = $invoice_no;
        $pdf->invoice_date = (new \DateTime($inov['date']))->format('d M Y');
        $pdf->amount = $inov['paid'];
        $pdf->do_number = $inov['do_number'];
        $pdf->trade_type = $docInfo['trade'];
        $pdf->exporter = $docInfo['consignee'];
        $pdf->container_qty = $qty;
        $pdf->agent = $docInfo['agent'];
        $pdf->vessel = $docInfo['vname'];
        $pdf->voyage_no = $docInfo['reference'];
        $pdf->recieved_date = (new \DateTime($suppExportInfo['recieve_date']))->format('d M Y') . ' at ' . (new \DateTime($suppExportInfo['recieve_date']))->format('g:ia');
        $pdf->arrival_date = (new \DateTime($docInfo['estimated_arrival']))->format('d M Y');
        $pdf->departure_date = (new \DateTime($docInfo['estimated_departure']))->format('d M Y');
        $pdf->rotation_no = $docInfo['rotation_number'];
        $pdf->outstanding = $suppExportInfo['outstanding'];
        $pdf->topBody();
        $pdf->import_Fbody();
        $pdf->tableHead();
        $pdf->SetFont('Arial','',10);
        $pdf->tableBody();
        $pdf->secondBody();
        $pdf->tableFooter();
        $pdf->Output();
    }


}


?>