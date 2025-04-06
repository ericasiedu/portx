<?php

namespace Lib;
use Fpdf\FPDF;

class ImportReceiptPdf extends FPDF{
    public $company_name;
    public $reciept_no;
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
    public $outstanding;
    public $recieved_date;
    public $invoice_no;
    public $user;
    public $payment_mode;
    public $currency;
    public $trade_type;


    function topBody(){
        $this->SetFont('Times','B',14.3);
        // Title
        $this->Cell(190,7,$this->company_name,0,1,'C');
        $this->AddFont('LiberationSerif','','LiberationSerif.php');
        $this->SetFont('LiberationSerif', '',9.8);
        $this->Cell(190,6,$this->company_address,0,1,'C');
        $this->Cell(190,6,'Tel +233'.'  '.$this->phone.' '.' e-mail:'.' '.$this->email.', '. 'website:'.' '.$this->company_web,0,1,'C');
        // Line break
        $this->Ln(3);
        $this->SetFont('Arial','B',9.8);
        $this->Cell(190,6,'RECEIPT ('.$this->trade_type.')',0,1,'L');
        $this->Ln(2);
        $this->Cell(190,0,'','B,B',1);
        $this->Ln(3);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(19,5,'DATE:',0,0);
        $this->SetFont('Arial','',8.8);
        $this->Cell(119,5,$this->invoice_date,0,0);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(23,5,'Receipt No:',0,0);
        $this->SetFont('Arial','',8.8);
        $this->Cell(37,5,$this->reciept_no,0,1);
        $this->Ln(3);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(19,5,'Invoice No:',0,0);
        $this->SetFont('Arial','',8.8);
        $this->cell(129.5,5,$this->invoice_no,0,0);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(13,5,'TIN:',0,0);
        $this->SetFont('Arial','',8.8);
        $this->Cell(29,5,$this->tin_no,0,1);
        $this->Ln(2);
        $this->Cell(190,0,'','B,B',1);
        $this->Ln(2);
    }

    function import_Fbody(){

        $this->SetFont('Arial','B',8.8);
        $this->Cell(63.3,6,'Consignee / Importer',0,0);
        $this->Cell(63.3,6,'Agent',0,0);
        $this->Cell(63.3,6,'',0,1);
        $this->SetFont('Arial','',8.8);
        $this->Cell(63.3,6,$this->importer,0,0);
        $this->Cell(63.3,6,$this->agent,0,0);
        $this->Cell(63.3,6,'',0,1);

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
        $this->Cell(63.33,7,'Amount Paid('.$this->currency.')',1,0);
        $this->Cell(63.33,7,'Amount Outstanding('.$this->currency.')',1,0);
        $this->Cell(63.33,7,'Payment Mode',1,0);
    }

    function tableBody(){




        $this->Ln(7);

        $this->SetFont('Arial','B',8.8);
        $this->Cell(63.33,7,$this->amount,1,0);
        $this->Cell(63.33,7,$this->outstanding,1,0);
        $this->Cell(63.33,7,$this->payment_mode,1,0);
        $this->Ln(7);
    }

    function secondBody(){
    }

    function  tableFooter(){
        $this->SetY(-42);
        $this->SetFont('Arial','B',8.8);
        $this->Cell(95,7,'','B,B',0);
        $this->Cell(95,7,'',0,1);
        $this->SetX(45);
        $this->Cell(95,7,'Received By',0,0);
        $this->Cell(95,7,'',0,1);
    }

// Page footer
    function Footer()
    {
        // Position at 1.5 cm from botto

        $this->SetY(-28);
        // Arial italic 8
        $this->SetFont('Arial','B',8.8);
        $this->Cell(190,0,'','B,B',1);
        // Page number
        $this->Cell(20,7,'Page '.$this->PageNo().'/{nb}',0,0);
        $this->Cell(50,7,$this->recieved_date,0,0,'C');
        $this->SetFont('Arial','B',8.8);
        $this->Cell(60,7,'By '.$this->user,0,0,'C');
        $this->Cell(60,7,'Distribution: CUSTOMER / BANK',0,0,'C');
        $this->ln(2);
        $this->SetFont('Times','',8.8);
        $this->SetY(-16);
        $this->Cell(190,5,$this->company_address,0,1,'C');
        $this->Cell(190,5,'Tel +233'.'  '.$this->phone.' '.' e-mail:'.' '.$this->email.', '. 'website:'.' '.$this->company_web,0,1,'C');
    }
}

?>