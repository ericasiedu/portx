<?php
namespace Lib;
// session_start();
use PhpOffice\PhpSpreadsheet\Spreadsheet,
    PhpOffice\PhpSpreadsheet\Writer\Xlsx,
    PhpOffice\PhpSpreadsheet\Calculation\DateTime;

class ReportGenerator{
    private $start_date;
    private $end_date;
    private $title;
    private $headers;
    private $data;
    private $widths;
    private $path;
    private $totals;

    public function __construct($start_date, $end_date, $title, $headers, $data, $widths, $totals=null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->title = $title;
        $this->headers = $headers;
        $this->data = $data;
        $this->widths = $widths;
        $this->totals = $totals;
        $this->path = stream_resolve_include_path('./PortX')."/Reports/";

        if(!file_exists($this->path)){
            $old = umask(0);
            mkdir($this->path, "0755", true);
            chmod($this->path, '0755');
            umask($old);
        }
    }

    public function printPdf(){
        $pdf = new ReportPdfGenerator();
        $pdf->SetColumnHeaders($this->headers);
        $start_date = date_create($this->start_date);
        $end_date = date_create($this->end_date);
        $start_date = date_format($start_date,'d M Y');
        $end_date = date_format($end_date,'d M Y');
        $pdf->SetTitle("$this->title \t ". ($this->start_date ? "$start_date - $end_date" : ""));
        $pdf->SetWidths($this->widths);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 1);
        $pdf->SetFont('Arial','');
        $pdf->SetTextColor(0, 0, 0);
        $start_date = date_create($this->start_date);
        $end_date = date_create($this->end_date);
        $pdf->start_date = $this->start_date ? date_format($start_date,'d M Y') : "";
        $pdf->end_date =  $this->end_date ? date_format($end_date,'d M Y') : "";

        $counter = 0;

        foreach ($this->data as $row) {
            $counter++;

            $pdf->SetFont('Arial','',9);
            $pdf->SetWidths($this->widths);
            $pdf->SetAligns(array('R', 'L', 'R', 'L'));
            if ($counter%2 != 0){
                $pdf->SetFillColor(237, 237, 237);
            }
            $pdf->bodyRow($row);
            $pdf->SetFillColor(255, 255, 255);
        }

        if (!is_null($this->totals)) {
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(125, 240, 255);
            $pdf->bodyRow($this->totals);          
            $pdf->SetFillColor(255, 255, 255);
        }


        $date = date('Ymd');
        $num = rand(1,1000);
        $strRand = $date.$num;


        $file_name = "$this->title-$strRand.pdf";

        while (true){
            $desti = $this->path.$file_name;

            if(file_exists($desti))
            {
                $num = rand(1, 1000);
                $strRand = $date . $num;
                $file_name = "$this->title-$strRand.pdf";
            }
            else{
                break;
            }
        }

        $desti = $this->path.$file_name;
        $pdf->Output('F',$desti);

        new Respond(252, array('file'=>$file_name));
    }

    public function printExcel(){
        $start_date = date_create($this->start_date);
        $end_date = date_create($this->end_date);
        $start_date = date_format($start_date,'d M Y');
        $end_date = date_format($end_date,'d M Y');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', "$this->title". ($this->start_date ?  "     $start_date - $end_date" : ""));

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);

        $column = "A";
        for($i = 0; $i < count($this->headers); $i++){
            $column = chr(ord("A")+$i);
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $sheet->setCellValue($column."2",$this->headers[$i]);
        }

        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(12);

        $spreadsheet->getActiveSheet()->mergeCells('A1:'.$column.'1');

        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => '12'
            ],
            [
                'wrapText' => true
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_JUSTIFY,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $styleArray1 = [
            'font' => [
                'bold' => true,
                'size' => '11'
            ],
            [
                'wrapText' => true
            ]
        ];
        $spreadsheet->getActiveSheet()->getStyle('A2:'.$column.'2')->applyFromArray($styleArray1);

        $i = 3;

        foreach ($this->data as $row) {
            for($j = 0; $j < count($row); $j ++)
            {
                $column = chr(ord("A")+$j);

                $value = $row[$j];

                $value = trim(is_null($value)? 0 : $value);

                $sheet->setCellValue($column.$i, $value);
            }
            $i++;
        }

        if (!is_null($this->totals)) {
            for($j = 0; $j < count($this->totals); $j ++)
            {
                $column = chr(ord("A")+$j);

                $value = $this->totals[$j];

                $value = trim(is_null($value)? 0 : $value);

                $sheet->setCellValue($column.$i, $value);
            }
        }
        $spreadsheet->getActiveSheet()->getStyle("A$i:" . $column . $i)->applyFromArray($styleArray1);

        $writer = new Xlsx($spreadsheet);

        $date = date('Ymd');
        $num = rand(1,1000);
        $strRand = $date.$num;

        $file_name = "$this->title-$strRand.xlsx";

        while (true){
            $desti = $this->path.$file_name;

            if(file_exists($desti))
            {
                $date = date('Ymd');
                $num = rand(1, 1000);
                $strRand = $date . $num;
                $file_name = "$this->title-$strRand.xlsx";
            }
            else{
                break;
            }
        }

        $desti = $this->path.$file_name;

        $writer->save($desti);

        new Respond(252, array('file'=>$file_name));
    }

}

?>