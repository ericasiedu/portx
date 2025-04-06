<?php

namespace Lib;
use Fpdf\FPDF;

class SuppImportPdf extends FPDF{
    public $company_name;
    public $invoice_no;
    public $tin_no;
    public $paid_up_to;
    public $importer;
    public $agent;
    public $address;
    public $vessel;
    public $voyage_no;
    public $rotation_no;
    public $arrival_date;
    public $departure_date;
    public $boe_no;
    public $bl_number;
    public $do_number;
    public $container;
    public $release_date;
    public $activity;
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
    public $charge_description;
    public $goods_description;
    public $sub_total;
    public $invoice_date;
    public $container_qty;
    public $length;
    public $full_status;
    public $description;
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
    public $release_instruction;
    public $customer;
    public $is_proforma;


    function topBody(){
        $this->SetAutoPageBreak(true,47);
        $this->SetFont('Times','B',14.3);
        // Title
        $this->Cell(190,7,$this->company_name,0,1,'C');
        $this->AddFont('LiberationSerif','','LiberationSerif.php');
        $this->SetFont('LiberationSerif', '',9.8);
        $this->Cell(190,6,$this->company_address,0,1,'C');
        $this->Cell(190,6,'Tel:'.' '.$this->phone.' '.' E-mail:'.' '.$this->email.' '. 'Website:'.' '.$this->company_web,0,1,'C');
        // Line break
        $this->Ln(3);
        $this->SetFont('Arial','B',9.8);
        $this->Cell(190,6, $this->is_proforma ?  'PRO FORMA SUPPLEMENTARY INVOICE': 'CASH DELIVERY SUPPLEMENTARY INVOICE',0,1,'L');

        $this->Ln(2);
        $this->Cell(190,0,'','B,B',1);
        $this->Ln(3);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(27,5,'Invoice Date:',0,0);
        $this->SetFont('Arial','',8.8);
        $this->Cell(103,5,$this->invoice_date,0,0);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(23,5,'Invoice No.:',0,0);
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
        $this->Ln(2);
    }

    function import_Fbody(){

        $this->SetFont('Arial','B',8.8);
        $this->Cell(38,6,'Importer',0,0);
        $this->Cell(38,6,'Agency',0,0);
        $this->Cell(38,6,'Release Instructions',0,0);
        $this->Cell(38,6,'Customer',0,0);
        $this->Cell(38,6,'Main Invoice',0,1);

        $this->SetFont('Arial','',8.8);
        $width = 38;
        $y = $this->GetY();
        $x = $this->GetX();
        $this->MultiCell($width,5,$this->importer,0,'L',0);
        $this->SetXY($x+$width,$y);
        $y = $this->GetY();
        $x = $this->GetX();
        $this->MultiCell(38,5,$this->agent,0,'L',0);
        $this->SetXY($x+$width,$y);
        $width = 38;
        $y = $this->GetY();
        $x = $this->GetX();
        $this->MultiCell(38,5,$this->release_instruction,0,'L',0);
        $this->SetXY($x+$width,$y);
        $width = 38;
        $y = $this->GetY();
        $x = $this->GetX();
        $this->MultiCell(38,5,$this->customer,0,'L',0);
        $this->SetXY($x+$width,$y);
        $this->MultiCell(38,5,$this->main_invoice,0,'L',0);


        $this->SetFont('Arial','',8.8);
        $y = $this->GetY();
        $x = $this->GetX();
        $width = 63.3;
        $this->MultiCell($width,5,$this->address,0,'L',FALSE);
        $this->SetXY($x+$width,$y);
        $y = $this->GetY();
        $x = $this->GetX();
        $this->MultiCell($width,5,'',0,'',FALSE);
        $this->SetXY($x+$width,$y);
        $this->MultiCell(63.3,5,'',0,'L',FALSE);
        $this->Ln(10);

        $this->SetFont('Arial','B',8.8);
        $this->Cell(65,6,'Vessel',0,0);
        $this->Cell(29.6,6,'Voyage No',0,0);
        $this->Cell(31.6,6,'Arrival Date',0,0);
        $this->Cell(31.6,6,'Departure Date',0,0);
        $this->Cell(31.6,6,'Rotation number',0,1);

        $this->SetFont('Arial','',8.8);
        $this->Cell(65,6,$this->vessel,0,0);
        $this->Cell(29.6,6,$this->voyage_no,0,0);
        $this->Cell(31.6,6,$this->arrival_date,0,0);
        $this->Cell(31.6,6,$this->departure_date,0,0);
        $this->Cell(31.6,6,$this->rotation_no,0,1);
        $this->Ln(8);

        $this->SetFont('Arial','B',8.8);
        $this->Cell(32.5,6,'BL Number',0,0);
        $this->Cell(32.5,6,'BoE Number',0,0);
        $this->Cell(61.2,6,'DO Number',0,0);
        $this->Cell(31.6,6,'Release Date',0,0);
        $this->Cell(31.6,6,'Containers',0,1);

        $this->SetFont('Arial','',8.8);
        $this->Cell(32.5,6,$this->bl_number,0,0);
        $this->Cell(32.5,6,$this->boe_no,0,0);
        $this->Cell(61.2,6,$this->do_number,0,0);
        $this->Cell(31.6,6,$this->release_date,0,0);
        $this->Cell(31.6,6,$this->container_qty,0,1);
        $this->Ln(8);
    }

    function tableHead(){
        $this->SetFont('Arial','B',8.8);
        $this->Cell(127,7,'Charge Description',1,0);
        $this->Cell(15,7,'Qty',1,0,'C');
        $this->Cell(23,7,'Unit Rate',1,0,'C');
        $this->Cell(25,7,'Amount',1,1,'C');
    }

    function tableBody(){
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
            $this->SetFont('Arial', '', 8.8);
            $this->Cell(25, 7, number_format($this->sub_total, 2), 'TRB', 1, 'C');
        }
        $this->SetFont('Arial','',8.8);
        $this->SetFont('Arial','B',8.8);
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
        $this->Cell(4,6,'#',0,0,'C');
        $this->Cell(28,6,'Container No',0,0,'C');
        $this->Cell(21,6,'ISO Type Code',0,0,'C');
        $this->Cell(31,6,'Container Type',0,0,'C');
        $this->Cell(32,6,'Goods Description',0,1,'C');
        $this->Ln(2);
        $this->Cell(190,0,'','B,B',1);
        $this->Ln(2);

    }


    function Footer(){

        $this->SetY(-47);
        $this->Ln(10);
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