<?php

namespace Api;
use Lib\ImportReceiptPdf;
use Lib\MyTransactionQuery;
use Lib\DocumentInfo;
use Lib\MyQuery;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportReceipt {
    private $request,$response;
    public $trade_title = "Import Receipt";

    public function __construct($request,$response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function showReceipt($receipt_no){

        $query= new MyTransactionQuery();
        $query->query("SELECT invoice.boe_number,invoice.do_number,trade_type.name as trade_type,currency.code AS currency,invoice.number,payment_mode.name AS payment_mode,concat(user.first_name,' ',user.last_name)as full_name,payment.paid, payment.date, invoice.bl_number, invoice.do_number, invoice.id, invoice.cost FROM payment 
              INNER JOIN invoice ON payment.invoice_id = invoice.id INNER JOIN user ON user.id = payment.user_id INNER JOIN payment_mode ON payment_mode.id = payment.mode INNER JOIN currency ON currency.id = invoice.currency INNER JOIN trade_type ON trade_type.id = invoice.trade_type
              WHERE payment.receipt_number = ?");
        $query->bind = array('s',&$receipt_no);
        $query->run();
        $inov = $query->fetch_assoc();
        $bl_nuumber = $inov['bl_number'];
        $invoice_id = $inov['id'];
        $invoice_no = $inov['number'];

        $info = new DocumentInfo();
        $docInfo = $info->getImportInfo($bl_nuumber);
        $importInfo = $info->getInvoiceDetails($receipt_no,$supplementary=false);

        $query->query("SELECT COUNT(container_id) AS qty FROM invoice_container WHERE invoice_id =?");
        $query->bind = array('i',&$invoice_id);
        $query->run();
        $container_qty = $query->fetch_assoc();
        $qty = $container_qty['qty'];


        $query->commit();


        $pdf = new ImportReceiptPdf();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetTitle($this->trade_title);
        $pdf->company_name = $importInfo['company_name'];
        $pdf->company_address = $importInfo['company_location'];
        $pdf->company_web = $importInfo['company_web'];
        $pdf->tin_no = $importInfo['company_tin'];
        $pdf->phone = $importInfo['company_phone1'];
        $pdf->email = $importInfo['company_email'];
        $pdf->user = $inov['full_name'];
        $pdf->trade_type = $inov['trade_type'];
        $pdf->boe_no = $inov['boe_number'];
        $pdf->bl_number = $bl_nuumber;
        $pdf->reciept_no = $receipt_no;
        $pdf->invoice_no = $invoice_no;
        $pdf->invoice_date = (new \DateTime($inov['date']))->format('d M Y');
        $pdf->amount = $inov['paid'];
        $pdf->container_qty = $qty;
        $pdf->do_number = $inov['do_number'];
        $pdf->importer = $docInfo['importer_address'];
        $pdf->agent = $docInfo['agent'];
        $pdf->vessel = $docInfo['vname'];
        $pdf->recieved_date = (new \DateTime($importInfo['recieve_date']))->format('d M Y') . ' at ' . (new \DateTime($importInfo['recieve_date']))->format('g:ia');
        $pdf->voyage_no = $docInfo['reference'];
        $pdf->payment_mode = $inov['payment_mode'];
        $pdf->arrival_date = (new \DateTime($docInfo['estimated_arrival']))->format('d M Y');
        $pdf->departure_date = (new \DateTime($docInfo['estimated_departure']))->format('d M Y');
        $pdf->rotation_no = $docInfo['rotation'];
        $pdf->currency = $inov['currency'];
        $pdf->outstanding = $importInfo['invoice_outstanding'];


        $pdf->company_name = html_entity_decode($pdf->company_name);
        $pdf->company_address = html_entity_decode($pdf->company_address);
        $pdf->address = html_entity_decode($pdf->address);
        $pdf->voyage_no = html_entity_decode($pdf->voyage_no);
        $pdf->importer = html_entity_decode($pdf->importer);
        $pdf->vessel = html_entity_decode($pdf->vessel);
        $pdf->agent = html_entity_decode($pdf->agent);

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