<?php

namespace Lib;
use Fpdf\FPDF;

class SuppExportPdf extends FPDF{

    public $invoice_no;
    public $tin_no;
    public $paid_up_to;
    public $importer;
    public $address;
    public $vessel;
    public $voyage_no;
    public $rotation_no;
    public $arrival_date;
    public $departure_date;
    public $bill_to;
    public $activity;
    public $shipping_line;
    public $charge_description;
    public $good_description;
    public $boe_no;
    public $booking_number;
    public $booking_date;
    public $container;
    public $release_date;
    public $qty;
    public $unit_price;
    public $amount;
    public $vat;
    public $nhil;
    public $vat_total;
    public $total_amount;
    public $container_no;
    public $iso_code;
    public $container_type;
    public $sub_total;
    public $container_qty;
    public $trade_type;
    public $invoice_date;
    public $company_name;
    public $phone;
    public $email;
    public $company_address;
    public $company_web;
    public $eta_date;
    public $due_date;
    public $storage_charge;
    public $days;
    public $currency;
    public $taxType;
    public $genericLabel;
    public $checkDate;
    public $main_invoice;
    public $user;
    public $waiver_amount;
    public $waiver_percent;
    public $status;
    public $customer;
    public $is_proforma;

    function topBody(){
        $this->SetAutoPageBreak(true,47);
        $this->SetFont('Times','B',14.3);
        $this->Cell(190,7,$this->company_name,0,1,'C');
        $this->AddFont('LiberationSerif','','LiberationSerif.php');
        $this->SetFont('LiberationSerif', '',9.8);
        $this->SetFont('Times', '',9.8);
        $this->Cell(190,6,$this->company_address,0,1,'C');
        $this->Cell(190,6,'Tel:'.' '.$this->phone.' '.' E-mail:'.' '.$this->email.' '. 'Website:'.' '.$this->company_web,0,1,'C');
        $this->Ln(5);

        $this->SetFont('Arial','B',9.8);
        $this->Cell(130,6,$this->is_proforma ?  'PRO FORMA SUPPLEMENTARY INVOICE ('.$this->trade_type.')' : 'CASH DELIVERY SUPPLEMENTARY INVOICE ('.$this->trade_type.')',0,0,'L');
        $this->Cell(10,6,'TAX INVOICE',0,1,'R');
        $this->Ln(2);
        $this->Cell(190,0,'','B,B',1);
        $this->Ln(3);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(27,5,'Invoice Date:',0,0);
        $this->SetFont('Arial','',8.8);
        $this->Cell(103,5,$this->invoice_date,0,0);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(23,5,'Invoice No:',0,0);
        $this->SetFont('Arial','',8.8);
        $this->Cell(37,5,$this->invoice_no,0,1);
        $this->Ln(3);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(27,5,'Paid Up to:',0,0);
        $this->SetFont('Arial','',8.8);
        $this->Cell(103,5,$this->paid_up_to,0,0);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(23,5,'TIN:',0,0);
        $this->SetFont('Arial','',8.8);
        $this->Cell(37,5,$this->tin_no,0,1);
        $this->Ln(2);
        $this->Cell(190,0,'','B,B',1);
        $this->Ln(4);
    }

    function firstBody(){

        $this->SetFont('Arial','B',8.8);
        $this->Cell(47.5,6,'Shipper',0,0);
        $this->Cell(47.5,6,'Shipping Line',0,0);
        $this->Cell(47.5,6,'Customer',0,0);
        $this->Cell(47.5,6,'Main Invoice',0,1);

        $this->SetFont('Arial','',8.8);
        $width = 47.5;
        $y = $this->GetY();
        $x = $this->GetX();
        $this->MultiCell($width,5,$this->bill_to,0,'L',0);
        $this->SetXY($x+$width,$y);
        $y = $this->GetY();
        $x = $this->GetX();
        $this->MultiCell(47.5,5,$this->shipping_line,0,'L',0);
        $this->SetXY($x+$width,$y);
        $y = $this->GetY();
        $x = $this->GetX();
        $this->MultiCell(47.5,5,$this->customer,0,'L',0);
        $this->SetXY($x+$width,$y);
        $this->MultiCell(47.5,5,$this->main_invoice,0,'L',0);
        $this->Ln(2);

        $this->SetFont('Arial','B',8.8);
        $this->Cell(70,6,'Vessel',0,0);
        $this->Cell(50,6,'Booking Number',0,0);
        $this->Cell(35,6,'Booking Date',0,0);
        $this->Cell(35,6,'Activity',0,1);

        $this->SetFont('Arial','',8.8);
        $this->Cell(70,6,$this->vessel,0,0);
        $this->Cell(50,6,$this->booking_number,0,0);
        $this->Cell(35,6,$this->booking_date,0,0);
        $this->Cell(35,6,$this->activity,0,1);
        $this->Ln(5);

        $this->SetFont('Arial','B',8.9);
        $this->Cell(190,6,'Container',0,1);
        $this->Cell(190,6,$this->container_qty,0,1);
        $this->Ln(5);
    }

