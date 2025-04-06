<?php

namespace Api;
use Lib\ImportReceiptPdf;
use Lib\MyTransactionQuery;
use Lib\DocumentInfo;
use Lib\MyQuery;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SuppImportReciept {
    private $request,$response;
    public $receipt_title = "Supplementary Import Receipt";

    public function __construct($request,$response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function showReceipt($receipt_no){

        $query= new MyTransactionQuery();
        $query->query("SELECT invoice.boe_number,payment_mode.name AS payment_mode,
                        currency.code AS currency,supplementary_invoice.number, 
                        supplementary_payment.paid, supplementary_payment.date, 
                        invoice.bl_number,
                        concat(user.first_name,' ',user.last_name)as full_name, 
                        invoice.do_number, supplementary_invoice.id, 
                        supplementary_invoice.cost, trade_type.name AS trade_type  
                        FROM supplementary_payment INNER JOIN supplementary_invoice 
                        ON supplementary_payment.invoice_id = supplementary_invoice.id 
                        INNER JOIN user ON user.id = supplementary_payment.user_id 
                        INNER JOIN payment_mode 
                        ON payment_mode.id = supplementary_payment.mode INNER JOIN invoice 
                        ON invoice.id = supplementary_invoice.invoice_id INNER JOIN currency 
                        ON currency.id = invoice.currency INNER JOIN trade_type 
                        ON invoice.trade_type = trade_type.id 
                        WHERE supplementary_payment.receipt_number = ?");
        $query->bind = array('s',&$receipt_no);
        $query->run();
        $inov = $query->fetch_assoc();
        $bl_nuumber = $inov['bl_number'];
        $invoice_id = $inov['id'];
        $invoice_no = $inov['number'];
        $trade_type = $inov['trade_type'];

        $info = new DocumentInfo();
        $docInfo = $info->getImportInfo($bl_nuumber);
        $suppImportInfo = $info->getInvoiceDetails($receipt_no,$supplementary=true);


        $query->query("SELECT COUNT(container_id) AS qty FROM supplementary_invoice_container WHERE invoice_id = '$invoice_id'");
        $query->run();
        $container_qty = $query->fetch_assoc();
        $qty = $container_qty['qty'];

        $query->commit();



        $pdf = new ImportReceiptPdf();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetTitle($this->receipt_title);
        $pdf->company_name = $suppImportInfo['company_name'];
        $pdf->company_address = $suppImportInfo['company_location'];
        $pdf->company_web = $suppImportInfo['company_web'];
        $pdf->tin_no = $suppImportInfo['company_tin'];
        $pdf->phone = $suppImportInfo['company_phone1'];
        $pdf->email = $suppImportInfo['company_email'];
        $pdf->boe_no = $inov['boe_number'];
        $pdf->user = $inov['full_name'];
        $pdf->bl_number = $bl_nuumber;
        $pdf->reciept_no = $receipt_no;
        $pdf->payment_mode = $inov['payment_mode'];
        $pdf->invoice_no = $invoice_no;
        $pdf->invoice_date = (new \DateTime($inov['date']))->format('d M Y');
        $pdf->amount = $inov['paid'];
        $pdf->container_qty = $qty;
        $pdf->do_number = $inov['do_number'];
        $pdf->importer = $docInfo['importer_address'];
        $pdf->trade_type = $trade_type;
        $pdf->currency = $suppImportInfo['currency'];
        $pdf->agent = $docInfo['agent'];
        $pdf->vessel = $docInfo['vname'];
        $pdf->currency = $inov['currency'];
        $pdf->voyage_no = $docInfo['reference'];
        $pdf->arrival_date = (new \DateTime($docInfo['estimated_arrival']))->format('d M Y');
        $pdf->departure_date = (new \DateTime($docInfo['estimated_departure']))->format('d M Y');
        $pdf->recieved_date = (new \DateTime($suppImportInfo['recieve_date']))->format('d M Y') . ' at ' . (new \DateTime($suppExportInfo['recieve_date']))->format('g:ia');
        $pdf->outstanding = $suppImportInfo['outstanding'];
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