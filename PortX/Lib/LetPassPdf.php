<?php
namespace Lib;
use Fpdf\FPDF;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LetPassPdf extends  FPDF {
    public $letpass_number;
    public $letpass_id;
    public $letpass_status;
    public $letpass_date;
    public $letpass_invoice_id;
    public $letpass_containers;
    public $letpass_drivers;
    public $letpass_bnumber;
    public $invoice_number;
    public $invoice_do_number;
    public $company_name;
    public $company_address;
    public $departure_date;
    public $agent;
    public $weight;
    public $arrival_gate;
    public $voyage;
    public $vessel;
    public $user;
    public $rotation;
    public $phone;
    public $email;
    public $company_web;
    public $tin_no;
    public $widths;
    public $aligns;
    public $b_color;
    public $boe_number;

    function __construct($letpass) {
        parent:: __construct();

        $this->letpass_number =  $letpass;
    }

    function Header(){
        $this->SetFont('Arial','B',12);
        $this->Cell(190,5,'',0,0,'C');
        $this->Ln();
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
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

    function Row($data, $border = 0)
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

            if($border) {
                $this->Rect($x, $y, $w, $h, false);
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

    function Footer(){
        $this->SetY(-47);
        $this->SetFont('Times','',9);
        $this->Cell(85, 9, 'Checked By', 'T', 0);

        $this->SetX($this->GetX() + 20);
        $this->Cell(85, 9, 'Remarks', 'T', 1);
        $this->Ln(10);
        $this->Cell(90,8,date( 'd M Y' ,strtotime($this->letpass_date)) . ' at '. date( 'h:i a' ,strtotime($this->letpass_date)),'T',0,'L');
        $this->Cell(90, 8, 'By: ' . $this->user, 'T', 0 , 'L');
        $this->Cell(10,8,'Page'.$this->PageNo().'/{nb}','T',0,'R');
    }

    public  function  generate(){
        $letpass = &$this->letpass_number;
        $qu = new MyQuery();
        $qu->query("Select invoice.boe_number,invoice.number as invoice_id,invoice.number as inv_num, letpass.id as id, letpass.status as status, letpass.date as date , invoice.bl_number, invoice.book_number, invoice.do_number, user.first_name, user.last_name
from letpass 
LEFT JOIN invoice on invoice.id = invoice_id
LEFT JOIN user on user.id = letpass.user_id where letpass.number = ?");
        $qu->bind = array('s', &$letpass);
        $res = $qu->run();

        $letpass_details = $res->fetch_assoc();

        $this->letpass_id = $letpass_details['id'];
        $this->letpass_invoice_id = $letpass_details['invoice_id'];
        $this->letpass_status = $letpass_details['status'];
        $this->invoice_number = $letpass_details['inv_num'];
        $this->letpass_date = $letpass_details['date'];
        $this->letpass_bnumber = $letpass_details['bl_number'];
        $this->invoice_do_number = $letpass_details['do_number'];
        $this->user = $letpass_details['first_name'] . ($letpass_details['last_name'] ? ' '.$letpass_details['last_name'] : '');
        $this->boe_number = $letpass_details['boe_number'];
        if(!$this->letpass_bnumber){
            $this->letpass_bnumber = $letpass_details['book_number'];
        }


        if(!is_null($this->letpass_id)) {
            $this->AliasNbPages();
            $this->AddPage();
            $this->SetTitle($this->letpass_number);

            $qu = new MyQuery();
            $qu->query("SELECT  company_tin, company_phone1, company_name, company_phone1, company_web, company_email, company_location, prefix FROM system");
            $res = $qu->run();
            $sys = $res->fetch_assoc();

            $this->company_name = $sys['company_name'];
            $this->company_address = $sys['company_location'];
            $this->company_web = $sys['company_web'];
            $this->tin_no = $sys['company_tin'];
            $this->phone = $sys['company_phone1'];
            $this->email = $sys['company_email'];

            $qu = new MyQuery();
            $qu->query("Select distinct content_of_goods as good, container.number as number, agency.name as agent, gate_record.consignee,
                            voyage.reference, voyage.rotation_number,voyage.actual_departure, voyage.gross_tonnage, vessel.name
                              from container 
                              LEFT JOIN letpass_container on container.id =  letpass_container.container_id
                              LEFT JOIN gate_record on container.id =  gate_record.container_id
                              LEFT JOIN agency on agency.id =  container.agency_id
                              LEFT JOIN voyage on container.voyage = voyage.id
                              LEFT JOIN vessel ON voyage.vessel_id = vessel.id
                              where letpass_id = ?");
            $qu->bind = array('i', &$this->letpass_id);
            $res = $qu->run();

            $this->letpass_containers = array();
            while($container = $res->fetch_assoc()){
                $this->letpass_containers[] = array('number'=> html_entity_decode($container['number']),
                                                    'goods' => html_entity_decode($container['good']),
                                                    'consignee' => html_entity_decode($container['consignee']),
                                                    'agent' => html_entity_decode($container['agent']));

                $this->weight = $container['gross_tonnage'];
                $this->departure_date = $container['actual_departure'];
                $this->voyage = $container['reference'];
                $this->vessel = $container['name'];
                $this->agent = $container['agent'];
                $this->rotation = $container['rotation_number'];
            }

            $qu = new MyQuery();
            $qu->query("Select *  from letpass_driver
                              where letpass_id = ?");
            $qu->bind = array('i', &$this->letpass_id);
            $res = $qu->run();

            $this->letpass_drivers = array();
            while($driver = $res->fetch_assoc()){
                $this->letpass_drivers[] = array('name'=> $driver['name'],
                    'license' => $driver['license']);
            }


            $this->company_name = html_entity_decode($this->company_name);
            $this->company_address = html_entity_decode($this->company_address);
            $this->agent = html_entity_decode($this->agent);
            $this->voyage = html_entity_decode($this->shipper);
            $this->vessel = html_entity_decode($this->consignee);

            $this->titleHeader();

            $this->writeLetPassHeader();

            $this->writeBody();

            $this->Output();
        }
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

    function writeLetPassHeader(){
        $this->SetTextColor(1, 23, 45);
        $this->SetFont('Times','B',9);
        $this->Cell(110,5,"LET PASS",'B',0);
        $this->Cell(0,5,'CONSIGNEE COPY','B',1,'R');
        $this->Ln(1);

        $this->SetFont('Times','',9);
        $this->Ln(2);
        $this->SetX(150);
        $this->Cell(50, 5, "Pass No:    " . $this->letpass_number, '', 1, '');
        $this->SetX(150);
        $this->Cell(50, 5, "Pass Date:  " . date("d-m-Y", strtotime($this->letpass_date)), '', 1, '');
        $this->Ln(2);
        $this->Cell(0, 1, ' ', 'B', 1);
    }

    function writeBody(){
        $label_width = 40;

        $this->Cell($label_width, 5, "Vessel",0, 0);

        $this->SetX(100);
        $this->Cell($label_width, 5, "Rotation No.",0, 0);
        $this->Cell($label_width, 5, "BOE Number",0, 0);
        $this->Cell($label_width, 5, "Release Date",0, 1);

        $this->Cell($label_width, 5, $this->vessel,0, 0);
        $this->SetX(100);
        $this->Cell($label_width, 5, $this->rotation,0, 0);
        $this->Cell($label_width, 5, $this->boe_number,0, 0);
        $this->Cell($label_width, 5, \date('d-m-Y', strtotime($this->letpass_date)),0, 1);
        $this->Ln(5);

        $label_width = 34;

        $this->Cell($label_width, 5, "B/L Number",0, 0);
        $this->Cell($label_width, 5, "Invoice Number",0, 0);
        $this->Cell($label_width, 5, "DO Number",0, 0);
        $this->Cell($label_width, 5, "Arrival Gate",0, 0);
        $this->Cell($label_width, 5, "Departure Date",0, 0);
        $this->Cell($label_width, 5, "Gross Weight",0, 1);

        $this->Cell($label_width, 5, $this->letpass_bnumber,'B', 0);
        $this->Cell($label_width, 5, $this->invoice_number,'B', 0);
        $this->Cell($label_width, 5, $this->invoice_do_number,'B', 0);
        $this->Cell($label_width, 5, "",'B', 0);
        $this->Cell($label_width, 5, date("d-m-Y", strtotime($this->departure_date)),'B', 0);
        $this->Cell(0, 5, $this->weight,'B', 1);

        $column_width = 96;

        $this->SetWidths(array($column_width, $column_width));
        $this->SetAligns(array('L', 'L'));

        foreach ($this->letpass_drivers as $driver){
            $this->ln(3);

            $this->Cell($column_width, 5, "Driver's Name", 0, 0);
            $this->Cell($column_width, 5, "Vehicle No.", 0, 1);
            $this->Row(array($driver['name'], $driver['license']));

            $this->Ln(5);
            $this->Cell(0, 1, '', 'B', 1);
        }

        $this->Ln(5);

        $this->Cell($column_width, 5, "Containers", 0, 1);
        $this->Ln(1);
        $this->SetWidths(array(48,48,48,48));
        $this->SetAligns(array('L', 'L', 'L', 'L'));
        $this->Row(array("Number", "Agent", "Consignee", "Goods"), true);
        foreach ($this->letpass_containers as $container){
            $this->Row(array($container['number'],$container['agent'],$container['consignee'],$container['goods']), true);
        }
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