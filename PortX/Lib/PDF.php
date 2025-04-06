<?php

namespace Lib;
use Fpdf\FPDF;

class PDF extends FPDF{

    public $container_no;
    public $waybill_no;
    public $gate_pass_no;
    public $content_of_goods;
    public $seal;
    public $shipping_line;
    public $truck;
    public $driver_name;
    public $activity;
    public $date;
    public $sys_waybill_no;
    public $container_damages;
    public $condition;
    public $bNumber;
    public $bLabel;

    function __construct($trade) {
        parent:: __construct();
        switch ($trade) {
            case 11:
                $this->bLabel = "BL Number";
                break;
            case 21:
                $this->bLabel = "Booking No";
                break;
            default:
                $this->bLabel = "BL Number";
        }

    }

    function Header(){
        $this->SetFont('Arial','B',12);
        $this->Cell(190,5,'',0,0,'C');
        $this->Ln();
    }

    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','',9);
        $this->Cell(0,6,'Page'.$this->PageNo().'/{nb}',0,0,'C');
    }

    function portHeader(){
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(1, 23, 45);
        $this->Cell(15,5,'Pro Port',0,0);
        $this->SetFont('Arial','Bu',9);
        $this->Cell(95,5,'Terminal',0,0);
        $this->SetFont('Arial','B',9);
        $this->Cell(60,5,'EIR Number:',0,0,'R');
        $this->Cell(40,5,$this->sys_waybill_no,0,1);
        $this->SetFont('Arial','B',9);
        $this->Cell(15,5,'',0,0);
        $this->Cell(95,5,'',0,0);
        $this->SetFont('Arial','B',9);
        $this->Cell(60,5,'Gate Pass Number:',0,0,'R');
        $this->Cell(40,5,$this->gate_pass_no,0,0);
        $this->Ln(10);
    }

    function portxBody(){
        $this->SetFont('Arial','B',9);
        $this->Cell(100,6,'Basic Details For EIR:',0,1);
        $this->SetFont('Arial','',9);
        $this->Cell(45,6,'Liner Code / Name:-',0,0,'R');
        $this->SetFillColor(204, 205, 206);
        $this->Cell(145,6,$this->shipping_line,0,1,'L',true);
        $this->Cell(45,6,'Shipment / Shipper:-',0,0,'R');
        $this->SetFillColor(204, 205, 206);
        $this->Cell(145,6,$this->content_of_goods,0,1,'L',true);
        $this->Cell(45,6,$this->bLabel . ':-',0,0,'R');
        $this->SetFillColor(204, 205, 206);
        $this->Cell(145,6,$this->bNumber,0,1,'L',true);
        $this->Cell(45,6,'Truck #/ Transporter:-',0,0,'R');
        $this->SetFillColor(204, 205, 206);
        $this->Cell(145,6,$this->truck,0,1,'L',true);
        $this->Cell(45,6,'Driver Name/License:-',0,0,'R');
        $this->SetFillColor(204, 205, 206);
        $this->Cell(145,6,$this->driver_name,0,0,'L',true);
        $this->Ln(10);
    }

    function tableBody(){

//        $pdf_image = "../Images/Eir.jpg";

        $this->SetFont('Arial','B',9);
        $this->Cell(100,5,'Container details for EIR:',0,1);
        $this->SetFont('Arial','B',9.5);
        $this->SetFillColor(193, 193, 193);
        $this->Cell(40,6,'Container Number',1,0,'C',true);
        $this->Cell(40,6,'Activity',1,0,'C',true);
        $this->Cell(34,6,'Date',1,0,'C',true);
        $this->Cell(30,6,'Seal',1,0,'C',true);
        $this->Cell(30,6,'Vent',1,0,'C',true);
        $this->Cell(16,6,'Temp',1,1,'C',true);

        $this->SetFont('Arial','',9);
        $this->Cell(40,6,$this->container_no,1,0,'C');
        $this->Cell(40,6,$this->activity,1,0,'C');
        $this->Cell(34,6,$this->date,1,0,'C');
        $this->Cell(30,6,$this->seal,1,0,'C');
        $this->Cell(30,6,'',1,0,'C');
        $this->Cell(16,6,'',1,1,'C');
        $this->Ln(5);
        $this->SetFont('Arial','B',9.5);
        $this->Cell(16,6,'Mark Container Damage Locations with ISO Damage Codes');
        $this->Ln(20);
        $this->Cell(190,30,$this->Image($this->container_damages,30,105,150),0,1);
        $this->Ln(20);
    }

    function comments(){
        $this->SetFont('Arial','B',9);
        $this->Cell(19,7,'Condition:',0,0);
        $this->SetFont('Arial','B',9);
        $this->Cell(100,7,$this->condition,0,1);
    }

}

?>
