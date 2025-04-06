<?php

namespace Api;

class TallyExport{

    private $request;
    private $start_date;
    private $end_date;
    private $type;
    private $status;

    function __construct($request){
        $this->request = $request;
        $start_date = $this->request->param('start');
        $end_date = $this->request->param('end');
        $key = $this->request->param('key');
        $this->type = $this->request->param('type');
        $this->status = $this->request->param('status');

        if(!$this->status)
        {
            $this->status = "paid";
        }
        if(strtolower($this->status) == "all")
        {
            $this->status = "%";
        }

        if($key != "D70F3E2933AE56254030F959DCCBB2C1" || ($this->type != "invoice" && $this->type != "payment")) {
            echo  "FAIL";

            exit;
        }

        $this->start_date = date("Y-m-d", strtotime($start_date));
        $this->end_date = date("Y-m-d", strtotime($end_date));
    }

    function export() {
        $export = new \Lib\TallyExport();
        $export->export($this->start_date, $this->end_date, $this->type, $this->status);
    }

    function export_file() {
        $export = new \Lib\TallyExport();
        $export->exportFile($this->start_date, $this->end_date, $this->type, $this->status);
    }
}


?>