    function tableHead(){
        $this->SetFont('Arial','B',8.8);
        $this->Cell(127,7,'Charge Description',1,0);
        $this->Cell(15,7,'Qty',1,0,'C');
        $this->Cell(23,7,'Unit Rate',1,0,'C');
        $this->Cell(25,7,'Amount',1,1,'C');
    }

    function tableBody()
    {
        $waiver_applied = $this->waiver_amount > 0;
        if ($waiver_applied) {
            $this->SetFont('Arial', 'B', 8.8);
            $this->Cell(127, 7, '', 'T', 0);
            $this->Cell(38, 7, 'Total', 1, 0, 'L');
            $this->SetFont('Arial', '', 8.8);
            $this->Cell(25, 7, number_format($this->waiver_amount + $this->sub_total, 2), 'TRB', 1, 'C');
            $this->SetFont('Arial', 'B', 8.8);
            $this->Cell(127, 7, '', '', 0);
            $this->Cell(38, 7, 'Waiver (' . number_format($this->waiver_percent, 2) . '%)', 1, 0, 'L');
            $this->SetFont('Arial', '', 8.8);
            $this->Cell(25, 7, number_format($this->waiver_amount, 2), 'TRB', 1, 'C');
        }
        if ($this->taxType != 3) {
            $this->SetFont('Arial', 'B', 8.8);
            $this->Cell(127, 7, '', $waiver_applied ? '' : 'T', 0);
            $this->Cell(38, 7, 'Sub Total', 1, 0, 'L');
            $this->SetFont('Arial', '', 9);
            $this->Cell(25, 7, number_format($this->sub_total, 2), 'TRB', 1, 'C');
        }
        $this->SetFont('Arial','',8.8);
    }

    function tableBottom() {
        $this->SetFont('Arial','B',8.8);
        $waiver_applied = $this->waiver_amount > 0;
        if ($this->taxType != 3) {
            $this->Cell(127, 7, '', 0, 0);
            $this->Cell(38, 7, 'Total Tax', 'LRB', 0, 'L');
            $this->SetFont('Arial', '', 8.8);
            $this->Cell(25, 7, $this->vat_total, 'LRB', 1, 'C');
            $this->SetFont('Arial','B',8.8);
            $this->Cell(127,7,'',0,0);
            $this->Cell(38,7,'Total Amount '.$this->currency,1,0,'L');
            $this->SetFont('Arial','',8.8);
            $this->Cell(25,7,number_format($this->total_amount,2),1,1,'C');
            $this->Ln(5);
        }
        else{
            $this->SetFont('Arial','B',8.8);
            $this->Cell(127,7,'',$waiver_applied ? '' : 'T',0);
            $this->Cell(38,7,'Total Amount '.$this->currency,1,0,'L');
            $this->SetFont('Arial','',8.8);
            $this->Cell(25,7,number_format($this->total_amount,2),1,1,'C');
            $this->Ln(5);
        }
    }

    function secondBody(){
        $this->SetFont('Arial','BU',11);
        $this->Cell(190,6,'INVOICE CONTAINER LIST:',0,1);
        $this->Ln(3);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(5,6,'#',0,0,'C');
        $this->Cell(28,6,'Container No',0,0,'C');
        $this->Cell(21,6,'ISO Type Code',0,0,'C');
        $this->Cell(31,6,'Container Type',0,0,'C');
        $this->Cell(32,6,'Goods Description',0,1,'C');
        $this->Ln(2);
        $this->Cell(190,0,'','B,B',1);
        $this->Ln(2);


    }

    function Footer(){
        $this->SetY(-37);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(95,7,'','B,B',0);
        $this->Cell(95,7,'',0,1);
        $this->SetX(45);
        $this->Cell(95,7,'Checked By',0,0);
        $this->Cell(95,7,'',0,1);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(190,0,'','B,B',1);
        $this->Cell(20,7,'Page '.$this->PageNo().'/{nb}',0,0);
        $this->Cell(50,7,$this->checkDate,0,0,'C');
        $this->SetFont('Arial','B',8.8);
        $this->Cell(60,7,'By '.$this->user,0,0,'C');
        $this->Cell(60,7,'Distribution: CUSTOMER / BANK',0,0,'C');
        $this->ln(2);
        $this->SetFont('Times','',8.8);
        $this->SetY(-16);
        $this->Cell(190,5,$this->company_address,0,1,'C');
        $this->Cell(190,5,'Tel:'.' '.$this->phone.' '.' E-mail:'.' '.$this->email.' '. 'Website:'.' '.$this->company_web,0,1,'C');
    }

}

?>