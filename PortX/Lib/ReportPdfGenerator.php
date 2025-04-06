<?php
namespace Lib;
use Fpdf\FPDF;

class ReportPdfGenerator extends FPDF{

    public $widths;
    public $height;
    public $aligns;
    public $start_date;
    public $end_date;
    public $title;
    public $columns;


    function Header(){
        $this->SetFont('Arial','',12);
        $this->Cell(190,9,$this->title,'0','1','C');
        $this->Ln(3);
        $this->SetFont('Arial','B',9);
        $this->SetFillColor(21, 44, 82);
        $this->SetTextColor(255, 255, 255);
        $this->SetAligns(array('R', 'L', 'R', 'L'));
        $this->Row($this->columns);
        $this->SetFillColor(255, 255, 255);
    }

    function SetColumnHeaders($headers)
    {
         $this->SetFont('Arial','B',9);
        $this->columns = $headers;
    }

    function SetTitle($title, $isUTF8=false)
    {
        parent::SetTitle($title, $isUTF8);

        $this->title = $title;
    }



    function SetColumnWidths($widths)
    {
        $this->SetWidths($widths);
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
//        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
//            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();

                $this->Rect($x,$y,$w,$h,true);


            //Draw the border
            //Print the text
            $this->MultiCell($w,5,is_null($data[$i])? 0 : $data[$i],0);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function bodyRow($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=6*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h + 20);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
//            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();

            $this->Rect($x,$y,$w,$h,true);


            //Draw the border
            //Print the text
            $this->MultiCell($w,5,is_null($data[$i])? 0 : $data[$i],0);
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

    function Footer(){
        $this->SetY(-15);
        $this->Cell(20,7,'Page '.$this->PageNo()."/{nb}". ($this->start_date ? "      Date: $this->start_date - $this->end_date" : ""),0,0);
    }
}

?>