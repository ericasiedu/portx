<?php

namespace Lib;
use Fpdf\FPDF;

class GatePdf extends FPDF{

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
    public $ourRef;
    public $yourRef;
    public $consignee;
    public $shipper;
    public $vesselName;
    public $voyageNo;
    public $tradeType;
    public $feStatus;
    public $isoCode;
    public $sealNo;
    public $driverId;
    public $bookingNo;
    public $truckCompany;
    public $condition;
    public $bNumber;
    public $bLabel;
    public $gate_type;
    public $widths;
    public $aligns;
    public $b_color;
    public $company_name;
    public $company_address;
    public $phone;
    public $email;
    public $company_web;
    public $gate_date;
    public $sys_user;



    function __construct($trade) {
        parent:: __construct();
        switch ($trade) {
            case 11:
                $this->bLabel = "BL Number";
                break;
            case 21:
            case 70:    
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
        $this->SetY(-47);
        $this->SetFont('Arial','',9);
        $this->cell(3,8,'','T');
        $this->cell(12,8,'For:','T');
        $this->cell(80,8,$this->company_name,'TR');

        $this->cell(3,8,'','T');
        $this->cell(12,8,'For:','T');
        $this->cell(80,8,'Consignee / Shipper / Ship\'s Agent','T',1);

        $this->SetFont('Arial','',6);
        $this->cell(5,8,'');
        $this->cell(90,8,'This container was received/delivered in Good Condition except where noted above','R');
        $this->cell(5,8,'');
        $this->cell(90,8,'This container was received/delivered in Good Condition except where noted above',0,1);

        $this->SetFont('Arial','',9);
        $this->cell(3,8,'',0);
        $this->cell(12,8,'Name:',0);
        $this->cell(80,8,$this->sys_user,'R');

        $this->cell(3,8,'',0);
        $this->cell(12,8,'Name:',0);
        $this->cell(80,8,'','B',1);

        $this->cell(3,8,'','B');
        $this->cell(12,8,'Sign:','B');
        $this->cell(80,8,'','BR');

        $this->cell(3,8,'','B');
        $this->cell(12,8,'Sign:','B');
        $this->cell(80,8,'','B',1);

        $this->Cell(180,8,$this->gate_date,0,0,'L');
        $this->Cell(10,8,'Page'.$this->PageNo().'/{nb}',0,0,'R');
    }

    function titleHeader(){
        $this->SetAutoPageBreak(true,47);
        $this->SetFont('Times','B',15);
        // Title
        $this->Cell(190,7,$this->company_name,0,1,'C');
        $this->SetFont('Times', '',10);
        $this->Cell(190,6,$this->company_address,0,1,'C');
        $this->Cell(190,6,'Tel:'.' '.$this->phone.' '.' E-mail:'.' '.$this->email.' '. 'Website:'.' '.$this->company_web,0,1,'C');
        // Line break
        $this->Ln(6);
    }

    function portHeader(){
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(1, 23, 45);
        $this->SetFont('Arial','B',9);
        $this->Cell(93,5,$this->gate_type,0,0);
        $this->SetFont('Arial','B',9);
        $this->Cell(60,5,'EIR Number:',0,0,'R');
        $this->Cell(40,5,$this->sys_waybill_no,0,1);
        $this->Ln(1);
    }

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function SetBackgroundColor($s){
        $this->b_color=$s;
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=6*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();

            if ($i%2 == 1){
                $this->Rect($x,$y,$w,$h,true);
            }

            //Draw the border
            //Print the text
            $this->MultiCell($w,5,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function portxBody(){
        $this->SetFont('Arial','B',9);
        $this->Cell(100,6,'Basic Details For EIR:',0,1);

        $this->SetFont('Arial','',9); // first
        
        $this->SetFillColor(204, 205, 206);
        $this->SetWidths(array(47.5,47.5,47.5,47.5));
        $this->SetAligns(array('R', 'L', 'R', 'L'));
        $this->Row(array('Liner Code / Name:-',$this->shipping_line,'Shipper:-',$this->shipper));
        $this->Row(array($this->bLabel,$this->bNumber ,'Truck #:',$this->truck));
        $this->Row(array('Driver Name:-',$this->driver_name ,'Driver License #:-',$this->driverId));
        $this->Row(array('Truck Company:-',$this->truckCompany ,'To:--',$this->consignee));
        $this->Row(array('Trade:-',$this->tradeType ,'FE Status:-',$this->feStatus));
        $this->Row(array('ISO Type Code-',$this->isoCode ,'Seal No(s):',$this->sealNo));

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
        $this->Ln(15);
        $y = $this->GetY();
        $this->Cell(190,30,$this->Image($this->container_damages,10, $y, 190),0,1);
        $this->Ln(25);
    }

    function comments(){
        $this->Ln(5);
        $this->SetFont('Arial','B',9);
        $this->Cell(19,7,'Condition:',0,0);
        $this->SetFont('Arial','',9);
        $this->Cell(100,7,$this->condition,0,1);
    }

    // Number of lines a MultiCell would use. http://www.fpdf.org/en/script/script3.php
    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

}

?>
