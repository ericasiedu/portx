<?php

namespace Api;
use Lib\MyQuery;
use Lib\GatePdf;

class GateWaybill {

    private $request,$response;

    public function __construct($request,$response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function portxPdf($cid){

//        $gate_record_id = $this->request->param('gate_record_id') ?? '';

        $id = $cid;

        $qu=new MyQuery();
        $qu->query("SELECT gate_record.user_id, gate_record.date AS date_record, container.id AS container_id, gate_record.id AS gate_record_id,gate_record.waybill, gate_record.date, gate_record.sys_waybill, trucking_company.name 
        AS trucking_company, container.content_of_goods, container.seal_number_1, container.seal_number_2, container.number
        as container_number,letpass_driver.name as letpass_driver,letpass_driver.license as letpass_vehicle, container.importer_address, gate_record.type AS gate_type, shipping_line.name as shipping_name, 
        container_isotype_code.code, gate_record.cond, container.trade_type_code,
        container.full_status, trade_type.name as trade_type_name, 
        container.bl_number, container.book_number,gate_record.vehicle_id,gate_record.driver_id,gate_record.type
        FROM gate_record INNER JOIN container ON gate_record.container_id = container.id 
        INNER JOIN shipping_line 
        ON container.shipping_line_id = shipping_line.id INNER JOIN trade_type 
        ON trade_type.code = container.trade_type_code INNER JOIN trucking_company ON 
        trucking_company.id = gate_record.trucking_company_id INNER JOIN container_isotype_code ON 
        container.iso_type_code = container_isotype_code.id left join letpass_container on letpass_container.container_id = gate_record.container_id 
        left join letpass_driver on letpass_driver.letpass_id = letpass_container.letpass_id WHERE gate_record.id =?");
        $qu->bind = array('i',&$id);
        $res=$qu->run();
        $result = $res->fetch_assoc();
        $container_id = $result['container_id'];
        $gate_record_id = $result['gate_record_id'];


        $qu2 = new MyQuery();
        $qu2->query("select number from vehicle where id =?");
        $qu2->bind = array('i',&$result['vehicle_id']);
        $ces2 = $qu2->run();
        $resuu2 = $ces2->fetch_assoc();

        $qu3 = new MyQuery();
        $qu3->query("select name from vehicle_driver where id=?");
        $qu3->bind = array('i',&$result['driver_id']);
        $ces3 = $qu3->run();
        $resuu3 = $ces3->fetch_assoc();

        $user_id = $result['user_id'];
        $su = new MyQuery();
        $su->query("select first_name,last_name from user where id = ?");
        $su->bind = array('i',$user_id);
        $res2 = $su->run();
        $result2 = $res2->fetch_assoc();

        $driver_query = new MyQuery();
        $driver_query->query("select license from vehicle_driver where id=?");
        $driver_query->bind = array('i',&$result['driver_id']);
        $driver_query->run();
        $driver_license = $driver_query->fetch_assoc();
       

        $tu = new MyQuery();
        $tu->query("SELECT  company_tin, company_phone1, company_name, company_phone1, company_web, company_email, company_location, prefix FROM system");
        $resi=$tu->run();
        $sys = $resi->fetch_assoc();

        $pdf = new GatePdf($result['trade_type_code']);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetTitle($result['container_number']);
        $pdf->sys_waybill_no = $result['sys_waybill'];
        $pdf->waybill_no = $result['waybill'];
        switch ($result['gate_type']) {
            case "GATE IN":
                $pdf->gate_type = "Gate In";
                break;
            case "GATE OUT":
                $pdf->gate_type = "Gate Out";
                break;
            default:
                $pdf->gate_type = "NA";
        }
        $pdf->company_name = $sys['company_name'];
        $pdf->company_address = $sys['company_location'];
        $pdf->company_web = $sys['company_web'];
        $pdf->tin_no = $sys['company_tin'];
        $pdf->phone = $sys['company_phone1'];
        $pdf->email = $sys['company_email'];
        $pdf->sys_user = $result2['first_name']." ".$result2['last_name'];
        $pdf->shipping_line = $result['shipping_name'];
        $pdf->seal = $result['seal_number_1'];
        $pdf->container_no = $result['container_number'];
        $pdf->shipper = $result['importer_address'];
        $pdf->date = $result['date'];
        $pdf->truck = $result['type'] == "GATE IN"  || ($result['trade_type_code'] != 11 && $result['trade_type_code'] != 13) ? $resuu2['number'] :$result['letpass_vehicle'];
        $pdf->activity = $result['type'] == "GATE IN" && $result['trade_type_code'] == 13 ? "IMPORT" : $result['trade_type_name'];
        $pdf->driver_name =  $result['type'] == "GATE IN" || ($result['trade_type_code'] != 11 && $result['trade_type_code'] != 13) ? $resuu3['name'] : $result['letpass_driver'];
        $pdf->truckCompany = $result['trucking_company'];
        $pdf->tradeType = $result['type'] == "GATE IN" && $result['trade_type_code'] == 13 ? "IMPORT" : $result['trade_type_name'];
        $pdf->feStatus = $result['full_status'] == 1 ? 'FULL' : 'EMPTY';
        $pdf->isoCode = $result['code'];
        $pdf->gate_date = (new \DateTime($result['date_record']))->format('d M Y') . ' at ' . (new \DateTime($result['date_record']))->format('g:ia');
        if ($result['seal_number_1'] != NULL && $result['seal_number_2'] != NULL){
            $pdf->sealNo = $result['seal_number_1'].", ".$result['seal_number_2'];
        }
        elseif ($result['seal_number_1'] != NULL && $result['seal_number_2'] == NULL){
            $pdf->sealNo = rtrim($result['seal_number_1'], ',');
        }
        elseif ($result['seal_number_1'] == NULL && $result['seal_number_2'] != NULL){
            $pdf->sealNo = rtrim($result['seal_number_2'], ',');
        }

        
        $pdf->driverId = $result['gate_type'] != "GATE OUT" ? $driver_license['license'] : "NA";
        $pdf->condition = $result['cond'];
        $pdf->gate_pass_no = '';
        switch ($result['trade_type_code']) {
            case 11:
            case 13:
                $pdf->bNumber = $result['bl_number'];
                break;
            case 21:
                $pdf->bNumber = $result['book_number'];
                break;
            case 70:
                $pdf->bNumber = $result['gate_type'] == 'GATE OUT' ? $result['book_number'] : "NA";
                break;
            default:
                $pdf->bNumber = "NA";
        }
        $pdf->container_damages =stream_resolve_include_path('./PortX').'/Images/Eir.jpg';
        $pdf->company_name = html_entity_decode($pdf->company_name);
        $pdf->company_address = html_entity_decode($pdf->company_address);
        $pdf->shipping_line = html_entity_decode($pdf->shipping_line);
        $pdf->shipper = html_entity_decode($pdf->shipper);
        $pdf->consignee = html_entity_decode($pdf->consignee);
        $pdf->truckCompany = html_entity_decode($pdf->truckCompany);
        $pdf->driver_name = html_entity_decode($pdf->driver_name);


        $pdf->titleHeader();

        $pdf->portHeader();
        $pdf->portxBody();
        $pdf->tableBody();
        $pdf->comments();
        $qu4 = new MyQuery();
        $qu4->query("SELECT container_damage_type.name AS damages, gate_record_container_condition.damage_severity, container_section.name AS container_section, gate_record_container_condition.note 
              FROM gate_record_container_condition INNER JOIN container_damage_type ON container_damage_type.id = gate_record_container_condition.damage_type 
              INNER JOIN gate_record ON gate_record_container_condition.gate_record = gate_record.id INNER JOIN container_section 
              ON container_section.id = gate_record_container_condition.container_section WHERE gate_record_container_condition.gate_record = '$gate_record_id' AND gate_record.container_id = '$container_id'");
        $res1 = $qu4->run();

        $pdf->Ln(5);

        if($pdf->condition === "NOT SOUND") {
            // Condition Table
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(100, 5, 'Container conditions:', 0, 1);
            $pdf->SetFont('Arial', 'B', 9.5);
            $pdf->SetFillColor(193, 193, 193);
            $pdf->Cell(40, 6, 'Container Section', 1, 0, 'C', true);
            $pdf->Cell(40, 6, 'Damage Type', 1, 0, 'C', true);
            $pdf->Cell(40, 6, 'Severity', 1, 0, 'C', true);
            $pdf->Cell(60, 6, 'Note', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 9);

            while ($result1 = $res1->fetch_assoc()) {
                $nbLines = max(1, $pdf->NbLines(60, $result1['note']));
                $pdf->Cell(40, 6 * $nbLines, $result1['container_section'], 1, 0, 'C');
                $pdf->Cell(40, 6 * $nbLines, $result1['damages'], 1, 0, 'C');
                $pdf->Cell(40, 6 * $nbLines, $result1['damage_severity'], 1, 0, 'C');
                $pdf->MultiCell(60, 6, $result1['note'], 1, 'L');
            }
        }
        $pdf->Output();
    }



}

?>
