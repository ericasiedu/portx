<?php
namespace Api;

use Lib\ExportReceiptPdf;
use Lib\MyTransactionQuery;
use Lib\DocumentInfo;
use Lib\MyQuery;

class ExportReceipt {
    private $request,$response;


    public function __construct($request,$response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function showReceipt($receipt_no){

        $query= new MyTransactionQuery();
        $query->query("SELECT trade_type.name as trade_type,trade_type.id as trade_id,currency.code AS currency,invoice.number,payment_mode.name AS payment_mode,concat(user.first_name,' ',user.last_name)as full_name, payment.paid, payment.date, invoice.book_number, invoice.do_number, invoice.id, invoice.cost 
                FROM payment INNER JOIN invoice ON payment.invoice_id = invoice.id INNER JOIN user ON user.id = payment.user_id INNER JOIN trade_type ON trade_type.id = invoice.trade_type INNER JOIN currency ON currency.id = invoice.currency INNER JOIN payment_mode ON payment_mode.id = payment.mode WHERE payment.receipt_number =?");
        $query->bind = array('s',&$receipt_no);
        $query->run();
        $inov = $query->fetch_assoc();
        $book_number = $inov['book_number'];
        $invoice_id = $inov['id'];
        $invoice_no = $inov['number'];
        
        $info = new DocumentInfo();
        $exportInfo = $info->getInvoiceDetails($receipt_no,$supplementary=false);

        $query->query("SELECT container_id AS id FROM invoice_details WHERE invoice_id =?");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        $container_query = $query->fetch_assoc();
        $container_id =  $container_query['id'];

        $docInfo = $info->getExportInfo($container_id,$book_number);

        $query->query("SELECT COUNT(container_id) AS qty FROM invoice_container WHERE invoice_id =?");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        $container_qty = $query->fetch_assoc();
        $qty = $container_qty['qty'];


        $query->commit();



        $pdf = new ExportReceiptPdf();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetTitle($inov['trade_id'] == 4 ?'Export Receipt':'Empty Receipt');
        $pdf->company_name = $exportInfo['company_name'];
        $pdf->company_address = $exportInfo['company_location'];
        $pdf->company_web = $exportInfo['company_web'];
        $pdf->tin_no = $exportInfo['company_tin'];
        $pdf->phone = $exportInfo['company_phone1'];
        $pdf->email = $exportInfo['company_email'];
        $pdf->user = $inov['full_name'];
        $pdf->trade_type = $docInfo['trade'];
        $pdf->bl_number = $book_number;
        $pdf->exporter = $docInfo['consignee'];
        // $pdf->agent = $result['name'];
        $pdf->reciept_no = $receipt_no;
        $pdf->invoice_no = $invoice_no;
        $pdf->invoice_date = (new \DateTime($inov['date']))->format('d M Y');
        $pdf->amount = $inov['paid'];
        $pdf->do_number = $inov['do_number'];
        $pdf->currency = $inov['currency'];
        $pdf->payment_mode = $inov['payment_mode'];
        $pdf->container_qty = $qty;
        $pdf->agent = $docInfo['agent'];
        $pdf->vessel = $docInfo['vname'];
        $pdf->voyage_no = $docInfo['reference'];
        $pdf->recieved_date = (new \DateTime($exportInfo['recieve_date']))->format('d M Y') . ' at ' . (new \DateTime($exportInfo['recieve_date']))->format('g:ia');
        $pdf->arrival_date = (new \DateTime($docInfo['estimated_arrival']))->format('d M Y');
        $pdf->departure_date = (new \DateTime($docInfo['estimated_departure']))->format('d M Y');
        $pdf->rotation_no = $docInfo['rotation_number'];
        $pdf->outstanding = $exportInfo['invoice_outstanding'];

        $pdf->company_name = html_entity_decode($pdf->company_name);
        $pdf->company_address = html_entity_decode($pdf->company_address);
        $pdf->address = html_entity_decode($pdf->address);
        $pdf->voyage_no = html_entity_decode($pdf->voyage_no);
        $pdf->exporter = html_entity_decode($pdf->exporter);
        $pdf->vessel = html_entity_decode($pdf->vessel);
        $pdf->agent = html_entity_decode($pdf->agent);
        $pdf->consignee_title = $inov['trade_id'] == 4 ? 'Consignee / Exporter':"Consignee";

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